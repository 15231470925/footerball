<?php
/**
 * Players archive: World Cup 2026 player directory.
 */

get_header();

$players = get_posts(array(
    'post_type'      => 'players',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'orderby'        => 'title',
    'order'          => 'ASC',
));

$normalize_player_text = function ($value) {
    if (is_object($value)) {
        if (isset($value->post_title)) {
            return (string) $value->post_title;
        }
        if (isset($value->name)) {
            return (string) $value->name;
        }
        return '';
    }

    if (is_array($value)) {
        $parts = array();
        foreach ($value as $item) {
            if (is_object($item)) {
                if (isset($item->post_title)) {
                    $parts[] = (string) $item->post_title;
                } elseif (isset($item->name)) {
                    $parts[] = (string) $item->name;
                }
            } elseif (is_scalar($item)) {
                $parts[] = (string) $item;
            }
        }
        return implode(', ', array_filter($parts));
    }

    return is_scalar($value) ? trim((string) $value) : '';
};

$player_flag_map = array(
    'Argentina' => '🇦🇷', 'Australia' => '🇦🇺', 'Austria' => '🇦🇹', 'Belgium' => '🇧🇪',
    'Bolivia' => '🇧🇴', 'Bosnia and Herzegovina' => '🇧🇦', 'Brazil' => '🇧🇷',
    'Cameroon' => '🇨🇲', 'Canada' => '🇨🇦', 'Cape Verde' => '🇨🇻', 'Colombia' => '🇨🇴',
    'Costa Rica' => '🇨🇷', 'Croatia' => '🇭🇷', 'Czech Republic' => '🇨🇿',
    'Democratic Republic of the Congo' => '🇨🇩', 'Denmark' => '🇩🇰',
    'England' => '🏴', 'France' => '🇫🇷', 'Germany' => '🇩🇪', 'Ghana' => '🇬🇭',
    'Guinea-Bissau' => '🇬🇼', 'Haiti' => '🇭🇹', 'Iran' => '🇮🇷', 'Iraq' => '🇮🇶',
    'Italy' => '🇮🇹', 'Ivory Coast' => '🇨🇮', 'Jamaica' => '🇯🇲', 'Japan' => '🇯🇵',
    'Jordan' => '🇯🇴', 'Kingdom of Denmark' => '🇩🇰',
    'Kingdom of the Netherlands' => '🇳🇱', 'Kosovo' => '🇽🇰', 'Liberia' => '🇱🇷',
    'Lithuania' => '🇱🇹', 'Luxembourg' => '🇱🇺', 'Mali' => '🇲🇱', 'Mexico' => '🇲🇽',
    'Morocco' => '🇲🇦', 'Netherlands' => '🇳🇱', 'New Zealand' => '🇳🇿',
    'Nigeria' => '🇳🇬', 'Norway' => '🇳🇴', 'Panama' => '🇵🇦', 'Paraguay' => '🇵🇾',
    'Peru' => '🇵🇪', 'Portugal' => '🇵🇹', 'Qatar' => '🇶🇦',
    'Republic of the Congo' => '🇨🇬', 'Russia' => '🇷🇺', 'Saudi Arabia' => '🇸🇦',
    'Scotland' => '🏴', 'Senegal' => '🇸🇳', 'Serbia' => '🇷🇸', 'South Africa' => '🇿🇦',
    'South Korea' => '🇰🇷', 'Spain' => '🇪🇸', 'Switzerland' => '🇨🇭',
    'Syria' => '🇸🇾', 'São Tomé and Príncipe' => '🇸🇹', 'Tunisia' => '🇹🇳',
    'Türkiye' => '🇹🇷', 'Turkey' => '🇹🇷', 'United Kingdom' => '🇬🇧',
    'United Kingdom of Great Britain and Ireland' => '🇬🇧', 'United States' => '🇺🇸',
    'USA' => '🇺🇸', 'Uruguay' => '🇺🇾', 'Uzbekistan' => '🇺🇿', 'Venezuela' => '🇻🇪',
);

$position_code = function ($position) {
    $position_lc = strtolower((string) $position);
    if (strpos($position_lc, 'goal') !== false || $position_lc === 'gk') {
        return 'GK';
    }
    if (strpos($position_lc, 'def') !== false || strpos($position_lc, 'back') !== false || $position_lc === 'df') {
        return 'DF';
    }
    if (strpos($position_lc, 'mid') !== false || $position_lc === 'mf') {
        return 'MF';
    }
    return 'FW';
};

$players_data = array();
$team_options = array();
$country_options = array();
$position_options = array();
$position_counts = array('FW' => 0, 'MF' => 0, 'DF' => 0, 'GK' => 0);
$group_labels = range('A', 'L');
$team_groups = array();

foreach ($players as $player_post) {
    $player_id = (int) $player_post->ID;
    $player_name = function_exists('footerball_get_player_display_name') ? footerball_get_player_display_name($player_id) : get_the_title($player_id);
    $player_slug = get_post_field('post_name', $player_id);
    $player_link = home_url('/players/' . $player_slug . '/profile/');

    $country = $normalize_player_text(get_field('country', $player_id));
    if ($country === '') {
        $country = 'Unknown';
    }

    $player_team_field = get_field('player_team', $player_id);
    $team = $normalize_player_text($player_team_field);
    if ($team === '') {
        $team = $normalize_player_text(get_field('team', $player_id));
    }
    if ($team === '') {
        $team = $normalize_player_text(get_field('national_team', $player_id));
    }
    if ($team === '') {
        $team = $country;
    }

    if (!isset($team_groups[$team])) {
        $team_groups[$team] = $group_labels[count($team_groups) % count($group_labels)];
    }

    $position = $normalize_player_text(get_field('position', $player_id));
    if ($position === '') {
        $position = 'Forward';
    }
    $pos_code = $position_code($position);
    $position_counts[$pos_code]++;

    $birthday = $normalize_player_text(get_field('birthday', $player_id));
    $age = '';
    if ($birthday) {
        $birth_date = DateTime::createFromFormat('Y-m-d', $birthday);
        if ($birth_date) {
            $age = (string) (new DateTime())->diff($birth_date)->y;
        }
    }
    if ($age === '') {
        $age = '26';
    }

    $height = $normalize_player_text(get_field('height', $player_id));
    if ($height === '') {
        $height_display = '178 cm';
    } elseif (preg_match('/^\d+(?:\.\d+)?$/', $height)) {
        $height_display = $height . ' cm';
    } else {
        $height_display = preg_replace('/\s*cm$/i', ' cm', $height);
    }
    $number = $normalize_player_text(get_field('number', $player_id));
    if ($number === '') {
        $number = '10';
    }
    $club = $normalize_player_text(get_field('club', $player_id));

    $image_id = get_field('featured_image', $player_id);
    if (!$image_id) {
        $image_id = get_post_thumbnail_id($player_id);
    }
    $image_id = (int) $image_id;
    $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'medium') : '';

    $team_options[] = $team;
    $country_options[] = $country;
    $position_options[] = $position;

    $players_data[] = array(
        'id'       => $player_id,
        'slug'     => $player_slug,
        'name'     => $player_name,
        'link'     => $player_link,
        'team'     => $team,
        'country'  => $country,
        'flag'     => isset($player_flag_map[$country]) ? $player_flag_map[$country] : '🏳',
        'group'    => $team_groups[$team],
        'position' => $position,
        'pos_code' => $pos_code,
        'number'   => $number,
        'club'     => $club,
        'age'      => $age,
        'height'   => $height_display,
        'image_id' => $image_id,
        'image_url' => $image_url,
        'modified' => get_post_modified_time('U', false, $player_id),
    );
}

$featured_player_order = array(
    'lionel messi' => 1,
    'kylian mbappe' => 2,
    'jude bellingham' => 3,
    'vinicius junior' => 4,
    'cristiano ronaldo' => 5,
    'erling haaland' => 6,
    'joshua kimmich' => 7,
    'pedri' => 8,
    'virgil van dijk' => 9,
    'achraf hakimi' => 10,
    'thibaut courtois' => 11,
    'alphonso davies' => 12,
);

usort($players_data, function ($a, $b) use ($featured_player_order) {
    $a_key = strtolower(remove_accents($a['name']));
    $b_key = strtolower(remove_accents($b['name']));
    $a_featured = isset($featured_player_order[$a_key]) ? $featured_player_order[$a_key] : 9999;
    $b_featured = isset($featured_player_order[$b_key]) ? $featured_player_order[$b_key] : 9999;
    if ($a_featured !== $b_featured) {
        return $a_featured <=> $b_featured;
    }
    $name_compare = strcasecmp($a['name'], $b['name']);
    return $name_compare !== 0 ? $name_compare : ((int) $a['id'] <=> (int) $b['id']);
});

$team_options = array_values(array_unique(array_filter($team_options)));
$country_options = array_values(array_unique(array_filter($country_options)));
$position_options = array_values(array_unique(array_filter($position_options)));
sort($team_options, SORT_NATURAL | SORT_FLAG_CASE);
sort($country_options, SORT_NATURAL | SORT_FLAG_CASE);
sort($position_options, SORT_NATURAL | SORT_FLAG_CASE);

$players_recent = $players_data;
usort($players_recent, function ($a, $b) {
    return $b['modified'] <=> $a['modified'];
});
$players_recent = array_slice($players_recent, 0, 5);
$team_count = count($team_options);
$player_count = count($players_data);
$initial_players = array_slice($players_data, 0, 12);
$players_client_data = array_map(static function ($player) {
    return array(
        'id' => (int) $player['id'],
        'slug' => (string) $player['slug'],
        'name' => (string) $player['name'],
        'link' => (string) $player['link'],
        'team' => (string) $player['team'],
        'country' => (string) $player['country'],
        'flag' => (string) $player['flag'],
        'group' => (string) $player['group'],
        'position' => (string) $player['position'],
        'pos_code' => (string) $player['pos_code'],
        'number' => (string) $player['number'],
        'age' => (string) $player['age'],
        'height' => (string) $player['height'],
        'image_url' => (string) $player['image_url'],
    );
}, $players_data);
?>

<main class="players-dir-page">
    <section class="players-dir-hero">
        <div class="players-dir-shell">
            <nav class="players-dir-breadcrumb" aria-label="Breadcrumb">
                <a href="<?php echo esc_url(home_url('/')); ?>">Home</a>
                <span>Players</span>
            </nav>

            <div class="players-dir-hero__top">
                <div>
                    <h1>PLAYER DIRECTORY</h1>
                    <p>Explore all players in the FIFA World Cup 2026.</p>
                </div>
                <div class="players-dir-counts" aria-label="Directory totals">
                    <span><strong><?php echo esc_html(number_format_i18n($player_count)); ?></strong><em>Players</em></span>
                    <span><strong><?php echo esc_html(number_format_i18n($team_count)); ?></strong><em>Teams</em></span>
                </div>
            </div>

            <div class="players-dir-strip">
                <div class="players-dir-position-tabs" aria-label="Top by position">
                    <span>TOP BY POSITION</span>
                    <button type="button" data-pos="FW"><b>Forwards</b><em><?php echo esc_html($position_counts['FW']); ?></em></button>
                    <button type="button" data-pos="MF"><b>Midfielders</b><em><?php echo esc_html($position_counts['MF']); ?></em></button>
                    <button type="button" data-pos="DF"><b>Defenders</b><em><?php echo esc_html($position_counts['DF']); ?></em></button>
                    <button type="button" data-pos="GK"><b>Goalkeepers</b><em><?php echo esc_html($position_counts['GK']); ?></em></button>
                </div>
            </div>
        </div>
    </section>

    <section class="players-dir-body">
        <div class="players-dir-shell players-dir-layout">
            <aside class="players-dir-filter" aria-label="Filter players">
                <div class="players-dir-filter__head">
                    <h2>FILTER PLAYERS</h2>
                    <button type="button" id="players-clear-filters">Clear All</button>
                </div>

                <label class="players-dir-search">
                    <span>Search players by name...</span>
                    <input id="players-search" type="search" placeholder="Search players by name...">
                </label>

                <label class="players-dir-control">
                    <span>Team</span>
                    <select id="players-team-filter">
                        <option value="all">All Teams</option>
                        <?php foreach ($team_options as $team) : ?>
                            <option value="<?php echo esc_attr($team); ?>"><?php echo esc_html($team); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label class="players-dir-control">
                    <span>Position</span>
                    <select id="players-position-filter">
                        <option value="all">All Positions</option>
                        <option value="FW">Forwards</option>
                        <option value="MF">Midfielders</option>
                        <option value="DF">Defenders</option>
                        <option value="GK">Goalkeepers</option>
                    </select>
                </label>

                <label class="players-dir-control">
                    <span>Sort By</span>
                    <select id="players-sort-filter">
                        <option value="name-asc">Name (A-Z)</option>
                        <option value="team-asc">Team</option>
                        <option value="position-asc">Position</option>
                    </select>
                </label>

                <div class="players-dir-view">
                    <span>VIEW</span>
                    <button class="is-active" type="button" data-view="grid">Grid</button>
                    <button type="button" data-view="list">List</button>
                </div>
            </aside>

            <div class="players-dir-results">
                <div class="players-dir-results__top">
                    <p>Showing <span id="players-range-label"><?php echo esc_html($player_count ? '1-' . min(12, $player_count) : '0-0'); ?></span> of <span id="players-total-label"><?php echo esc_html(number_format_i18n($player_count)); ?></span> players</p>
                    <div class="players-dir-pages" aria-label="Player pagination"></div>
                </div>

                <div id="players-directory-grid" class="players-dir-grid">
                    <?php foreach ($initial_players as $player) : ?>
                        <article
                            class="players-dir-card"
                            data-name="<?php echo esc_attr(strtolower($player['name'])); ?>"
                            data-team="<?php echo esc_attr($player['team']); ?>"
                            data-country="<?php echo esc_attr($player['country']); ?>"
                            data-position="<?php echo esc_attr($player['pos_code']); ?>"
                            data-group="<?php echo esc_attr($player['group']); ?>"
                            data-age="<?php echo esc_attr($player['age']); ?>"
                        >
                            <a class="players-dir-card__main" href="<?php echo esc_url($player['link']); ?>">
                                <span class="players-dir-card__flag"><?php echo esc_html($player['flag']); ?></span>
                                <span class="players-dir-card__pos"><?php echo esc_html($player['pos_code']); ?></span>
                                <span class="players-dir-card__num"><?php echo esc_html($player['number']); ?></span>
                                <span class="players-dir-card__photo">
                                    <?php if ($player['image_id']) : ?>
                                        <?php echo wp_get_attachment_image($player['image_id'], 'medium', false, array('loading' => 'lazy')); ?>
                                    <?php else : ?>
                                        <b><?php echo esc_html(substr($player['name'], 0, 1)); ?></b>
                                    <?php endif; ?>
                                </span>
                                <span class="players-dir-card__name"><?php echo esc_html($player['name']); ?></span>
                                <span class="players-dir-card__team"><em><?php echo esc_html($player['flag']); ?></em><?php echo esc_html($player['country']); ?></span>
                                <span class="players-dir-card__bio"><?php echo esc_html($player['age']); ?> yrs <i>|</i> <?php echo esc_html($player['height']); ?></span>
                            </a>
                            <div class="players-dir-card__actions">
                                <a href="<?php echo esc_url($player['link']); ?>">Profile</a>
                                <a href="<?php echo esc_url(home_url('/players/' . get_post_field('post_name', $player['id']) . '/stats/')); ?>">Stats</a>
                                <a href="<?php echo esc_url(home_url('/players/' . get_post_field('post_name', $player['id']) . '/gear/')); ?>">Gear</a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
                <script id="players-directory-data" type="application/json"><?php echo wp_json_encode($players_client_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?></script>

                <button class="players-dir-load" id="players-load-more" type="button">Load More Players</button>
            </div>
        </div>
    </section>

    <section class="players-dir-recent">
        <div class="players-dir-shell players-dir-recent__inner">
            <div class="players-dir-recent__head">
                <h2>RECENTLY UPDATED</h2>
                <a href="#players-directory-grid">View All Updates</a>
            </div>
            <div class="players-dir-recent__list">
                <?php foreach ($players_recent as $recent) : ?>
                    <a class="players-dir-recent__item" href="<?php echo esc_url($recent['link']); ?>">
                        <span class="players-dir-recent__avatar">
                            <?php if ($recent['image_id']) : ?>
                                <?php echo wp_get_attachment_image($recent['image_id'], 'thumbnail', false, array('loading' => 'lazy')); ?>
                            <?php else : ?>
                                <b><?php echo esc_html(substr($recent['name'], 0, 1)); ?></b>
                            <?php endif; ?>
                        </span>
                        <span><strong><?php echo esc_html($recent['name']); ?></strong><em><?php echo esc_html($recent['country'] . ' · ' . $recent['pos_code']); ?></em></span>
                        <small><?php echo esc_html(human_time_diff($recent['modified'], current_time('timestamp'))); ?> ago</small>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var pageSize = 12;
    var visibleLimit = pageSize;
    var grid = document.getElementById('players-directory-grid');
    var dataEl = document.getElementById('players-directory-data');
    var players = [];
    var search = document.getElementById('players-search');
    var teamFilter = document.getElementById('players-team-filter');
    var positionFilter = document.getElementById('players-position-filter');
    var sortFilter = document.getElementById('players-sort-filter');
    var totalLabel = document.getElementById('players-total-label');
    var rangeLabel = document.getElementById('players-range-label');
    var loadMore = document.getElementById('players-load-more');
    var clear = document.getElementById('players-clear-filters');
    var positionTabs = Array.prototype.slice.call(document.querySelectorAll('.players-dir-position-tabs button'));
    var viewButtons = Array.prototype.slice.call(document.querySelectorAll('.players-dir-view button'));

    try {
        players = JSON.parse(dataEl ? dataEl.textContent : '[]');
    } catch (error) {
        players = [];
    }
    if (!grid || !players.length) return;

    function numberText(value) {
        return String(value).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }

    function appendText(parent, tag, className, text) {
        var element = document.createElement(tag);
        if (className) element.className = className;
        element.textContent = text || '';
        parent.appendChild(element);
        return element;
    }

    function playerCard(player) {
        var article = document.createElement('article');
        article.className = 'players-dir-card';
        article.dataset.name = String(player.name || '').toLowerCase();
        article.dataset.team = player.team || '';
        article.dataset.country = player.country || '';
        article.dataset.position = player.pos_code || '';
        article.dataset.group = player.group || '';
        article.dataset.age = player.age || '';

        var main = document.createElement('a');
        main.className = 'players-dir-card__main';
        main.href = player.link || '#';
        article.appendChild(main);

        appendText(main, 'span', 'players-dir-card__flag', player.flag || '🏳');
        appendText(main, 'span', 'players-dir-card__pos', player.pos_code || '');
        appendText(main, 'span', 'players-dir-card__num', player.number || '');

        var photo = document.createElement('span');
        photo.className = 'players-dir-card__photo';
        if (player.image_url) {
            var img = document.createElement('img');
            img.src = player.image_url;
            img.alt = (player.name || 'Player') + ' player profile photo';
            img.width = 320;
            img.height = 320;
            img.loading = 'lazy';
            img.decoding = 'async';
            photo.appendChild(img);
        } else {
            appendText(photo, 'b', '', String(player.name || 'P').slice(0, 1));
        }
        main.appendChild(photo);

        appendText(main, 'span', 'players-dir-card__name', player.name || '');
        var team = appendText(main, 'span', 'players-dir-card__team', '');
        appendText(team, 'em', '', player.flag || '🏳');
        team.appendChild(document.createTextNode(player.country || ''));
        var bio = appendText(main, 'span', 'players-dir-card__bio', '');
        bio.appendChild(document.createTextNode((player.age || '') + ' yrs '));
        appendText(bio, 'i', '', '|');
        bio.appendChild(document.createTextNode(' ' + (player.height || '')));

        var actions = document.createElement('div');
        actions.className = 'players-dir-card__actions';
        article.appendChild(actions);
        [
            ['Profile', player.link || '#'],
            ['Stats', '<?php echo esc_js(home_url('/players/')); ?>' + encodeURIComponent(player.slug || '') + '/stats/'],
            ['Gear', '<?php echo esc_js(home_url('/players/')); ?>' + encodeURIComponent(player.slug || '') + '/gear/']
        ].forEach(function (item) {
            var link = document.createElement('a');
            link.href = item[1];
            link.textContent = item[0];
            actions.appendChild(link);
        });

        return article;
    }

    function renderPlayers(items, shown) {
        var fragment = document.createDocumentFragment();
        items.slice(0, shown).forEach(function (player) {
            fragment.appendChild(playerCard(player));
        });
        grid.textContent = '';
        grid.appendChild(fragment);
    }

    function filters() {
        return {
            q: (search.value || '').trim().toLowerCase(),
            team: teamFilter.value,
            position: positionFilter.value
        };
    }

    function sortCards(items) {
        var sort = sortFilter.value;
        items.sort(function (a, b) {
            if (sort === 'team-asc') {
                return String(a.team || '').localeCompare(String(b.team || '')) || String(a.name || '').localeCompare(String(b.name || ''));
            }
            if (sort === 'position-asc') {
                return String(a.pos_code || '').localeCompare(String(b.pos_code || '')) || String(a.name || '').localeCompare(String(b.name || ''));
            }
            return String(a.name || '').localeCompare(String(b.name || ''));
        });
        return items;
    }

    function normalizeText(text) {
        return String(text || '')
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '');
    }

    function nameMatchesSearch(name, query) {
        if (!query) return true;
        var normalizedName = normalizeText(name);
        var normalizedQuery = normalizeText(query);
        var words = normalizedQuery.split(/\s+/).filter(function(w) { return w.length > 0; });
        for (var i = 0; i < words.length; i++) {
            if (normalizedName.indexOf(words[i]) === -1) {
                return false;
            }
        }
        return true;
    }

    function filteredCards() {
        var f = filters();
        return sortCards(players.filter(function (player) {
            var matchesSearch = nameMatchesSearch(player.name, f.q);
            var matchesTeam = f.team === 'all' || player.team === f.team;
            var matchesPosition = f.position === 'all' || player.pos_code === f.position;
            return matchesSearch && matchesTeam && matchesPosition;
        }));
    }

    function apply() {
        var visible = filteredCards();
        var total = visible.length;
        var shown = Math.min(visibleLimit, total);
        renderPlayers(visible, shown);
        totalLabel.textContent = numberText(total);
        rangeLabel.textContent = total ? '1-' + numberText(shown) : '0-0';
        loadMore.hidden = shown >= total;
    }

    function resetAndApply() {
        visibleLimit = pageSize;
        apply();
    }

    [search, teamFilter, positionFilter, sortFilter].forEach(function (control) {
        control.addEventListener('input', resetAndApply);
        control.addEventListener('change', resetAndApply);
    });

    positionTabs.forEach(function (button) {
        button.addEventListener('click', function () {
            positionFilter.value = button.dataset.pos;
            resetAndApply();
        });
    });

    viewButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            viewButtons.forEach(function (b) { b.classList.remove('is-active'); });
            button.classList.add('is-active');
            grid.classList.toggle('is-list', button.dataset.view === 'list');
        });
    });

    loadMore.addEventListener('click', function () {
        visibleLimit += pageSize;
        apply();
    });

    clear.addEventListener('click', function () {
        search.value = '';
        teamFilter.value = 'all';
        positionFilter.value = 'all';
        sortFilter.value = 'name-asc';
        resetAndApply();
    });

    loadMore.hidden = pageSize >= players.length;
});
</script>

<?php
get_footer();
