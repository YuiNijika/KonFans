<?php
if (!defined('ANON_ALLOWED_ACCESS')) exit;
?>
<!doctype html>
<html lang="zh-cn">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <meta name="renderer" content="webkit">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="../src/output.css">
    <title>鹿乃子乃子虎视眈眈</title>
</head>

<body>
    <?php if (Anon_Check::isLoggedIn()) {
        Anon_Component::View('AppNavbar');
    } ?>
    <main class="mockup-browser border border-base-300 w-full min-h-screen">