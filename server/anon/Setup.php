<?php
if (!defined('ANON_ALLOWED_ACCESS')) exit;

$routeConfigs = [
    // 后台路由
    [
        'route' => 'admin',
        'handler' => function () {
            Anon_Router::View('Admin/Index');
        },
        'useComponent' => true
    ],
    // API路由
    [
        'route' => 'api/message',
        'handler' => function () {
            Anon_Router::View('API/Message');
        },
        'useComponent' => false
    ],
    [
        'route' => 'api/member',
        'handler' => function () {
            Anon_Router::View('API/Member');
        },
        'useComponent' => false
    ],
    // anon路由
    [
        'route' => 'anon/login',
        'handler' => function () {
            Anon_Router::View('Login');
        },
        'useComponent' => true
    ],
    [
        'route' => 'anon/logout',
        'handler' => function () {
            Anon_Check::logout();
        },
        'useComponent' => false
    ],
    [
        'route' => 'anon/install',
        'handler' => function () {
            require_once __DIR__ . '/Modules/Install/Install.php';
        },
        'useComponent' => false
    ]
];

/**
 * 注册路由
 */
foreach ($routeConfigs as $config) {
    $handler = $config['useComponent'] 
        ? function () use ($config) {
            Anon_Component::View('AppHeader');
            $config['handler']();
            Anon_Component::View('AppFooter');
        }
        : $config['handler'];
    
    Anon_Config::addRoute($config['route'], $handler);
}