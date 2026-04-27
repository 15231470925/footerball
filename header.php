<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php bloginfo('name'); ?></title>

    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<div class="top-header">

    <div class="logo">
        ⚽ FIFA2026
    </div>

    <div class="nav">
        <a href="#">球员库</a>
        <a href="#">球队排名</a>
        <a href="#">赛程结果</a>
        <a href="#">官方商城</a>
        <a href="#">赛事中心</a>
    </div>

    <div class="right-box">
        <div class="search-box">
            <input type="text" placeholder="搜索球员/球队...">
        </div>
        <div class="auth">
            <a href="#">登录</a> |
            <a href="#">注册</a>
        </div>
    </div>

</div>

<div class="bottom-line"></div>