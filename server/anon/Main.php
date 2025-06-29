<?php

/**
 * Anon配置
 */
if (!defined('ANON_ALLOWED_ACCESS')) exit;

class Anon
{
    public static function run()
    {
        $Root = __DIR__ . '/';
        $Server  = __DIR__ . '/Server/';
        $Modules = __DIR__ . '/Modules/';
        require_once $Modules . 'Config.php';
        require_once $Modules  . 'Common.php';
        // 如果是安装路由则跳过检查
        if (strpos($_SERVER['REQUEST_URI'], '/anon/install') === false) {
            if (!Anon_Config::isInstalled()) {
                header('Location: /anon/install');
                exit;
            }
        }
        require_once $Server  . 'Database.php';
        require_once $Modules . 'Check.php';
        require_once $Root . 'Setup.php';
        require_once $Modules . 'Router.php';
    }
}

// 启动应用
Anon::run();
