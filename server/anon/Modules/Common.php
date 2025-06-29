<?php
if (!defined('ANON_ALLOWED_ACCESS')) exit;

class Anon_Common
{
    /**
     * 获取客户端真实IP
     * @return string
     */
    public static function GetClientIp()
    {
        // 可能的IP来源数组
        $sources = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'REMOTE_ADDR'
        ];

        foreach ($sources as $source) {
            if (!empty($_SERVER[$source])) {
                $ip = $_SERVER[$source];

                // 处理X-Forwarded-For可能有多个IP的情况
                if ($source === 'HTTP_X_FORWARDED_FOR') {
                    $ips = explode(',', $ip);
                    $ip = trim($ips[0]);
                }

                // 验证IP格式
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }

        // 所有来源都找不到有效IP时返回null
        return null;
    }
}

class Anon_Component
{
    public static function View(string $fileView): void
    {
        // 使用更可靠的路径构建方式
        $filePath = realpath(__DIR__ . '/../../app/Components/' . $fileView . '.php');

        if (!$filePath || !file_exists($filePath)) {
            throw new RuntimeException("Components view file not found: {$fileView}");
        }

        require $filePath;
    }
}
