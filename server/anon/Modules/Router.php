<?php

/**
 * 路由处理
 */
if (!defined('ANON_ALLOWED_ACCESS')) exit;

class Anon_Router
{
    /**
     * @var array 路由配置
     */
    private static $routes = [];

    /**
     * @var array 错误处理器配置
     */
    private static $errorHandlers = [];

    /**
     * @var string 日志文件路径
     */
    private static $logFile = __DIR__ . '/../../logs/router.log';

    /**
     * @var bool 是否已初始化日志系统
     */
    private static $logInitialized = false;

    /**
     * 初始化路由系统
     * @throws RuntimeException 如果路由配置无效
     */
    public static function init(): void
    {
        try {
            self::loadConfig();
            self::handleRequest();
        } catch (RuntimeException $e) {
            self::logError("Router ERROR: " . $e->getMessage(), $e->getFile(), $e->getLine());
            self::handleError(500);
        }
    }

    /**
     * 加载路由配置
     */
    private static function loadConfig(): void
    {
        $routerConfig = Anon_Config::getRouterConfig();

        if (!is_array($routerConfig)) {
            throw new RuntimeException("Invalid router configuration");
        }

        self::$routes = $routerConfig['routes'] ?? [];
        self::$errorHandlers = $routerConfig['error_handlers'] ?? [];
    }

    /**
     * View
     */
    public static function View(string $fileView): void
    {
        $filePath = realpath(__DIR__ . '/../../app/Router/' . $fileView . '.php');

        if (!$filePath || !file_exists($filePath)) {
            throw new RuntimeException("Router view file not found: {$fileView}");
        }

        require $filePath;
    }

    /**
     * 是否启用调试模式
     * @return bool
     */
    private static function isDebugEnabled(): bool
    {
        return defined('ANON_ROUTER_DEBUG') && ANON_ROUTER_DEBUG;
    }

    /**
     * 初始化日志系统
     */
    private static function initLogSystem(): void
    {
        if (self::$logInitialized) {
            return;
        }

        $logDir = dirname(self::$logFile);
        if (!file_exists($logDir)) {
            mkdir($logDir, 0755, true);
        }

        self::$logInitialized = true;
    }

    /**
     * 记录调试信息
     * @param string $message
     */
    private static function debugLog(string $message): void
    {
        if (self::isDebugEnabled()) {
            self::writeLog("[DEBUG] " . $message);
        }
    }

    /**
     * 记录错误信息
     * @param string $message
     * @param string $file
     * @param int $line
     */
    private static function logError(string $message, string $file = '', int $line = 0): void
    {
        $logMessage = "[ERROR] " . $message;
        if ($file) {
            $logMessage .= " in {$file}";
            if ($line) {
                $logMessage .= " on line {$line}";
            }
        }
        self::writeLog($logMessage);
    }

    /**
     * 写入日志文件
     * @param string $message
     */
    private static function writeLog(string $message): void
    {
        self::initLogSystem();
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[{$timestamp}] {$message}" . PHP_EOL;
        
        file_put_contents(
            self::$logFile, 
            $logMessage, 
            FILE_APPEND | LOCK_EX
        );
    }

    /**
     * 处理当前请求
     */
    private static function handleRequest(): void
    {
        try {
            $requestPath = self::getRequestPath();
            self::debugLog("Request path: " . $requestPath);

            if (isset(self::$routes[$requestPath])) {
                self::debugLog("Matched route: " . $requestPath);
                self::dispatch($requestPath);
            } else {
                self::debugLog("No route matched for: " . $requestPath);
                self::handleError(404);
            }
        } catch (Throwable $e) {
            self::logError("Request handling failed: " . $e->getMessage(), $e->getFile(), $e->getLine());
            self::handleError(500);
        }
    }

    /**
     * 执行路由处理器
     * @param string $routeKey 路由标识
     */
    private static function dispatch(string $routeKey): void
    {
        $handler = self::$routes[$routeKey];
        self::debugLog("Dispatching route: " . $routeKey);

        if (!is_callable($handler)) {
            self::debugLog("Invalid handler for route: " . $routeKey);
            self::handleError(404);
            return;
        }

        try {
            $handler();
        } catch (Throwable $e) {
            self::logError("Route execution failed [{$routeKey}]: " . $e->getMessage(), $e->getFile(), $e->getLine());
            self::handleError(500);
        }

        exit;
    }

    /**
     * 获取规范化请求路径
     * @return string 不含查询参数的路径
     */
    private static function getRequestPath(): string
    {
        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = trim(parse_url($requestUri, PHP_URL_PATH), '/');

        return strstr($path, '?', true) ?: $path;
    }

    /**
     * 处理HTTP错误
     * @param int $statusCode HTTP状态码
     */
    private static function handleError(int $statusCode): void
    {
        http_response_code($statusCode);

        if (
            isset(self::$errorHandlers[$statusCode]) &&
            is_callable(self::$errorHandlers[$statusCode])
        ) {
            self::$errorHandlers[$statusCode]();
        } else {
            self::showDefaultError($statusCode);
        }

        exit;
    }

    /**
     * 显示默认错误页
     * @param int $statusCode HTTP状态码
     */
    private static function showDefaultError(int $statusCode): void
    {
        header("Content-Type: text/plain; charset=utf-8");
        $messages = [
            400 => '400 Bad Request',
            404 => '404 Not Found',
            500 => '500 Internal Server Error'
        ];

        echo $messages[$statusCode] ?? "HTTP {$statusCode}";
    }
}

// 初始化路由
Anon_Router::init();