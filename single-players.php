<?php get_header(); ?>

<?php while (have_posts()):
    the_post(); ?>

    <?php
    // 基础字段
    $number = get_field('number');
    $country = get_field('country');
    $position = get_field('position');
    $height = get_field('height');
    $weight = get_field('weight');
    $birthday = get_field('birthday');
    $birthplace = get_field('birthplace');
    $foot = get_field('foot');
    $club = get_field('club');
    $description = get_field('description');
    $featured_image_id = get_field('featured_image');
    $profile = get_field('profile'); // 修复：添加 profile 字段
    $OHM = get_field('o_h_m');
    
    // 计算年龄
    $age = '';
    if ($birthday) {
        $birth_date = DateTime::createFromFormat('Y-m-d', $birthday);
        if ($birth_date) {
            $today = new DateTime();
            $age = $today->diff($birth_date)->y;
        }
    }

    // 技术能力
    $pace = get_field('pace');
    $shooting = get_field('shooting');
    $passing = get_field('passing');
    $dribbling = get_field('dribbling');
    $defending = get_field('defending');
    $physical = get_field('physical');

    // 身价走势（repeater）
    $market_values = get_field('market_values');

    // 比赛记录（repeater）
    $matches = get_field('matches');
    ?>

    <div class="container">

        <!-- 面包屑 -->
        <div class="breadcrumbs">
            <a href="<?php echo home_url(); ?>">Home</a> »
            <a href="<?php echo get_post_type_archive_link('players'); ?>">Players</a> »
            <span><?php the_title(); ?></span>
        </div>

        <!-- ================= Banner ================= -->
        <div class="player-banner">

            <div class="banner-left">
                <?php
                if ($featured_image_id) {
                    echo wp_get_attachment_image($featured_image_id, 'large');
                }
                ?>
            </div>

            <div class="banner-right">

                <div class="meta-top">
                    <div class="player-meta-bar">

                        <span class="country-badge">
                            <?php echo strtoupper($country); ?> #<?php echo esc_html($number); ?>
                        </span>

                        <span class="meta-divider"></span>

                        <span class="national-team">
                            <span class="flag">🇫🇷</span>
                            <?php echo esc_html($country); ?>
                        </span>

                        <span class="meta-divider"></span>

                        <span class="club-team">
                            <?php echo esc_html($club); ?>
                        </span>

                    </div>
                </div>

                <h1 class="player-title"><?php the_title(); ?></h1>

                <?php if ($description): ?>
                    <div class="player-description">
                        <?php echo esc_html($description); ?>
                    </div>
                <?php endif; ?>

                <div class="info-grid">
                    <div class="info-item"><label>Height</label><span><?php echo esc_html($height); ?></span></div>
                    <div class="info-item"><label>Weight</label><span><?php echo esc_html($weight); ?></span></div>
                    <div class="info-item"><label>Age</label><span><?php echo esc_html($age); ?></span></div>
                    <div class="info-item"><label>Birthday</label><span><?php echo esc_html($birthday); ?></span></div>
                    <div class="info-item"><label>Birthplace</label><span><?php echo esc_html($birthplace); ?></span></div>
                    <div class="info-item"><label>Foot</label><span><?php echo esc_html($foot); ?></span></div>
                </div>

            </div>

            <div class="big-name"><?php echo strtoupper(get_the_title()); ?></div>
        </div>

        <!-- ================= Tabs ================= -->
        <div class="tabs">

            <div class="tab-nav">
                <button class="tab-btn active" data-tab="tab1">Profile</button>
                <button class="tab-btn" data-tab="tab2">Equipment</button>
                <button class="tab-btn" data-tab="tab3">Career Data</button>
                <button class="tab-btn" data-tab="tab4">News</button>
            </div>

            <!-- Tab 1 -->
            <div id="tab1" class="tab-content active">
                <div class="grid-3">

                    <div class="card">
                        <h3>Skill Radar</h3>
                        <canvas id="radarChart"></canvas>
                    </div>

                    <div class="card">
                        <h3>Market value trend</h3>
                        <canvas id="marketChart"></canvas>
                    </div>

                    <div class="card">
                        <h3>Offensive Heat Map</h3>
                        <?php
                        if ($OHM) {
                            echo wp_get_attachment_image($OHM, 'large');
                        }
                        ?>
                    </div>


                    <!-- <div class="card poll">
                        <h3>胜负竞猜 POLL</h3>
                        <div class="poll-item">法国 82%</div>
                        <div class="poll-item">加拿大 12%</div>
                        <div class="poll-item">平局 6%</div>
                    </div> -->

                </div>

                <div class="card mt30">
                    <h3>球员简介 Profile</h3>
                    <?php 
                    // 修复：添加空值检查,避免传递 null 给 wp_kses_post
                    if ($profile) {
                        echo wp_kses_post($profile);
                    } else {
                        echo '<p>暂无球员简介</p>';
                    }
                    ?>
                </div>




                <div class="player-card">
                    <h2 class="card-title">Player Details</h2>

                    <div class="info-end-grid">
                        <div class="info-item">
                            <span class="label">Name</span>
                            <span class="value">Lionel Messi</span>
                        </div>

                        <div class="info-item">
                            <span class="label">Height</span>
                            <span class="value">5'7"</span>
                        </div>

                        <div class="info-end-item">
                            <span class="label">Weight</span>
                            <span class="value">148 lbs</span>
                        </div>

                        <div class="info-end-item">
                            <span class="label">Date of Birth</span>
                            <span class="value">6.24.1987 (38)</span>
                        </div>

                        <div class="info-end-item">
                            <span class="label">Birthplace</span>
                            <span class="value">Rosario, Argentina</span>
                        </div>

                        <div class="info-end-item">
                            <span class="label">Position</span>
                            <span class="value">Midfielder</span>
                        </div>

                        <div class="info-end-item">
                            <span class="label">Footedness</span>
                            <span class="value">Left</span>
                        </div>

                        <div class="info-end-item">
                            <span class="label">Roster Category</span>
                            <span class="value">Senior</span>
                        </div>

                        <div class="info-end-item full-width">
                            <span class="label">Player Category</span>
                            <span class="value">Designated Player, International</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Tab 2 -->
            <div id="tab2" class="tab-content">
                <div class="card">
                    <h3>市场价值变化</h3>
                    <canvas id="marketChart2"></canvas>
                </div>
            </div>

            <!-- Tab 3 -->
            <div id="tab3" class="tab-content">
                <div class="card">
                    <h3>比赛记录</h3>
                    <table class="match-table">
                        <thead>
                            <tr>
                                <th>日期</th>
                                <th>赛事</th>
                                <th>对手</th>
                                <th>场地</th>
                                <th>状态</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($matches):
                                foreach ($matches as $row): ?>
                                    <tr>
                                        <td><?php echo esc_html($row['date']); ?></td>
                                        <td><?php echo esc_html($row['competition']); ?></td>
                                        <td><?php echo esc_html($row['opponent']); ?></td>
                                        <td><?php echo esc_html($row['venue']); ?></td>
                                        <td><span class="status"><?php echo esc_html($row['status']); ?></span></td>
                                    </tr>
                                <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab 4 -->
            <div id="tab4" class="tab-content">
                <div class="card">
                    <h3>相关文章</h3>
                    <?php
                    $related = new WP_Query([
                        'post_type' => 'post',
                        'posts_per_page' => 3
                    ]);
                    while ($related->have_posts()):
                        $related->the_post(); ?>
                        <div class="related-item">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </div>
                    <?php endwhile;
                    wp_reset_postdata(); ?>
                </div>
            </div>

        </div>

    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
    document.addEventListener("DOMContentLoaded", function() {

        /* =========================
        ✅ Radar Chart
        ========================== */
        const radarCanvas = document.getElementById('radarChart');

        if (radarCanvas) {
            new Chart(radarCanvas, {
                type: 'radar',
                data: {
                    labels: ['速度', '射门', '传球', '盘带', '防守', '身体'],
                    datasets: [{
                        data: [
                            <?php echo intval($pace); ?>,
                            <?php echo intval($shooting); ?>,
                            <?php echo intval($passing); ?>,
                            <?php echo intval($dribbling); ?>,
                            <?php echo intval($defending); ?>,
                            <?php echo intval($physical); ?>
                        ],
                        backgroundColor: 'rgba(255,193,7,0.2)',
                        borderColor: '#ffc107',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        r: {
                            beginAtZero: true,
                            max: 100
                        }
                    }
                }
            });
        }

        /* =========================
        ✅ Tabs 切换
        ========================== */
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));

                this.classList.add('active');
                const target = document.getElementById(this.dataset.tab);
                if (target) target.classList.add('active');
            });
        });

        /* =========================
        ✅ Market Chart
        ========================== */
        const marketCanvas = document.getElementById('marketChart');

        if (marketCanvas) {
            new Chart(marketCanvas.getContext('2d'), {
                type: 'line',
                data: {
                    labels: ['2020','2021','2022','2023','2024','2025','2026'],
                    datasets: [{
                        data: [1.2,2.1,2.6,2.6,3.0,3.2,3.4],
                        borderColor: '#1f3b73',
                        backgroundColor: 'rgba(31,59,115,0.1)',
                        borderWidth: 3,
                        pointBackgroundColor: '#f5a623',
                        pointRadius: 5,
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: { grid: { display: false } },
                        y: {
                            min: 0,
                            max: 4,
                            ticks: { stepSize: 1 },
                            grid: {
                                color: '#dcdcdc',
                                borderDash: [5,5]
                            }
                        }
                    }
                }
            });
        }

    });
    </script>


<?php endwhile; ?>
<?php get_footer(); ?>