<?php
if (!defined('ANON_ALLOWED_ACCESS')) exit;

// 设置跨域头
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json');

// 处理预检请求
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

try {
    // 初始化数据库连接
    $db = new Anon_Database();

    // 处理POST请求
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // 获取输入数据
        $input = json_decode(file_get_contents('php://input'), true);

        // 验证必填字段
        $requiredFields = ['name', 'email'];
        foreach ($requiredFields as $field) {
            if (empty($input[$field])) {
                throw new Exception("字段 {$field} 不能为空");
            }
        }

        // 基本验证
        $name = trim($input['name']);
        $email = trim($input['email']);
        $gender = isset($input['gender']) ? trim($input['gender']) : 'secret';
        $url = isset($input['url']) && !empty($input['url']) ? trim($input['url']) : null;
        $bio = isset($input['bio']) && !empty($input['bio']) ? trim($input['bio']) : null;

        // 获取客户端IP和User-Agent
        $ip = Anon_Common::GetClientIp();
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        if (!in_array($gender, ['male', 'female', 'secret'])) {
            throw new Exception('无效的性别选项');
        }

        if (strlen($name) > 255) {
            throw new Exception('昵称过长');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('邮箱格式不正确');
        }

        if ($url && !filter_var($url, FILTER_VALIDATE_URL)) {
            throw new Exception('个人主页URL格式不正确');
        }

        if ($bio && strlen($bio) > 1000) {
            throw new Exception('个人简介过长');
        }

        // 检查邮箱是否已存在
        $existingMember = $db->getMemberByEmail($email);
        if ($existingMember) {
            throw new Exception('该邮箱已提交过申请');
        }

        // 添加成员申请
        $success = $db->addMemberApplication($name, $email, $url, $bio, $ip, $userAgent, $gender);

        if ($success) {
            echo json_encode([
                'success' => true,
                'message' => '提交成功'
            ]);
        } else {
            throw new Exception('提交失败');
        }

        exit;
    }

    // 获取公开成员列表GET请求
    // ID获取详情信息
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        if ($id <= 0) {
            throw new Exception('无效的成员ID');
        }

        $member = $db->getMemberById($id);
        if (!$member) {
            throw new Exception('成员不存在');
        }

        echo json_encode([
            'success' => true,
            'data' => $member
        ]);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // 获取查询参数
        $size = isset($_GET['size']) ? (int)$_GET['size'] : 10;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $order = isset($_GET['order']) ? strtolower($_GET['order']) : 'desc';

        // 参数验证
        $size = max(1, min(50, $size));
        $page = max(1, $page);
        $offset = ($page - 1) * $size;

        // 获取数据
        $members = $db->getPublicMembers($size, $offset, $order);
        $total = $db->getPublicMemberCount();

        // 返回JSON响应
        echo json_encode([
            'success' => true,
            'data' => $members,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'size' => $size,
                'pages' => ceil($total / $size),
                'order' => $order
            ]
        ], JSON_PRETTY_PRINT);

        exit;
    }

    // 其他请求方法
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error' => 'Method not allowed'
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}