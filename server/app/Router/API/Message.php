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
    $db = new Anon_Database();

    // 处理POST请求
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // 获取输入数据
        $input = json_decode(file_get_contents('php://input'), true);

        // 基本验证和处理
        $userName = isset($input['name']) ? trim($input['name']) : null;
        $userEmail = isset($input['email']) ? trim($input['email']) : null;
        $content = isset($input['content']) ? trim($input['content']) : null;
        $parentId = isset($input['parent_id']) ? (int)$input['parent_id'] : null;

        // 验证非空字段
        if (empty($content)) {
            throw new Exception('留言内容不能为空');
        }

        // 验证长度限制
        if ($userName && strlen($userName) > 255) {
            throw new Exception('用户名过长');
        }

        if ($userEmail && !filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('邮箱格式不正确');
        }

        if (strlen($content) > 2000) {
            throw new Exception('留言内容过长');
        }

        // 获取客户端信息
        $ip = Anon_Common::GetClientIp();
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;

        // 添加留言或回复
        $success = $db->addMessageEntry($userName, $userEmail, $content, $ip, $userAgent, $parentId);

        if ($success) {
            echo json_encode([
                'success' => true,
                'message' => $parentId ? '回复提交成功' : '留言提交成功'
            ]);
        } else {
            throw new Exception($parentId ? '回复提交失败' : '留言提交失败');
        }

        exit;
    }

    // 获取留言列表
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // 获取分页参数
        $size = isset($_GET['size']) ? (int)$_GET['size'] : 10;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $order = isset($_GET['order']) ? strtolower($_GET['order']) : 'desc'; // 新增排序参数

        // 参数验证
        $size = max(1, min(50, $size));  // 限制每页1-50条
        $page = max(1, $page);           // 页码最小为1
        $offset = ($page - 1) * $size;   // 计算偏移量

        // 获取数据
        $entries = $db->getMessageEntries($size, $offset, $order);
        $total = $db->getMessageCount();

        // 返回JSON响应
        echo json_encode([
            'success' => true,
            'data' => $entries,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'size' => $size,
                'pages' => ceil($total / $size),
                'order' => $order  // 返回当前排序方式
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
