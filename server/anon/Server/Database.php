<?php

/**
 * Anon Database
 */
if (!defined('ANON_ALLOWED_ACCESS')) exit;

class Anon_Database
{
    private $conn;

    /**
     * 构造函数：初始化数据库连接
     */
    public function __construct()
    {
        $this->conn = new mysqli(
            ANON_DB_HOST,
            ANON_DB_USER,
            ANON_DB_PASSWORD,
            ANON_DB_DATABASE,
            ANON_DB_PORT
        );

        if ($this->conn->connect_error) {
            die("数据库连接失败: " . $this->conn->connect_error);
        }

        $this->conn->set_charset(ANON_DB_CHARSET);
    }

    /**
     * 执行查询并返回结果
     * @param string $sql SQL 查询语句
     * @return array 查询结果
     */
    public function query($sql)
    {
        $result = $this->conn->query($sql);
        if (!$result) {
            die("SQL 查询错误: " . $this->conn->error);
        }

        if ($result instanceof mysqli_result) {
            $rows = [];
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
            return $rows;
        }

        return $this->conn->affected_rows;
    }

    /**
     * 准备并执行预处理语句
     * @param string $sql SQL 查询语句
     * @param array $params 参数数组
     * @return mysqli_stmt 预处理语句对象
     */
    public function prepare($sql, $params = [])
    {
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            die("SQL 预处理错误: " . $this->conn->error);
        }

        if (!empty($params)) {
            $types = '';
            $bindParams = [];

            foreach ($params as $param) {
                if (is_null($param)) {
                    $types .= 's'; // 即使为null也使用's'类型
                    $bindParams[] = null;
                } else {
                    $types .= 's'; // 默认所有参数为字符串
                    $bindParams[] = $param;
                }
            }

            $stmt->bind_param($types, ...$bindParams);
        }

        $stmt->execute();
        return $stmt;
    }

    /**
     * 获取用户信息
     * @param int $uid 用户 ID
     * @return array|null 用户信息
     */
    public function getUserInfo($uid)
    {
        $sql = "SELECT * FROM " . ANON_DB_PREFIX . "users WHERE uid = ?";
        $stmt = $this->prepare($sql, [$uid]);
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($userId, $name, $password, $email, $group);
            $stmt->fetch();
            return [
                'uid' => $userId,
                'name' => $name,
                'email' => $email,
                'group' => $group
            ];
        }

        return null;
    }

    /**
     * 添加留言或回复
     * @param string $userName 用户名
     * @param string $userEmail 用户邮箱
     * @param string $content 留言内容
     * @param string $ip IP地址
     * @param string $userAgent 用户浏览器UA
     * @param int|null $parentId 父留言ID
     * @return bool 是否成功
     */
    public function addMessageEntry($userName, $userEmail, $content, $ip, $userAgent, $parentId = null)
    {
        $sql = "INSERT INTO " . ANON_DB_PREFIX . "messages 
            (name, email, content, ip, user_agent, parent_id) 
            VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $this->prepare($sql, [
            $userName,
            $userEmail,
            $content,
            // $ip,
            // $userAgent,
            $parentId
        ]);

        return $stmt->affected_rows > 0;
    }

    /**
     * 获取留言列表
     * @param int $size 每页数量
     * @param int $offset 偏移量
     * @param string $order 排序方式
     * @return array 留言列表
     */
    public function getMessageEntries($size = 10, $offset = 0, $order = 'desc')
    {
        $order = strtolower($order) === 'asc' ? 'ASC' : 'DESC';

        $sql = "SELECT * FROM " . ANON_DB_PREFIX . "messages 
        WHERE parent_id IS NULL
        ORDER BY created_at $order, id $order
        LIMIT ? OFFSET ?";

        $stmt = $this->prepare($sql, [$size, $offset]);
        $stmt->store_result();

        $entries = [];
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $name, $email, $content, $ip, $ua, $createdAt, $parentId);
            while ($stmt->fetch()) {
                $entry = [
                    'id' => $id,
                    'name' => $name,
                    'email' => $email,
                    'content' => $content,
                    // 'ip' => $ip,
                    // 'user_agent' => $ua,
                    'created_at' => $createdAt,
                    'replies' => $this->getRepliesForMessage($id)
                ];
                $entries[] = $entry;
            }
        }

        return $entries;
    }

    /**
     * 获取指定留言的回复
     * @param int $messageId 留言ID
     * @return array 回复列表
     */
    private function getRepliesForMessage($messageId)
    {
        $sql = "SELECT * FROM " . ANON_DB_PREFIX . "messages 
            WHERE parent_id = ?
            ORDER BY created_at ASC";

        $stmt = $this->prepare($sql, [$messageId]);
        $stmt->store_result();

        $replies = [];
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $name, $email, $content, $ip, $ua, $createdAt, $parentId);
            while ($stmt->fetch()) {
                $replies[] = [
                    'id' => $id,
                    'name' => $name,
                    'email' => $email,
                    'content' => $content,
                    'ip' => $ip,
                    'user_agent' => $ua,
                    'created_at' => $createdAt
                ];
            }
        }

        return $replies;
    }

    /**
     * 获取留言总数
     * @return int 留言总数
     */
    public function getMessageCount()
    {
        $sql = "SELECT COUNT(*) FROM " . ANON_DB_PREFIX . "messages WHERE parent_id IS NULL";
        $stmt = $this->prepare($sql);
        $stmt->store_result();
        $stmt->bind_result($count);
        $stmt->fetch();
        return $count;
    }

    /**
     * 通过邮箱获取成员信息
     * @param string $email 成员邮箱
     * @return array|null 成员信息
     */
    public function getMemberByEmail($email)
    {
        $sql = "SELECT id, name, email, url, bio, created_at, display 
                FROM " . ANON_DB_PREFIX . "members 
                WHERE email = ?";
        $stmt = $this->prepare($sql, [$email]);
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $name, $email, $url, $bio, $createdAt, $display);
            $stmt->fetch();
            return [
                'id' => $id,
                'name' => $name,
                'email' => $email,
                'url' => $url,
                'bio' => $bio,
                'created_at' => $createdAt,
                'display' => $display
            ];
        }

        return null;
    }

    /**
     * 添加社团成员
     * @param string $name 成员昵称
     * @param string $email 成员邮箱
     * @param string|null $url 个人主页
     * @param string|null $bio 个人简介
     * @param string|null $ip 用户IP地址
     * @param string|null $userAgent 用户浏览器UA
     * @param string $gender 性别 (male/female/secret)
     * @return bool 是否成功
     */
    public function addMemberApplication($name, $email, $url = null, $bio = null, $ip = null, $userAgent = null, $gender = 'secret')
    {
        $sql = "INSERT INTO " . ANON_DB_PREFIX . "members 
            (name, email, url, bio, ip, user_agent, gender, display) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 'public')";

        $stmt = $this->prepare($sql, [
            $name,
            $email,
            $url,
            $bio,
            $ip,
            $userAgent,
            $gender
        ]);

        return $stmt->affected_rows > 0;
    }

    /**
     * 获取公开成员列表
     * @param int $size 每页数量
     * @param int $offset 偏移量
     * @param string $order 排序方式 (desc/asc)
     * @return array 成员列表
     */
    public function getPublicMembers($size = 10, $offset = 0, $order = 'desc')
    {
        // 验证排序参数
        $order = strtolower($order) === 'asc' ? 'ASC' : 'DESC';

        // 准备SQL查询
        $sql = "SELECT 
                id, 
                name, 
                email, 
                gender, 
                url, 
                bio, 
                created_at 
            FROM " . ANON_DB_PREFIX . "members 
            WHERE display = 'public'
            ORDER BY created_at $order, id $order
            LIMIT ? OFFSET ?";

        // 准备预处理语句
        $stmt = $this->prepare($sql, [$size, $offset]);
        $stmt->store_result();

        $members = [];

        // 绑定结果变量
        $stmt->bind_result(
            $id,
            $name,
            $email,
            $gender,
            $url,
            $bio,
            $createdAt
        );

        // 遍历结果集
        while ($stmt->fetch()) {
            $members[] = [
                'id' => $id,
                'name' => $name,
                'email' => $email,
                'gender' => $gender,
                'url' => $url,
                'bio' => $bio,
                'created_at' => $createdAt
            ];
        }

        return $members;
    }


    /**
     * 通过ID获取成员详情
     * @param int $id 成员ID
     * @return array|null 成员信息 (包含所有字段)
     */
    public function getMemberById($id)
    {
        // 准备SQL查询
        $sql = "SELECT 
                id, 
                name, 
                email, 
                gender, 
                url, 
                bio, 
                created_at 
            FROM " . ANON_DB_PREFIX . "members 
            WHERE id = ? AND display = 'public'";

        // 准备预处理语句
        $stmt = $this->prepare($sql, [$id]);
        $stmt->store_result();

        // 如果没有结果返回null
        if ($stmt->num_rows === 0) {
            return null;
        }

        // 绑定结果变量
        $stmt->bind_result(
            $id,
            $name,
            $email,
            $gender,
            $url,
            $bio,
            $createdAt
        );

        // 获取第一条记录
        $stmt->fetch();

        // 返回成员信息数组
        return [
            'id' => $id,
            'name' => $name,
            'email' => $email,
            'gender' => $gender,
            'url' => $url,
            'bio' => $bio,
            'created_at' => $createdAt
        ];
    }

    /**
     * 获取公开成员总数
     * @return int 成员总数
     */
    public function getPublicMemberCount()
    {
        $sql = "SELECT COUNT(*) FROM " . ANON_DB_PREFIX . "members WHERE display = 'public'";
        $stmt = $this->prepare($sql);
        $stmt->store_result();
        $stmt->bind_result($count);
        $stmt->fetch();
        return $count;
    }

    /**
     * 关闭数据库连接
     */
    public function __destruct()
    {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
