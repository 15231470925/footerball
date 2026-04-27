<?php get_header(); ?>

<?php while (have_posts()): the_post(); ?>

<?php
// ===== 获取 ACF 字段 =====
$team_motto = get_field('team_motto');
$team_fifa_rank = get_field('team_fifa_rank');
$team_confederation = get_field('team_confederation');
$team_world_cup_titles = get_field('team_world_cup_titles');
$team_founded = get_field('team_founded');
$team_stadium = get_field('team_stadium');
$team_coach = get_field('team_coach');
$team_captain = get_field('team_captain');
$team_description = get_field('team_description');

// FIFA 排名统计
$fifa_rank_highest = get_field('fifa_rank_highest');
$fifa_rank_lowest = get_field('fifa_rank_lowest');
$fifa_rank_current = get_field('fifa_rank_current');

// Top Scorers (repeater)
$top_scorers = get_field('top_scorers');

// 近期战绩 (repeater)
$recent_matches = get_field('recent_matches');

// 官方周边 (repeater)
$official_merch = get_field('official_merch');

// 球队新闻 (repeater)
$team_news = get_field('team_news');

// 球迷投票
$vote_option_a = get_field('vote_option_a');
$vote_option_a_pct = get_field('vote_option_a_pct');
$vote_option_b = get_field('vote_option_b');
$vote_option_b_pct = get_field('vote_option_b_pct');

// Top Players (repeater)
$top_players = get_field('top_players');

// FAQ (repeater)
$faq_items = get_field('faq_items');

// 球队 Logo
$team_logo_id = get_field('team_logo');

// 球队颜色
$team_primary_color = get_field('team_primary_color') ?: '#75AADB';
$team_secondary_color = get_field('team_secondary_color') ?: '#FFFFFF';

// 大背景图
$team_banner_bg = get_field('team_banner_bg');

// 历史荣誉 (repeater)
$team_honors = get_field('team_honors');

// 2026 WC 预期
$wc_prediction = get_field('world_cup_prediction');
?>

<!-- ==================== Team Banner ==================== -->
<section class="team-banner-section">
    <div class="team-banner-bg" style="
        background: linear-gradient(180deg, rgba(10,25,60,0.95) 0%, rgba(10,25,60,0.7) 50%, rgba(10,25,60,0.95) 100%),
        url('<?php echo $team_banner_bg ? wp_get_attachment_image_url($team_banner_bg, 'full') : 'https://images.unsplash.com/photo-1574629810360-7efbbe195018?w=1920'; ?>');
        background-size: cover;
        background-position: center;
    ">
        <div class="container">
            <div class="team-banner-content">
                <!-- 面包屑 -->
                <div class="team-breadcrumbs">
                    <a href="<?php echo home_url(); ?>">Home</a>
                    <span class="breadcrumb-sep">/</span>
                    <a href="<?php echo get_post_type_archive_link('teams'); ?>">Teams</a>
                    <span class="breadcrumb-sep">/</span>
                    <span><?php the_title(); ?></span>
                </div>

                <div class="team-banner-main">
                    <!-- 左侧：球队 Logo + 队名 -->
                    <div class="team-banner-left">
                        <div class="team-logo-wrapper">
                            <?php if ($team_logo_id): ?>
                                <?php echo wp_get_attachment_image($team_logo_id, 'large', false, array('class' => 'team-logo-img')); ?>
                            <?php else: ?>
                                <div class="team-logo-placeholder" style="background: <?php echo esc_attr($team_primary_color); ?>;">
                                    <span><?php echo strtoupper(mb_substr(get_the_title(), 0, 2)); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="team-name-section">
                            <p class="team-confederation-label">CONMEBOL / 南美足联</p>
                            <h1 class="team-name-large"><?php the_title(); ?></h1>
                            <?php if ($team_motto): ?>
                                <p class="team-motto">"<?php echo esc_html($team_motto); ?>"</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- 右侧：关键数据 -->
                    <div class="team-banner-right">
                        <div class="team-stats-grid">
                            <div class="team-stat-item">
                                <span class="stat-value"><?php echo esc_html($team_fifa_rank); ?></span>
                                <span class="stat-label">FIFA 排名</span>
                            </div>
                            <div class="team-stat-item">
                                <span class="stat-value"><?php echo esc_html($team_confederation); ?></span>
                                <span class="stat-label">所属足联</span>
                            </div>
                            <div class="team-stat-item">
                                <span class="stat-value"><?php echo esc_html($team_world_cup_titles); ?></span>
                                <span class="stat-label">世界杯冠军次数</span>
                            </div>
                        </div>
                        <div class="team-colors-row">
                            <span class="team-color-label">主色</span>
                            <div class="color-dots">
                                <span class="color-dot" style="background: <?php echo esc_attr($team_primary_color); ?>;"></span>
                                <span class="color-dot" style="background: <?php echo esc_attr($team_secondary_color); ?>;"></span>
                                <span class="color-dot" style="background: #1B1B1B;"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==================== Tab Navigation ==================== -->
<section class="team-tabs-section">
    <div class="container">
        <div class="team-tabs-nav">
            <button class="team-tab-btn active" data-tab="team-info">团队信息</button>
            <button class="team-tab-btn" data-tab="team-shop">官方商城</button>
            <button class="team-tab-btn" data-tab="team-players">球员数据库</button>
            <button class="team-tab-btn" data-tab="team-news">新闻中心</button>
        </div>
    </div>
</section>

<!-- ==================== Tab Content: Team Info ==================== -->
<div class="team-tab-content active" id="tab-team-info">
    <div class="container">
        <div class="team-info-layout">

            <!-- ===== 左侧主内容区 ===== -->
            <div class="team-info-main">

                <!-- 球队信息 Profile -->
                <div class="info-card">
                    <h3 class="info-card-title"><span class="title-accent"></span>球队信息 Profile</h3>
                    <div class="team-profile-text">
                        <?php if ($team_description): ?>
                            <?php echo wp_kses_post($team_description); ?>
                        <?php else: ?>
                            <p><?php the_content(); ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- 基本信息网格 -->
                    <div class="team-info-grid">
                        <div class="info-grid-item">
                            <span class="info-label">成立时间</span>
                            <span class="info-value"><?php echo esc_html($team_founded); ?></span>
                        </div>
                        <div class="info-grid-item">
                            <span class="info-label">主场球场</span>
                            <span class="info-value"><?php echo esc_html($team_stadium); ?></span>
                        </div>
                        <div class="info-grid-item">
                            <span class="info-label">主教练</span>
                            <span class="info-value"><?php echo esc_html($team_coach); ?></span>
                        </div>
                        <div class="info-grid-item">
                            <span class="info-label">队长</span>
                            <span class="info-value"><?php echo esc_html($team_captain); ?></span>
                        </div>
                    </div>

                    <!-- 2026世界杯预期 -->
                    <?php if (get_field('world_cup_prediction')): ?>
                    <div class="world-cup-prediction">
                        <h4>2026 世界杯预期 (2026 WC)</h4>
                        <p><?php echo esc_html(get_field('world_cup_prediction')); ?></p>
                    </div>
                    <?php endif; ?>

                    <!-- 历史荣誉 -->
                    <?php $honors = get_field('team_honors'); if ($honors): ?>
                    <div class="team-honors">
                        <h4>历史荣誉</h4>
                        <div class="honors-list">
                            <?php foreach ($honors as $honor): ?>
                            <div class="honor-item">
                                <span class="honor-year"><?php echo esc_html($honor['year']); ?></span>
                                <span class="honor-title"><?php echo esc_html($honor['title']); ?></span>
                                <span class="honor-icon">🏆</span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- 近期大赛战绩 Stats -->
                <?php if ($recent_matches): ?>
                <div class="info-card">
                    <h3 class="info-card-title"><span class="title-accent"></span>近期大赛战绩 Stats</h3>
                    <div class="stats-table-wrapper">
                        <table class="stats-table">
                            <thead>
                                <tr>
                                    <th>年份</th>
                                    <th>赛事</th>
                                    <th>赛</th>
                                    <th>胜</th>
                                    <th>平</th>
                                    <th>负</th>
                                    <th>进球</th>
                                    <th>失球</th>
                                    <th>排名/名次</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_matches as $match): ?>
                                <tr>
                                    <td><?php echo esc_html($match['year']); ?></td>
                                    <td><?php echo esc_html($match['competition']); ?></td>
                                    <td><?php echo esc_html($match['played']); ?></td>
                                    <td class="stat-win"><?php echo esc_html($match['won']); ?></td>
                                    <td><?php echo esc_html($match['drawn']); ?></td>
                                    <td class="stat-loss"><?php echo esc_html($match['lost']); ?></td>
                                    <td><?php echo esc_html($match['goals_for']); ?></td>
                                    <td><?php echo esc_html($match['goals_against']); ?></td>
                                    <td><span class="rank-badge rank-<?php echo esc_attr($match['rank_color'] ?? 'gold'); ?>"><?php echo esc_html($match['rank']); ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Top Scorers -->
                <?php if ($top_scorers): ?>
                <div class="info-card">
                    <h3 class="info-card-title"><span class="title-accent"></span>Top Scorers</h3>
                    <div class="top-scorers-list">
                        <?php foreach ($top_scorers as $scorer): ?>
                        <div class="scorer-card">
                            <?php if (!empty($scorer['avatar'])): ?>
                                <img src="<?php echo esc_url($scorer['avatar']); ?>" alt="<?php echo esc_attr($scorer['name']); ?>" class="scorer-avatar">
                            <?php endif; ?>
                            <span class="scorer-name"><?php echo esc_html($scorer['name']); ?></span>
                            <span class="scorer-position"><?php echo esc_html($scorer['position']); ?></span>
                            <span class="scorer-goals"><?php echo esc_html($scorer['goals']); ?></span>
                            <span class="scorer-goals-label">进球</span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- 官方周边商品 Gear -->
                <?php if ($official_merch): ?>
                <div class="info-card">
                    <h3 class="info-card-title"><span class="title-accent"></span>官方周边 Gear</h3>
                    <div class="merch-grid">
                        <?php foreach ($official_merch as $merch): ?>
                        <div class="merch-card">
                            <div class="merch-image">
                                <img src="<?php echo esc_url($merch['image']); ?>" alt="<?php echo esc_attr($merch['name']); ?>">
                            </div>
                            <h4 class="merch-name"><?php echo esc_html($merch['name']); ?></h4>
                            <span class="merch-price">¥<?php echo esc_html($merch['price']); ?></span>
                            <a href="<?php echo esc_url($merch['link']); ?>" class="merch-btn">立即购买</a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- 球队新闻 News -->
                <?php if ($team_news): ?>
                <div class="info-card">
                    <h3 class="info-card-title"><span class="title-accent"></span>球队新闻 News</h3>
                    <div class="team-news-list">
                        <?php foreach ($team_news as $news): ?>
                        <a href="<?php echo esc_url($news['link']); ?>" class="team-news-item">
                            <?php if (!empty($news['thumb'])): ?>
                            <div class="news-thumb">
                                <img src="<?php echo esc_url($news['thumb']); ?>" alt="">
                            </div>
                            <?php endif; ?>
                            <div class="news-content">
                                <span class="news-tag"><?php echo esc_html($news['tag']); ?></span>
                                <h4><?php echo esc_html($news['title']); ?></h4>
                                <p><?php echo esc_html($news['excerpt']); ?></p>
                                <span class="news-date"><?php echo esc_html($news['date']); ?></span>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- FAQ -->
                <?php if ($faq_items): ?>
                <div class="info-card">
                    <h3 class="info-card-title"><span class="title-accent"></span>常见问答 FAQ</h3>
                    <div class="faq-list">
                        <?php foreach ($faq_items as $faq): ?>
                        <div class="faq-item">
                            <button class="faq-question" onclick="this.parentElement.classList.toggle('open')">
                                <span><?php echo esc_html($faq['question']); ?></span>
                                <span class="faq-arrow">▼</span>
                            </button>
                            <div class="faq-answer">
                                <p><?php echo wp_kses_post($faq['answer']); ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

            </div>

            <!-- ===== 右侧边栏 ===== -->
            <aside class="team-info-sidebar">

                <!-- 排名数据 -->
                <div class="sidebar-card">
                    <h4 class="sidebar-card-title">排名数据</h4>
                    <div class="rank-data-list">
                        <div class="rank-data-item">
                            <span class="rank-data-label">历史最高排名</span>
                            <span class="rank-data-value highlight"><?php echo esc_html($fifa_rank_highest); ?></span>
                        </div>
                        <div class="rank-data-item">
                            <span class="rank-data-label">历史最低排名</span>
                            <span class="rank-data-value"><?php echo esc_html($fifa_rank_lowest); ?></span>
                        </div>
                        <div class="rank-data-item">
                            <span class="rank-data-label">当前排名</span>
                            <span class="rank-data-value highlight"><?php echo esc_html($fifa_rank_current); ?></span>
                        </div>
                    </div>
                </div>

                <!-- 胜负竞猜 POLL -->
                <div class="sidebar-card poll-card">
                    <h4 class="sidebar-card-title">胜负竞猜 POLL</h4>
                    <p class="poll-question">阿根廷 vs 法国，谁赢呢？</p>
                    <div class="poll-bars">
                        <div class="poll-bar-item">
                            <div class="poll-bar-label">
                                <span>阿根廷</span>
                                <span class="poll-pct"><?php echo $vote_option_a_pct ? esc_html($vote_option_a_pct) : '72'; ?>%</span>
                            </div>
                            <div class="poll-bar-bg">
                                <div class="poll-bar-fill" style="width: <?php echo $vote_option_a_pct ? esc_attr($vote_option_a_pct) : '72'; ?>%"></div>
                            </div>
                        </div>
                        <div class="poll-bar-item">
                            <div class="poll-bar-label">
                                <span>法国</span>
                                <span class="poll-pct"><?php echo $vote_option_b_pct ? esc_html($vote_option_b_pct) : '28'; ?>%</span>
                            </div>
                            <div class="poll-bar-bg">
                                <div class="poll-bar-fill" style="width: <?php echo $vote_option_b_pct ? esc_attr($vote_option_b_pct) : '28'; ?>%; background: #1a3a5c;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 核心球员 Top Players -->
                <?php if ($top_players): ?>
                <div class="sidebar-card">
                    <h4 class="sidebar-card-title">核心球员</h4>
                    <div class="top-players-grid">
                        <?php foreach ($top_players as $player): ?>
                        <a href="<?php echo esc_url($player['link'] ?? '#'); ?>" class="top-player-item">
                            <img src="<?php echo esc_url($player['avatar']); ?>" alt="<?php echo esc_attr($player['name']); ?>">
                            <span class="top-player-name"><?php echo esc_html($player['name']); ?></span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- 广告 Banner -->
                <div class="sidebar-ad">
                    <div class="ad-inner">
                        <span class="ad-badge">限时特惠</span>
                        <h4>阿根廷国家队官方正品球衣</h4>
                        <p>全场满299元包邮</p>
                        <a href="#" class="ad-btn">立即选购</a>
                    </div>
                </div>

            </aside>

        </div>
    </div>
</div>

<!-- ==================== Tab Content: Shop ==================== -->
<div class="team-tab-content" id="tab-team-shop">
    <div class="container">
        <div class="tab-placeholder">
            <div class="placeholder-icon">🛍️</div>
            <h3>官方商城</h3>
            <p>阿根廷官方周边商品即将上线</p>
        </div>
    </div>
</div>

<!-- ==================== Tab Content: Players ==================== -->
<div class="team-tab-content" id="tab-team-players">
    <div class="container">
        <div class="tab-placeholder">
            <div class="placeholder-icon">⚽</div>
            <h3>球员数据库</h3>
            <p>阿根廷国家队球员完整名单即将上线</p>
        </div>
    </div>
</div>

<!-- ==================== Tab Content: News ==================== -->
<div class="team-tab-content" id="tab-team-news">
    <div class="container">
        <div class="tab-placeholder">
            <div class="placeholder-icon">📰</div>
            <h3>新闻中心</h3>
            <p>阿根廷相关新闻报道即将上线</p>
        </div>
    </div>
</div>

<!-- ==================== Tab JS ==================== -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    var tabs = document.querySelectorAll('.team-tab-btn');
    var contents = document.querySelectorAll('.team-tab-content');

    tabs.forEach(function(tab) {
        tab.addEventListener('click', function() {
            tabs.forEach(function(t) { t.classList.remove('active'); });
            contents.forEach(function(c) { c.classList.remove('active'); });
            this.classList.add('active');
            var target = document.getElementById('tab-' + this.dataset.tab);
            if (target) target.classList.add('active');
        });
    });
});
</script>

<?php endwhile; ?>

<?php get_footer(); ?>
