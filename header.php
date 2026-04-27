<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php wp_title('|', true, 'right'); ?><?php bloginfo('name'); ?></title>

    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<header class="site-header">
    <div class="header-inner">

        <!-- Logo -->
        <a href="<?php echo home_url('/'); ?>" class="header-logo">
            ⚽ FIFA2026
        </a>

        <!-- PC Navigation -->
        <nav class="header-nav">
            <a href="<?php echo get_post_type_archive_link('teams'); ?>">球队排名</a>
            <a href="<?php echo get_post_type_archive_link('players'); ?>">球员库</a>
            <a href="<?php echo get_post_type_archive_link('lifestyle'); ?>">生活方式</a>
            <a href="#">赛程结果</a>
            <a href="#">官方商城</a>
        </nav>

        <!-- Right Side (PC only) -->
        <div class="header-right">
            <div class="header-search">
                <form method="get" action="<?php echo home_url('/'); ?>">
                    <input type="text" name="s" placeholder="搜索球员/球队...">
                </form>
            </div>
        </div>

        <!-- Mobile Menu Toggle -->
        <button class="mobile-menu-toggle" aria-label="菜单">
            <span></span>
            <span></span>
            <span></span>
        </button>

    </div>

    <!-- Mobile Breadcrumb / Menu Overlay -->
    <div class="mobile-menu-overlay">
        <div class="mobile-menu-container">
            <!-- WAP 面包屑导航 -->
            <nav class="mobile-breadcrumb">
                <?php
                $breadcrumbs = array();
                $breadcrumbs[] = array('label' => 'Home', 'url' => home_url('/'));

                if (is_singular('teams')) {
                    $breadcrumbs[] = array('label' => 'Teams', 'url' => get_post_type_archive_link('teams'));
                    $breadcrumbs[] = array('label' => get_the_title(), 'url' => null);
                } elseif (is_singular('players')) {
                    $breadcrumbs[] = array('label' => 'Players', 'url' => get_post_type_archive_link('players'));
                    $breadcrumbs[] = array('label' => get_the_title(), 'url' => null);
                } elseif (is_singular('lifestyle')) {
                    $breadcrumbs[] = array('label' => 'Lifestyle', 'url' => get_post_type_archive_link('lifestyle'));
                    $breadcrumbs[] = array('label' => get_the_title(), 'url' => null);
                } elseif (is_post_type_archive('teams')) {
                    $breadcrumbs[] = array('label' => 'Teams', 'url' => null);
                } elseif (is_post_type_archive('players')) {
                    $breadcrumbs[] = array('label' => 'Players', 'url' => null);
                } elseif (is_post_type_archive('lifestyle')) {
                    $breadcrumbs[] = array('label' => 'Lifestyle', 'url' => null);
                } elseif (is_search()) {
                    $breadcrumbs[] = array('label' => '搜索结果', 'url' => null);
                } elseif (is_single() || is_page()) {
                    $post_type = get_post_type();
                    if ($post_type && $post_type !== 'post' && $post_type !== 'page') {
                        $post_type_obj = get_post_type_object($post_type);
                        if ($post_type_obj) {
                            $breadcrumbs[] = array('label' => $post_type_obj->label, 'url' => get_post_type_archive_link($post_type));
                        }
                    }
                    $breadcrumbs[] = array('label' => get_the_title(), 'url' => null);
                }

                foreach ($breadcrumbs as $index => $crumb) {
                    if ($index > 0) {
                        echo '<span class="breadcrumb-sep">›</span>';
                    }
                    if ($crumb['url']) {
                        echo '<a href="' . esc_url($crumb['url']) . '">' . esc_html($crumb['label']) . '</a>';
                    } else {
                        echo '<span class="breadcrumb-current">' . esc_html($crumb['label']) . '</span>';
                    }
                }
                ?>
            </nav>

            <!-- Mobile Nav Links -->
            <nav class="mobile-nav-links">
                <a href="<?php echo home_url('/'); ?>">
                    <span class="nav-icon">🏠</span> 首页
                </a>
                <a href="<?php echo get_post_type_archive_link('teams'); ?>">
                    <span class="nav-icon">🏆</span> 球队排名
                </a>
                <a href="<?php echo get_post_type_archive_link('players'); ?>">
                    <span class="nav-icon">⚽</span> 球员库
                </a>
                <a href="<?php echo get_post_type_archive_link('lifestyle'); ?>">
                    <span class="nav-icon">🎯</span> 生活方式
                </a>
                <a href="#">
                    <span class="nav-icon">📅</span> 赛程结果
                </a>
                <a href="#">
                    <span class="nav-icon">🛍️</span> 官方商城
                </a>
            </nav>
        </div>
    </div>

    <div class="header-line"></div>
</header>
