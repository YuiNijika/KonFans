<?php

/**
 * Anon Install
 */
if (!defined('ANON_ALLOWED_ACCESS')) exit;

if (Anon_Config::isInstalled()) {
    die("系统已安装，无法重复安装。<br />如需重新安装，请删除 installed.lock 文件并修改 env.php。");
}

// 读取配置
$db_host = defined('ANON_DB_HOST') ? ANON_DB_HOST : 'localhost';
$db_port = defined('ANON_DB_PORT') ? ANON_DB_PORT : 3306;
$db_user = defined('ANON_DB_USER') ? ANON_DB_USER : 'root';
$db_pass = defined('ANON_DB_PASSWORD') ? ANON_DB_PASSWORD : '';
$db_name = defined('ANON_DB_DATABASE') ? ANON_DB_DATABASE : '';
$db_prefix = defined('ANON_DB_PREFIX') ? ANON_DB_PREFIX : 'anon_';

/**
 * 更新配置文件
 */
function updateConfig($dbHost, $dbUser, $dbPass, $dbName, $dbPrefix)
{
    $configFile = __DIR__ . '/../../../env.php';
    $content = file_get_contents($configFile);

    // 更新数据库配置
    $content = preg_replace("/define\('ANON_DB_HOST', '[^']*'\)/", "define('ANON_DB_HOST', '$dbHost')", $content);
    $content = preg_replace("/define\('ANON_DB_USER', '[^']*'\)/", "define('ANON_DB_USER', '$dbUser')", $content);
    $content = preg_replace("/define\('ANON_DB_PASSWORD', '[^']*'\)/", "define('ANON_DB_PASSWORD', '$dbPass')", $content);
    $content = preg_replace("/define\('ANON_DB_DATABASE', '[^']*'\)/", "define('ANON_DB_DATABASE', '$dbName')", $content);

    // 新增前缀更新
    $content = preg_replace("/define\('ANON_DB_PREFIX', '[^']*'\)/", "define('ANON_DB_PREFIX', '$dbPrefix')", $content);

    // 更新安装状态
    $content = preg_replace("/define\('ANON_INSTALLED', [^;]*\);/", "define('ANON_INSTALLED', true);", $content);

    file_put_contents($configFile, $content);
}

/**
 * 执行 SQL 文件
 */
function executeSqlFile($conn, $sqlFile, $tablePrefix)
{
    if (!file_exists($sqlFile)) {
        die("SQL 文件不存在，请检查路径: $sqlFile");
    }
    $sqlContent = file_get_contents($sqlFile);
    $sqlContent = str_replace('{prefix}', $tablePrefix, $sqlContent);

    $queries = array_filter(array_map('trim', explode(';', $sqlContent)));
    foreach ($queries as $query) {
        if (!empty($query) && !$conn->query($query)) {
            die("SQL 执行错误: " . $conn->error);
        }
    }
}

/**
 * 插入初始用户数据
 */
function insertUserData($conn, $username, $password, $email, $tablePrefix, $group = 'admin')
{
    $tableName = $tablePrefix . 'users';
    $stmt = $conn->prepare("INSERT INTO $tableName (name, password, email, `group`) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        die("SQL 语句错误: " . $conn->error);
    }
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $stmt->bind_param("ssss", $username, $hashedPassword, $email, $group);
    return $stmt->execute();
}

/**
 * 验证表单输入
 */
function validateInput($data)
{
    return htmlspecialchars(trim($data));
}

/**
 * 自定义错误处理
 */
function handleError($message)
{
    error_log($message); // 记录到日志
    echo "发生错误，请稍后重试。";
    exit;
}

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['db_host'])) {
    try {
        // 获取并验证数据库连接信息
        $db_host = validateInput($_POST['db_host']);
        $db_user = validateInput($_POST['db_user']);
        $db_pass = validateInput($_POST['db_pass']);
        $db_name = validateInput($_POST['db_name']);
        $db_prefix = validateInput($_POST['db_prefix']);

        if (empty($db_host) || empty($db_user) || empty($db_name)) {
            throw new Exception("所有数据库连接字段都是必填的。");
        }

        // 更新配置文件
        updateConfig($db_host, $db_user, $db_pass, $db_name, $db_prefix);

        // 重新加载配置文件
        require_once __DIR__ . '/../../../env.php';

        // 连接到数据库
        $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
        if ($conn->connect_error) {
            throw new Exception("数据库连接失败: " . $conn->connect_error);
        }
        echo "数据库连接成功！<br>";

        // 执行 SQL 文件
        $sqlFile = __DIR__ . '/Mysql.sql';
        executeSqlFile($conn, $sqlFile, $db_prefix);
        echo "数据表创建成功！<br>";

        // 创建初始用户
        if (isset($_POST['username'])) {
            $username = validateInput($_POST['username']);
            $password = validateInput($_POST['password']);
            $email = validateInput($_POST['email']);

            if (empty($username) || empty($password) || empty($email)) {
                throw new Exception("所有字段都是必填的。");
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("邮箱格式不正确。");
            }

            if (insertUserData($conn, $username, $password, $email, $db_prefix, 'admin')) {
                // 确保这一行被执行到
                file_put_contents(__DIR__ . '/installed.lock', 'Installed at ' . date('Y-m-d H:i:s'));
                echo "<script>alert('安装成功！'); window.location.href='/';</script>";
                exit; // 确保后续代码不会干扰跳转
            } else {
                throw new Exception("用户数据插入失败: " . $conn->error);
            } 
        }

        $conn->close();
    } catch (Exception $e) {
        handleError("安装过程中发生错误: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>系统安装向导</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto,
                Helvetica, Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background: #f8f9fa;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 40px auto;
            padding: 30px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        h1 {
            text-align: center;
            margin: 0 0 30px;
            color: #2c3e50;
        }

        h3 {
            margin: 20px 0 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
            color: #3498db;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            font-size: 14px;
        }

        input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        input:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
        }

        button {
            width: 100%;
            padding: 12px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s;
        }

        button:hover {
            background: #2980b9;
        }

        .error {
            color: #e74c3c;
            margin-top: 5px;
            font-size: 14px;
        }

        .alert {
            padding: 15px;
            background: #f8d7da;
            color: #721c24;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .password-strength {
            height: 4px;
            background: #eee;
            margin-top: 5px;
            border-radius: 2px;
            overflow: hidden;
        }

        .password-strength-bar {
            height: 100%;
            width: 0;
            background: #e74c3c;
            transition: width 0.3s, background 0.3s;
        }

        .requirements {
            font-size: 13px;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>系统安装向导</h1>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert"><?= htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <form method="post" id="installForm">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['install_csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

            <h3>数据库配置</h3>

            <div class="form-group">
                <label for="db_host">数据库主机</label>
                <input type="text" id="db_host" name="db_host" value="<?= htmlspecialchars($db_host, ENT_QUOTES, 'UTF-8') ?>" required>
            </div>

            <div class="form-group">
                <label for="db_port">数据库端口</label>
                <input type="number" id="db_port" name="db_port" min="1" max="65535" value="<?= htmlspecialchars($db_port, ENT_QUOTES, 'UTF-8') ?>" required>
            </div>

            <div class="form-group">
                <label for="db_user">数据库用户名</label>
                <input type="text" id="db_user" name="db_user" value="<?= htmlspecialchars($db_user, ENT_QUOTES, 'UTF-8') ?>" required>
            </div>

            <div class="form-group">
                <label for="db_pass">数据库密码</label>
                <input type="password" id="db_pass" name="db_pass" value="<?= htmlspecialchars($db_pass, ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="form-group">
                <label for="db_name">数据库名称</label>
                <input type="text" id="db_name" name="db_name" value="<?= htmlspecialchars($db_name, ENT_QUOTES, 'UTF-8') ?>" required>
            </div>

            <div class="form-group">
                <label for="db_prefix">数据表前缀</label>
                <input type="text" id="db_prefix" name="db_prefix" pattern="[a-zA-Z0-9_]+" value="<?= htmlspecialchars($db_prefix, ENT_QUOTES, 'UTF-8') ?>" required>
                <div class="requirements">只能包含字母、数字和下划线</div>
            </div>

            <h3>管理员账号</h3>

            <div class="form-group">
                <label for="username">用户名</label>
                <input type="text" id="username" name="username" value="admin" required>
            </div>

            <div class="form-group">
                <label for="password">密码</label>
                <input type="password" id="password" name="password" required minlength="8">
                <div class="password-strength">
                    <div class="password-strength-bar" id="passwordStrengthBar"></div>
                </div>
                <div class="requirements">至少8个字符</div>
            </div>

            <div class="form-group">
                <label for="email">邮箱</label>
                <input type="email" id="email" name="email" required>
            </div>

            <button type="submit" id="submitBtn">开始安装</button>
        </form>
    </div>

    <script>
        // 密码强度检测
        document.getElementById('password').addEventListener('input', function(e) {
            const password = e.target.value;
            const strengthBar = document.getElementById('passwordStrengthBar');
            let strength = 0;

            if (password.length >= 8) strength += 20;
            if (password.length >= 12) strength += 20;
            if (/[A-Z]/.test(password)) strength += 20;
            if (/[0-9]/.test(password)) strength += 20;
            if (/[^A-Za-z0-9]/.test(password)) strength += 20;

            strengthBar.style.width = strength + '%';

            if (strength < 40) {
                strengthBar.style.backgroundColor = '#e74c3c';
            } else if (strength < 70) {
                strengthBar.style.backgroundColor = '#f39c12';
            } else {
                strengthBar.style.backgroundColor = '#2ecc71';
            }
        });

        // 表单提交前验证
        document.getElementById('installForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const email = document.getElementById('email').value;

            if (password.length < 8) {
                alert('密码长度至少需要8个字符');
                e.preventDefault();
                return;
            }

            if (!email.includes('@')) {
                alert('请输入有效的邮箱地址');
                e.preventDefault();
                return;
            }

            // 防止重复提交
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.textContent = '安装中...';
        });
    </script>
</body>

</html>