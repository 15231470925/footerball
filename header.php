<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php bloginfo('name'); ?></title>

    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<header class="site-header">
    <div class="header-inner">
        
        <!-- Logo -->
        <div class="header-logo">
            ⚽ FIFA2026
        </div>

        <!-- Navigation -->
        <nav class="header-nav">
            <a href="#">球员库</a>
            <a href="#">球队排名</a>
            <a href="#">赛程结果</a>
            <a href="#">官方商城</a>
            <a href="#">赛事中心</a>
        </nav>

        <!-- Right Side -->
        <div class="header-right">
            <div class="header-search">
                <input type="text" placeholder="搜索球员/球队...">
            </div>
            <div class="header-auth">
                <a href="#">登录</a>
                <span class="auth-divider">|</span>
                <a href="#">注册</a>
            </div>
            <div class="header-user-icon">
                <span>👤</span>
            </div>
        </div>

    </div>

    <div class="header-line"></div>
</header>
