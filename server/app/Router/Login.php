<?php
if (!defined('ANON_ALLOWED_ACCESS')) exit;
// 开启会话，确保全局会话已启动
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = ''; // 用于存储错误信息

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 使用 filter_input 获取和过滤输入数据，防止 XSS 和空值情况
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW);

    if (empty($username) || empty($password)) {
        $error = "用户名和密码均不能为空";
    } else {
        // 创建 DB 实例
        global $anon_Db;
        $db = new Anon_Database($anon_Db);

        // 查询用户信息
        $sql = "SELECT uid, name, password, email FROM " . ANON_DB_PREFIX . "users WHERE name = ?";
        $stmt = $db->prepare($sql, [$username]);

        if ($stmt) {
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                // 绑定返回字段
                $stmt->bind_result($uid, $name, $hashedPassword, $email);
                $stmt->fetch();

                // 验证密码
                if (password_verify($password, $hashedPassword)) {
                    // 登录成功，重置会话ID以防会话固定攻击
                    session_regenerate_id(true);

                    // 设置会话变量
                    $_SESSION['user_id'] = $uid;
                    $_SESSION['username'] = $name;

                    // 设置安全性更高的 Cookie，确保 HttpOnly
                    $cookieOptions = [
                        'expires'  => time() + (86400 * 30),
                        'path'     => '/',
                        'httponly' => true,
                        'secure'   => $AnonSite['HTTPS'],
                        'samesite' => 'Lax'
                    ];
                    setcookie('user_id', $uid, $cookieOptions);
                    setcookie('username', $name, $cookieOptions);

                    // 重定向到首页
                    header("Location: /admin");
                    exit;
                } else {
                    $error = "用户名或密码错误";
                }
            } else {
                $error = "用户名或密码错误";
            }
        } else {
            $error = "数据库查询错误";
        }
    }
}
?>
<?php if (!empty($error)): ?>
    <div role="alert" class="alert alert-warning">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        <span><?= htmlspecialchars($error) ?></span>
    </div>
<?php endif; ?>

<?php if (Anon_Check::isLoggedIn()) {
    echo <<<HTML
        <script>
            window.location.href = '/admin/';
        </script>
    HTML;
} else {
?>
    <div class="hero bg-base-200 min-h-screen">
        <div class="hero-content flex-col lg:flex-row-reverse">
            <div class="text-center lg:text-left">
                <h1 class="text-5xl font-bold">Login now!</h1>
                <p class="py-6">
                    登录到<span class="text-primary"> 轻音部 </span>后台~
                    <br />除虎子外的部员都没有权限喔!
                </p>
            </div>
            <div class="card bg-base-100 w-full max-w-sm shrink-0 shadow-2xl">
                <div class="card-body">
                    <fieldset class="fieldset">
                        <form method="post" action="">
                            <label class="label">Email</label>
                            <input type="text" name="username" class="input" placeholder="Username" required />
                            <label class="label">Password</label>
                            <input type="password" name="password" class="input" placeholder="Password" required />
                            <div><a class="link link-hover">Forgot password?</a></div>
                            <button class="btn btn-neutral mt-4" type="submit">Login</button>
                        </form>
                    </fieldset>
                </div>
            </div>
        </div>
    </div>
<?php
} ?>