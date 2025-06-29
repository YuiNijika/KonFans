<?php

/**
 * Anon配置
 */
if (!defined('ANON_ALLOWED_ACCESS')) exit;

class Anon_Config
{
    /**
     * 路由配置
     * @var array
     */
    private static $routerConfig = [
        'routes' => [],
        'error_handlers' => []
    ];

    /**
     * 注册路由
     * @param string $path 路由路径
     * @param callable $handler 处理函数
     */
    public static function addRoute(string $path, callable $handler)
    {
        self::$routerConfig['routes'][$path] = $handler;
    }

    /**
     * 注册错误处理器
     * @param int $code HTTP状态码
     * @param callable $handler 处理函数
     */
    public static function addErrorHandler(int $code, callable $handler)
    {
        self::$routerConfig['error_handlers'][$code] = $handler;
    }

    /**
     * 获取路由配置
     * @return array 路由配置数组
     */
    public static function getRouterConfig(): array
    {
        return self::$routerConfig;
    }

    /**
     * 判断程序是否安装
     * @return bool
     */
    public static function isInstalled(): bool
    {
        // 确保常量已定义
        if (!defined('ANON_INSTALLED')) {
            return false;
        }
        $lockFile = __DIR__. '/Install/installed.lock';

        return ANON_INSTALLED && file_exists($lockFile);
    }
}
