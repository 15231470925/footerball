<?php get_header(); ?>

<?php if ( have_posts() ) :
    while ( have_posts() ) : the_post();

/* ===============================
   ① Article（ACF Group 字段）
   =============================== */

$article = get_field('article');
$article_data = []; // 初始化变量

if ( $article ) {
    $article_data = [
        'post_title'            => $article['post_title'] ?? '',
        'post_date'             => $article['post_date'] ?? '',
        'name'                  => $article['name'] ?? '',
        'avatar'                => !empty($article['avatar']) 
            ? wp_get_attachment_image_url($article['avatar'], 'full') 
            : '',
        'initials'              => $article['initials'] ?? '',
        'post_thumbnail'        => !empty($article['post_thumbnail']) 
            ? wp_get_attachment_image_url($article['post_thumbnail'], 'full') 
            : '',
        'post_thumbnail_caption'=> $article['post_thumbnail_caption'] ?? '',
        'post_content_main'     => $article['post_content_main'] ?? '',
        'post_tags'             => $article['post_tags'] ?? [],
        'position'              => $article['position'] ?? '',
        'bio'                   => $article['bio'] ?? '',
        'profile_link'          => $article['profile_link'] ?? '',
    ];
}

/* ===============================
   ② Prod Group（Group 字段）
   =============================== */

$prod_items = [];
$prod = get_field('prod');

if ( $prod ) {
    $prod_items[] = [
        'left' => [
            'title' => $prod['product_title_left'] ?? '',
            'price' => $prod['product_price_left'] ?? '',
            'image' => !empty($prod['product_image_left'])
                ? wp_get_attachment_image_url( $prod['product_image_left'], 'full' )
                : '',
            'link'  => $prod['product_link_left'] ?? '',
        ],
        'right_1' => [
            'title' => $prod['product_title_right_1'] ?? '',
            'price' => $prod['product_price_right_1'] ?? '',
            'image' => !empty($prod['product_image_right_1'])
                ? wp_get_attachment_image_url( $prod['product_image_right_1'], 'full' )
                : '',
            'link'  => $prod['product_link_right_1'] ?? '',
        ],
        'right_2' => [
            'title' => $prod['product_title_right_2'] ?? '',
            'price' => $prod['product_price_right_2'] ?? '',
            'image' => !empty($prod['product_image_right_2'])
                ? wp_get_attachment_image_url( $prod['product_image_right_2'], 'full' )
                : '',
            'link'  => $prod['product_link_right_2'] ?? '',
        ],
        'all_link' => $prod['all_link'] ?? '',
    ];
}

/* ===============================
   ③ Trending Now（重复器字段）
   =============================== */
$trending_items = [];

if ( have_rows('trending_now') ) :
    while ( have_rows('trending_now') ) : the_row();
        
        // 每次循环只添加一组数据
        $trending_items[] = [
            'type'        => get_sub_field('item_type') ?? '',
            'title'       => get_sub_field('item_title') ?? '',
            'description' => get_sub_field('item_description') ?? '',
        ];

    endwhile;
endif;
?>

<!-- 面包屑导航 -->
<div class="breadcrumb">
    <a href="<?php echo home_url('/'); ?>">HOME</a> / 
    <a href="<?php echo home_url('/news-analysis/'); ?>">NEWS & ANALYSIS</a> / 
    <a href="<?php echo home_url('/tactical-breakdown/'); ?>">TACTICAL BREAKDOWN</a>
</div>

<!-- 主内容区域 -->
<main class="page-content">
    <article class="article-content">
        
        <!-- 文章标题 -->
        <h1 class="article-title"><?php echo esc_html( $article_data['post_title'] ?? get_the_title() ); ?></h1>
        <!-- 文章标题与作者信息 -->
        <div class="article-meta">

            <?php if ( $article_data['avatar'] ) : ?>
                <img src="<?php echo esc_url( $article_data['avatar'] ); ?>" class="author-avatar">
            <?php else : ?>
                <div class="author-avatar author-initials">
                    <?php echo esc_html( $article_data['initials'] ); ?>
                </div>
            <?php endif; ?>

            <span class="author-name">
                By <?php echo esc_html( $article_data['name'] ); ?>
            </span>

            <span class="author-role">
                <?php echo esc_html( strtoupper($article_data['position']) ); ?>
            </span>

            <span class="publish-date">
                PUBLISHED <?php echo esc_html( strtoupper($article_data['post_date']) ); ?>
            </span>

        </div>

        <!-- 文章配图 -->
        <?php if ( $article_data['post_thumbnail'] ) : ?>
        <figure class="article-figure">
            <img src="<?php echo esc_url( $article_data['post_thumbnail'] ); ?>" alt="<?php the_title_attribute(); ?>" class="article-img">
            <?php if ( $article_data['post_thumbnail_caption'] ) : ?>
                <figcaption><?php echo esc_html( $article_data['post_thumbnail_caption'] ); ?></figcaption>
            <?php endif; ?>
        </figure>
        <?php endif; ?>

        <!-- 文章正文 -->
        <div class="article-body">
            <?php echo wp_kses_post( $article_data['post_content_main'] ); ?>

            <!-- 商品推荐（第一个 Prod 项的 left） -->
            <?php if ( ! empty( $prod_items ) && ! empty( $prod_items[0]['left']['image'] ) ) : ?>
            <div class="product-ad">
                <div class="product-card">
                    <img src="<?php echo esc_url( $prod_items[0]['left']['image'] ); ?>" alt="<?php echo esc_attr( $prod_items[0]['left']['title'] ); ?>" class="product-img">
                    <div class="product-info">
                        <h3>OFFICIAL JERSEY</h3>
                        <h4><?php echo esc_html( $prod_items[0]['left']['title'] ); ?></h4>
                        <p>Get match-ready. Authentic player fit.</p>
                        <span class="product-price"><?php echo esc_html( $prod_items[0]['left']['price'] ); ?></span>
                        <?php if ( $prod_items[0]['left']['link'] ) : ?>
                            <a href="<?php echo esc_url( $prod_items[0]['left']['link'] ); ?>" class="shop-now">SHOP NOW</a>
                        <?php else : ?>
                            <button class="shop-now">SHOP NOW</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- 文章标签 -->
        <?php if ( ! empty( $article_data['post_tags'] ) ) : ?>
        <div class="article-tags">
            Tags:
            <?php foreach ( $article_data['post_tags'] as $index => $tag ) : ?>
                <?php if ( $index > 0 ) echo ', '; ?>
                <span><?php echo esc_html( $tag ); ?></span>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- 作者卡片 -->
        <div class="author-card">

            <?php if ( $article_data['avatar'] ) : ?>
                <img src="<?php echo esc_url( $article_data['avatar'] ); ?>" class="author-card-avatar">
            <?php else : ?>
                <div class="author-card-avatar author-initials-large">
                    <?php echo esc_html( $article_data['initials'] ); ?>
                </div>
            <?php endif; ?>

            <div class="author-card-info">
                <h3><?php echo esc_html( $article_data['name'] ); ?></h3>

                <h4><?php echo esc_html( strtoupper($article_data['position']) ); ?></h4>

                <?php if ( $article_data['bio'] ) : ?>
                    <p><?php echo esc_html( $article_data['bio'] ); ?></p>
                <?php endif; ?>

                <?php if ( $article_data['profile_link'] ) : ?>
                    <a href="<?php echo esc_url( $article_data['profile_link'] ); ?>" class="view-all-posts">
                        View all posts →
                    </a>
                <?php endif; ?>

            </div>
        </div>
        </article>

    <!-- 侧边栏 -->
    <aside class="sidebar">
        <!-- 粉丝投票 -->
        <div class="sidebar-widget fan-poll">
            <h3>FAN POLL</h3>
            <h4>Will Argentina Repeat in 2026?</h4>
            <p>Vote to unlock a 10% off promo code!</p>
            <button class="poll-option">Yes, back-to-back!</button>
            <button class="poll-option">No, tough competition...</button>
        </div>

        <!-- 商品推荐 -->
        <?php if ( ! empty( $prod_items ) ) : ?>
        <div class="sidebar-widget shop-argentina">
            <h3>SHOP ARGENTINA</h3>
            
            <?php if ( ! empty( $prod_items[0]['right_1']['image'] ) ) : ?>
            <div class="shop-item">
                <img src="<?php echo esc_url( $prod_items[0]['right_1']['image'] ); ?>" alt="<?php echo esc_attr( $prod_items[0]['right_1']['title'] ); ?>" class="shop-item-img">
                <div class="shop-item-info">
                    <h4><?php echo esc_html( $prod_items[0]['right_1']['title'] ); ?></h4>
                    <span class="shop-item-price"><?php echo esc_html( $prod_items[0]['right_1']['price'] ); ?></span>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ( ! empty( $prod_items[0]['right_2']['image'] ) ) : ?>
            <div class="shop-item">
                <img src="<?php echo esc_url( $prod_items[0]['right_2']['image'] ); ?>" alt="<?php echo esc_attr( $prod_items[0]['right_2']['title'] ); ?>" class="shop-item-img">
                <div class="shop-item-info">
                    <h4><?php echo esc_html( $prod_items[0]['right_2']['title'] ); ?></h4>
                    <span class="shop-item-price"><?php echo esc_html( $prod_items[0]['right_2']['price'] ); ?></span>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ( ! empty( $prod_items[0]['all_link'] ) ) : ?>
                <a href="<?php echo esc_url( $prod_items[0]['all_link'] ); ?>" class="view-all-gear">VIEW ALL FAN GEAR →</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- 热门趋势 -->
        <?php if ( ! empty( $trending_items ) ) : ?>
        <div class="sidebar-widget trending-now">
            <h3>TRENDING NOW</h3>
            <?php foreach ( $trending_items as $trend ) : ?>
                <?php if ( ! empty( $trend['type'] ) || ! empty( $trend['title'] ) ) : ?>
                <div class="trend-item">
                    <?php if ( ! empty( $trend['type'] ) ) : ?>
                        <span class="trend-type"><?php echo esc_html( strtoupper( $trend['type'] ) ); ?></span>
                    <?php endif; ?>
                    <?php if ( ! empty( $trend['title'] ) ) : ?>
                        <h4 class="trend-title"><?php echo esc_html( $trend['title'] ); ?></h4>
                    <?php endif; ?>
                    <?php if ( ! empty( $trend['description'] ) ) : ?>
                        <p class="trend-description"><?php echo esc_html( $trend['description'] ); ?></p>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </aside>
</main>

<?php 
    endwhile;
endif; 
?>

<?php get_footer(); ?>