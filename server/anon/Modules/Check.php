<?php

/**
 * 状态管理类
 */
if (!defined('ANON_ALLOWED_ACCESS')) exit;

class Anon_Check
{
    /**
     * 检查用户是否已登录
     * 
     * @return bool 返回是否已登录
     */
    public static function isLoggedIn(): bool
    {
        self::startSessionIfNotStarted();

        // 检查会话中的用户ID
        if (isset($_SESSION['user_id'])) {
            return true;
        }

        // 检查Cookie中的用户ID和用户名
        if (isset($_COOKIE['user_id']) && isset($_COOKIE['username'])) {
            // 验证Cookie值是否有效（这里可以添加更严格的验证）
            if (self::validateCookie($_COOKIE['user_id'], $_COOKIE['username'])) {
                $_SESSION['user_id'] = $_COOKIE['user_id'];
                $_SESSION['username'] = $_COOKIE['username'];
                return true;
            }
        }

        return false;
    }

    /**
     * 用户注销
     */
    public static function logout(): void
    {
        self::startSessionIfNotStarted();

        // 清空会话数据
        $_SESSION = [];
        session_unset();
        session_destroy();

        // 清除Cookie
        self::clearAuthCookies();
        header("Location: /anon/login");
        exit;
    }

    /**
     * 设置认证Cookie
     * 
     * @param int $userId 用户ID
     * @param string $username 用户名
     */
    public static function setAuthCookies(int $userId, string $username): void
    {
        $cookieOptions = [
            'expires'  => time() + (86400 * 30), // 30天有效期
            'path'     => '/',
            'httponly' => true,
            'secure'   => $AnonSite['HTTPS'], // HTTPS
            'samesite' => 'Lax'
        ];

        setcookie('user_id', $userId, $cookieOptions);
        setcookie('username', $username, $cookieOptions);
    }

    /**
     * 清除认证Cookie
     */
    private static function clearAuthCookies(): void
    {
        $cookieOptions = [
            'expires'  => time() - 3600,
            'path'     => '/',
            'httponly' => true,
            'secure'   => true,
            'samesite' => 'Lax'
        ];

        setcookie('user_id', '', $cookieOptions);
        setcookie('username', '', $cookieOptions);
    }

    /**
     * 验证Cookie值是否有效
     * 
     * @param mixed $userId 用户ID
     * @param string $username 用户名
     * @return bool 返回是否有效
     */
    private static function validateCookie($userId, string $username): bool
    {
        // 这里可以添加更严格的验证逻辑
        // 例如检查用户ID是否为数字，用户名是否符合格式等
        return is_numeric($userId) && !empty($username);
    }

    /**
     * 如果会话未启动，则启动会话
     */
    private static function startSessionIfNotStarted(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start([
                'cookie_httponly' => true,
                'cookie_secure' => true,
                'cookie_samesite' => 'Lax'
            ]);
        }
    }
}
