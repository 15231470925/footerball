<?php
/**
 * ============================================
 * Twenty Twenty-Five 子主题 Functions
 * ============================================
 */

/* ============================================
   ✅ 样式加载
   ============================================ */

function twenty_twenty_five_child_enqueue_styles() {
    // 父主题样式
    wp_enqueue_style(
        'parent-style',
        get_template_directory_uri() . '/style.css'
    );

    // 子主题主样式（main.css）
    wp_enqueue_style(
        'main-style',
        get_stylesheet_directory_uri() . '/css/main.css',
        array('parent-style'),
        '1.0.0'
    );
}
add_action('wp_enqueue_scripts', 'twenty_twenty_five_child_enqueue_styles');

/* ✅ 首页样式 */
function my_home_styles() {
    if (is_front_page()) {
        wp_enqueue_style(
            'home-style',
            get_stylesheet_directory_uri() . '/css/home.css',
            array('main-style'),
            '1.0.0'
        );
    }
}
add_action('wp_enqueue_scripts', 'my_home_styles');

/* ✅ footer 样式 */
function my_footer_styles() {
    wp_enqueue_style(
        'footer-style',
        get_stylesheet_directory_uri() . '/css/footer.css',
        array('main-style'),
        '1.0.0'
    );
}
add_action('wp_enqueue_scripts', 'my_footer_styles');

/* ✅ header 样式 */
function my_header_styles() {
    wp_enqueue_style(
        'header-style',
        get_stylesheet_directory_uri() . '/css/header.css',
        array('main-style'),
        '1.0.0'
    );
}
add_action('wp_enqueue_scripts', 'my_header_styles');

/* ✅ players 页面样式 */
function my_player_styles() {
    if (is_singular('players')) {
        wp_enqueue_style(
            'players-style',
            get_stylesheet_directory_uri() . '/css/players.css',
            array('main-style'),
            '1.0.0'
        );
    }
}
add_action('wp_enqueue_scripts', 'my_player_styles');

/* ✅ lifestyle 页面样式 */
function my_lifestyle_styles() {
    if (is_singular('lifestyle')) {
        wp_enqueue_style(
            'lifestyle-style',
            get_stylesheet_directory_uri() . '/css/lifestyle.css',
            array('main-style'),
            '1.0.0'
        );
    }
}
add_action('wp_enqueue_scripts', 'my_lifestyle_styles');

/* ============================================
   ✅ ACF 选项页面
   ============================================ */

if (function_exists('acf_add_options_page')) {
    acf_add_options_page(array(
        'page_title' => '首页设置',
        'menu_title' => '首页设置',
        'menu_slug'  => 'home-settings',
        'capability' => 'edit_posts',
        'redirect'   => false
    ));
}

/* ============================================
   ✅ 注册 Players 自定义文章类型
   ============================================ */

function register_players_cpt() {
    register_post_type('players', array(
        'label'         => 'Players',
        'labels'        => array(
            'name'          => 'Players',
            'singular_name' => 'Player',
            'add_new'       => 'Add New Player',
            'edit_item'     => 'Edit Player',
            'view_item'     => 'View Player',
        ),
        'public'        => true,
        'has_archive'   => true,
        'rewrite'       => array('slug' => 'players'),
        'supports'      => array('title', 'editor', 'thumbnail'),
        'menu_icon'     => 'dashicons-groups',
        'show_in_rest'  => true,
    ));
}
add_action('init', 'register_players_cpt');

/* ============================================
   ✅ 注册 Lifestyle 自定义文章类型
   ============================================ */

function register_lifestyle_cpt() {
    register_post_type('lifestyle', array(
        'label'         => 'Lifestyle',
        'labels'        => array(
            'name'          => 'Lifestyle',
            'singular_name' => 'Lifestyle',
            'add_new'       => 'Add New Lifestyle',
            'edit_item'     => 'Edit Lifestyle',
            'view_item'     => 'View Lifestyle',
            'all_items'     => 'All Lifestyle',
        ),
        'public'        => true,
        'has_archive'   => true,
        'rewrite'       => array('slug' => 'lifestyle'),
        'supports'      => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
        'menu_icon'     => 'dashicons-heart',
        'show_in_rest'  => true,
        'menu_position' => 6,
    ));
}
add_action('init', 'register_lifestyle_cpt');

/* ============================================
   ✅ 自定义模板加载
   ============================================ */

add_filter('template_include', function ($template) {
    // Players 单页模板
    if (is_singular('players')) {
        $custom = get_stylesheet_directory() . '/single-players.php';
        if (file_exists($custom)) {
            return $custom;
        }
    }
    
    // Lifestyle 单页模板
    if (is_singular('lifestyle')) {
        $custom = get_stylesheet_directory() . '/single-lifestyle.php';
        if (file_exists($custom)) {
            return $custom;
        }
    }
    
    return $template;
}, 99);