<?php
/**
 * ============================================
 * Twenty Twenty-Five Child Theme Functions
 * ============================================
 */

/* ============================================
   ✅ Global Version Control
   ============================================
   Bump this version after every CSS/JS change to force frontend cache refresh.
   Recommended format: YYYYMMDD-sequence, e.g. 20260427-1
*/
define('THEME_VERSION', '20260616-about-us-3');

require_once get_stylesheet_directory() . '/inc/homepage.php';
require_once get_stylesheet_directory() . '/inc/homepage-config.php';
require_once get_stylesheet_directory() . '/inc/dhgate-vertical-promo-config.php';
require_once get_stylesheet_directory() . '/inc/kses-inline-svg.php';
require_once get_stylesheet_directory() . '/inc/rest-lifestyle-entity-relations.php';
require_once get_stylesheet_directory() . '/inc/rest-player-erfolge-cards.php';

/**
 * FIFA World Cup 2026 tournament size (national teams). Used for hero stats copy;
 * CMS player rows may still reference clubs — see archive-players filter labels.
 *
 * @return int
 */
function footerball_wc_2026_national_team_count() {
    return 48;
}

/**
 * Whether the player's primary position is goalkeeper (for GC vs goals stats).
 *
 * @param string $position Raw position field from ACF.
 * @return bool
 */
function footerball_player_position_is_goalkeeper($position) {
    $p = strtolower(wp_strip_all_tags((string) $position));
    if ($p === '') {
        return false;
    }
    foreach (array('goalkeeper', 'keeper', 'gk', '门将', '守门员') as $needle) {
        if (strpos($p, $needle) !== false) {
            return true;
        }
    }
    return false;
}

/**
 * Whether to show goalkeeper «goals conceded» (GC) on single-player frontend tiles/badges.
 * Enabled after the goalkeeper career profile dataset was synced site-wide.
 *
 * @return bool
 */
function footerball_show_goalkeeper_conceded_frontend() {
    return (bool) apply_filters('footerball_show_goalkeeper_conceded_frontend', true);
}

/* ============================================
   ✅ Stylesheet Loading
   ============================================ */

function twenty_twenty_five_child_enqueue_styles() {
    // Parent theme stylesheet
    wp_enqueue_style(
        'parent-style',
        get_template_directory_uri() . '/style.css'
    );

    // Keep this shared dependency handle without requesting a missing main.css file.
    wp_register_style(
        'main-style',
        false,
        array('parent-style'),
        THEME_VERSION
    );
    wp_enqueue_style('main-style');
}
add_action('wp_enqueue_scripts', 'twenty_twenty_five_child_enqueue_styles');

/* ✅ Homepage styles */
function my_home_styles() {
    if (is_front_page()) {
        wp_enqueue_style(
            'home-style',
            get_stylesheet_directory_uri() . '/css/home-20260429-38.css',
            array('main-style'),
            THEME_VERSION
        );
    }
}
add_action('wp_enqueue_scripts', 'my_home_styles');

/* ✅ Footer styles */
function my_footer_styles() {
    wp_enqueue_style(
        'footer-style',
        get_stylesheet_directory_uri() . '/css/footer.css',
        array('main-style'),
        THEME_VERSION
    );
}
add_action('wp_enqueue_scripts', 'my_footer_styles');

/* ✅ Header styles */
function my_header_styles() {
    wp_enqueue_style(
        'header-style',
        get_stylesheet_directory_uri() . '/css/header.css',
        array('main-style'),
        THEME_VERSION
    );
}
add_action('wp_enqueue_scripts', 'my_header_styles');

/* ✅ AI Guide Shopping (chat bot) */
function enqueue_ai_guide_shopping() {
    wp_enqueue_style(
        'ai-guide-shopping',
        get_stylesheet_directory_uri() . '/css/ai_guide_shopping.css',
        array(),
        THEME_VERSION
    );

    wp_enqueue_script(
        'ai-guide-shopping',
        get_stylesheet_directory_uri() . '/js/ai-guide-shopping.js',
        array(),
        THEME_VERSION,
        true
    );
}
add_action('wp_enqueue_scripts', 'enqueue_ai_guide_shopping');

function footerball_enqueue_local_time_script() {
    wp_enqueue_script(
        'footerball-local-time',
        get_stylesheet_directory_uri() . '/js/footerball-local-time.js',
        array(),
        THEME_VERSION,
        array(
            'strategy'  => 'defer',
            'in_footer' => true,
        )
    );
}
add_action('wp_enqueue_scripts', 'footerball_enqueue_local_time_script');

function footerball_enqueue_whatsapp_community_popup() {
    if (is_admin()) {
        return;
    }

    wp_enqueue_style(
        'footerball-whatsapp-community-popup',
        get_stylesheet_directory_uri() . '/css/whatsapp-community-popup.css',
        array(),
        THEME_VERSION
    );

    wp_enqueue_script(
        'footerball-whatsapp-community-popup',
        get_stylesheet_directory_uri() . '/js/whatsapp-community-popup.js',
        array(),
        THEME_VERSION,
        true
    );

    $asset = static function ($path) {
        return add_query_arg('v', rawurlencode((string) THEME_VERSION), footerball_theme_asset_url($path));
    };

    wp_localize_script(
        'footerball-whatsapp-community-popup',
        'footerballWhatsappPopup',
        array(
            'delay'         => 3000,
            'link'          => 'https://cutt.ly/pt9gFhpD',
            'desktop'       => $asset('images/whatsapp-community/football-fan-community-desktop-960.webp'),
            'desktopSrcset' => $asset('images/whatsapp-community/football-fan-community-desktop-720.webp') . ' 720w, ' . $asset('images/whatsapp-community/football-fan-community-desktop-960.webp') . ' 960w',
            'mobile'        => $asset('images/whatsapp-community/football-fan-community-mobile-720.webp'),
            'mobileSrcset'  => $asset('images/whatsapp-community/football-fan-community-mobile-480.webp') . ' 480w, ' . $asset('images/whatsapp-community/football-fan-community-mobile-720.webp') . ' 720w',
        )
    );
}
add_action('wp_enqueue_scripts', 'footerball_enqueue_whatsapp_community_popup');

function footerball_admin_image_details_url_editor($hook) {
    if (!in_array($hook, array('post.php', 'post-new.php'), true)) {
        return;
    }

    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (!$screen || $screen->base !== 'post') {
        return;
    }

    wp_enqueue_media();
    wp_enqueue_script(
        'footerball-admin-image-details-url',
        get_stylesheet_directory_uri() . '/js/admin-image-details-url.js',
        array('jquery', 'media-views', 'underscore'),
        THEME_VERSION,
        true
    );
}
add_action('admin_enqueue_scripts', 'footerball_admin_image_details_url_editor');

	function footerball_theme_asset_url($relative_path) {
	    return get_stylesheet_directory_uri() . '/' . ltrim((string) $relative_path, '/');
	}

function footerball_is_about_us_request() {
    if (is_admin()) {
        return false;
    }

    $request_uri = isset($_SERVER['REQUEST_URI']) ? wp_unslash((string) $_SERVER['REQUEST_URI']) : '';
    $path = parse_url($request_uri, PHP_URL_PATH);
    if (!is_string($path)) {
        return false;
    }

    $home_path = parse_url(home_url('/'), PHP_URL_PATH);
    $home_path = is_string($home_path) ? rtrim($home_path, '/') : '';
    if ($home_path !== '' && $home_path !== '/' && strpos($path, $home_path) === 0) {
        $path = substr($path, strlen($home_path));
    }

    return '/' . trim($path, '/') === '/about-us';
}

function footerball_about_us_body_class($classes) {
    if (footerball_is_about_us_request()) {
        $classes[] = 'footerball-about-us-page';
    }

    return $classes;
}
add_filter('body_class', 'footerball_about_us_body_class');

function footerball_about_us_pre_handle_404($preempt, $wp_query) {
    if (!footerball_is_about_us_request()) {
        return $preempt;
    }

    if ($wp_query instanceof WP_Query) {
        $wp_query->is_404 = false;
        $wp_query->is_page = true;
        $wp_query->is_singular = true;
    }

    status_header(200);
    return true;
}
add_filter('pre_handle_404', 'footerball_about_us_pre_handle_404', 10, 2);

	/**
	 * Hostnames treated as this Football site (for internal link detection).
	 *
	 * @return string[]
	 */
	function footerball_site_hosts() {
		static $hosts = null;
		if ($hosts !== null) {
			return $hosts;
		}

		$hosts = array();
		foreach (array(home_url('/'), site_url('/')) as $base_url) {
			$parsed = wp_parse_url($base_url);
			if (!empty($parsed['host'])) {
				$hosts[] = strtolower((string) $parsed['host']);
			}
		}

		return $hosts = array_values(array_unique($hosts));
	}

	/**
	 * @param string $url
	 * @return bool
	 */
	function footerball_is_internal_site_url($url) {
		$url = trim(html_entity_decode((string) $url, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
		if ($url === '' || $url[0] === '#') {
			return false;
		}
		if (preg_match('/^(?:mailto:|tel:|javascript:)/i', $url)) {
			return false;
		}

		$parsed = wp_parse_url($url);
		if (empty($parsed['host'])) {
			return $url[0] === '/' || !preg_match('/^[a-z][a-z0-9+.-]*:/i', $url);
		}

		return in_array(strtolower((string) $parsed['host']), footerball_site_hosts(), true);
	}

	/**
	 * HTML attributes for same-site content links that should open in a new tab.
	 *
	 * @param string $url
	 * @return string Empty or ` target="_blank" rel="noopener noreferrer"`.
	 */
	function footerball_internal_link_attrs($url = '') {
		return footerball_is_internal_site_url($url) ? ' target="_blank" rel="noopener noreferrer"' : '';
	}

	/**
	 * Add target="_blank" to internal <a> tags inside rich HTML (article body, etc.).
	 *
	 * @param string $html
	 * @return string
	 */
	function footerball_add_internal_link_targets($html) {
		if (!is_string($html) || $html === '' || stripos($html, '<a') === false) {
			return $html;
		}

		return (string) preg_replace_callback(
			'/<a\b([^>]*)>/i',
			static function ($matches) {
				$attrs = $matches[1];
				if (preg_match('/\btarget\s*=/i', $attrs)) {
					return $matches[0];
				}
				if (!preg_match('/\bhref\s*=\s*(["\'])([^"\']*)\1/i', $attrs, $href_match)) {
					return $matches[0];
				}
				$href = html_entity_decode($href_match[2], ENT_QUOTES | ENT_HTML5, 'UTF-8');
				if (!footerball_is_internal_site_url($href)) {
					return $matches[0];
				}
				if (preg_match('/\brel\s*=\s*(["\'])([^"\']*)\1/i', $attrs)) {
					$attrs = (string) preg_replace_callback(
						'/\brel\s*=\s*(["\'])([^"\']*)\1/i',
						static function ($rel_match) {
							$parts = array_filter(preg_split('/\s+/', $rel_match[2]));
							foreach (array('noopener', 'noreferrer') as $token) {
								if (!in_array($token, $parts, true)) {
									$parts[] = $token;
								}
							}
							return 'rel="' . esc_attr(implode(' ', $parts)) . '"';
						},
						$attrs,
						1
					);
					return '<a' . $attrs . ' target="_blank">';
				}

				return '<a' . $attrs . ' target="_blank" rel="noopener noreferrer">';
			},
			$html
		);
	}

	/**
	 * Official DHgate marketplace home (sponsored promo targets).
	 */
	function footerball_dhgate_market_home_url() {
		return function_exists('footerball_dhgate_get_vertical_promo_url')
			? footerball_dhgate_get_vertical_promo_url()
			: 'https://www.dhgate.com/sales/market/football_tournament.html';
	}

	/**
	 * Versioned URL for the vertical Football 2026 / DHgate fan-gear promo image.
	 */
	function footerball_dhgate_vertical_promo_image_url() {
		if (!function_exists('footerball_dhgate_vertical_promo_image_sources')) {
			return add_query_arg('v', rawurlencode((string) THEME_VERSION), footerball_theme_asset_url('images/dhgate-football-2026-fan-gear-promo.png'));
		}

		$sources = footerball_dhgate_vertical_promo_image_sources();
		return $sources['png'];
	}

	/**
	 * Renders the vertical DHgate promo (SEO-friendly aside + descriptive alt/title).
	 *
	 * @param array<string, string> $args Optional: context (unique id slug), loading (lazy|eager), extra_class.
	 */
	function footerball_render_dhgate_vertical_promo($args = array()) {
		$args = wp_parse_args(
			$args,
			array(
				'context'     => 'promo',
				'loading'     => 'lazy',
				'extra_class' => '',
			)
		);
		$context = sanitize_title((string) $args['context']);
		if ($context === '') {
			$context = 'promo';
		}
		$loading = $args['loading'] === 'eager' ? 'eager' : 'lazy';
		$href    = footerball_dhgate_market_home_url();
		$sources = function_exists('footerball_dhgate_vertical_promo_image_sources')
			? footerball_dhgate_vertical_promo_image_sources()
			: array(
				'png'    => footerball_dhgate_vertical_promo_image_url(),
				'webp'   => '',
				'width'  => 354,
				'height' => 520,
			);
		$src     = $sources['png'];
		$webp    = !empty($sources['webp']) ? (string) $sources['webp'] : '';
		$width   = max(1, (int) ($sources['width'] ?? 354));
		$height  = max(1, (int) ($sources['height'] ?? 520));
		$alt     = 'Kick-Off sale: up to 90% off football fan gear on DHgate. Opens in a new tab.';
		$title   = 'Shop the football tournament sale on DHgate (new tab)';
		$id      = 'fb-dhgate-promo-' . $context;
		$extra   = trim((string) $args['extra_class']);

		echo '<aside id="' . esc_attr($id) . '" class="fb-dhgate-promo' . ($extra !== '' ? ' ' . esc_attr($extra) : '') . '" aria-label="' . esc_attr__('Sponsored: DHgate marketplace — World Cup 2026 fan gear', 'twentytwentyfive-child') . '">';
		echo '<a class="fb-dhgate-promo__link" href="' . esc_url($href) . '" target="_blank" rel="nofollow sponsored noopener noreferrer" title="' . esc_attr($title) . '">';
		echo '<span class="fb-dhgate-promo__frame">';
		if ($webp !== '') {
			echo '<picture>';
			echo '<source type="image/webp" srcset="' . esc_url($webp) . '">';
			echo '<img src="' . esc_url($src) . '" alt="' . esc_attr($alt) . '" width="' . esc_attr((string) $width) . '" height="' . esc_attr((string) $height) . '" loading="' . esc_attr($loading) . '" decoding="async">';
			echo '</picture>';
		} else {
			echo '<img src="' . esc_url($src) . '" alt="' . esc_attr($alt) . '" width="' . esc_attr((string) $width) . '" height="' . esc_attr((string) $height) . '" loading="' . esc_attr($loading) . '" decoding="async">';
		}
		echo '</span>';
		echo '<span class="fb-dhgate-promo__sr">' . esc_html__('Opens DHgate.com in a new browser tab.', 'twentytwentyfive-child') . '</span>';
		echo '</a>';
		echo '</aside>';
	}

	function footerball_team_logo_asset_url($team = '', $width = 0) {
	    $slug = '';
	    if (is_numeric($team)) {
	        $slug = (string) get_post_field('post_name', (int) $team);
	    } else {
	        $slug = (string) $team;
	    }
	    $slug = sanitize_title($slug);
	    if ($slug === '') {
	        return '';
	    }

	    $width = absint($width);
	    $suffix = $width > 0 ? '-' . $width : '';
	    $relative = 'images/team-logos/' . sanitize_file_name($slug . $suffix) . '.webp';
	    if (is_readable(get_stylesheet_directory() . '/' . $relative)) {
	        return add_query_arg('v', THEME_VERSION, footerball_theme_asset_url($relative));
	    }

	    if ($width > 0) {
	        $fallback = 'images/team-logos/' . sanitize_file_name($slug) . '.webp';
	        if (is_readable(get_stylesheet_directory() . '/' . $fallback)) {
	            return add_query_arg('v', THEME_VERSION, footerball_theme_asset_url($fallback));
	        }
	    }

	    return '';
	}
	
	function footerball_logo_image_url($format = 'svg') {
	    $logos = array(
	        'svg'  => footerball_theme_asset_url('images/football-2026-dhgate-logo.svg'),
	        'webp' => footerball_theme_asset_url('images/football-2026-dhgate-logo.webp'),
	        'png'  => footerball_theme_asset_url('images/football-2026-dhgate-logo.png'),
	    );
	
	    return isset($logos[$format]) ? $logos[$format] : $logos['svg'];
	}
	
	function footerball_site_icon_url($format = 'svg') {
	    $icons = array(
	        'svg'  => footerball_theme_asset_url('images/football-2026-dhgate-icon.svg'),
	        'webp' => footerball_theme_asset_url('images/football-2026-dhgate-icon.webp'),
	        'png'  => footerball_theme_asset_url('images/football-2026-dhgate-icon.png'),
	    );
	
	    return isset($icons[$format]) ? $icons[$format] : $icons['svg'];
	}
	
	function footerball_site_icons() {
		/* Fixed cache-bust string so favicon URL matches CDN / marketing tag (e.g. ?v=20260511-brand3). */
		$icon_cache = '20260511-brand3';
		$favicon_png = add_query_arg('v', rawurlencode($icon_cache), footerball_site_icon_url('png'));
		echo '<link rel="icon" href="' . esc_url($favicon_png) . '" type="image/png">' . "\n";
		echo '<link rel="apple-touch-icon" href="' . esc_url($favicon_png) . '" sizes="512x512">' . "\n";
		echo '<link rel="shortcut icon" href="' . esc_url($favicon_png) . '" type="image/png">' . "\n";
	}
	add_action( 'wp_head', 'footerball_site_icons', 1 );
	remove_action( 'wp_head', 'wp_site_icon', 99 );

	/**
	 * Mobile browser chrome + install bookmark labels (keeps favicon/logo story aligned with DHgate Football).
	 */
	function footerball_head_branding_meta() {
		echo '<meta name="theme-color" content="#07111f">' . "\n";
		echo '<meta name="apple-mobile-web-app-title" content="DHgate Football">' . "\n";
		echo '<meta name="application-name" content="DHgate Football">' . "\n";
	}
	add_action( 'wp_head', 'footerball_head_branding_meta', 2 );

	/**
	 * WordPress “Site title” is often left as the short internal name (e.g. Football), which leaks into
	 * RSS discovery links and other front-end meta. Override only on the public site (not wp-admin).
	 */
	function footerball_filter_public_blogname( $value ) {
		if ( is_admin() || wp_installing() ) {
			return $value;
		}
		$v = (string) $value;
		if ( '' === $v || 0 === strcasecmp( $v, 'Football' ) || 0 === strcasecmp( $v, 'Footerball' ) ) {
			return 'DHgate Football';
		}

		return $value;
	}
	add_filter( 'option_blogname', 'footerball_filter_public_blogname', 10, 1 );

	function footerball_image_id_from_value($image) {
	    if (is_numeric($image)) {
	        return absint($image);
	    }
	
	    if (is_array($image)) {
	        foreach (array('ID', 'id') as $key) {
	            if (!empty($image[$key]) && is_numeric($image[$key])) {
	                return absint($image[$key]);
	            }
	        }
	        if (!empty($image['url'])) {
	            return absint(attachment_url_to_postid((string) $image['url']));
	        }
	    }
	
	    if (is_string($image) && preg_match('#^https?://#i', $image)) {
	        return absint(attachment_url_to_postid($image));
	    }
	
	    return 0;
	}
	
	function footerball_image_url_from_value($image, $size = 'full') {
	    $image_id = footerball_image_id_from_value($image);
	    if ($image_id) {
	        $url = wp_get_attachment_image_url($image_id, $size);
	        if ($url) {
	            return $url;
	        }
	    }
	
	    if (is_array($image) && !empty($image['url'])) {
	        return (string) $image['url'];
	    }
	
	    return is_string($image) ? $image : '';
	}
	
	function footerball_theme_image_srcset($url, $widths = array()) {
	    $url = (string) $url;
	    $theme_uri = get_stylesheet_directory_uri();
	    if ($url === '' || strpos($url, $theme_uri . '/') !== 0) {
	        return '';
	    }
	
	    $relative = ltrim(substr($url, strlen($theme_uri)), '/');
	    $path = get_stylesheet_directory() . '/' . $relative;
	    if (!is_readable($path)) {
	        return '';
	    }
	
	    $info = pathinfo($relative);
	    if (empty($info['dirname']) || empty($info['filename']) || empty($info['extension'])) {
	        return '';
	    }
	
	    $widths = array_filter(array_map('absint', (array) $widths));
	    if (empty($widths)) {
	        $widths = array(320, 480, 640, 768, 960, 1280, 1536, 1920);
	    }
	
	    $sources = array();
	    $dir = $info['dirname'] === '.' ? '' : $info['dirname'] . '/';
	    foreach ($widths as $width) {
	        $variant = $dir . $info['filename'] . '-' . $width . '.' . $info['extension'];
	        if (is_readable(get_stylesheet_directory() . '/' . $variant)) {
	            $sources[$width] = footerball_theme_asset_url($variant) . ' ' . $width . 'w';
	        }
	    }
	
	    $image_size = function_exists('wp_getimagesize') ? wp_getimagesize($path) : @getimagesize($path);
	    if (!empty($image_size[0])) {
	        $sources[(int) $image_size[0]] = $url . ' ' . (int) $image_size[0] . 'w';
	    }
	
	    if (empty($sources)) {
	        return '';
	    }
	
	    ksort($sources, SORT_NUMERIC);
	    return implode(', ', array_values($sources));
	}

	function footerball_theme_image_variant_url($url, $width) {
	    $url = (string) $url;
	    $width = absint($width);
	    $theme_uri = get_stylesheet_directory_uri();
	    if ($url === '' || $width <= 0 || strpos($url, $theme_uri . '/') !== 0) {
	        return $url;
	    }

	    $relative = ltrim(substr($url, strlen($theme_uri)), '/');
	    $info = pathinfo($relative);
	    if (empty($info['dirname']) || empty($info['filename']) || empty($info['extension'])) {
	        return $url;
	    }

	    $dir = $info['dirname'] === '.' ? '' : $info['dirname'] . '/';
	    $variant = $dir . $info['filename'] . '-' . $width . '.' . $info['extension'];
	    if (is_readable(get_stylesheet_directory() . '/' . $variant)) {
	        return $theme_uri . '/' . $variant;
	    }

	    return $url;
	}
	
	function footerball_responsive_image_html($image, $alt, $args = array()) {
	    $args = wp_parse_args($args, array(
	        'size'           => 'medium_large',
	        'class'          => '',
	        'width'          => '',
	        'height'         => '',
	        'loading'        => 'lazy',
	        'decoding'       => 'async',
	        'fetchpriority'  => '',
	        'sizes'          => '',
	        'srcset_widths'  => array(),
	    ));
	
	    $alt = footerball_clean_meta_text($alt);
	    $image_id = footerball_image_id_from_value($image);
	    $attrs = array_filter(array(
	        'alt'           => $alt,
	        'class'         => $args['class'],
	        'loading'       => $args['loading'],
	        'decoding'      => $args['decoding'],
	        'fetchpriority' => $args['fetchpriority'],
	        'sizes'         => $args['sizes'],
	    ), static function ($value) {
	        return $value !== '' && $value !== null;
	    });
	
	    if ($args['width'] !== '') {
	        $attrs['width'] = (string) absint($args['width']);
	    }
	    if ($args['height'] !== '') {
	        $attrs['height'] = (string) absint($args['height']);
	    }
	
	    if ($image_id) {
	        return wp_get_attachment_image($image_id, $args['size'], false, $attrs);
	    }
	
	    $url = footerball_image_url_from_value($image, $args['size']);
	    if ($url === '') {
	        return '';
	    }
	
	    $srcset = footerball_theme_image_srcset($url, $args['srcset_widths']);
	    $html_attrs = array_merge(array('src' => esc_url($url), 'alt' => esc_attr($alt)), $attrs);
	    if ($srcset !== '') {
	        $html_attrs['srcset'] = esc_attr($srcset);
	    }
	
	    $parts = array();
	    foreach ($html_attrs as $name => $value) {
	        if ($value === '' || $value === null) {
	            continue;
	        }
	        $parts[] = esc_attr($name) . '="' . esc_attr($value) . '"';
	    }
	
	    return '<img ' . implode(' ', $parts) . '>';
	}
	
	function footerball_match_team_label($team) {
    if (!is_array($team)) {
        return $team ? (string) $team : 'TBD';
    }
    return !empty($team['title']) ? (string) $team['title'] : (!empty($team['abbr']) ? (string) $team['abbr'] : 'TBD');
}

function footerball_match_team_flag($team) {
    return is_array($team) && !empty($team['flag']) ? (string) $team['flag'] : '🏳️';
}

function footerball_match_source_timezone() {
    return 'Asia/Shanghai';
}

function footerball_parse_match_datetime($datetime) {
    $datetime = trim((string) $datetime);
    if ($datetime === '') {
        return 0;
    }

    try {
        $timezone = new DateTimeZone(footerball_match_source_timezone());
    } catch (Exception $error) {
        $timezone = new DateTimeZone('UTC');
    }

    foreach (array('Y-m-d H:i:s', 'Y-m-d H:i') as $format) {
        $date = DateTimeImmutable::createFromFormat('!' . $format, $datetime, $timezone);
        if ($date instanceof DateTimeImmutable) {
            return $date->getTimestamp();
        }
    }

    $timestamp = strtotime($datetime);
    return $timestamp ?: 0;
}

function footerball_match_timestamp($match) {
    if (!empty($match['ts'])) {
        return (int) $match['ts'];
    }
    if (!empty($match['datetime'])) {
        return footerball_parse_match_datetime((string) $match['datetime']);
    }
    return 0;
}

function footerball_local_datetime_iso($timestamp) {
    $timestamp = (int) $timestamp;
    if ($timestamp <= 0) {
        return '';
    }

    return function_exists('wp_date') ? wp_date('c', $timestamp) : date('c', $timestamp);
}

function footerball_local_datetime_iso_from_text($date, $time = '') {
    $date = trim((string) $date);
    $time = trim((string) $time);
    if ($date === '') {
        return '';
    }

    $time = preg_replace('/^[A-Za-z]{3,},?\s+/', '', $time);
    $timestamp = footerball_parse_match_datetime(trim($date . ' ' . $time));
    return $timestamp ? footerball_local_datetime_iso($timestamp) : '';
}

function footerball_match_short_team_label($team) {
    $label = footerball_match_team_label($team);
    $label = html_entity_decode($label, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $label = preg_replace('/\s+(?:Men.?s|Women.?s)?\s*National\s+(?:Football|Soccer)\s+Team.*$/iu', '', $label);
    $label = preg_replace('/\s+(?:Football|Soccer)\s+Team$/iu', '', $label);
    $label = preg_replace('/\s+(?:Men.?s|Women.?s)$/iu', '', $label);
    $label = trim((string) $label);
    return $label !== '' ? $label : 'TBD';
}

/**
 * Stadium venue label normalized for loose matching (schedule CSV vs ACF).
 *
 * @param string $label
 * @return string
 */
function footerball_normalize_venue_key($label) {
    $label = strtolower(html_entity_decode((string) $label, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    $label = str_replace(array('’', '‘', '`'), "'", $label);
    return preg_replace('/[^a-z0-9]+/', '', $label);
}

/**
 * Normalize host city post slug for built-in maps (opened year, schedule venue phrases).
 * Handles duplicate WP posts (e.g. mexico-city-2) and URL slug new-york vs new-york-new-jersey.
 *
 * @param string $slug post_name from host_cities CPT.
 * @return string
 */
function footerball_host_city_slug_for_builtin_maps($slug) {
    $slug = sanitize_title((string) $slug);
    if ($slug === '') {
        return '';
    }
    if ($slug === 'new-york') {
        return 'new-york-new-jersey';
    }
    if (preg_match('/^(.+)-(\d+)$/', $slug, $m)) {
        return $m[1];
    }

    return $slug;
}

/**
 * Default stadium opened-year by host city slug (major renovation / venue opening).
 *
 * @param string $slug
 * @return string Four-digit year or empty.
 */
function footerball_host_city_default_opened_year($slug) {
    $slug = footerball_host_city_slug_for_builtin_maps($slug);
    $map = array(
        'toronto' => '2007',
        'vancouver' => '1983',
        'guadalajara' => '2010',
        'mexico-city' => '1966',
        'monterrey' => '2015',
        'atlanta' => '2017',
        'boston' => '2002',
        'dallas' => '2009',
        'houston' => '2002',
        'kansas-city' => '1972',
        'los-angeles' => '2020',
        'miami' => '1987',
        'new-york-new-jersey' => '2010',
        'philadelphia' => '2003',
        'seattle' => '2002',
        'san-francisco-bay-area' => '2014',
    );

    return isset($map[$slug]) ? $map[$slug] : '';
}

/**
 * Stadium names used in master schedule for each host city slug (for filtering rows).
 *
 * @return array<string, array<int, string>>
 */
function footerball_host_city_slug_schedule_stadium_phrases() {
    return array(
        'toronto' => array('bmo field'),
        'vancouver' => array('bc place'),
        'guadalajara' => array('estadio akron'),
        'mexico-city' => array('estadio azteca'),
        'monterrey' => array('estadio bbva'),
        'atlanta' => array('mercedes-benz stadium'),
        'boston' => array('gillette stadium'),
        'dallas' => array('at&t stadium'),
        'houston' => array('nrg stadium'),
        'kansas-city' => array('arrowhead stadium'),
        'los-angeles' => array('sofi stadium'),
        'miami' => array('hard rock stadium'),
        'new-york-new-jersey' => array('metlife stadium'),
        'philadelphia' => array('lincoln financial field'),
        'seattle' => array('lumen field'),
        'san-francisco-bay-area' => array('levi\'s stadium', 'levis stadium'),
    );
}

/**
 * Whether a normalized schedule row belongs to this host city venue.
 *
 * @param array<string, mixed> $row
 * @param string               $city_slug
 * @param string               $acf_stadium_name
 * @return bool
 */
function footerball_host_city_schedule_row_matches_venue($row, $city_slug, $acf_stadium_name) {
    $venue = isset($row['stadium']) ? trim((string) $row['stadium']) : '';
    $vkey = footerball_normalize_venue_key($venue);
    if ($vkey === '') {
        return false;
    }

    $needles = array();
    $acf_key = footerball_normalize_venue_key($acf_stadium_name);
    if ($acf_key !== '') {
        $needles[] = $acf_key;
    }

    $slug = footerball_host_city_slug_for_builtin_maps((string) $city_slug);
    $map = footerball_host_city_slug_schedule_stadium_phrases();
    if (isset($map[$slug])) {
        foreach ($map[$slug] as $phrase) {
            $pk = footerball_normalize_venue_key($phrase);
            if ($pk !== '') {
                $needles[] = $pk;
            }
        }
    }

    $needles = array_values(array_unique(array_filter($needles)));
    foreach ($needles as $needle) {
        if ($needle === '') {
            continue;
        }
        if ($vkey === $needle) {
            return true;
        }
        $nlen = strlen($needle);
        $vlen = strlen($vkey);
        if ($nlen >= 8 && $vlen >= $nlen && strpos($vkey, $needle) !== false) {
            return true;
        }
        if ($vlen >= 8 && $nlen >= $vlen && strpos($needle, $vkey) !== false) {
            return true;
        }
    }

    return false;
}

/**
 * Build host-city fixture rows from the same Matches schedule used on /matches (CPT or defaults).
 *
 * @param string $city_slug
 * @param string $acf_stadium_name
 * @return array<int, array<string, mixed>>
 */
function footerball_host_city_fixtures_from_matches_schedule($city_slug, $acf_stadium_name) {
    if (!function_exists('footerball_matches_get_schedule_rows')) {
        return array();
    }

    $rows = footerball_matches_get_schedule_rows();
    if (empty($rows)) {
        return array();
    }

    $out = array();
    foreach ($rows as $row) {
        if (!footerball_host_city_schedule_row_matches_venue($row, $city_slug, $acf_stadium_name)) {
            continue;
        }

        $ts = isset($row['ts']) ? (int) $row['ts'] : 0;
        if ($ts <= 0 && function_exists('footerball_match_timestamp')) {
            $ts = footerball_match_timestamp($row);
        }
        if ($ts <= 0) {
            continue;
        }

        $iso = footerball_local_datetime_iso($ts);
        if ($iso === '') {
            continue;
        }

        $home = isset($row['home']) && is_array($row['home']) ? $row['home'] : array();
        $away = isset($row['away']) && is_array($row['away']) ? $row['away'] : array();

        $status_raw = isset($row['status']) ? strtolower((string) $row['status']) : 'scheduled';
        $status_label = 'Upcoming';
        if (strpos($status_raw, 'finish') !== false || strpos($status_raw, 'complete') !== false) {
            $status_label = 'Finished';
        }

        $date_short = strtoupper(function_exists('wp_date') ? wp_date('M j', $ts) : date_i18n('M j', $ts));
        $date_short .= ' ' . strtoupper(function_exists('wp_date') ? wp_date('D', $ts) : date_i18n('D', $ts));

        $out[] = array(
            'datetime_iso' => $iso,
            'kickoff_ts' => $ts,
            'datetime' => (function_exists('wp_date') ? wp_date('D, M j, Y', $ts) : date_i18n('D, M j, Y', $ts)) .
                ' · ' . (function_exists('wp_date') ? wp_date('H:i', $ts) : date_i18n('H:i', $ts)),
            'date_short' => $date_short,
            'stage' => isset($row['stage']) ? (string) $row['stage'] : '',
            'home_team' => footerball_match_short_team_label($home),
            'home_flag' => footerball_match_team_flag($home),
            'away_team' => footerball_match_short_team_label($away),
            'away_flag' => footerball_match_team_flag($away),
            'status' => $status_label,
            'availability' => $status_label,
        );
    }

    usort(
        $out,
        static function ($a, $b) {
            return ((int) ($a['kickoff_ts'] ?? 0)) <=> ((int) ($b['kickoff_ts'] ?? 0));
        }
    );

    return $out;
}

function footerball_render_upcoming_matches_module($matches = array(), $args = array()) {
    if (empty($matches) && function_exists('footerball_matches_get_schedule_rows')) {
        $matches = footerball_matches_get_schedule_rows();
    }
    if (!is_array($matches)) {
        $matches = array();
    }

    $defaults = array(
        'title' => 'UPCOMING MATCHES',
        'limit' => 3,
        'url' => home_url('/matches/'),
        'class' => '',
        'empty' => 'No upcoming matches scheduled.',
    );
    $args = wp_parse_args($args, $defaults);
    $matches = array_slice(array_values($matches), 0, max(1, (int) $args['limit']));
    $use_short_team_labels = strpos((string) $args['class'], 'fb-upcoming-matches--panel') !== false
        || strpos((string) $args['class'], 'fb-upcoming-matches--sidebar-compact') !== false;

    ob_start();
    ?>
    <section class="fb-upcoming-matches <?php echo esc_attr($args['class']); ?>">
        <div class="fb-upcoming-matches__head">
            <span class="fb-upcoming-matches__trophy" aria-hidden="true">🏆</span>
            <h2><?php echo esc_html($args['title']); ?></h2>
            <a href="<?php echo esc_url($args['url']); ?>"<?php echo footerball_internal_link_attrs($args['url']); ?>>View all <span aria-hidden="true">›</span></a>
        </div>
        <?php if (!empty($matches)) : ?>
            <div class="fb-upcoming-matches__list">
                <?php foreach ($matches as $match) : ?>
                    <?php
                    $ts = footerball_match_timestamp($match);
                    $date_label = $ts ? date_i18n('M j, H:i', $ts) : (!empty($match['datetime']) ? (string) $match['datetime'] : 'Date TBD');
                    $year_label = $ts ? date_i18n('Y', $ts) : '2026';
                    $home = !empty($match['home']) && is_array($match['home']) ? $match['home'] : array('title' => $match['home_team'] ?? 'TBD', 'flag' => $match['home_flag'] ?? '');
                    $away = !empty($match['away']) && is_array($match['away']) ? $match['away'] : array('title' => $match['away_team'] ?? 'TBD', 'flag' => $match['away_flag'] ?? '');
                    $home_label = footerball_match_team_label($home);
                    $away_label = footerball_match_team_label($away);
                    $home_display_label = $use_short_team_labels ? footerball_match_short_team_label($home) : $home_label;
                    $away_display_label = $use_short_team_labels ? footerball_match_short_team_label($away) : $away_label;
                    $stage = !empty($match['group']) ? (string) $match['group'] : (!empty($match['stage']) ? (string) $match['stage'] : 'Group Stage');
                    $venue = !empty($match['stadium']) ? (string) $match['stadium'] : 'Venue TBD';
                    ?>
                    <a class="fb-upcoming-match" href="<?php echo esc_url($args['url']); ?>"<?php echo footerball_internal_link_attrs($args['url']); ?>>
                        <div class="fb-upcoming-match__top">
                            <span class="fb-upcoming-match__date"><i aria-hidden="true">▣</i><?php if ($ts) : ?><time datetime="<?php echo esc_attr(footerball_local_datetime_iso($ts)); ?>" data-footerball-local-time data-footerball-time-format="date-time"><?php echo esc_html($date_label); ?></time><?php else : ?><?php echo esc_html($date_label); ?><?php endif; ?></span>
                            <span class="fb-upcoming-match__year"><i aria-hidden="true">🏆</i><?php if ($ts) : ?><time datetime="<?php echo esc_attr(footerball_local_datetime_iso($ts)); ?>" data-footerball-local-time data-footerball-time-format="year"><?php echo esc_html($year_label); ?></time><?php else : ?><?php echo esc_html($year_label); ?><?php endif; ?></span>
                        </div>
                        <div class="fb-upcoming-match__teams">
                            <span class="fb-upcoming-match__team">
                                <em aria-hidden="true"><?php echo esc_html(footerball_match_team_flag($home)); ?></em>
                                <strong title="<?php echo esc_attr($home_label); ?>"><?php echo esc_html($home_display_label); ?></strong>
                            </span>
                            <b>VS</b>
                            <span class="fb-upcoming-match__team fb-upcoming-match__team--away">
                                <strong title="<?php echo esc_attr($away_label); ?>"><?php echo esc_html($away_display_label); ?></strong>
                                <em aria-hidden="true"><?php echo esc_html(footerball_match_team_flag($away)); ?></em>
                            </span>
                        </div>
                        <div class="fb-upcoming-match__meta">
                            <span><i aria-hidden="true">◎</i><?php echo esc_html($venue); ?></span>
                            <span><i aria-hidden="true">♙</i><?php echo esc_html($stage); ?></span>
                            <span class="fb-upcoming-match__arrow" aria-hidden="true">›</span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <p class="fb-upcoming-matches__empty"><?php echo esc_html($args['empty']); ?></p>
        <?php endif; ?>
    </section>
    <?php
    return ob_get_clean();
}

/* ✅ Set DHgate tracking Cookies (ref_f + vid) */
function footerball_set_tracking_cookies() {
    $default_ref_f = 'seo|smart-shopping|football|organic||http|';
    $legacy_ref_f = 'seo|smart-shopping||organic||http|';
    $current_ref_f = isset($_COOKIE['ref_f']) ? rawurldecode((string) $_COOKIE['ref_f']) : '';

    if (!isset($_COOKIE['ref_f']) || $current_ref_f === $legacy_ref_f) {
        setcookie('ref_f', $default_ref_f, array(
            'expires'  => time() + 30 * 86400,
            'path'     => '/',
            'secure'   => is_ssl(),
            'samesite' => 'Lax',
        ));
        $_COOKIE['ref_f'] = $default_ref_f;
    }
    if (!isset($_COOKIE['vid'])) {
        $vid = bin2hex(random_bytes(16));
        setcookie('vid', $vid, array(
            'expires'  => time() + 86400,
            'path'     => '/',
            'secure'   => is_ssl(),
            'samesite' => 'Lax',
        ));
    }
}
add_action('init', 'footerball_set_tracking_cookies', 1);

/* ============================================
   Global SEO Meta
   Values follow the provided World Cup SEO spreadsheet.
   ============================================ */

function footerball_clean_meta_text($value) {
    $value = wp_strip_all_tags((string) $value);
    $value = preg_replace('/\s+/', ' ', $value);
    return trim($value);
}

function footerball_team_short_name($name) {
    $name = footerball_clean_meta_text($name);
    $short = preg_replace('/\s+National\s+Football\s+Team.*$/i', '', $name);
    $short = preg_replace('/\s+Football\s+Team$/i', '', $short);
    $short = footerball_clean_meta_text($short);
    return $short !== '' ? $short : $name;
}

function footerball_get_team_display_name($post_id) {
    $post_id = (int) $post_id;
    $acf_name = function_exists('get_field') ? get_field('name', $post_id) : '';
    if (is_string($acf_name) && trim($acf_name) !== '') {
        return footerball_clean_meta_text($acf_name);
    }

    $meta_name = get_post_meta($post_id, 'name', true);
    if (is_string($meta_name) && trim($meta_name) !== '') {
        return footerball_clean_meta_text($meta_name);
    }

    return footerball_clean_meta_text(get_the_title($post_id));
}

function footerball_clean_player_display_name($name) {
    $name = footerball_clean_meta_text($name);
    /* Wikipedia / import noise (red-link artifact pasted into titles). */
    $name = preg_replace('/\s*\(\s*page\s+does\s+not\s+exist\s*\)/iu', '', $name);
    $name = preg_replace('/\s*\([^)]*\b(?:footballer|soccer player|soccer)\b[^)]*\)\s*/i', ' ', $name);
    $name = preg_replace('/\s+\b(?:footballer|soccer player)\b\s*$/i', '', $name);
    return footerball_clean_meta_text($name);
}

/**
 * Whether a stored country string is a placeholder (should not be shown as nationality).
 *
 * @param string $label
 * @return bool
 */
function footerball_is_placeholder_country_label($label) {
    $t = strtolower(trim(footerball_clean_meta_text((string) $label)));
    if ($t === '') {
        return true;
    }
    static $bad = null;
    if ($bad === null) {
        $bad = array(
            'unknown', 'unknow', 'n/a', 'na', 'tbd', 'tbc', '-', '--', '—', '?', 'none',
            'not specified', 'not available', 'pending', 'unspecified', 'null', 'undefined',
        );
    }
    return in_array($t, $bad, true);
}

/**
 * Derive a display country name from a linked national team (teams CPT).
 *
 * @param int $team_id
 * @return string Empty when not derivable.
 */
function footerball_country_label_from_team_post($team_id) {
    $team_id = (int) $team_id;
    if ($team_id <= 0) {
        return '';
    }

    $country = '';
    if (function_exists('get_field')) {
        $c = get_field('country', $team_id);
        if (is_string($c) && trim($c) !== '') {
            $country = footerball_clean_meta_text($c);
        }
    }
    if ($country === '') {
        $meta_c = get_post_meta($team_id, 'country', true);
        if (is_string($meta_c) && trim($meta_c) !== '') {
            $country = footerball_clean_meta_text($meta_c);
        }
    }
    if ($country !== '' && !footerball_is_placeholder_country_label($country)) {
        return $country;
    }

    $team_display = function_exists('footerball_get_team_display_name')
        ? footerball_get_team_display_name($team_id)
        : get_the_title($team_id);
    $short = function_exists('footerball_team_short_name')
        ? footerball_team_short_name($team_display)
        : footerball_clean_meta_text($team_display);
    $short = trim(footerball_clean_meta_text((string) $short));
    if ($short !== '' && !footerball_is_placeholder_country_label($short)) {
        return $short;
    }

    return '';
}

/**
 * Resolved nationality label for cards and hero (handles "Unknown" + fills from player_team).
 *
 * @param int    $player_id
 * @param string $raw_country From player `country` field.
 * @return string Empty when nothing credible to show.
 */
function footerball_resolve_player_country_for_display($player_id, $raw_country) {
    $player_id = (int) $player_id;
    $raw = trim(footerball_clean_meta_text((string) $raw_country));
    if ($raw !== '' && !footerball_is_placeholder_country_label($raw)) {
        return $raw;
    }

    $team_id = 0;
    if (function_exists('get_field')) {
        $team_id = absint(get_field('player_team', $player_id));
    }
    if (!$team_id) {
        $team_id = absint(get_post_meta($player_id, 'player_team', true));
    }
    if ($team_id) {
        $from_team = footerball_country_label_from_team_post($team_id);
        if ($from_team !== '') {
            return $from_team;
        }
    }

    return '';
}

function footerball_get_player_display_name($post_id) {
    $post_id = (int) $post_id;
    $acf_name = function_exists('get_field') ? get_field('name', $post_id) : '';
    if (is_string($acf_name) && trim($acf_name) !== '') {
        return footerball_clean_player_display_name($acf_name);
    }

    $meta_name = get_post_meta($post_id, 'name', true);
    if (is_string($meta_name) && trim($meta_name) !== '') {
        return footerball_clean_player_display_name($meta_name);
    }

    return footerball_clean_player_display_name(get_the_title($post_id));
}

/**
 * Normalize ACF / meta birthday value to a parseable string (same rules as single-players.php).
 *
 * @param mixed $birthday_acf Raw `birthday` field.
 * @return string Empty when unknown.
 */
function footerball_normalize_player_birthday_raw($birthday_acf) {
    if (is_array($birthday_acf)) {
        if (!empty($birthday_acf['date'])) {
            $birthday_acf = $birthday_acf['date'];
        } elseif (isset($birthday_acf['Y'], $birthday_acf['m'], $birthday_acf['d'])) {
            $birthday_acf = sprintf('%04d-%02d-%02d', (int) $birthday_acf['Y'], (int) $birthday_acf['m'], (int) $birthday_acf['d']);
        } else {
            $birthday_acf = '';
        }
    }
    if ($birthday_acf instanceof DateTimeInterface) {
        return $birthday_acf->format('Y-m-d');
    }
    if ($birthday_acf !== null && $birthday_acf !== '' && $birthday_acf !== false && is_scalar($birthday_acf)) {
        return trim((string) $birthday_acf);
    }

    return '';
}

/**
 * Age in full years from a birthday string, or empty if unknown / invalid (same rules as single-players.php).
 */
function footerball_player_age_years_from_birthday_string($birthday_for_age) {
    $birthday_for_age = trim((string) $birthday_for_age);
    if ($birthday_for_age === '' || strcasecmp($birthday_for_age, 'TBD') === 0) {
        return '';
    }

    $birth_date = DateTime::createFromFormat('Y-m-d', $birthday_for_age);
    if (!($birth_date instanceof DateTime) && ctype_digit($birthday_for_age) && strlen($birthday_for_age) === 8) {
        $birth_date = DateTime::createFromFormat('Ymd', $birthday_for_age);
    }
    if (!($birth_date instanceof DateTime)) {
        $parsed = date_create($birthday_for_age);
        if ($parsed instanceof DateTime) {
            $birth_date = $parsed;
        }
    }

    if ($birth_date instanceof DateTime) {
        $today = new DateTime('today');
        $years = (int) $today->diff($birth_date)->y;
        if ($years >= 0 && $years < 120) {
            return (string) $years;
        }
    }

    return '';
}

function footerball_lifestyle_title_needs_fallback($title, $fallback_title = '') {
    $title = footerball_clean_meta_text($title);
    $fallback_title = footerball_clean_meta_text($fallback_title);
    if ($title === '') {
        return true;
    }
    if ($fallback_title !== '' && preg_match('/[\x{4e00}-\x{9fff}]/u', $title)) {
        return true;
    }
    return $fallback_title !== '' && strlen($title) < 18 && strcasecmp($title, $fallback_title) !== 0;
}

function footerball_get_lifestyle_display_title($post_id, $article = null) {
    $post_id = (int) $post_id;
    if (!is_array($article)) {
        $article = footerball_get_lifestyle_article_data($post_id);
    }
    $post_title = get_the_title($post_id);
    $article_title = !empty($article['post_title']) ? $article['post_title'] : '';
    return footerball_lifestyle_title_needs_fallback($article_title, $post_title) ? $post_title : footerball_clean_meta_text($article_title);
}

function footerball_default_og_image_url() {
    return footerball_logo_image_url('webp');
}

function footerball_lifestyle_generic_fallback_image_url() {
    return add_query_arg('v', rawurlencode((string) THEME_VERSION), footerball_theme_asset_url('images/home-hero-trophy.webp'));
}

function footerball_current_url_without_query() {
    $path = isset($_SERVER['REQUEST_URI']) ? (string) strtok($_SERVER['REQUEST_URI'], '?') : '/';
    $path = '/' . ltrim($path, '/');
    return home_url(user_trailingslashit(ltrim($path, '/')));
}

function footerball_archive_url($post_type, $fallback_path = '/') {
    $url = get_post_type_archive_link($post_type);
    return $url ?: home_url($fallback_path);
}

function footerball_get_attachment_or_thumbnail_url($post_id, $field_name = '') {
    $post_id = (int) $post_id;
    if ($post_id <= 0) {
        return '';
    }

    if ($field_name && function_exists('get_field')) {
        $image_id = get_field($field_name, $post_id);
        if ($image_id) {
            $image_url = wp_get_attachment_image_url((int) $image_id, 'full');
            if ($image_url) {
                return $image_url;
            }
        }
    }

    if (has_post_thumbnail($post_id)) {
        $image_url = get_the_post_thumbnail_url($post_id, 'full');
        if ($image_url) {
            return $image_url;
        }
    }

    return '';
}

function footerball_get_lifestyle_article_data($post_id) {
    $article = function_exists('get_field') ? get_field('article', $post_id) : array();
    return is_array($article) ? $article : array();
}

/**
 * Bundled 1200×630 WebP hero for lifestyle posts, keyed by post slug (see images/lifestyle-heroes/).
 * Used when WordPress/ACF featured image is not set.
 *
 * @param int $post_id Post ID.
 * @return string Absolute URL or empty string.
 */
function footerball_get_lifestyle_editorial_hero_url($post_id) {
    $post_id = (int) $post_id;
    if ($post_id <= 0) {
        return '';
    }
    $slug = (string) get_post_field('post_name', $post_id);
    if ($slug === '') {
        return '';
    }
    $safe = sanitize_file_name($slug);
    $rel = 'images/lifestyle-heroes/' . $safe . '.webp';
    $path = get_stylesheet_directory() . '/' . $rel;
    if (!is_readable($path)) {
        return '';
    }

    return add_query_arg('v', rawurlencode((string) THEME_VERSION), footerball_theme_asset_url($rel));
}

/**
 * Image URL for article hero (featured / ACF first, then bundled editorial WebP).
 *
 * @param int                $post_id Post ID.
 * @param array<string,mixed>|null $article Optional ACF article row.
 * @return string URL or empty if none.
 */
function footerball_get_lifestyle_hero_visual_url($post_id, $article = null) {
    $post_id = (int) $post_id;
    if (!is_array($article)) {
        $article = footerball_get_lifestyle_article_data($post_id);
    }
    if (!empty($article['post_thumbnail'])) {
        $url = wp_get_attachment_image_url((int) $article['post_thumbnail'], 'large');
        if ($url) {
            return $url;
        }
    }
    if (has_post_thumbnail($post_id)) {
        $url = get_the_post_thumbnail_url($post_id, 'large');
        if ($url) {
            return $url;
        }
    }

    return footerball_get_lifestyle_editorial_hero_url($post_id);
}

function footerball_get_lifestyle_image_url($post_id) {
    $article = footerball_get_lifestyle_article_data($post_id);
    if (!empty($article['post_thumbnail'])) {
        $image_url = wp_get_attachment_image_url((int) $article['post_thumbnail'], 'full');
        if ($image_url) {
            return $image_url;
        }
    }

    $image_url = footerball_get_attachment_or_thumbnail_url($post_id);
    if ($image_url) {
        return $image_url;
    }

    $editorial = footerball_get_lifestyle_editorial_hero_url($post_id);
    if ($editorial) {
        return $editorial;
    }

    return footerball_lifestyle_generic_fallback_image_url();
}

function footerball_get_lifestyle_category_label($post_id) {
    $article = footerball_get_lifestyle_article_data($post_id);
    if (!empty($article['post_tags']) && is_array($article['post_tags'])) {
        $first_tag = trim((string) reset($article['post_tags']));
        if ($first_tag !== '') {
            return $first_tag;
        }
    }

    $terms = get_the_terms($post_id, 'category');
    if (!empty($terms) && !is_wp_error($terms)) {
        return $terms[0]->name;
    }

    return 'Lifestyle';
}

/**
 * Lifestyle article body HTML (ACF post_content_main first).
 *
 * @param int                       $post_id Post ID.
 * @param array<string,mixed>|null  $article Optional ACF article row.
 * @return string
 */
function footerball_get_lifestyle_body_html($post_id, $article = null) {
    $post_id = (int) $post_id;
    if ($post_id <= 0) {
        return '';
    }

    if (!is_array($article)) {
        $article = footerball_get_lifestyle_article_data($post_id);
    }

    $html = !empty($article['post_content_main'])
        ? (string) $article['post_content_main']
        : (string) get_post_field('post_content', $post_id);
    if ($html === '') {
        return '';
    }

    if (function_exists('footerball_clean_lifestyle_article_content')) {
        $title = footerball_get_lifestyle_display_title($post_id, $article);
        $html = footerball_clean_lifestyle_article_content($html, $title);
    }

    return $html;
}

/**
 * Plaintext lifestyle body for excerpts and word counts.
 *
 * @param int                       $post_id Post ID.
 * @param array<string,mixed>|null  $article Optional ACF article row.
 * @return string
 */
function footerball_get_lifestyle_body_plaintext($post_id, $article = null) {
    $text = wp_strip_all_tags(footerball_get_lifestyle_body_html($post_id, $article));
    if ($text === '') {
        return '';
    }

    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    return trim(preg_replace('/\s+/u', ' ', $text));
}

/**
 * Unified list-card excerpt: first N words from article body, then "...".
 *
 * @param int                       $post_id    Post ID.
 * @param int                       $word_count Words to keep before ellipsis.
 * @param array<string,mixed>|null  $article    Optional ACF article row.
 * @return string
 */
function footerball_get_lifestyle_list_excerpt($post_id, $word_count = 22, $article = null) {
    $post_id = (int) $post_id;
    $word_count = max(1, (int) $word_count);
    $text = footerball_get_lifestyle_body_plaintext($post_id, $article);
    if ($text === '') {
        return '';
    }

    return wp_trim_words($text, $word_count, '...');
}

function footerball_get_lifestyle_related_team_ids($post_id) {
    $team_ids = get_post_meta((int) $post_id, '_footerball_related_teams', true);
    if (!$team_ids && function_exists('get_field')) {
        $team_ids = get_field('footerball_related_teams', $post_id) ?: get_field('related_teams', $post_id) ?: get_field('related_team', $post_id);
    }
    if (!is_array($team_ids)) {
        $team_ids = $team_ids ? preg_split('/[\s,|]+/', (string) $team_ids) : array();
    }

    $normalized = array();
    foreach ($team_ids as $team_id) {
        if (is_object($team_id) && isset($team_id->ID)) {
            $team_id = $team_id->ID;
        }
        $team_id = absint($team_id);
        if ($team_id) {
            $normalized[] = $team_id;
        }
    }

    return array_values(array_unique($normalized));
}

function footerball_get_lifestyle_related_player_ids($post_id) {
    $player_ids = get_post_meta((int) $post_id, '_footerball_related_players', true);
    if (!$player_ids && function_exists('get_field')) {
        $player_ids = get_field('footerball_related_players', $post_id) ?: get_field('related_players', $post_id) ?: get_field('related_player', $post_id);
    }
    if (!is_array($player_ids)) {
        $player_ids = $player_ids ? preg_split('/[\s,|]+/', (string) $player_ids) : array();
    }

    $normalized = array();
    foreach ($player_ids as $player_id) {
        if (is_object($player_id) && isset($player_id->ID)) {
            $player_id = $player_id->ID;
        }
        $player_id = absint($player_id);
        if ($player_id) {
            $normalized[] = $player_id;
        }
    }

    return array_values(array_unique($normalized));
}

function footerball_get_lifestyle_related_host_city_ids($post_id) {
    $city_ids = get_post_meta((int) $post_id, '_footerball_related_host_cities', true);
    if (!$city_ids && function_exists('get_field')) {
        $city_ids = get_field('footerball_related_host_cities', $post_id)
            ?: get_field('related_host_cities', $post_id)
            ?: get_field('related_host_city', $post_id)
            ?: get_field('host_cities', $post_id)
            ?: get_field('host_city', $post_id);
    }
    if (!is_array($city_ids)) {
        $city_ids = $city_ids ? preg_split('/[\s,|]+/', (string) $city_ids) : array();
    }

    $normalized = array();
    foreach ($city_ids as $city_id) {
        if (is_object($city_id) && isset($city_id->ID)) {
            $city_id = $city_id->ID;
        }
        $city_id = absint($city_id);
        if ($city_id) {
            $normalized[] = $city_id;
        }
    }

    return array_values(array_unique($normalized));
}

/**
 * Related teams / players / host cities for lifestyle article tag lists.
 *
 * @param int $post_id Lifestyle post ID.
 * @return array<string, array<int, array{label:string,link:string}>>
 */
function footerball_get_lifestyle_entity_tag_groups($post_id) {
    $post_id = (int) $post_id;
    if ($post_id <= 0) {
        return array();
    }

    $groups = array();

    foreach (footerball_get_lifestyle_related_team_ids($post_id) as $team_id) {
        if (get_post_type($team_id) !== 'teams' || get_post_status($team_id) !== 'publish') {
            continue;
        }
        $label = function_exists('footerball_get_team_display_name')
            ? footerball_get_team_display_name($team_id)
            : get_the_title($team_id);
        $link = function_exists('footerball_team_tab_url')
            ? footerball_team_tab_url($team_id, 'profile')
            : get_permalink($team_id);
        $label = footerball_clean_meta_text((string) $label);
        $link = esc_url_raw((string) $link);
        if ($label === '' || $link === '') {
            continue;
        }
        $groups['teams'][] = array(
            'label' => $label,
            'link'  => $link,
        );
    }

    foreach (footerball_get_lifestyle_related_player_ids($post_id) as $player_id) {
        if (get_post_type($player_id) !== 'players' || get_post_status($player_id) !== 'publish') {
            continue;
        }
        $label = function_exists('footerball_get_player_display_name')
            ? footerball_get_player_display_name($player_id)
            : get_the_title($player_id);
        $link = function_exists('footerball_player_tab_url')
            ? footerball_player_tab_url($player_id, 'profile')
            : get_permalink($player_id);
        $label = footerball_clean_meta_text((string) $label);
        $link = esc_url_raw((string) $link);
        if ($label === '' || $link === '') {
            continue;
        }
        $groups['players'][] = array(
            'label' => $label,
            'link'  => $link,
        );
    }

    foreach (footerball_get_lifestyle_related_host_city_ids($post_id) as $city_id) {
        if (get_post_type($city_id) !== 'host_cities' || get_post_status($city_id) !== 'publish') {
            continue;
        }
        $label = footerball_clean_meta_text(get_the_title($city_id));
        $link = function_exists('footerball_host_city_tab_url')
            ? footerball_host_city_tab_url($city_id, 'ticket-information')
            : get_permalink($city_id);
        $link = esc_url_raw((string) $link);
        if ($label === '' || $link === '') {
            continue;
        }
        $groups['host_cities'][] = array(
            'label' => $label,
            'link'  => $link,
        );
    }

    return $groups;
}

/**
 * @param int $post_id Lifestyle post ID.
 * @return string
 */
function footerball_render_lifestyle_entity_tags($post_id) {
    $groups = footerball_get_lifestyle_entity_tag_groups($post_id);
    if ($groups === array()) {
        return '';
    }

    $sections = array(
        'teams'       => __('Teams', 'twentytwentyfive-child'),
        'players'     => __('Players', 'twentytwentyfive-child'),
        'host_cities' => __('Host Cities', 'twentytwentyfive-child'),
    );

    ob_start();
    ?>
    <section class="blog-entity-tags" aria-label="<?php esc_attr_e('Related teams, players, and host cities', 'twentytwentyfive-child'); ?>">
        <?php foreach ($sections as $key => $heading) : ?>
            <?php if (empty($groups[$key])) : ?>
                <?php continue; ?>
            <?php endif; ?>
            <div class="blog-entity-tags__group">
                <h2 class="blog-entity-tags__label"><?php echo esc_html($heading); ?></h2>
                <div class="blog-entity-tags__list">
                    <?php foreach ($groups[$key] as $item) : ?>
                        <a class="blog-entity-tags__tag" href="<?php echo esc_url($item['link']); ?>"<?php echo footerball_internal_link_attrs($item['link']); ?>><?php echo esc_html($item['label']); ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </section>
    <?php
    return trim(ob_get_clean());
}

/**
 * Players shown in the lifestyle "Related Players" meta box.
 * Loading every published player at once can exhaust memory/time on large catalogs.
 *
 * @param int[] $selected_ids Already-linked player post IDs (always included in the list).
 * @return WP_Post[]
 */
function footerball_lifestyle_admin_player_posts_for_meta_box($selected_ids) {
    $selected_ids = array_values(array_unique(array_filter(array_map('absint', (array) $selected_ids))));
    $cap = (int) apply_filters('footerball_lifestyle_admin_players_meta_cap', 500);
    if ($cap < 80) {
        $cap = 80;
    }
    if ($cap > 2000) {
        $cap = 2000;
    }

    $primary = get_posts(array(
        'post_type'              => 'players',
        'post_status'            => 'publish',
        'posts_per_page'         => $cap,
        'orderby'                => 'title',
        'order'                  => 'ASC',
        'no_found_rows'          => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
    ));

    $loaded = wp_list_pluck($primary, 'ID');
    $missing = array_values(array_diff($selected_ids, $loaded));
    $extra = array();
    if (!empty($missing)) {
        $extra = get_posts(array(
            'post_type'              => 'players',
            'post_status'            => 'publish',
            'post__in'               => $missing,
            'posts_per_page'         => count($missing),
            'orderby'                => 'title',
            'order'                  => 'ASC',
            'no_found_rows'          => true,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
        ));
    }

    $by_id = array();
    foreach (array_merge($primary, $extra) as $player_post) {
        $by_id[(int) $player_post->ID] = $player_post;
    }
    $merged = array_values($by_id);
    usort($merged, static function ($a, $b) {
        return strcasecmp((string) $a->post_title, (string) $b->post_title);
    });

    return $merged;
}

function footerball_lifestyle_to_team_news_item($post_id) {
    $post_id = (int) $post_id;
    $article = footerball_get_lifestyle_article_data($post_id);
    $title = footerball_get_lifestyle_display_title($post_id, $article);
    $excerpt = footerball_get_lifestyle_list_excerpt($post_id, 26, $article);
    $word_count = str_word_count(footerball_get_lifestyle_body_plaintext($post_id, $article));
    $read_time = max(2, (int) ceil($word_count / 220)) . ' min read';

    return array(
        'id'      => $post_id,
        'tag'     => footerball_get_lifestyle_category_label($post_id),
        'title'   => $title,
        'excerpt' => $excerpt,
        'date'    => get_the_date('M j, Y', $post_id) . ' · ' . $read_time,
        'link'       => get_permalink($post_id),
        'link_attrs' => footerball_internal_link_attrs(get_permalink($post_id)),
        'thumb'      => footerball_get_lifestyle_image_url($post_id),
        'source'     => 'lifestyle',
    );
}

function footerball_get_latest_lifestyle_posts($limit = 6, $exclude_ids = array()) {
    $limit = max(1, (int) $limit);
    $exclude_ids = array_values(array_filter(array_map('absint', (array) $exclude_ids)));
    $args = array(
        'post_type'           => 'lifestyle',
        'post_status'         => 'publish',
        'posts_per_page'      => $limit,
        'orderby'             => 'date',
        'order'               => 'DESC',
        'ignore_sticky_posts' => true,
        'no_found_rows'       => true,
    );
    if (!empty($exclude_ids)) {
        $args['post__not_in'] = $exclude_ids;
    }

    return get_posts($args);
}

function footerball_get_team_lifestyle_news($team_id, $limit = 6) {
    $team_id = (int) $team_id;
    if ($team_id <= 0) {
        return array();
    }

    $limit = max(1, (int) $limit);
    $team_meta_query = array('relation' => 'OR');
    foreach (array('_footerball_related_teams', 'footerball_related_teams', 'related_teams', 'related_team', 'team') as $meta_key) {
        $team_meta_query[] = array(
            'key'     => $meta_key,
            'value'   => '"' . $team_id . '"',
            'compare' => 'LIKE',
        );
        $team_meta_query[] = array(
            'key'     => $meta_key,
            'value'   => 'i:' . $team_id . ';',
            'compare' => 'LIKE',
        );
        $team_meta_query[] = array(
            'key'     => $meta_key,
            'value'   => (string) $team_id,
            'compare' => '=',
        );
    }
    $team_meta_query[] = array(
        'key'     => '_footerball_related_team',
        'value'   => (string) $team_id,
        'compare' => '=',
    );

    $posts = get_posts(array(
        'post_type'      => 'lifestyle',
        'post_status'    => 'publish',
        'posts_per_page' => $limit,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'meta_query'     => $team_meta_query,
    ));
    if (empty($posts)) {
        $posts = footerball_get_latest_lifestyle_posts($limit);
    }

    $items = array();
    foreach ($posts as $post_obj) {
        $items[] = footerball_lifestyle_to_team_news_item($post_obj->ID);
    }

    return $items;
}

function footerball_get_host_city_lifestyle_news($host_city_id, $limit = 6) {
    $host_city_id = (int) $host_city_id;
    if ($host_city_id <= 0) {
        return array();
    }

    $limit = max(1, (int) $limit);
    $city_meta_query = footerball_get_lifestyle_relation_meta_query('host_city', $host_city_id);

    $posts = get_posts(array(
        'post_type'           => 'lifestyle',
        'post_status'         => 'publish',
        'posts_per_page'      => $limit,
        'orderby'             => 'date',
        'order'               => 'DESC',
        'ignore_sticky_posts' => true,
        'no_found_rows'       => true,
        'meta_query'          => $city_meta_query,
    ));

    if (empty($posts)) {
        $city_name = get_the_title($host_city_id);
        if ($city_name) {
            $posts = get_posts(array(
                'post_type'           => 'lifestyle',
                'post_status'         => 'publish',
                'posts_per_page'      => $limit,
                'orderby'             => 'date',
                'order'               => 'DESC',
                'ignore_sticky_posts' => true,
                'no_found_rows'       => true,
                's'                   => $city_name,
            ));
            $city_needle = strtolower($city_name);
            $city_slug = sanitize_title($city_name);
            usort($posts, static function ($a, $b) use ($city_needle, $city_slug) {
                $a_title = strtolower(get_the_title($a));
                $b_title = strtolower(get_the_title($b));
                $a_slug = isset($a->post_name) ? (string) $a->post_name : '';
                $b_slug = isset($b->post_name) ? (string) $b->post_name : '';
                $a_exact = (strpos($a_title, $city_needle) !== false || ($city_slug && strpos($a_slug, $city_slug) !== false)) ? 0 : 1;
                $b_exact = (strpos($b_title, $city_needle) !== false || ($city_slug && strpos($b_slug, $city_slug) !== false)) ? 0 : 1;
                if ($a_exact !== $b_exact) {
                    return $a_exact <=> $b_exact;
                }
                return strcmp((string) $b->post_date, (string) $a->post_date);
            });
        }
    }
    if (empty($posts)) {
        $posts = footerball_get_latest_lifestyle_posts($limit);
    }

    $items = array();
    foreach ($posts as $post_obj) {
        $items[] = footerball_lifestyle_to_team_news_item($post_obj->ID);
    }

    return $items;
}

function footerball_format_lifestyle_views($views) {
    $views = max(0, (int) $views);
    return $views >= 1000 ? number_format($views / 1000, 1) . 'K' : number_format($views);
}

function footerball_get_lifestyle_relation_meta_query($entity_type, $entity_id) {
    $entity_id = absint($entity_id);
    if ($entity_id <= 0) {
        return array();
    }

    if ($entity_type === 'team') {
        $meta_keys = array('_footerball_related_teams', 'footerball_related_teams', 'related_teams', 'related_team', 'team');
        $single_meta_key = '_footerball_related_team';
    } elseif ($entity_type === 'player') {
        $meta_keys = array('_footerball_related_players', 'footerball_related_players', 'related_players', 'related_player', 'player');
        $single_meta_key = '_footerball_related_player';
    } elseif ($entity_type === 'host_city') {
        $meta_keys = array('_footerball_related_host_cities', 'footerball_related_host_cities', 'related_host_cities', 'related_host_city', 'host_cities', 'host_city', 'city');
        $single_meta_key = '_footerball_related_host_city';
    } else {
        return array();
    }

    $meta_query = array('relation' => 'OR');
    foreach ($meta_keys as $meta_key) {
        $meta_query[] = array(
            'key'     => $meta_key,
            'value'   => '"' . $entity_id . '"',
            'compare' => 'LIKE',
        );
        $meta_query[] = array(
            'key'     => $meta_key,
            'value'   => 'i:' . $entity_id . ';',
            'compare' => 'LIKE',
        );
        $meta_query[] = array(
            'key'     => $meta_key,
            'value'   => (string) $entity_id,
            'compare' => '=',
        );
    }
    $meta_query[] = array(
        'key'     => $single_meta_key,
        'value'   => (string) $entity_id,
        'compare' => '=',
    );

    return $meta_query;
}

function footerball_get_trending_lifestyle_posts($args = array()) {
    $defaults = array(
        'limit'              => 5,
        'team_id'            => 0,
        'player_id'          => 0,
        'host_city_id'       => 0,
        'post__in'           => array(),
        'post__not_in'       => array(),
        'fallback_to_latest' => true,
    );
    $args = wp_parse_args($args, $defaults);
    $limit = max(1, (int) $args['limit']);

    $base_args = array(
        'post_type'           => 'lifestyle',
        'post_status'         => 'publish',
        'posts_per_page'      => $limit,
        'ignore_sticky_posts' => true,
        'no_found_rows'       => true,
    );

    $post_in = array_values(array_filter(array_map('absint', (array) $args['post__in'])));
    if (!empty($post_in)) {
        $base_args['post__in'] = $post_in;
    }

    $post_not_in = array_values(array_filter(array_map('absint', (array) $args['post__not_in'])));
    if (!empty($post_not_in)) {
        $base_args['post__not_in'] = $post_not_in;
    }

    $relation_query = array();
    if (!empty($args['team_id'])) {
        $relation_query = footerball_get_lifestyle_relation_meta_query('team', $args['team_id']);
    } elseif (!empty($args['player_id'])) {
        $relation_query = footerball_get_lifestyle_relation_meta_query('player', $args['player_id']);
    } elseif (!empty($args['host_city_id'])) {
        $relation_query = footerball_get_lifestyle_relation_meta_query('host_city', $args['host_city_id']);
    }

    $meta_query = array(
        'relation'     => 'AND',
        'views_clause' => array(
            'key'     => 'lifestyle_views',
            'compare' => 'EXISTS',
            'type'    => 'NUMERIC',
        ),
    );
    if (!empty($relation_query)) {
        $meta_query[] = $relation_query;
    }

    $query_args = $base_args;
    $query_args['meta_query'] = $meta_query;
    $query_args['orderby'] = array(
        'views_clause' => 'DESC',
        'date'         => 'DESC',
    );
    $query_args['order'] = 'DESC';

    $posts = get_posts($query_args);

    if (empty($posts) && !empty($args['fallback_to_latest'])) {
        $fallback_args = $base_args;
        $fallback_args['orderby'] = 'date';
        $fallback_args['order'] = 'DESC';
        if (!empty($relation_query)) {
            $fallback_args['meta_query'] = $relation_query;
        }
        $posts = get_posts($fallback_args);
    }

    $items = array();
    foreach ($posts as $post_obj) {
        $post_id = (int) $post_obj->ID;
        $article = footerball_get_lifestyle_article_data($post_id);
        $views = (int) get_post_meta($post_id, 'lifestyle_views', true);
        $items[] = array(
            'id'        => $post_id,
            'title'     => footerball_get_lifestyle_display_title($post_id, $article),
            'views'     => footerball_format_lifestyle_views($views),
            'views_raw' => max(0, $views),
            'link'       => get_permalink($post_id),
            'link_attrs' => footerball_internal_link_attrs(get_permalink($post_id)),
        );
    }

    return $items;
}

function footerball_render_lifestyle_trending_topics($topics, $args = array()) {
    if (empty($topics) || !is_array($topics)) {
        return '';
    }

    $defaults = array(
        'title'        => 'Trending Topics',
        'view_all_url' => get_post_type_archive_link('lifestyle') ?: home_url('/lifestyle/'),
        'view_all'     => '',
        'panel_class'  => 'lifestyle-panel footerball-trending-panel',
        'inner_class'  => '',
        'list_class'   => 'topic-list footerball-topic-list',
    );
    $args = wp_parse_args($args, $defaults);
    $view_all_label = $args['view_all'];
    if ($view_all_label === '' && !empty($args['view_all_url'])) {
        $view_all_label = 'View all';
    }

    ob_start();
    ?>
    <section class="<?php echo esc_attr($args['panel_class']); ?>">
        <?php if (!empty($args['inner_class'])) : ?>
        <div class="<?php echo esc_attr($args['inner_class']); ?>">
        <?php endif; ?>
            <div class="lifestyle-panel-head footerball-trending-panel__head">
                <h2><?php echo esc_html($args['title']); ?></h2>
                <?php if (!empty($args['view_all_url']) && $view_all_label !== '') : ?>
                    <a href="<?php echo esc_url($args['view_all_url']); ?>"<?php echo footerball_internal_link_attrs($args['view_all_url']); ?>><?php echo esc_html($view_all_label); ?></a>
                <?php endif; ?>
            </div>
            <ol class="<?php echo esc_attr($args['list_class']); ?>">
                <?php foreach (array_slice($topics, 0, 5) as $topic_index => $topic) : ?>
                    <li>
                        <span><?php echo esc_html($topic_index + 1); ?></span>
                        <a href="<?php echo esc_url($topic['link']); ?>" class="topic-title-link"<?php echo !empty($topic['link_attrs']) ? $topic['link_attrs'] : footerball_internal_link_attrs($topic['link']); ?>>
                            <strong class="topic-title" title="<?php echo esc_attr($topic['title']); ?>"><?php echo esc_html($topic['title']); ?></strong>
                        </a>
                        <em><?php echo esc_html($topic['views']); ?> views</em>
                    </li>
                <?php endforeach; ?>
            </ol>
        <?php if (!empty($args['inner_class'])) : ?>
        </div>
        <?php endif; ?>
    </section>
    <?php
    return trim(ob_get_clean());
}

function footerball_get_lifestyle_seo_field($post_id, $field) {
    $post_id = (int) $post_id;
    if ($post_id <= 0) {
        return '';
    }

    $allowed_fields = array('meta_title', 'meta_description');
    if (!in_array($field, $allowed_fields, true)) {
        return '';
    }

    return footerball_clean_meta_text(get_post_meta($post_id, '_footerball_lifestyle_' . $field, true));
}

function footerball_get_seo_meta() {
    $home_title = '2026 Global Football Tournament: Schedule, City Guides & Gear | DHgate';
    $home_h1 = footerball_home_hero_title();
    $home_description = 'Your ultimate hub for the 2026 international summer of soccer. Find live match schedules, host city guides, tournament countdowns, star player news, and top football gears on sale.';

    $meta = array(
        'h1'          => $home_h1,
        'title'       => $home_title,
        'description' => $home_description,
        'canonical'   => footerball_current_url_without_query(),
        'og_type'     => 'website',
        'og_image'    => footerball_default_og_image_url(),
        'robots'      => 'all, max-image-preview:large, max-snippet:-1, max-video-preview:-1',
    );

    if (footerball_is_about_us_request()) {
        $meta['title'] = 'About DHgate Football Hub | DHgate';
        $meta['h1'] = 'About DHgate Football Hub';
        $meta['description'] = 'Learn who runs DHgate Football Hub, how our in-house editorial team works, and how football.dhgate.com connects fan coverage with DHgate.com.';
        $meta['canonical'] = home_url('/about-us/');
        return $meta;
    }

    if ((int) get_query_var('footerball_matches_page') === 1 || is_post_type_archive('matches')) {
        $meta['title'] = '2026 World Cup Schedule: Matches by Team, City & Stadium';
        $meta['h1'] = $meta['title'];
        $meta['description'] = 'Find the complete 2026 World Cup match schedule. Easily search fixtures by your favorite team, host city, or stadium. Get dates and kickoff times here.';
        $meta['canonical'] = home_url('/matches/');
        return $meta;
    }

    if (is_singular('players')) {
        $post_id = get_queried_object_id();
        $name = function_exists('footerball_get_player_display_name') ? footerball_get_player_display_name($post_id) : get_the_title($post_id);
        $tab = get_query_var('player_tab');
        $valid_tabs = array('profile', 'gear', 'stats', 'updates');
        $tab = in_array($tab, $valid_tabs, true) ? $tab : 'profile';
        $player_meta = array(
            'profile' => array(
                'title' => sprintf('%s Profile: Biography, Age, Career & Facts | Football DHgate', $name),
                'description' => sprintf('Discover everything about %s. Read his full biography, early life, career highlights, age, height, playing position, and interesting personal facts.', $name),
            ),
            'gear' => array(
                'title' => sprintf('Shop %s Jerseys, Boots & Football Gear | Football DHgate', $name),
                'description' => sprintf('Support %s with affordable gear. Shop a wide selection of %s football jerseys, signature boots, t-shirts, and exclusive fan merchandise.', $name, $name),
            ),
            'stats' => array(
                'title' => sprintf('%s Stats 2025/2026: Goals, Assists & Records | Football DHgate', $name),
                'description' => sprintf('View the latest %s football statistics. Track his goals, assists, appearances, clean sheets, and historic career records for both club and country.', $name),
            ),
            'updates' => array(
                'title' => sprintf('%s News, Transfer Rumors & Injury Updates | DHgate', $name),
                'description' => sprintf('Get the latest %s updates. Stay informed on his transfer rumors, injury reports, match performances, recent interviews, and breaking football news.', $name),
            ),
        );
        $meta['title'] = $player_meta[$tab]['title'];
        $meta['h1'] = $name;
        $meta['description'] = $player_meta[$tab]['description'];
        $meta['canonical'] = function_exists('footerball_player_tab_url') ? footerball_player_tab_url($post_id, $tab) : footerball_current_url_without_query();
        $meta['og_type'] = 'profile';
        $meta['og_image'] = footerball_get_attachment_or_thumbnail_url($post_id, 'featured_image') ?: footerball_default_og_image_url();
        return $meta;
    }

    if (is_singular('teams')) {
        $post_id = get_queried_object_id();
        $name = footerball_get_team_display_name($post_id);
        $tab = get_query_var('team_tab');
        $valid_tabs = array('profile', 'gear', 'players', 'news');
        $tab = in_array($tab, $valid_tabs, true) ? $tab : 'profile';
        $team_meta = array(
            'gear' => array(
                'title' => sprintf('Shop %s Gear, Jerseys & Apparel | Football DHgate', $name),
                'description' => sprintf('Find the best deals on %s gear. Shop our massive selection of affordable %s jerseys, shirts, hats, and exclusive merchandise. Fast shipping available.', $name, $name),
            ),
            'profile' => array(
                'title' => sprintf('%s Profile: History, Stats & Overview | Football DHgate', $name),
                'description' => sprintf('Explore the complete %s profile. Discover the football club\'s rich history, all-time stats, major trophies, stadium details, and key facts.', $name),
            ),
            'players' => array(
                'title' => sprintf('%s Roster, Players & Squad 2026 | Football DHgate', $name),
                'description' => sprintf('Meet the current %s squad. View full player rosters, detailed profiles, stats, jersey numbers, and positions for your favorite football stars.', $name),
            ),
            'news' => array(
                'title' => sprintf('Latest %s News, Rumors & Updates | Football DHgate', $name),
                'description' => sprintf('Stay updated with the latest %s news. Get breaking football updates on match results, transfer rumors, injury reports, and daily team highlights.', $name),
            ),
        );
        $meta['title'] = $team_meta[$tab]['title'];
        $meta['h1'] = $name;
        $meta['description'] = $team_meta[$tab]['description'];
        $meta['canonical'] = function_exists('footerball_team_tab_url') ? footerball_team_tab_url($post_id, $tab) : footerball_current_url_without_query();
        $meta['og_image'] = (function_exists('footerball_team_logo_asset_url') ? footerball_team_logo_asset_url($post_id) : '') ?: footerball_get_attachment_or_thumbnail_url($post_id, 'team_logo') ?: footerball_get_attachment_or_thumbnail_url($post_id, 'team_banner_bg') ?: footerball_default_og_image_url();
        return $meta;
    }

    if (is_singular('host_cities')) {
        $post_id = get_queried_object_id();
        $city = get_the_title($post_id);
        $tab = get_query_var('host_city_tab');
        $valid_tabs = array('ticket-information', 'fixtures', 'stadium-day-guide', 'official-gear');
        $tab = in_array($tab, $valid_tabs, true) ? $tab : 'ticket-information';
        $tab_meta = array(
            'ticket-information' => array(
                'title' => sprintf('%s Match Tickets 2026: Info & Pricing | Football DHgate', $city),
                'description' => sprintf('Looking for football tickets in %s? Get all the essential information on ticket prices, availability, seating charts, and how to secure your seats.', $city),
            ),
            'fixtures' => array(
                'title' => sprintf('%s Football Fixtures & Match Schedule 2026 | Football DHgate', $city),
                'description' => sprintf('Check out the complete 2026 football match schedule for %s. Stay updated on upcoming fixtures, kickoff times, dates, and teams playing in the city.', $city),
            ),
            'stadium-day-guide' => array(
                'title' => sprintf('%s Stadium & Match Day Guide 2026 | DHgate', $city),
                'description' => sprintf('Plan your ultimate match day in %s. Read our comprehensive stadium guide covering parking, public transit, gate entry rules, and local fan zones.', $city),
            ),
            'official-gear' => array(
                'title' => sprintf('Shop %s Match Gear & Football Merchandise | DHgate', $city),
                'description' => sprintf('Celebrate the 2026 games with exclusive %s gear! Shop our massive selection of affordable host city apparel, football souvenirs, scarves, and hats.', $city),
            ),
        );
        $meta['title'] = $tab_meta[$tab]['title'];
        $meta['h1'] = $city;
        $meta['description'] = $tab_meta[$tab]['description'];
        $meta['canonical'] = function_exists('footerball_host_city_tab_url') ? footerball_host_city_tab_url($post_id, $tab) : footerball_current_url_without_query();
        $meta['og_image'] = function_exists('footerball_host_city_banner_url') ? footerball_host_city_banner_url($post_id) : '';
        if (!$meta['og_image']) {
            $meta['og_image'] = footerball_default_og_image_url();
        }
        return $meta;
    }

    if (is_singular('lifestyle')) {
        $post_id = get_queried_object_id();
        $article = footerball_get_lifestyle_article_data($post_id);
        $content = !empty($article['post_content_main']) ? $article['post_content_main'] : get_post_field('post_content', $post_id);
        $description = has_excerpt($post_id) ? get_the_excerpt($post_id) : wp_trim_words(wp_strip_all_tags($content), 32, '');
        $custom_title = footerball_get_lifestyle_seo_field($post_id, 'meta_title');
        $custom_description = footerball_get_lifestyle_seo_field($post_id, 'meta_description');

        $article_title = footerball_get_lifestyle_display_title($post_id, $article);
        $meta['title'] = $custom_title ?: $article_title;
        $meta['h1'] = $article_title;
        $meta['description'] = $custom_description ?: ($description ?: $home_description);
        $meta['canonical'] = get_permalink($post_id);
        $meta['og_type'] = 'article';
        $meta['og_image'] = footerball_get_lifestyle_image_url($post_id);
        return $meta;
    }

    if (is_search()) {
        $query = footerball_clean_meta_text(get_search_query(false));
        $meta['title'] = $query ? sprintf('Search results for %s | DHgate Football', $query) : 'Search | DHgate Football';
        $meta['description'] = $query ? sprintf('Explore DHgate Football search results for %s across players, teams, matches, host cities, and lifestyle guides.', $query) : 'Search DHgate Football for 2026 players, teams, matches, host cities, and lifestyle guides.';
        $meta['canonical'] = $query ? add_query_arg('s', $query, home_url('/')) : home_url('/');
        $meta['robots'] = 'noindex, follow';
        return $meta;
    }

    if (is_post_type_archive('players')) {
        $meta['title'] = '2026 Football Players Directory | DHgate';
        $meta['description'] = 'Explore player profiles, stats, schedules, updates, and football gear for the 2026 international football event.';
        $meta['canonical'] = footerball_archive_url('players', '/players/');
        return $meta;
    }

    if (is_post_type_archive('teams')) {
        $meta['title'] = '2026 Football Teams Directory | DHgate';
        $meta['description'] = 'Browse national team guides with 2026 roster notes, match schedules, historical stats, news, and football jerseys.';
        $meta['canonical'] = footerball_archive_url('teams', '/teams/');
        return $meta;
    }

    if (is_post_type_archive('host_cities')) {
        $meta['title'] = '2026 Football Host City Guides | DHgate';
        $meta['description'] = 'Plan matchday travel with 2026 host city guides covering stadiums, transport, local fan stops, weather, and football gear.';
        $meta['canonical'] = footerball_archive_url('host_cities', '/host-cities/');
        return $meta;
    }

    if (is_post_type_archive('lifestyle')) {
        $meta['title'] = '2026 Football Lifestyle & Fan Guides | DHgate';
        $meta['description'] = 'Read 2026 football news, fan guides, player stories, city tips, match previews, and shopping ideas for tournament season.';
        $meta['canonical'] = footerball_archive_url('lifestyle', '/lifestyle/');
        return $meta;
    }

    if (is_front_page() || is_home()) {
        $meta['canonical'] = home_url('/');
        return $meta;
    }

    if (is_author()) {
        $author = get_queried_object();
        $author_name = $author && !empty($author->display_name) ? $author->display_name : 'Football Editor';
        $meta['title'] = sprintf('%s, Author at DHgate Football', $author_name);
        $meta['description'] = sprintf('Read 2026 football guides, team stories, player updates, and fan gear coverage written by %s for DHgate Football.', $author_name);
        $meta['canonical'] = get_author_posts_url((int) get_query_var('author'));
        $meta['robots'] = 'noindex, follow';
        return $meta;
    }

    if (is_page()) {
        $post_id = get_queried_object_id();
        $page_title = get_the_title($post_id);
        $content = get_post_field('post_content', $post_id);
        $description = has_excerpt($post_id) ? get_the_excerpt($post_id) : wp_trim_words(wp_strip_all_tags($content), 28, '');
        $meta['title'] = sprintf('%s | DHgate Football', $page_title ?: 'DHgate Football');
        $meta['description'] = $description ?: $home_description;
        $meta['canonical'] = get_permalink($post_id);
        return $meta;
    }

    if (is_404()) {
        $meta['title'] = 'Page Not Found | DHgate Football';
        $meta['description'] = 'The page you requested could not be found. Return to DHgate Football for 2026 schedules, team guides, player news, and gear.';
        $meta['canonical'] = home_url('/');
        $meta['robots'] = 'noindex, follow';
        return $meta;
    }

    return $meta;
}

function footerball_home_hero_title() {
    return 'The Ultimate 2026 Football Fan Hub';
}

function footerball_get_document_title($title = '') {
    $meta = footerball_get_seo_meta();
    return footerball_clean_meta_text($meta['title']);
}
add_filter('pre_get_document_title', 'footerball_get_document_title', 20);

function footerball_get_seo_h1() {
    $meta = footerball_get_seo_meta();
    $h1 = !empty($meta['h1']) ? $meta['h1'] : ($meta['title'] ?? '');
    return footerball_clean_meta_text($h1);
}

function footerball_remove_core_head_duplicates() {
    remove_action('wp_head', '_wp_render_title_tag', 1);
    remove_action('wp_head', 'wp_viewport_meta', 0);
}
footerball_remove_core_head_duplicates();
add_action('after_setup_theme', 'footerball_remove_core_head_duplicates', 100);
add_action('init', 'footerball_remove_core_head_duplicates', 100);
add_action('wp_head', 'footerball_remove_core_head_duplicates', -1000);

function footerball_render_global_seo_meta() {
    $meta = footerball_get_seo_meta();

    $title = footerball_clean_meta_text($meta['title']);
    $description = footerball_clean_meta_text($meta['description']);
    $canonical = !empty($meta['canonical']) ? $meta['canonical'] : footerball_current_url_without_query();
    $og_type = !empty($meta['og_type']) ? $meta['og_type'] : 'website';
    $og_image = !empty($meta['og_image']) ? $meta['og_image'] : footerball_default_og_image_url();
    $robots = !empty($meta['robots']) ? $meta['robots'] : 'all, max-image-preview:large, max-snippet:-1, max-video-preview:-1';

    echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
    if (!empty($meta['keywords'])) {
        echo '<meta name="keywords" content="' . esc_attr(footerball_clean_meta_text($meta['keywords'])) . '">' . "\n";
    }
    echo '<meta name="robots" content="' . esc_attr($robots) . '">' . "\n";
    echo '<link rel="canonical" href="' . esc_url($canonical) . '">' . "\n";
    echo '<meta property="og:site_name" content="DHgate Football">' . "\n";
    echo '<meta property="og:type" content="' . esc_attr($og_type) . '">' . "\n";
    echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
    echo '<meta property="og:description" content="' . esc_attr($description) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url($canonical) . '">' . "\n";
    echo '<meta property="og:image" content="' . esc_url($og_image) . '">' . "\n";
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr($title) . '">' . "\n";
    echo '<meta name="twitter:description" content="' . esc_attr($description) . '">' . "\n";
    echo '<meta name="twitter:image" content="' . esc_url($og_image) . '">' . "\n";
}
remove_action('wp_head', 'rel_canonical');
remove_action('wp_head', 'wp_robots', 1);
add_action('wp_head', function () {
    remove_action('wp_head', 'wp_robots', 1);
}, 0);
add_action('wp_head', 'footerball_render_global_seo_meta', 2);

function footerball_link_rel_tokens($rel) {
    $tokens = preg_split('/\s+/', strtolower((string) $rel), -1, PREG_SPLIT_NO_EMPTY);
    return array_values(array_unique(array_filter($tokens)));
}

function footerball_anchor_attr_value($tag, $attr) {
    $pattern = '/\s' . preg_quote($attr, '/') . '\s*=\s*("([^"]*)"|\'([^\']*)\'|([^\s>]+))/i';
    if (!preg_match($pattern, (string) $tag, $match)) {
        return '';
    }
    return html_entity_decode($match[2] ?? $match[3] ?? $match[4] ?? '', ENT_QUOTES, get_bloginfo('charset') ?: 'UTF-8');
}

function footerball_anchor_set_attr($tag, $attr, $value) {
    $tag = (string) $tag;
    $attr = (string) $attr;
    $value = esc_attr((string) $value);
    $pattern = '/(\s' . preg_quote($attr, '/') . '\s*=\s*)("([^"]*)"|\'([^\']*)\'|([^\s>]+))/i';
    if (preg_match($pattern, $tag)) {
        return preg_replace($pattern, '$1"' . $value . '"', $tag, 1);
    }
    return preg_replace('/\s*>$/', ' ' . $attr . '="' . $value . '">', $tag, 1);
}

function footerball_normalize_external_link_rel($html) {
    if (stripos((string) $html, '<a ') === false) {
        return $html;
    }

    $site_host = wp_parse_url(home_url('/'), PHP_URL_HOST);
    $site_host = strtolower((string) $site_host);

    return preg_replace_callback('/<a\b[^>]*>/i', function ($matches) use ($site_host) {
        $tag = $matches[0];
        $href = footerball_anchor_attr_value($tag, 'href');
        if ($href === '' || !preg_match('#^(https?:)?//#i', $href)) {
            return $tag;
        }

        $host = strtolower((string) wp_parse_url($href, PHP_URL_HOST));
        if ($host === '') {
            return $tag;
        }

        $is_dhgate_domain = $host === 'dhgate.com' || str_ends_with($host, '.dhgate.com');
        $is_same_site = $site_host !== '' && $host === $site_host;
        $tokens = footerball_link_rel_tokens(footerball_anchor_attr_value($tag, 'rel'));

        if ($is_dhgate_domain) {
            $tokens = array_values(array_diff($tokens, array('nofollow', 'sponsored')));
        } elseif (!$is_same_site) {
            $tokens[] = 'nofollow';
        }

        if (strtolower(footerball_anchor_attr_value($tag, 'target')) === '_blank') {
            $tokens[] = 'noopener';
            $tokens[] = 'noreferrer';
        }

        $tokens = array_values(array_unique(array_filter($tokens)));
        if (empty($tokens)) {
            return preg_replace('/\srel\s*=\s*("([^"]*)"|\'([^\']*)\'|([^\s>]+))/i', '', $tag, 1);
        }

        return footerball_anchor_set_attr($tag, 'rel', implode(' ', $tokens));
    }, $html);
}

function footerball_start_link_rel_buffer() {
    if (is_admin() || wp_doing_ajax() || is_feed() || is_robots() || is_singular('lifestyle')) {
        return;
    }

    ob_start('footerball_normalize_external_link_rel');
}
add_action('template_redirect', 'footerball_start_link_rel_buffer', -1000);

function footerball_json_ld_clean($value) {
    if (is_array($value)) {
        $clean = array();
        foreach ($value as $key => $item) {
            $item = footerball_json_ld_clean($item);
            if ($item === '' || $item === null || $item === array()) {
                continue;
            }
            $clean[$key] = $item;
        }
        return $clean;
    }

    if (is_string($value)) {
        return footerball_clean_meta_text($value);
    }

    return $value;
}

function footerball_json_ld_image($url) {
    $url = esc_url_raw((string) $url);
    return $url ? array('@type' => 'ImageObject', 'url' => $url) : array();
}

function footerball_get_breadcrumb_schema() {
    $items = array(
        array(
            '@type' => 'ListItem',
            'position' => 1,
            'name' => 'Home',
            'item' => home_url('/'),
        ),
    );

    if (is_post_type_archive('teams') || is_singular('teams')) {
        $items[] = array('@type' => 'ListItem', 'position' => 2, 'name' => 'Teams', 'item' => footerball_archive_url('teams', '/teams/'));
    } elseif (is_post_type_archive('players') || is_singular('players')) {
        $items[] = array('@type' => 'ListItem', 'position' => 2, 'name' => 'Players', 'item' => footerball_archive_url('players', '/players/'));
    } elseif (is_post_type_archive('host_cities') || is_singular('host_cities')) {
        $items[] = array('@type' => 'ListItem', 'position' => 2, 'name' => 'Host Cities', 'item' => footerball_archive_url('host_cities', '/host-cities/'));
    } elseif (is_post_type_archive('lifestyle') || is_singular('lifestyle')) {
        $items[] = array('@type' => 'ListItem', 'position' => 2, 'name' => 'Lifestyle', 'item' => footerball_archive_url('lifestyle', '/lifestyle/'));
    } elseif ((int) get_query_var('footerball_matches_page') === 1 || is_post_type_archive('matches')) {
        $items[] = array('@type' => 'ListItem', 'position' => 2, 'name' => 'Matches', 'item' => home_url('/matches/'));
    } elseif (is_search()) {
        $items[] = array('@type' => 'ListItem', 'position' => 2, 'name' => 'Search', 'item' => footerball_current_url_without_query());
    }

    if (is_singular()) {
        if (is_singular('teams')) {
            $breadcrumb_title = footerball_get_team_display_name(get_queried_object_id());
        } elseif (is_singular('players')) {
            $breadcrumb_title = function_exists('footerball_get_player_display_name') ? footerball_get_player_display_name(get_queried_object_id()) : get_the_title();
        } elseif (is_singular('lifestyle')) {
            $breadcrumb_title = footerball_get_lifestyle_display_title(get_queried_object_id());
        } else {
            $breadcrumb_title = get_the_title();
        }
        $items[] = array(
            '@type' => 'ListItem',
            'position' => count($items) + 1,
            'name' => $breadcrumb_title,
            'item' => footerball_current_url_without_query(),
        );
    }

    return array(
        '@type' => 'BreadcrumbList',
        '@id' => footerball_current_url_without_query() . '#breadcrumb',
        'itemListElement' => $items,
    );
}

function footerball_get_collection_item_list_schema($post_type, $archive_url) {
    $posts = get_posts(array(
        'post_type' => $post_type,
        'post_status' => 'publish',
        'posts_per_page' => 12,
        'orderby' => 'menu_order date',
        'order' => 'DESC',
        'no_found_rows' => true,
    ));

    $items = array();
    foreach ($posts as $index => $post_obj) {
        $items[] = array(
            '@type' => 'ListItem',
            'position' => $index + 1,
            'name' => get_the_title($post_obj),
            'url' => get_permalink($post_obj),
        );
    }

    return array(
        '@type' => 'ItemList',
        '@id' => trailingslashit($archive_url) . '#itemlist',
        'itemListElement' => $items,
    );
}

function footerball_get_matches_schema_nodes($canonical) {
    if (!function_exists('footerball_matches_get_schedule_rows')) {
        return array();
    }

    $matches = footerball_matches_get_schedule_rows();
    if (empty($matches) || !is_array($matches)) {
        return array();
    }

    $nodes = array();
    $items = array();
    $status_map = array(
        'scheduled' => 'https://schema.org/EventScheduled',
        'live'      => 'https://schema.org/EventInProgress',
        'finished'  => 'https://schema.org/EventCompleted',
        'postponed' => 'https://schema.org/EventPostponed',
    );

    foreach (array_slice(array_values($matches), 0, 24) as $index => $row) {
        $ts = footerball_match_timestamp($row);
        $home = footerball_match_team_label($row['home'] ?? '');
        $away = footerball_match_team_label($row['away'] ?? '');
        $stadium = !empty($row['stadium']) ? (string) $row['stadium'] : 'Venue TBD';
        $stage = !empty($row['group']) ? (string) $row['group'] : (!empty($row['stage']) ? (string) $row['stage'] : 'Match');
        $status = !empty($row['status']) ? (string) $row['status'] : 'scheduled';
        $event_id = trailingslashit($canonical) . '#match-' . sanitize_title($home . '-' . $away . '-' . ($ts ?: $index));

        $event = array(
            '@type' => 'SportsEvent',
            '@id' => $event_id,
            'name' => sprintf('%s vs %s', $home, $away),
            'url' => $canonical . '#all-matches',
            'description' => sprintf('%s fixture: %s vs %s at %s.', $stage, $home, $away, $stadium),
            'sport' => 'Soccer',
            'eventStatus' => $status_map[$status] ?? 'https://schema.org/EventScheduled',
            'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
            'location' => array(
                '@type' => 'Place',
                'name' => $stadium,
            ),
            'homeTeam' => array('@type' => 'SportsTeam', 'name' => $home, 'sport' => 'Soccer'),
            'awayTeam' => array('@type' => 'SportsTeam', 'name' => $away, 'sport' => 'Soccer'),
            'organizer' => array('@id' => home_url('/#organization')),
        );
        if ($ts) {
            $event['startDate'] = wp_date('c', $ts);
        }

        $nodes[] = $event;
        $items[] = array(
            '@type' => 'ListItem',
            'position' => $index + 1,
            'item' => array('@id' => $event_id),
        );
    }

    if (!empty($items)) {
        array_unshift($nodes, array(
            '@type' => 'ItemList',
            '@id' => trailingslashit($canonical) . '#match-list',
            'name' => '2026 Football Match Schedule',
            'numberOfItems' => count($items),
            'itemListElement' => $items,
        ));
    }

    return $nodes;
}

function footerball_get_homepage_next_match_schema($canonical) {
    if (!function_exists('footerball_home_get_schedule_rows') || !function_exists('footerball_home_pick_next_match')) {
        return array();
    }

    $next_match = footerball_home_pick_next_match(footerball_home_get_schedule_rows());
    if (empty($next_match) || !is_array($next_match)) {
        return array();
    }

    $home = function_exists('footerball_match_team_label') ? footerball_match_team_label($next_match['home'] ?? '') : (string) ($next_match['home']['title'] ?? 'TBD');
    $away = function_exists('footerball_match_team_label') ? footerball_match_team_label($next_match['away'] ?? '') : (string) ($next_match['away']['title'] ?? 'TBD');
    $stadium = !empty($next_match['stadium']) ? (string) $next_match['stadium'] : 'Venue TBD';
    $stage = !empty($next_match['group']) ? (string) $next_match['group'] : (!empty($next_match['stage']) ? (string) $next_match['stage'] : 'Match');
    $ts = !empty($next_match['ts']) ? (int) $next_match['ts'] : (!empty($next_match['datetime']) ? strtotime((string) $next_match['datetime']) : 0);

    $node = array(
        '@type' => 'SportsEvent',
        '@id' => footerball_schema_hash_id($canonical, 'next-match'),
        'name' => sprintf('%s vs %s', $home, $away),
        'url' => home_url('/matches/'),
        'description' => sprintf('%s: %s vs %s at %s.', $stage, $home, $away, $stadium),
        'sport' => 'Soccer',
        'eventStatus' => 'https://schema.org/EventScheduled',
        'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
        'location' => array('@type' => 'Place', 'name' => $stadium),
        'homeTeam' => array('@type' => 'SportsTeam', 'name' => $home, 'sport' => 'Soccer'),
        'awayTeam' => array('@type' => 'SportsTeam', 'name' => $away, 'sport' => 'Soccer'),
        'organizer' => array('@id' => home_url('/#organization')),
    );

    if ($ts) {
        $node['startDate'] = wp_date('c', $ts);
    }

    return $node;
}

function footerball_get_homepage_featured_team_rows() {
    if (!function_exists('footerball_home_team_by_slug')) {
        return array();
    }

    $rows = array();
    foreach (array('brazil', 'argentina', 'france', 'england', 'spain', 'germany', 'portugal', 'netherlands', 'belgium', 'croatia') as $slug) {
        $team = footerball_home_team_by_slug($slug);
        if (empty($team)) {
            continue;
        }
        $rows[] = array(
            'title' => $team['title'] ?? '',
            'link' => $team['link'] ?? '',
            'image' => $team['logo_url'] ?? '',
            '_schema_type' => 'SportsTeam',
        );
    }
    return $rows;
}

function footerball_get_homepage_featured_player_slugs() {
    return array(
        'kylian-mbapp',
        'lionel-messi',
        'lamine-yamal',
        'erling-haaland',
        'cristiano-ronaldo',
        'neymar',
        'harry-kane',
        'jude-bellingham',
    );
}

function footerball_get_homepage_featured_player_labels() {
    return array(
        'kylian-mbapp' => 'Kylian Mbappé',
        'lionel-messi' => 'Lionel Messi',
        'lamine-yamal' => 'Lamine Yamal',
        'erling-haaland' => 'Erling Haaland',
        'cristiano-ronaldo' => 'Cristiano Ronaldo',
        'neymar' => 'Neymar Jr.',
        'harry-kane' => 'Harry Kane',
        'jude-bellingham' => 'Jude Bellingham',
    );
}

function footerball_get_homepage_featured_player_posts() {
    $players = array();
    foreach (footerball_get_homepage_featured_player_slugs() as $slug) {
        $player = get_page_by_path($slug, OBJECT, 'players');
        if ($player && $player->post_status === 'publish') {
            $players[] = $player;
        }
    }
    return $players;
}

function footerball_homepage_featured_player_label($post_id, $fallback = '') {
    $slug = get_post_field('post_name', (int) $post_id);
    $labels = footerball_get_homepage_featured_player_labels();
    if ($slug && isset($labels[$slug])) {
        return $labels[$slug];
    }

    $fallback = trim((string) $fallback);
    if ($fallback !== '') {
        return $fallback;
    }

    $title = get_the_title((int) $post_id);
    return function_exists('footerball_clean_player_display_name') ? footerball_clean_player_display_name($title) : $title;
}

function footerball_get_homepage_player_rows() {
    $players = footerball_get_homepage_featured_player_posts();

    $rows = array();
    foreach ($players as $player) {
        $post_id = (int) $player->ID;
        $thumb_id = get_post_thumbnail_id($post_id);
        if (!$thumb_id && function_exists('get_field')) {
            $thumb_id = (int) get_field('featured_image', $post_id);
        }
        $rows[] = array(
            'title' => footerball_homepage_featured_player_label($post_id),
            'link' => home_url('/players/' . get_post_field('post_name', $post_id) . '/profile/'),
            'image' => $thumb_id ? wp_get_attachment_image_url($thumb_id, 'medium') : '',
            '_schema_type' => 'Person',
        );
    }
    return $rows;
}

function footerball_get_homepage_host_city_rows() {
    $cities = get_posts(array(
        'post_type' => 'host_cities',
        'post_status' => 'publish',
        'posts_per_page' => 6,
        'orderby' => 'title',
        'order' => 'ASC',
        'no_found_rows' => true,
    ));

    $rows = array();
    foreach ($cities as $city) {
        $post_id = (int) $city->ID;
        $stadium = function_exists('get_field') ? (string) get_field('host_city_stadium_name', $post_id) : '';
        $rows[] = array(
            'title' => get_the_title($post_id),
            'link' => get_permalink($post_id),
            'image' => function_exists('footerball_host_city_banner_url') ? footerball_host_city_banner_url($post_id) : '',
            'description' => $stadium ? sprintf('%s host city guide for %s.', get_the_title($post_id), $stadium) : '',
            '_schema_type' => 'TouristDestination',
        );
    }
    return $rows;
}

function footerball_get_homepage_latest_news_schema($canonical) {
    $posts = get_posts(array(
        'post_type' => 'lifestyle',
        'post_status' => 'publish',
        'posts_per_page' => 3,
        'orderby' => 'date',
        'order' => 'DESC',
        'no_found_rows' => true,
    ));
    if (empty($posts)) {
        return array();
    }

    $nodes = array();
    $items = array();
    foreach ($posts as $index => $post_obj) {
        $post_id = (int) $post_obj->ID;
        $url = get_permalink($post_id);
        $article_id = trailingslashit($url) . '#article';
        $image = get_the_post_thumbnail_url($post_id, 'large');
        $excerpt = footerball_get_lifestyle_list_excerpt($post_id, 28);

        $nodes[] = array(
            '@type' => 'NewsArticle',
            '@id' => $article_id,
            'headline' => get_the_title($post_id),
            'url' => $url,
            'description' => $excerpt,
            'image' => $image ? footerball_json_ld_image($image) : array(),
            'datePublished' => get_the_date('c', $post_id),
            'dateModified' => get_the_modified_date('c', $post_id),
            'author' => array('@id' => home_url('/#organization')),
            'publisher' => array('@id' => home_url('/#organization')),
            'mainEntityOfPage' => array('@type' => 'WebPage', '@id' => $url),
        );
        $items[] = array(
            '@type' => 'ListItem',
            'position' => $index + 1,
            'item' => array('@id' => $article_id),
        );
    }

    array_unshift($nodes, array(
        '@type' => 'ItemList',
        '@id' => footerball_schema_hash_id($canonical, 'latest-news'),
        'name' => 'Latest News & Analysis',
        'numberOfItems' => count($items),
        'itemListElement' => $items,
    ));

    return $nodes;
}

function footerball_get_homepage_schema_nodes($canonical) {
    $nodes = array(
        array(
            '@type' => 'SportsEvent',
            '@id' => footerball_schema_hash_id($canonical, 'fifa-world-cup-2026'),
            'name' => 'FIFA World Cup 2026',
            'sport' => 'Soccer',
            'startDate' => '2026-06-11',
            'endDate' => '2026-07-19',
            'organizer' => array('@id' => home_url('/#organization')),
        ),
    );

    $next_match = footerball_get_homepage_next_match_schema($canonical);
    if (!empty($next_match)) {
        $nodes[] = $next_match;
    }

    if (function_exists('footerball_home_static_matchday_essentials')) {
        footerball_schema_append_item_list($nodes, $canonical, footerball_home_static_matchday_essentials(), 'matchday-essentials', 'Matchday Essentials');
    }
    footerball_schema_append_item_list($nodes, $canonical, footerball_get_homepage_featured_team_rows(), 'featured-teams', 'Featured Teams', 'SportsTeam');
    footerball_schema_append_item_list($nodes, $canonical, footerball_get_homepage_player_rows(), 'trending-stars', 'Trending Stars', 'Person');
    footerball_schema_append_item_list($nodes, $canonical, footerball_get_homepage_host_city_rows(), 'host-city-guides', 'Host City Fan Guide', 'TouristDestination');

    foreach (footerball_get_homepage_latest_news_schema($canonical) as $node) {
        $nodes[] = $node;
    }

    return $nodes;
}

function footerball_get_team_news_item_list_schema($team_id, $canonical) {
    $team_id = (int) $team_id;
    if ($team_id <= 0 || !function_exists('footerball_get_team_lifestyle_news')) {
        return array();
    }

    $news_items = footerball_get_team_lifestyle_news($team_id, 8);
    $items = array();
    foreach ($news_items as $index => $news_item) {
        $link = !empty($news_item['link']) ? (string) $news_item['link'] : '';
        if ($link === '' || $link === '#') {
            continue;
        }
        $items[] = array(
            '@type' => 'ListItem',
            'position' => $index + 1,
            'name' => footerball_clean_meta_text($news_item['title'] ?? 'Team update'),
            'url' => esc_url_raw($link),
        );
    }

    if (empty($items)) {
        return array();
    }

    return array(
        '@type' => 'ItemList',
        '@id' => trailingslashit($canonical) . '#team-news-list',
        'name' => footerball_get_team_display_name($team_id) . ' News',
        'itemListElement' => $items,
    );
}

function footerball_schema_hash_id($canonical, $suffix) {
    return rtrim((string) $canonical, '#') . '#' . sanitize_title((string) $suffix);
}

function footerball_schema_field($post_id, $field, $default = '') {
    $post_id = (int) $post_id;
    $value = function_exists('get_field') ? get_field($field, $post_id) : '';
    if ($value === null || $value === '' || (is_array($value) && empty($value))) {
        $meta = get_post_meta($post_id, $field, true);
        $value = ($meta !== '' && $meta !== null) ? maybe_unserialize($meta) : $default;
    }
    return ($value === null || $value === '') ? $default : $value;
}

function footerball_schema_parse_pipe_rows($raw, $columns) {
    $rows = array();
    foreach (preg_split('/\r\n|\r|\n/', trim((string) $raw)) as $line) {
        if (trim($line) === '') {
            continue;
        }
        $parts = array_map('trim', explode('|', $line));
        $row = array();
        foreach ($columns as $index => $column) {
            $row[$column] = $parts[$index] ?? '';
        }
        $rows[] = $row;
    }
    return $rows;
}

function footerball_schema_parse_lines($raw) {
    if ($raw === '' || $raw === null) {
        return array();
    }
    return array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', wp_strip_all_tags((string) $raw)))));
}

function footerball_get_faq_page_schema($canonical, $items, $name = 'FAQ') {
    $entities = array();
    foreach ((array) $items as $item) {
        if (!is_array($item)) {
            continue;
        }
        $question = footerball_clean_meta_text($item['question'] ?? $item['q'] ?? '');
        $answer = footerball_clean_meta_text($item['answer'] ?? $item['a'] ?? '');
        if ($question === '' || $answer === '') {
            continue;
        }
        $entities[] = array(
            '@type' => 'Question',
            'name' => $question,
            'acceptedAnswer' => array(
                '@type' => 'Answer',
                'text' => $answer,
            ),
        );
    }

    if (empty($entities)) {
        return array();
    }

    return array(
        '@type' => 'FAQPage',
        '@id' => footerball_schema_hash_id($canonical, 'faq'),
        'url' => $canonical,
        'name' => footerball_clean_meta_text($name),
        'isPartOf' => array('@id' => $canonical . '#webpage'),
        'mainEntity' => $entities,
    );
}

function footerball_get_lifestyle_author_schema($post_id, $article = null) {
    $post_id = (int) $post_id;
    if (!is_array($article)) {
        $article = footerball_get_lifestyle_article_data($post_id);
    }

    $author_id = absint(get_post_field('post_author', $post_id));
    $name = '';
    if (!empty($article['name']) && is_string($article['name'])) {
        $name = $article['name'];
    } elseif ($author_id) {
        $name = get_the_author_meta('display_name', $author_id);
    }
    $name = footerball_clean_meta_text($name ?: get_bloginfo('name'));

    $author = array(
        '@type' => 'Person',
        'name' => $name,
    );

    if ($author_id) {
        $author['url'] = get_author_posts_url($author_id);
    }

    if (!empty($article['position']) && is_string($article['position'])) {
        $author['jobTitle'] = footerball_clean_meta_text($article['position']);
    }

    if (!empty($article['bio']) && is_string($article['bio'])) {
        $author['description'] = footerball_clean_meta_text($article['bio']);
    }

    return $author;
}

function footerball_lifestyle_schema_clean_faq_text($html) {
    $html = (string) $html;
    if ($html === '') {
        return '';
    }

    $html = preg_replace('/<\/(?:p|div|li|blockquote|h[1-6])>/i', "\n", $html);
    $text = html_entity_decode(wp_strip_all_tags($html, true), ENT_QUOTES, get_bloginfo('charset') ?: 'UTF-8');
    $lines = preg_split('/\r\n|\r|\n/', (string) $text);
    $clean_lines = array();
    foreach ((array) $lines as $line) {
        $line = trim(preg_replace('/\s+/', ' ', (string) $line));
        if ($line === '' || preg_match('/^Source signal\s*:/i', $line)) {
            continue;
        }
        $clean_lines[] = $line;
    }

    return footerball_clean_meta_text(implode(' ', $clean_lines));
}

function footerball_get_lifestyle_faq_schema_items($post_id) {
    $post_id = (int) $post_id;
    if ($post_id <= 0) {
        return array();
    }

    $article = footerball_get_lifestyle_article_data($post_id);
    $article_title = footerball_get_lifestyle_display_title($post_id, $article);
    $content = !empty($article['post_content_main']) ? $article['post_content_main'] : get_post_field('post_content', $post_id);
    $content = footerball_clean_lifestyle_article_content($content, $article_title);
    if ($content === '') {
        return array();
    }

    preg_match_all('/<h2\b[^>]*>.*?<\/h2>/is', $content, $headings, PREG_OFFSET_CAPTURE);
    if (empty($headings[0])) {
        return array();
    }

    $faq_start = null;
    $faq_end = strlen($content);
    foreach ($headings[0] as $index => $heading) {
        $heading_text = footerball_lifestyle_schema_clean_faq_text($heading[0]);
        if (strcasecmp($heading_text, 'FAQ') !== 0) {
            continue;
        }

        $faq_start = $heading[1] + strlen($heading[0]);
        if (!empty($headings[0][$index + 1])) {
            $faq_end = $headings[0][$index + 1][1];
        }
        break;
    }

    if ($faq_start === null) {
        return array();
    }

    $faq_html = substr($content, $faq_start, max(0, $faq_end - $faq_start));
    preg_match_all('/<h3\b[^>]*>(.*?)<\/h3>/is', $faq_html, $questions, PREG_OFFSET_CAPTURE);
    if (empty($questions[0])) {
        return array();
    }

    $items = array();
    foreach ($questions[0] as $index => $question_heading) {
        $question = footerball_lifestyle_schema_clean_faq_text($questions[1][$index][0] ?? '');
        if ($question === '') {
            continue;
        }

        $answer_start = $question_heading[1] + strlen($question_heading[0]);
        $answer_end = !empty($questions[0][$index + 1]) ? $questions[0][$index + 1][1] : strlen($faq_html);
        $answer = footerball_lifestyle_schema_clean_faq_text(substr($faq_html, $answer_start, max(0, $answer_end - $answer_start)));
        if ($answer === '') {
            continue;
        }

        $items[] = array(
            'question' => $question,
            'answer' => $answer,
        );
    }

    return $items;
}

function footerball_schema_image_url($image, $size = 'medium') {
    if (is_numeric($image)) {
        return wp_get_attachment_image_url((int) $image, $size) ?: '';
    }
    return esc_url_raw((string) $image);
}

function footerball_schema_text($value) {
    return html_entity_decode(footerball_clean_meta_text($value), ENT_QUOTES, 'UTF-8');
}

function footerball_schema_catalog_item($row) {
    if (!is_array($row)) {
        return array();
    }

    $name = $row['name'] ?? $row['title'] ?? $row['rg_name'] ?? $row['coll_name'] ?? $row['category_name'] ?? '';
    $url = $row['url'] ?? $row['link'] ?? $row['rg_link'] ?? $row['coll_link'] ?? $row['category_link'] ?? '';
    $image = $row['image'] ?? $row['thumb'] ?? $row['rg_image'] ?? $row['coll_image'] ?? $row['category_image'] ?? '';
    $price = $row['price'] ?? $row['rg_price'] ?? $row['coll_price'] ?? '';
    $description = $row['description'] ?? $row['excerpt'] ?? $row['desc'] ?? $row['rg_reason'] ?? $row['category_desc'] ?? '';
    $rating = $row['rating'] ?? $row['rg_rating'] ?? '';

    $name = footerball_schema_text($name);
    if ($name === '') {
        return array();
    }

    return array(
        'name' => $name,
        'url' => esc_url_raw((string) $url),
        'image' => footerball_schema_image_url($image),
        'price' => footerball_schema_text($price),
        'description' => footerball_schema_text($description),
        'rating' => footerball_schema_text($rating),
        'item_code' => footerball_schema_text($row['item_code'] ?? $row['sku'] ?? ''),
        'type' => footerball_schema_text($row['_schema_type'] ?? $row['type'] ?? 'Thing'),
    );
}

function footerball_schema_price_parts($price) {
    $price = footerball_clean_meta_text($price);
    if ($price === '' || stripos($price, 'view') !== false || stripos($price, 'search') !== false) {
        return array();
    }

    $currency = '';
    if (preg_match('/(?:US\s*)?\$\s*([0-9][0-9,]*(?:\.[0-9]+)?)/i', $price, $match)) {
        $currency = 'USD';
    } elseif (preg_match('/USD\s*([0-9][0-9,]*(?:\.[0-9]+)?)/i', $price, $match)) {
        $currency = 'USD';
    } else {
        return array();
    }

    return array(
        'price' => str_replace(',', '', $match[1]),
        'priceCurrency' => $currency,
    );
}

function footerball_schema_rating($rating) {
    $rating = footerball_clean_meta_text($rating);
    if ($rating === '' || !preg_match('/([0-5](?:\.[0-9]+)?)(?:\s*\/\s*([0-9,]+)\s*reviews?)?/i', $rating, $match)) {
        return array();
    }

    $node = array(
        '@type' => 'AggregateRating',
        'ratingValue' => $match[1],
        'bestRating' => '5',
        'worstRating' => '1',
    );
    if (!empty($match[2])) {
        $node['reviewCount'] = str_replace(',', '', $match[2]);
    }
    return $node;
}

function footerball_schema_is_reliable_product($item) {
    if (empty($item['name']) || empty($item['url']) || empty($item['image'])) {
        return false;
    }
    if (stripos($item['name'], 'search ') === 0 || strpos($item['url'], 'search.do') !== false) {
        return false;
    }
    if (!preg_match('#^https?://(?:www\.)?dhgate\.com/product/#i', $item['url'])) {
        return false;
    }
    return !empty(footerball_schema_price_parts($item['price'] ?? ''));
}

function footerball_schema_product_node($item, $canonical, $index, $suffix = 'product') {
    if (!footerball_schema_is_reliable_product($item)) {
        return array();
    }

    $price = footerball_schema_price_parts($item['price']);
    $node = array(
        '@type' => 'Product',
        '@id' => footerball_schema_hash_id($canonical, $suffix . '-' . $index),
        'name' => $item['name'],
        'url' => $item['url'],
        'image' => $item['image'],
        'description' => !empty($item['description']) ? $item['description'] : $item['name'],
        'offers' => array(
            '@type' => 'Offer',
            'url' => $item['url'],
            'price' => $price['price'],
            'priceCurrency' => $price['priceCurrency'],
            'availability' => 'https://schema.org/InStock',
            'seller' => array('@type' => 'Organization', 'name' => 'DHgate'),
        ),
    );

    if (!empty($item['item_code'])) {
        $node['sku'] = $item['item_code'];
    }

    $rating = footerball_schema_rating($item['rating'] ?? '');
    if (!empty($rating)) {
        $node['aggregateRating'] = $rating;
    }

    return $node;
}

function footerball_schema_item_list_with_products($canonical, $rows, $id_suffix, $name, $default_type = 'Thing') {
    $list_items = array();
    $product_nodes = array();
    $seen = array();

    foreach ((array) $rows as $row) {
        $item = footerball_schema_catalog_item($row);
        if (empty($item['name'])) {
            continue;
        }

        $key = strtolower($item['name'] . '|' . $item['url']);
        if (isset($seen[$key])) {
            continue;
        }
        $seen[$key] = true;

        $position = count($list_items) + 1;
        $product_node = footerball_schema_product_node($item, $canonical, $position, $id_suffix . '-product');
        if (!empty($product_node)) {
            $product_nodes[] = $product_node;
            $list_item_target = array('@id' => $product_node['@id']);
        } else {
            $thing_type = !empty($item['type']) && $item['type'] !== 'Thing' ? $item['type'] : $default_type;
            $list_item_target = array('@type' => $thing_type, 'name' => $item['name']);
            if (!empty($item['url'])) {
                $list_item_target['url'] = $item['url'];
            }
            if (!empty($item['image'])) {
                $list_item_target['image'] = footerball_json_ld_image($item['image']);
            }
            if (!empty($item['description'])) {
                $list_item_target['description'] = $item['description'];
            }
        }

        $list_items[] = array(
            '@type' => 'ListItem',
            'position' => $position,
            'item' => $list_item_target,
        );
    }

    if (empty($list_items)) {
        return array('list' => array(), 'products' => array());
    }

    return array(
        'list' => array(
            '@type' => 'ItemList',
            '@id' => footerball_schema_hash_id($canonical, $id_suffix),
            'name' => footerball_clean_meta_text($name),
            'numberOfItems' => count($list_items),
            'itemListElement' => $list_items,
        ),
        'products' => $product_nodes,
    );
}

function footerball_schema_append_item_list(&$graph, $canonical, $rows, $id_suffix, $name, $default_type = 'Thing') {
    $schema = footerball_schema_item_list_with_products($canonical, $rows, $id_suffix, $name, $default_type);
    if (!empty($schema['list'])) {
        $graph[] = $schema['list'];
    }
    foreach ($schema['products'] as $product_node) {
        $graph[] = $product_node;
    }
}

function footerball_get_player_faq_schema_items($post_id) {
    $items = footerball_schema_field($post_id, 'player_faq', array());
    if (!empty($items) && is_array($items)) {
        return array_map(static function ($item) {
            if (!is_array($item)) {
                return $item;
            }
            foreach (array('question', 'q', 'answer', 'a') as $key) {
                if (!isset($item[$key]) || !is_string($item[$key])) {
                    continue;
                }
                $item[$key] = footerball_clean_player_display_name($item[$key]);
            }
            return $item;
        }, $items);
    }

    $name = function_exists('footerball_get_player_display_name') ? footerball_get_player_display_name($post_id) : get_the_title($post_id);
    $country = footerball_schema_field($post_id, 'country', '');
    $club = footerball_schema_field($post_id, 'club', '');
    $squad_answer = $country
        ? sprintf('%s is expected to be an important squad option for %s, with final shirt numbers subject to the official tournament roster.', $name, $country)
        : sprintf('%s is expected to be an important squad option, with final shirt numbers subject to the official tournament roster.', $name);
    $club_answer = $club
        ? sprintf('%s currently plays for %s.', $name, $club)
        : sprintf('%s club details should be checked against the latest official roster before matchday.', $name);

    return array(
        array('question' => 'What jersey number will he wear at the 2026 World Cup?', 'answer' => $squad_answer),
        array('question' => 'Which club does he currently play for?', 'answer' => $club_answer),
        array('question' => 'How has he performed for club and country?', 'answer' => sprintf('%s brings club and international experience into the 2026 World Cup cycle.', $name)),
        array('question' => 'Where can I buy his jersey and merchandise?', 'answer' => 'Use the Gear tab for DHgate football jersey, boots, collectibles, and fan accessory ideas.'),
    );
}

function footerball_player_measurement_schema($value, $default_unit) {
    $value = footerball_clean_meta_text($value);
    if ($value === '' || !preg_match('/([0-9]+(?:\.[0-9]+)?)/', $value, $match)) {
        return array();
    }

    $unit = $default_unit;
    if (preg_match('/\b(cm|kg|m|lb|lbs|ft|in)\b/i', $value, $unit_match)) {
        $unit = strtolower($unit_match[1]);
        if ($unit === 'lbs') {
            $unit = 'lb';
        }
    }

    return array(
        '@type' => 'QuantitativeValue',
        'value' => $match[1],
        'unitText' => $unit,
    );
}

function footerball_player_member_of_schema($post_id, $team_id = 0) {
    $memberships = array();
    $seen = array();
    $add_team = static function ($name, $url = '') use (&$memberships, &$seen) {
        $name = footerball_clean_meta_text($name);
        if ($name === '') {
            return;
        }

        $key = strtolower($name);
        if (isset($seen[$key])) {
            return;
        }
        $seen[$key] = true;

        $team = array(
            '@type' => 'SportsTeam',
            'name' => $name,
            'sport' => 'Soccer',
        );
        if ($url) {
            $team['url'] = esc_url_raw($url);
        }
        $memberships[] = $team;
    };

    $team_id = absint($team_id);
    if ($team_id) {
        $team_url = function_exists('footerball_team_tab_url') ? footerball_team_tab_url($team_id, 'profile') : get_permalink($team_id);
        $add_team(footerball_get_team_display_name($team_id), $team_url);
    }

    $country = footerball_schema_field($post_id, 'country', '');
    if ($country) {
        $add_team($country);
    }

    $club = footerball_schema_field($post_id, 'club', '');
    if ($club) {
        $add_team($club);
    }

    return $memberships;
}

function footerball_get_player_gear_schema_rows($post_id) {
    $name = get_the_title($post_id);
    $country = footerball_schema_field($post_id, 'country', '');

    $categories = footerball_schema_field($post_id, 'gear_categories', array());
    if (empty($categories) || !is_array($categories)) {
        $categories = array(
            array('category_name' => 'Official Jerseys', 'category_desc' => 'Home, away, and custom name-set shirts for match days.', 'category_link' => footerball_dhgate_search_url($name . ' jersey')),
            array('category_name' => 'On-Pitch Boots', 'category_desc' => 'Speed-focused soccer cleats inspired by elite players.', 'category_link' => footerball_dhgate_search_url('Mercurial soccer cleats')),
            array('category_name' => 'Fan Accessories', 'category_desc' => 'Scarves, flags, patches, and stadium-ready support pieces.', 'category_link' => footerball_dhgate_search_url(($country ?: $name) . ' football scarf')),
            array('category_name' => 'Collectibles', 'category_desc' => 'Cards, posters, display pieces, and signed-style memorabilia.', 'category_link' => footerball_dhgate_search_url($name . ' trading card')),
        );
    }

    $recommended = footerball_schema_field($post_id, 'recommended_gear', array());
    if (empty($recommended) || !is_array($recommended)) {
        $recommended = array(
            array('rg_name' => 'Search ' . $name . ' Gear on DHgate', 'rg_price' => 'View on DHgate', 'rg_reason' => 'Find jerseys, boots, accessories, and fan gear.', 'rg_link' => footerball_dhgate_search_url($name . ' gear')),
        );
    }

    $collectibles = footerball_schema_field($post_id, 'collectibles', array());
    if (empty($collectibles) || !is_array($collectibles)) {
        $collectibles = array(
            array('coll_name' => 'Search ' . $name . ' Collectibles', 'coll_tag' => 'Search DHgate', 'coll_price' => 'View on DHgate', 'coll_link' => footerball_dhgate_search_url($name . ' collectible')),
        );
    }

    return array(
        'categories' => $categories,
        'recommended' => $recommended,
        'collectibles' => $collectibles,
    );
}

function footerball_get_team_faq_schema_items($post_id) {
    $team_name = footerball_get_team_display_name($post_id);
    $wc_prediction = footerball_schema_field($post_id, 'world_cup_prediction', 'Target: reach the knockout stage and push for a quarter-final or better.');
    $items = footerball_schema_parse_pipe_rows(get_post_meta($post_id, '_team_faq_items_data', true), array('question', 'answer'));
    if (empty($items)) {
        $items = footerball_schema_field($post_id, 'faq_items', array());
    }
    if (!empty($items) && is_array($items)) {
        return $items;
    }
    return array(
        array('question' => 'What is ' . $team_name . ' targeting for Football 2026?', 'answer' => $wc_prediction),
        array('question' => 'Where can I buy ' . $team_name . ' gear?', 'answer' => 'Use the Official Store tab for static DHgate product picks and search links.'),
    );
}

function footerball_team_year_schema_field($post_id, $field) {
    $value = footerball_clean_meta_text(footerball_schema_field($post_id, $field, ''));
    if ($value === '') {
        return '';
    }
    if (preg_match('/\b(18|19|20)\d{2}\b/', $value, $match)) {
        return $match[0];
    }
    return $value;
}

function footerball_get_team_profile_schema_fields($post_id) {
    $fields = array();

    $founded = footerball_team_year_schema_field($post_id, 'team_founded');
    if ($founded !== '') {
        $fields['foundingDate'] = $founded;
    }

    $confederation = footerball_clean_meta_text(footerball_schema_field($post_id, 'team_confederation', ''));
    if ($confederation !== '') {
        $fields['memberOf'] = array(
            '@type' => 'SportsOrganization',
            'name' => $confederation,
        );
    }

    $coach = footerball_clean_meta_text(footerball_schema_field($post_id, 'team_coach', ''));
    if ($coach !== '') {
        $fields['coach'] = array(
            '@type' => 'Person',
            'name' => $coach,
        );
    }

    $stadium = footerball_clean_meta_text(footerball_schema_field($post_id, 'team_stadium', ''));
    if ($stadium !== '') {
        $fields['location'] = array(
            '@type' => 'Place',
            'name' => $stadium,
        );
    }

    $same_as = array();
    foreach (array('team_twitter', 'team_instagram', 'team_facebook', 'team_youtube', 'team_tiktok') as $field) {
        $url = esc_url_raw((string) footerball_schema_field($post_id, $field, ''));
        if ($url !== '') {
            $same_as[] = $url;
        }
    }
    if (!empty($same_as)) {
        $fields['sameAs'] = array_values(array_unique($same_as));
    }

    return $fields;
}

function footerball_get_team_merch_schema_rows($post_id) {
    $team_name = footerball_get_team_display_name($post_id);
    $rows = footerball_schema_parse_pipe_rows(get_post_meta($post_id, '_team_official_merch_data', true), array('name', 'price', 'link', 'image'));
    if (!empty($rows)) {
        return $rows;
    }
    return array(
        array('name' => 'Search ' . $team_name . ' Gear', 'price' => 'View on DHgate', 'link' => footerball_dhgate_search_url($team_name . ' gear'), 'image' => ''),
    );
}

function footerball_get_team_player_schema_rows($post_id, $limit = 24) {
    $players = get_posts(array(
        'post_type' => 'players',
        'post_status' => 'publish',
        'posts_per_page' => $limit,
        'orderby' => 'title',
        'order' => 'ASC',
        'meta_query' => array(array('key' => 'player_team', 'value' => (string) $post_id, 'compare' => '=')),
        'no_found_rows' => true,
    ));

    $rows = array();
    foreach ($players as $player) {
        $pid = (int) $player->ID;
        $image_id = get_post_thumbnail_id($pid) ?: (function_exists('get_field') ? get_field('featured_image', $pid) : 0);
        $rows[] = array(
            'name' => function_exists('footerball_get_player_display_name') ? footerball_get_player_display_name($pid) : get_the_title($pid),
            'link' => function_exists('footerball_player_tab_url') ? footerball_player_tab_url($pid, 'profile') : get_permalink($pid),
            'image' => $image_id ? wp_get_attachment_image_url((int) $image_id, 'medium') : '',
            'description' => footerball_clean_meta_text((function_exists('get_field') ? get_field('position', $pid) : '') ?: 'Football player'),
            '_schema_type' => 'Person',
        );
    }
    return $rows;
}

function footerball_schema_detail_products($type, $post_id) {
    $path = get_stylesheet_directory() . '/footerball_detail_products.json';
    if (!is_readable($path)) {
        $path = dirname(get_stylesheet_directory()) . '/footerball_detail_products.json';
    }
    if (!is_readable($path)) {
        return array();
    }

    $decoded = json_decode(file_get_contents($path), true);
    if (!is_array($decoded) || empty($decoded[$type][(string) $post_id])) {
        return array();
    }
    return is_array($decoded[$type][(string) $post_id]) ? $decoded[$type][(string) $post_id] : array();
}

function footerball_get_host_city_product_schema_rows($post_id) {
    $city_name = get_the_title($post_id);
    $rows = array();
    foreach (footerball_schema_parse_lines(footerball_schema_field($post_id, 'host_city_official_gear', '')) as $line) {
        $parts = array_map('trim', explode('|', $line));
        if (!empty($parts[0])) {
            $rows[] = array(
                'name' => $parts[0],
                'price' => $parts[1] ?? '',
                'link' => (!empty($parts[2]) && $parts[2] !== '#') ? $parts[2] : footerball_dhgate_search_url($parts[0] . ' ' . $city_name . ' football gear'),
                'image' => $parts[3] ?? '',
                'rating' => $parts[4] ?? '',
            );
        }
    }
    if (!empty($rows)) {
        return $rows;
    }

    $json_rows = footerball_schema_detail_products('host_cities', $post_id);
    if (!empty($json_rows)) {
        return array_slice($json_rows, 0, 8);
    }

    $search_url = footerball_dhgate_search_url($city_name . ' 2026 football gear');
    return array(
        array('name' => $city_name . ' Host City Home Jersey 2026', 'price' => 'US $69.99', 'link' => $search_url, 'image' => '', 'rating' => ''),
        array('name' => $city_name . ' Host City Scarf 2026', 'price' => 'US $24.99', 'link' => $search_url, 'image' => '', 'rating' => ''),
        array('name' => $city_name . ' Host City Cap 2026', 'price' => 'US $29.99', 'link' => $search_url, 'image' => '', 'rating' => ''),
        array('name' => $city_name . ' Matchday Mug 2026', 'price' => 'US $16.99', 'link' => $search_url, 'image' => '', 'rating' => ''),
    );
}

function footerball_get_host_city_fixture_schema_rows($post_id) {
    $city_name = get_the_title($post_id);
    $stadium_name = footerball_schema_field($post_id, 'host_city_stadium_name', 'BMO Field');
    $fixtures = array();
    foreach (footerball_schema_parse_lines(footerball_schema_field($post_id, 'host_city_fixtures', '')) as $line) {
        $parts = array_map('trim', explode('|', $line));
        if (!empty($parts[0])) {
            $fixtures[] = array(
                'datetime' => $parts[0],
                'stage' => $parts[1] ?? 'Group Stage',
                'home_team' => $parts[2] ?? 'TBD',
                'away_team' => $parts[3] ?? 'TBD',
                'status' => $parts[4] ?? 'Upcoming',
            );
        }
    }
    if (empty($fixtures)) {
        $fixtures = array(
            array('datetime' => 'Fri, Jun 13, 2026 · 15:00', 'stage' => 'Group Stage', 'home_team' => 'Canada', 'away_team' => 'Morocco', 'status' => 'Upcoming'),
            array('datetime' => 'Wed, Jun 18, 2026 · 18:00', 'stage' => 'Group Stage', 'home_team' => 'Brazil', 'away_team' => 'Japan', 'status' => 'Selling Fast'),
            array('datetime' => 'Sun, Jun 22, 2026 · 20:00', 'stage' => 'Group Stage', 'home_team' => 'England', 'away_team' => 'USA', 'status' => 'Selling Fast'),
        );
    }

    $rows = array();
    foreach (array_slice($fixtures, 0, 12) as $fixture) {
        $home = footerball_clean_meta_text($fixture['home_team'] ?? 'TBD');
        $away = footerball_clean_meta_text($fixture['away_team'] ?? 'TBD');
        $rows[] = array(
            'name' => $home . ' vs ' . $away,
            'link' => home_url('/matches/#all-matches'),
            'description' => footerball_clean_meta_text(($fixture['stage'] ?? 'Match') . ' at ' . $stadium_name . ', ' . $city_name . '. ' . ($fixture['status'] ?? 'Upcoming')),
            '_schema_type' => 'SportsEvent',
        );
    }
    return $rows;
}

function footerball_get_host_city_faq_schema_items($tab) {
    if ($tab === 'stadium-day-guide') {
        return array(
            array('question' => 'What time should I arrive?', 'answer' => 'Plan to arrive at least two hours before kickoff so you have time for transit, security screening, food, and finding your section.'),
            array('question' => 'Can I bring a bag?', 'answer' => 'Bring only a small approved bag. Oversized backpacks, luggage, and hard-sided bags may be refused at the gate.'),
            array('question' => 'Where can I find food and restrooms?', 'answer' => 'Food stands, restrooms, and guest services are available across the concourses. Follow in-stadium signs once you enter.'),
            array('question' => 'Do I need a mobile ticket?', 'answer' => 'Mobile ticketing is expected for most matches. Charge your phone and save your ticket before leaving for the stadium.'),
        );
    }

    return array(
        array('question' => 'Can I transfer my ticket?', 'answer' => 'Digital transfer is usually available through the official ticketing app.'),
        array('question' => 'Is mobile ticketing available?', 'answer' => 'Yes. Charge your phone and save your ticket before arriving.'),
        array('question' => 'What items are not allowed?', 'answer' => 'Large bags, outside food, drones, flares, and professional cameras are restricted.'),
    );
}

function footerball_get_json_ld_graph() {
    $meta = footerball_get_seo_meta();
    $canonical = !empty($meta['canonical']) ? $meta['canonical'] : footerball_current_url_without_query();
    $title = footerball_clean_meta_text($meta['title']);
    $description = footerball_clean_meta_text($meta['description']);
    $image = !empty($meta['og_image']) ? $meta['og_image'] : footerball_default_og_image_url();
    $breadcrumb_id = footerball_current_url_without_query() . '#breadcrumb';
    $is_collection_without_breadcrumb_schema = (
        is_front_page()
        || is_home()
        || (int) get_query_var('footerball_matches_page') === 1
        || is_post_type_archive(array('players', 'teams', 'host_cities', 'lifestyle', 'matches'))
    );
    $include_breadcrumb_schema = !$is_collection_without_breadcrumb_schema;

    $graph = array(
        array(
            '@type' => 'Organization',
            '@id' => home_url('/#organization'),
            'name' => 'DHgate Football 2026',
            'url' => home_url('/'),
            'logo' => footerball_json_ld_image(footerball_default_og_image_url()),
        ),
        array(
            '@type' => 'WebSite',
            '@id' => home_url('/#website'),
            'name' => 'DHgate Football 2026',
            'url' => home_url('/'),
            'publisher' => array('@id' => home_url('/#organization')),
            'potentialAction' => array(
                '@type' => 'SearchAction',
                'target' => home_url('/?s={search_term_string}'),
                'query-input' => 'required name=search_term_string',
            ),
        ),
    );
    if ($include_breadcrumb_schema) {
        $graph[] = footerball_get_breadcrumb_schema();
    }

    $page_schema = array(
        '@type' => 'WebPage',
        '@id' => $canonical . '#webpage',
        'url' => $canonical,
        'name' => $title,
        'description' => $description,
        'isPartOf' => array('@id' => home_url('/#website')),
        'primaryImageOfPage' => footerball_json_ld_image($image),
    );
    if ($include_breadcrumb_schema) {
        $page_schema['breadcrumb'] = array('@id' => $breadcrumb_id);
    }

    if (is_front_page() || is_home()) {
        $page_schema['@type'] = 'CollectionPage';
        $page_schema['name'] = 'The Ultimate 2026 Football Fan Hub | DHgate';
        $page_schema['description'] = 'From 48-team rosters and live schedules to local guides and lifestyle gear. Everything a passionate fan needs, all in one place.';
        $page_schema['about'] = array('@id' => footerball_schema_hash_id($canonical, 'fifa-world-cup-2026'));
        $page_schema['mainEntity'] = array('@id' => footerball_schema_hash_id($canonical, 'fifa-world-cup-2026'));
        $page_schema['hasPart'] = array(
            array('@id' => footerball_schema_hash_id($canonical, 'next-match')),
            array('@id' => footerball_schema_hash_id($canonical, 'matchday-essentials')),
            array('@id' => footerball_schema_hash_id($canonical, 'featured-teams')),
            array('@id' => footerball_schema_hash_id($canonical, 'trending-stars')),
            array('@id' => footerball_schema_hash_id($canonical, 'host-city-guides')),
            array('@id' => footerball_schema_hash_id($canonical, 'latest-news')),
        );
        $graph = array_merge($graph, footerball_get_homepage_schema_nodes($canonical));
    }

    if (footerball_is_about_us_request()) {
        $page_schema['@type'] = 'AboutPage';
        $page_schema['about'] = 'DHgate Football Hub editorial operations and relationship with DHgate.com';
        $page_schema['mainEntity'] = array('@id' => home_url('/#organization'));
    } elseif ((int) get_query_var('footerball_matches_page') === 1 || is_post_type_archive('matches')) {
        $page_schema['@type'] = 'CollectionPage';
        $page_schema['about'] = '2026 football match schedule';
        $graph = array_merge($graph, footerball_get_matches_schema_nodes($canonical));
    } elseif (is_post_type_archive('teams')) {
        $page_schema['@type'] = 'CollectionPage';
        $graph[] = footerball_get_collection_item_list_schema('teams', $canonical);
    } elseif (is_post_type_archive('players')) {
        $page_schema['@type'] = 'CollectionPage';
        $graph[] = footerball_get_collection_item_list_schema('players', $canonical);
    } elseif (is_post_type_archive('host_cities')) {
        $page_schema['@type'] = 'CollectionPage';
        $graph[] = footerball_get_collection_item_list_schema('host_cities', $canonical);
    } elseif (is_post_type_archive('lifestyle')) {
        $page_schema['@type'] = 'CollectionPage';
        $graph[] = footerball_get_collection_item_list_schema('lifestyle', $canonical);
    } elseif (is_singular('teams')) {
        $post_id = get_queried_object_id();
        $team_tab = get_query_var('team_tab');
        $team_tab = in_array($team_tab, array('profile', 'gear', 'players', 'news'), true) ? $team_tab : 'profile';
        $team_logo = (function_exists('footerball_team_logo_asset_url') ? footerball_team_logo_asset_url($post_id) : '') ?: footerball_get_attachment_or_thumbnail_url($post_id, 'team_logo') ?: $image;
        $team_schema = array(
            '@type' => 'SportsTeam',
            '@id' => $canonical . '#team',
            'name' => footerball_get_team_display_name($post_id),
            'url' => $canonical,
            'sport' => 'Soccer',
            'logo' => footerball_json_ld_image($team_logo),
            'image' => footerball_json_ld_image($image),
            'description' => $description,
        );
        if ($team_tab === 'profile') {
            $team_schema = array_merge($team_schema, footerball_get_team_profile_schema_fields($post_id));
        }
        $graph[] = $team_schema;
        if ($team_tab === 'profile') {
            $faq_schema = footerball_get_faq_page_schema($canonical, footerball_get_team_faq_schema_items($post_id), footerball_get_team_display_name($post_id) . ' FAQ');
            if (!empty($faq_schema)) {
                $graph[] = $faq_schema;
            }
        } elseif ($team_tab === 'gear') {
            $page_schema['@type'] = 'CollectionPage';
            footerball_schema_append_item_list($graph, $canonical, footerball_get_team_merch_schema_rows($post_id), 'recommended-gear-list', footerball_get_team_display_name($post_id) . ' Recommended Gear');
        } elseif ($team_tab === 'players') {
            $page_schema['@type'] = 'CollectionPage';
            footerball_schema_append_item_list($graph, $canonical, footerball_get_team_player_schema_rows($post_id), 'player-directory-list', footerball_get_team_display_name($post_id) . ' Player Directory', 'Person');
        } elseif ($team_tab === 'news') {
            $page_schema['@type'] = 'CollectionPage';
            $team_news_schema = footerball_get_team_news_item_list_schema($post_id, $canonical);
            if (!empty($team_news_schema)) {
                $graph[] = $team_news_schema;
            }
        }
        $page_schema['mainEntity'] = array('@id' => $canonical . '#team');
    } elseif (is_singular('players')) {
        $post_id = get_queried_object_id();
        $team_id = function_exists('get_field') ? absint(get_field('player_team', $post_id)) : 0;
        $player_tab = get_query_var('player_tab');
        $player_tab = in_array($player_tab, array('profile', 'gear', 'stats', 'updates'), true) ? $player_tab : 'profile';
        if ($player_tab === 'profile') {
            $page_schema['@type'] = 'ProfilePage';
        }
        $person = array(
            '@type' => 'Person',
            '@id' => $canonical . '#person',
            'name' => function_exists('footerball_get_player_display_name') ? footerball_get_player_display_name($post_id) : get_the_title($post_id),
            'url' => $canonical,
            'image' => footerball_json_ld_image($image),
            'description' => $description,
            'jobTitle' => function_exists('get_field') ? get_field('position', $post_id) : '',
            'nationality' => function_exists('get_field') ? get_field('country', $post_id) : '',
            'birthDate' => function_exists('get_field') ? get_field('birthday', $post_id) : '',
        );
        $height = footerball_player_measurement_schema(function_exists('get_field') ? get_field('height', $post_id) : '', 'cm');
        if (!empty($height)) {
            $person['height'] = $height;
        }
        $weight = footerball_player_measurement_schema(function_exists('get_field') ? get_field('weight', $post_id) : '', 'kg');
        if (!empty($weight)) {
            $person['weight'] = $weight;
        }
        $member_of = footerball_player_member_of_schema($post_id, $team_id);
        if (!empty($member_of)) {
            $person['memberOf'] = $member_of;
        }
        $graph[] = $person;
        if ($player_tab === 'profile') {
            $player_display_name = function_exists('footerball_get_player_display_name') ? footerball_get_player_display_name($post_id) : get_the_title($post_id);
            $faq_schema = footerball_get_faq_page_schema($canonical, footerball_get_player_faq_schema_items($post_id), $player_display_name . ' FAQ');
            if (!empty($faq_schema)) {
                $graph[] = $faq_schema;
            }
        } elseif ($player_tab === 'gear') {
            $page_schema['@type'] = 'CollectionPage';
            $gear_rows = footerball_get_player_gear_schema_rows($post_id);
            footerball_schema_append_item_list($graph, $canonical, $gear_rows['categories'], 'gear-category-list', get_the_title($post_id) . ' Gear Categories');
            footerball_schema_append_item_list($graph, $canonical, $gear_rows['recommended'], 'matchday-gear-list', get_the_title($post_id) . ' Matchday Gear Picks');
            footerball_schema_append_item_list($graph, $canonical, $gear_rows['collectibles'], 'collectibles-list', get_the_title($post_id) . ' Collectibles and Merch');
        }
        $page_schema['mainEntity'] = array('@id' => $canonical . '#person');
    } elseif (is_singular('host_cities')) {
        $post_id = get_queried_object_id();
        $city = get_the_title($post_id);
        $stadium = function_exists('get_field') ? get_field('host_city_stadium_name', $post_id) : '';
        $graph[] = array(
            '@type' => 'TouristDestination',
            '@id' => $canonical . '#destination',
            'name' => $city,
            'url' => $canonical,
            'image' => footerball_json_ld_image($image),
            'description' => $description,
            'touristType' => 'Football fans',
            'containsPlace' => $stadium ? array('@type' => 'StadiumOrArena', 'name' => $stadium) : array(),
        );
        $city_tab = get_query_var('host_city_tab');
        $city_tab = in_array($city_tab, array('ticket-information', 'fixtures', 'stadium-day-guide', 'official-gear'), true) ? $city_tab : 'ticket-information';
        if ($city_tab === 'ticket-information') {
            $faq_schema = footerball_get_faq_page_schema($canonical, footerball_get_host_city_faq_schema_items($city_tab), $city . ' Ticket FAQ');
            if (!empty($faq_schema)) {
                $graph[] = $faq_schema;
            }
            footerball_schema_append_item_list($graph, $canonical, footerball_get_host_city_fixture_schema_rows($post_id), 'ticket-availability-list', $city . ' Ticket Availability', 'SportsEvent');
        } elseif ($city_tab === 'fixtures') {
            $page_schema['@type'] = 'CollectionPage';
            footerball_schema_append_item_list($graph, $canonical, footerball_get_host_city_fixture_schema_rows($post_id), 'fixture-list', $city . ' Match Fixtures', 'SportsEvent');
        } elseif ($city_tab === 'stadium-day-guide') {
            $faq_schema = footerball_get_faq_page_schema($canonical, footerball_get_host_city_faq_schema_items($city_tab), $city . ' Stadium Day FAQ');
            if (!empty($faq_schema)) {
                $graph[] = $faq_schema;
            }
        } elseif ($city_tab === 'official-gear') {
            $page_schema['@type'] = 'CollectionPage';
            footerball_schema_append_item_list($graph, $canonical, footerball_get_host_city_product_schema_rows($post_id), 'host-city-gear-list', $city . ' Official Gear');
        }
        $page_schema['mainEntity'] = array('@id' => $canonical . '#destination');
    } elseif (is_singular('lifestyle')) {
        $post_id = get_queried_object_id();
        $article = footerball_get_lifestyle_article_data($post_id);
        $article_title = footerball_get_lifestyle_display_title($post_id, $article);
        $page_schema['name'] = $article_title;
        $page_schema['mainEntity'] = array('@id' => $canonical . '#article');
        $graph[] = array(
            '@type' => 'Article',
            '@id' => $canonical . '#article',
            'headline' => $article_title,
            'description' => $description,
            'image' => footerball_json_ld_image($image),
            'url' => $canonical,
            'datePublished' => get_the_date('c', $post_id),
            'dateModified' => get_the_modified_date('c', $post_id),
            'author' => footerball_get_lifestyle_author_schema($post_id, $article),
            'publisher' => array('@id' => home_url('/#organization')),
            'mainEntityOfPage' => array('@id' => $canonical . '#webpage'),
        );
        $faq_schema = footerball_get_faq_page_schema($canonical, footerball_get_lifestyle_faq_schema_items($post_id), $article_title . ' FAQ');
        if (!empty($faq_schema)) {
            $graph[] = $faq_schema;
        }
    }

    array_splice($graph, 2, 0, array($page_schema));

    return footerball_json_ld_clean(array(
        '@context' => 'https://schema.org',
        '@graph' => $graph,
    ));
}

function footerball_render_json_ld() {
    $schema = footerball_get_json_ld_graph();
    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
}
add_action('wp_head', 'footerball_render_json_ld', 20);

function footerball_sitemap_url($name = '') {
    $name = sanitize_key((string) $name);
    return $name ? home_url('/sitemap-' . $name . '.xml') : home_url('/sitemap.xml');
}

function footerball_robots_txt($output, $public) {
    $lines = array('User-agent: *');

    if ((int) $public === 0) {
        $lines[] = 'Disallow: /';
    } else {
        $lines[] = 'Disallow:';
        $lines[] = 'Allow: /';
    }

    $lines[] = '';
    $lines[] = 'Sitemap: ' . footerball_sitemap_url();

    return implode("\n", $lines) . "\n";
}
add_filter('robots_txt', 'footerball_robots_txt', 20, 2);

add_filter('wp_sitemaps_add_provider', function ($provider, $name) {
    return $name === 'users' ? false : $provider;
}, 10, 2);

function footerball_register_sitemap_route() {
    add_rewrite_tag('%footerball_sitemap%', '([01])');
    add_rewrite_tag('%footerball_sitemap_name%', '([a-z0-9-]+)');
    add_rewrite_rule('^sitemap\.xml$', 'index.php?footerball_sitemap=1', 'top');
    add_rewrite_rule('^sitemap-([a-z0-9-]+)\.xml$', 'index.php?footerball_sitemap=1&footerball_sitemap_name=$matches[1]', 'top');
}
add_action('init', 'footerball_register_sitemap_route');

add_filter('query_vars', function ($vars) {
    $vars[] = 'footerball_sitemap';
    $vars[] = 'footerball_sitemap_name';
    return $vars;
});

add_action('init', function () {
    if (get_option('footerball_sitemap_route_flushed_v2')) {
        return;
    }
    flush_rewrite_rules(false);
    update_option('footerball_sitemap_route_flushed_v2', 1);
}, 100);

function footerball_sitemap_xml_escape($value) {
    return function_exists('esc_xml') ? esc_xml($value) : esc_html($value);
}

function footerball_sitemap_format_timestamp($timestamp) {
    $timestamp = (int) $timestamp;
    return $timestamp > 0 ? gmdate('c', $timestamp) : '';
}

function footerball_sitemap_post_lastmod($post_id) {
    $timestamp = get_post_modified_time('U', true, (int) $post_id);
    return footerball_sitemap_format_timestamp($timestamp);
}

function footerball_sitemap_template_lastmod($file) {
    $path = get_stylesheet_directory() . '/' . ltrim((string) $file, '/');
    return is_readable($path) ? footerball_sitemap_format_timestamp(filemtime($path)) : '';
}

function footerball_sitemap_latest_lastmod($dates) {
    $latest = 0;
    foreach ((array) $dates as $date) {
        if (!$date) {
            continue;
        }
        $timestamp = is_numeric($date) ? (int) $date : strtotime((string) $date);
        if ($timestamp > $latest) {
            $latest = $timestamp;
        }
    }
    return footerball_sitemap_format_timestamp($latest);
}

function footerball_sitemap_latest_post_lastmod($post_type) {
    $posts = get_posts(array(
        'post_type'              => $post_type,
        'post_status'            => 'publish',
        'posts_per_page'         => 1,
        'orderby'                => 'modified',
        'order'                  => 'DESC',
        'fields'                 => 'ids',
        'no_found_rows'          => true,
        'ignore_sticky_posts'    => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
        'has_password'           => false,
    ));

    return !empty($posts[0]) ? footerball_sitemap_post_lastmod((int) $posts[0]) : '';
}

function footerball_sitemap_lifestyle_per_page() {
    return max(1, (int) apply_filters('footerball_sitemap_lifestyle_per_page', 500));
}

function footerball_sitemap_published_post_count($post_type) {
    global $wpdb;

    return (int) $wpdb->get_var(
        $wpdb->prepare(
            "SELECT COUNT(ID) FROM {$wpdb->posts} WHERE post_type = %s AND post_status = 'publish' AND post_password = ''",
            (string) $post_type
        )
    );
}

function footerball_sitemap_lifestyle_page_count() {
    $total = footerball_sitemap_published_post_count('lifestyle');
    if ($total <= 0) {
        return 0;
    }

    return (int) ceil($total / footerball_sitemap_lifestyle_per_page());
}

function footerball_sitemap_lifestyle_page_name($page) {
    return 'lifestyle-detail-' . max(1, (int) $page);
}

function footerball_sitemap_parse_lifestyle_page_name($name) {
    $name = sanitize_key((string) $name);
    if (preg_match('/^lifestyle-detail-(\d+)$/', $name, $match)) {
        return max(1, (int) $match[1]);
    }

    return 0;
}

function footerball_render_sitemap_lifestyle_subindex_xml() {
    status_header(200);
    nocache_headers();
    header('Content-Type: application/xml; charset=' . get_bloginfo('charset'), true);
    echo '<?xml version="1.0" encoding="' . esc_attr(get_bloginfo('charset') ?: 'UTF-8') . '"?>' . "\n";
    echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    $definition = array('type' => 'lifestyle-detail', 'page' => 1);
    for ($page = 1, $max = footerball_sitemap_lifestyle_page_count(); $page <= $max; $page++) {
        $name = footerball_sitemap_lifestyle_page_name($page);
        echo "  <sitemap>\n";
        echo '    <loc>' . footerball_sitemap_xml_escape(footerball_sitemap_url($name)) . "</loc>\n";
        $lastmod = footerball_sitemap_group_lastmod($definition);
        if ($lastmod) {
            echo '    <lastmod>' . footerball_sitemap_xml_escape($lastmod) . "</lastmod>\n";
        }
        echo "  </sitemap>\n";
    }

    echo "</sitemapindex>\n";
}

function footerball_sitemap_get_post_ids($post_type) {
    return get_posts(array(
        'post_type'              => $post_type,
        'post_status'            => 'publish',
        'posts_per_page'         => -1,
        'orderby'                => array('menu_order' => 'ASC', 'title' => 'ASC', 'date' => 'DESC'),
        'fields'                 => 'ids',
        'no_found_rows'          => true,
        'ignore_sticky_posts'    => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
        'has_password'           => false,
    ));
}

function footerball_sitemap_lifestyle_rows_for_page($page) {
    global $wpdb;

    $page = max(1, (int) $page);
    $per_page = footerball_sitemap_lifestyle_per_page();
    $offset = ($page - 1) * $per_page;

    return $wpdb->get_results(
        $wpdb->prepare(
            "SELECT ID, post_name, post_modified_gmt
             FROM {$wpdb->posts}
             WHERE post_type = 'lifestyle' AND post_status = 'publish' AND post_password = ''
             ORDER BY post_date DESC, ID DESC
             LIMIT %d OFFSET %d",
            $per_page,
            $offset
        )
    );
}

function footerball_sitemap_lifestyle_entries_for_page($page) {
    $entries = array();
    $seen = array();

    foreach (footerball_sitemap_lifestyle_rows_for_page($page) as $row) {
        $slug = sanitize_title((string) $row->post_name);
        if ($slug === '') {
            continue;
        }
        $loc = home_url('/lifestyle/' . $slug . '/');
        $lastmod = footerball_sitemap_format_timestamp(strtotime((string) $row->post_modified_gmt . ' GMT'));
        footerball_sitemap_add_entry($entries, $seen, $loc, $lastmod, 'weekly', '0.78');
    }

    return $entries;
}

function footerball_sitemap_definitions() {
    return array(
        'list-pages' => array('type' => 'list-pages'),
        'players-profile' => array('type' => 'player-tab', 'tab' => 'profile'),
        'players-gear' => array('type' => 'player-tab', 'tab' => 'gear'),
        'players-stats' => array('type' => 'player-tab', 'tab' => 'stats'),
        'players-updates' => array('type' => 'player-tab', 'tab' => 'updates'),
        'teams-detail' => array('type' => 'team-tab', 'tab' => 'profile'),
        'teams-gear' => array('type' => 'team-tab', 'tab' => 'gear'),
        'teams-players' => array('type' => 'team-tab', 'tab' => 'players'),
        'teams-news' => array('type' => 'team-tab', 'tab' => 'news'),
        'host-cities-ticket-information' => array('type' => 'host-city-tab', 'tab' => 'ticket-information'),
        'host-cities-fixtures' => array('type' => 'host-city-tab', 'tab' => 'fixtures'),
        'host-cities-stadium-day-guide' => array('type' => 'host-city-tab', 'tab' => 'stadium-day-guide'),
        'host-cities-official-gear' => array('type' => 'host-city-tab', 'tab' => 'official-gear'),
    );
}

function footerball_sitemap_index_items() {
    $items = array();

    foreach (footerball_sitemap_definitions() as $name => $definition) {
        $items[] = array(
            'name' => $name,
            'definition' => $definition,
        );
    }

    for ($page = 1, $max = footerball_sitemap_lifestyle_page_count(); $page <= $max; $page++) {
        $items[] = array(
            'name' => footerball_sitemap_lifestyle_page_name($page),
            'definition' => array('type' => 'lifestyle-detail', 'page' => $page),
        );
    }

    return $items;
}

function footerball_sitemap_add_entry(&$entries, &$seen, $loc, $lastmod = '', $changefreq = '', $priority = '') {
    $loc = esc_url_raw($loc);
    if (!$loc || isset($seen[$loc])) {
        return;
    }

    $seen[$loc] = true;
    $entry = array('loc' => $loc);
    if ($lastmod) {
        $entry['lastmod'] = $lastmod;
    }
    if ($changefreq) {
        $entry['changefreq'] = $changefreq;
    }
    if ($priority !== '') {
        $entry['priority'] = (string) $priority;
    }
    $entries[] = $entry;
}

function footerball_sitemap_list_page_lastmod() {
    return footerball_sitemap_latest_lastmod(array(
        footerball_sitemap_template_lastmod('front-page.php'),
        footerball_sitemap_template_lastmod('archive-teams.php'),
        footerball_sitemap_template_lastmod('archive-players.php'),
        footerball_sitemap_template_lastmod('archive-matches.php'),
        footerball_sitemap_template_lastmod('archive-host-cities.php'),
        footerball_sitemap_template_lastmod('archive-lifestyle.php'),
        footerball_sitemap_template_lastmod('page-about-us.php'),
        footerball_sitemap_latest_post_lastmod('page'),
        footerball_sitemap_latest_post_lastmod('teams'),
        footerball_sitemap_latest_post_lastmod('players'),
        footerball_sitemap_latest_post_lastmod('host_cities'),
        footerball_sitemap_latest_post_lastmod('lifestyle'),
    ));
}

function footerball_sitemap_group_lastmod($definition) {
    $type = isset($definition['type']) ? (string) $definition['type'] : '';

    if ($type === 'list-pages') {
        return footerball_sitemap_list_page_lastmod();
    }
    if ($type === 'player-tab') {
        return footerball_sitemap_latest_post_lastmod('players');
    }
    if ($type === 'team-detail' || $type === 'team-tab') {
        return footerball_sitemap_latest_post_lastmod('teams');
    }
    if ($type === 'host-city-tab') {
        return footerball_sitemap_latest_post_lastmod('host_cities');
    }
    if ($type === 'lifestyle-detail') {
        return footerball_sitemap_latest_post_lastmod('lifestyle');
    }

    return '';
}

function footerball_sitemap_list_page_entries() {
    $entries = array();
    $seen = array();

    footerball_sitemap_add_entry($entries, $seen, home_url('/'), footerball_sitemap_list_page_lastmod(), 'daily', '1.0');
    footerball_sitemap_add_entry($entries, $seen, home_url('/about-us/'), footerball_sitemap_latest_lastmod(array(footerball_sitemap_template_lastmod('page-about-us.php'), footerball_sitemap_template_lastmod('css/about-us.css'))), 'monthly', '0.7');
    footerball_sitemap_add_entry($entries, $seen, footerball_archive_url('teams', '/teams/'), footerball_sitemap_latest_lastmod(array(footerball_sitemap_latest_post_lastmod('teams'), footerball_sitemap_template_lastmod('archive-teams.php'))), 'weekly', '0.9');
    footerball_sitemap_add_entry($entries, $seen, footerball_archive_url('players', '/players/'), footerball_sitemap_latest_lastmod(array(footerball_sitemap_latest_post_lastmod('players'), footerball_sitemap_template_lastmod('archive-players.php'))), 'weekly', '0.9');
    footerball_sitemap_add_entry($entries, $seen, home_url('/matches/'), footerball_sitemap_template_lastmod('archive-matches.php'), 'weekly', '0.85');
    footerball_sitemap_add_entry($entries, $seen, footerball_archive_url('host_cities', '/host-cities/'), footerball_sitemap_latest_lastmod(array(footerball_sitemap_latest_post_lastmod('host_cities'), footerball_sitemap_template_lastmod('archive-host-cities.php'))), 'weekly', '0.9');
    footerball_sitemap_add_entry($entries, $seen, footerball_archive_url('lifestyle', '/lifestyle/'), footerball_sitemap_latest_lastmod(array(footerball_sitemap_latest_post_lastmod('lifestyle'), footerball_sitemap_template_lastmod('archive-lifestyle.php'))), 'daily', '0.9');

    $front_page_id = (int) get_option('page_on_front');
    foreach (footerball_sitemap_get_post_ids('page') as $post_id) {
        if ((int) $post_id === $front_page_id) {
            continue;
        }
        $page_url = get_permalink($post_id);
        if (untrailingslashit($page_url) === untrailingslashit(home_url('/page/'))) {
            continue;
        }
        footerball_sitemap_add_entry($entries, $seen, $page_url, footerball_sitemap_post_lastmod($post_id), 'monthly', '0.6');
    }

    return $entries;
}

function footerball_sitemap_entries_for_definition($definition) {
    $type = isset($definition['type']) ? (string) $definition['type'] : '';

    if ($type === 'list-pages') {
        return footerball_sitemap_list_page_entries();
    }

    $entries = array();
    $seen = array();

    if ($type === 'player-tab') {
        $tab = isset($definition['tab']) ? (string) $definition['tab'] : 'profile';
        foreach (footerball_sitemap_get_post_ids('players') as $post_id) {
            $url = function_exists('footerball_player_tab_url') ? footerball_player_tab_url($post_id, $tab) : get_permalink($post_id);
            footerball_sitemap_add_entry($entries, $seen, $url, footerball_sitemap_post_lastmod($post_id), 'weekly', $tab === 'profile' ? '0.82' : '0.72');
        }
    } elseif ($type === 'team-detail') {
        foreach (footerball_sitemap_get_post_ids('teams') as $post_id) {
            $url = function_exists('footerball_team_tab_url') ? footerball_team_tab_url($post_id, 'profile') : get_permalink($post_id);
            footerball_sitemap_add_entry($entries, $seen, $url, footerball_sitemap_post_lastmod($post_id), 'weekly', '0.82');
        }
    } elseif ($type === 'team-tab') {
        $tab = isset($definition['tab']) ? (string) $definition['tab'] : 'profile';
        foreach (footerball_sitemap_get_post_ids('teams') as $post_id) {
            $url = function_exists('footerball_team_tab_url') ? footerball_team_tab_url($post_id, $tab) : get_permalink($post_id);
            footerball_sitemap_add_entry($entries, $seen, $url, footerball_sitemap_post_lastmod($post_id), 'weekly', $tab === 'profile' ? '0.82' : '0.74');
        }
    } elseif ($type === 'host-city-tab') {
        $tab = isset($definition['tab']) ? (string) $definition['tab'] : 'ticket-information';
        foreach (footerball_sitemap_get_post_ids('host_cities') as $post_id) {
            $url = function_exists('footerball_host_city_tab_url') ? footerball_host_city_tab_url($post_id, $tab) : get_permalink($post_id);
            footerball_sitemap_add_entry($entries, $seen, $url, footerball_sitemap_post_lastmod($post_id), 'weekly', $tab === 'ticket-information' ? '0.82' : '0.72');
        }
    } elseif ($type === 'lifestyle-detail') {
        $page = isset($definition['page']) ? max(1, (int) $definition['page']) : 1;
        return footerball_sitemap_lifestyle_entries_for_page($page);
    }

    return $entries;
}

function footerball_sitemap_request_name() {
    if ((int) get_query_var('footerball_sitemap') === 1) {
        $name = sanitize_key((string) get_query_var('footerball_sitemap_name'));
        return $name ?: 'index';
    }

    $path = isset($_SERVER['REQUEST_URI']) ? (string) wp_parse_url((string) $_SERVER['REQUEST_URI'], PHP_URL_PATH) : '';
    $home_path = (string) wp_parse_url(home_url('/'), PHP_URL_PATH);
    $home_path = '/' . trim($home_path, '/');
    if ($home_path !== '/' && strpos($path, $home_path . '/') === 0) {
        $path = substr($path, strlen($home_path));
    }

    $file = trim($path, '/');
    if ($file === 'sitemap.xml') {
        return 'index';
    }
    if (preg_match('/^sitemap-([a-z0-9-]+)\.xml$/', $file, $match)) {
        return sanitize_key($match[1]);
    }

    return '';
}

function footerball_render_sitemap_index_xml() {
    status_header(200);
    nocache_headers();
    header('Content-Type: application/xml; charset=' . get_bloginfo('charset'), true);
    echo '<?xml version="1.0" encoding="' . esc_attr(get_bloginfo('charset') ?: 'UTF-8') . '"?>' . "\n";
    echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    foreach (footerball_sitemap_index_items() as $item) {
        $name = $item['name'];
        $definition = $item['definition'];
        echo "  <sitemap>\n";
        echo '    <loc>' . footerball_sitemap_xml_escape(footerball_sitemap_url($name)) . "</loc>\n";
        $lastmod = footerball_sitemap_group_lastmod($definition);
        if ($lastmod) {
            echo '    <lastmod>' . footerball_sitemap_xml_escape($lastmod) . "</lastmod>\n";
        }
        echo "  </sitemap>\n";
    }

    echo "</sitemapindex>\n";
}

function footerball_render_sitemap_urlset_xml($definition) {
    status_header(200);
    nocache_headers();
    header('Content-Type: application/xml; charset=' . get_bloginfo('charset'), true);
    echo '<?xml version="1.0" encoding="' . esc_attr(get_bloginfo('charset') ?: 'UTF-8') . '"?>' . "\n";
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    foreach (footerball_sitemap_entries_for_definition($definition) as $entry) {
        echo "  <url>\n";
        echo '    <loc>' . footerball_sitemap_xml_escape($entry['loc']) . "</loc>\n";
        if (!empty($entry['lastmod'])) {
            echo '    <lastmod>' . footerball_sitemap_xml_escape($entry['lastmod']) . "</lastmod>\n";
        }
        if (!empty($entry['changefreq'])) {
            echo '    <changefreq>' . footerball_sitemap_xml_escape($entry['changefreq']) . "</changefreq>\n";
        }
        if (isset($entry['priority'])) {
            echo '    <priority>' . footerball_sitemap_xml_escape($entry['priority']) . "</priority>\n";
        }
        echo "  </url>\n";
    }

    echo "</urlset>\n";
}

function footerball_render_sitemap_not_found() {
    status_header(404);
    header('Content-Type: text/plain; charset=' . get_bloginfo('charset'), true);
    echo 'Sitemap not found.';
}

add_action('template_redirect', function () {
    $sitemap_name = footerball_sitemap_request_name();
    if (!$sitemap_name) {
        return;
    }

    if ($sitemap_name === 'index') {
        footerball_render_sitemap_index_xml();
        exit;
    }

    if ($sitemap_name === 'lifestyle-detail') {
        footerball_render_sitemap_lifestyle_subindex_xml();
        exit;
    }

    $lifestyle_page = footerball_sitemap_parse_lifestyle_page_name($sitemap_name);
    if ($lifestyle_page > 0) {
        if ($lifestyle_page > footerball_sitemap_lifestyle_page_count()) {
            footerball_render_sitemap_not_found();
            exit;
        }
        footerball_render_sitemap_urlset_xml(array('type' => 'lifestyle-detail', 'page' => $lifestyle_page));
        exit;
    }

    $definitions = footerball_sitemap_definitions();
    if (!isset($definitions[$sitemap_name])) {
        footerball_render_sitemap_not_found();
        exit;
    }

    footerball_render_sitemap_urlset_xml($definitions[$sitemap_name]);
    exit;
}, 0);

add_action('template_redirect', function () {
    if (!get_query_var('sitemap')) {
        return;
    }

    status_header(200);
    nocache_headers();
}, 100);

function footerball_resource_hints($urls, $relation_type) {
    if ($relation_type === 'preconnect') {
        $urls[] = array('href' => 'https://img4.dhresource.com', 'crossorigin' => 'anonymous');
        $urls[] = array('href' => 'https://js.dhresource.com', 'crossorigin' => 'anonymous');
        if (is_singular('players') || is_singular('host_cities')) {
            $urls[] = array('href' => 'https://media.smart.dhgate.com', 'crossorigin' => 'anonymous');
        }
        if (is_singular('players')) {
            $urls[] = array('href' => 'https://cdn.jsdelivr.net', 'crossorigin' => 'anonymous');
        }
    }

    return $urls;
}
add_filter('wp_resource_hints', 'footerball_resource_hints', 10, 2);

function footerball_print_image_preload($href, $srcset = '', $sizes = '') {
    $href = (string) $href;
    if ($href === '') {
        return;
    }

    echo '<link rel="preload" as="image" href="' . esc_url($href) . '"';
    if ($srcset !== '' && $sizes !== '') {
        echo ' imagesrcset="' . esc_attr($srcset) . '" imagesizes="' . esc_attr($sizes) . '"';
    }
    echo ' fetchpriority="high">' . "\n";
}

function footerball_preload_critical_assets() {
    if (is_front_page() || is_home()) {
        $hero = function_exists('footerball_home_hero_banner_url') ? footerball_home_hero_banner_url() : '';
        if ($hero) {
            $home_hero_base = footerball_theme_asset_url('images/home-hero-trophy.webp');
            $home_hero_2x = footerball_theme_asset_url('images/home-hero-trophy-3840.webp');
            $home_hero_base_file = get_stylesheet_directory() . '/images/home-hero-trophy.webp';
            $home_hero_2x_file = get_stylesheet_directory() . '/images/home-hero-trophy-3840.webp';

            if (($hero === $home_hero_base || $hero === $home_hero_2x) && is_readable($home_hero_base_file)) {
                $srcset = $home_hero_base . ' 1920w';
                if (is_readable($home_hero_2x_file)) {
                    $srcset .= ', ' . $home_hero_2x . ' 3840w';
                }
                footerball_print_image_preload($home_hero_base, $srcset, '100vw');
            } else {
                footerball_print_image_preload($hero);
            }
        }
    }

    if (is_singular('players')) {
        $post_id = get_queried_object_id();
        $image_id = function_exists('get_field') ? absint(get_field('featured_image', $post_id)) : 0;
        if (!$image_id) {
            $image_id = absint(get_post_thumbnail_id($post_id));
        }

        if ($image_id) {
            $image = wp_get_attachment_image_src($image_id, 'medium_large');
            if (!empty($image[0])) {
                $srcset = wp_get_attachment_image_srcset($image_id, 'medium_large');
                footerball_print_image_preload($image[0], $srcset ?: '', '(max-width: 768px) min(68vw, 260px), 248px');
            }
        }
    }

    $queried_post_type = get_post_type(get_queried_object_id());
    if ($queried_post_type === 'teams') {
        $hero = footerball_theme_asset_url('images/team-hero-stadium-night-v5.webp');
        $srcset = footerball_theme_image_srcset($hero, array(768, 1280, 1920));
        footerball_print_image_preload(
            footerball_theme_asset_url('images/team-hero-stadium-night-v5-1280.webp'),
            $srcset,
            '100vw'
        );
    }

    if ($queried_post_type === 'host_cities') {
        $post_id = get_queried_object_id();
        $banner = function_exists('footerball_host_city_banner_url') ? footerball_host_city_banner_url($post_id) : '';
        if ($banner) {
            $srcset = footerball_theme_image_srcset($banner, array(768, 1280));
            footerball_print_image_preload(
                $banner,
                $srcset,
                '(max-width: 760px) calc(100vw - 28px), (max-width: 1180px) calc(100vw - 56px), 560px'
            );
        }
    }
}
add_action('wp_head', 'footerball_preload_critical_assets', 3);

function footerball_defer_theme_scripts($tag, $handle, $src) {
    $defer_handles = array('ai-guide-shopping', 'chart-js', 'footerball-local-time', 'footerball-whatsapp-community-popup');
    if (in_array($handle, $defer_handles, true) && strpos($tag, ' defer') === false) {
        return str_replace(' src=', ' defer src=', $tag);
    }
    return $tag;
}
add_filter('script_loader_tag', 'footerball_defer_theme_scripts', 10, 3);

function footerball_optimize_html_images($html, $fallback_alt = '') {
    $html = (string) $html;
    if ($html === '' || stripos($html, '<img') === false || !class_exists('WP_HTML_Tag_Processor')) {
        return $html;
    }

    $fallback_alt = footerball_clean_meta_text($fallback_alt);
    $processor = new WP_HTML_Tag_Processor($html);
    while ($processor->next_tag('img')) {
        $src = (string) $processor->get_attribute('src');

        $current_alt = $processor->get_attribute('alt');
        if ($fallback_alt !== '' && ($current_alt === null || footerball_clean_meta_text($current_alt) === '')) {
            $processor->set_attribute('alt', $fallback_alt);
        }
        if ($processor->get_attribute('loading') === null) {
            $processor->set_attribute('loading', 'lazy');
        }
        if ($processor->get_attribute('decoding') === null) {
            $processor->set_attribute('decoding', 'async');
        }

	        if ($src && $processor->get_attribute('srcset') === null) {
	            $attachment_id = attachment_url_to_postid($src);
	            if ($attachment_id) {
	                $srcset = wp_get_attachment_image_srcset($attachment_id, 'large');
	                if ($srcset) {
	                    $processor->set_attribute('srcset', $srcset);
	                    if ($processor->get_attribute('sizes') === null) {
	                        $processor->set_attribute('sizes', '(max-width: 760px) calc(100vw - 32px), 760px');
	                    }
	                }
	            }
	        }
	
	        if ($src && ($processor->get_attribute('width') === null || $processor->get_attribute('height') === null)) {
	            $attachment_id = attachment_url_to_postid($src);
	            if ($attachment_id) {
                $meta = wp_get_attachment_metadata($attachment_id);
                if (!empty($meta['width']) && !empty($meta['height'])) {
                    $processor->set_attribute('width', (string) absint($meta['width']));
                    $processor->set_attribute('height', (string) absint($meta['height']));
                }
            }
        }
    }

    return $processor->get_updated_html();
}

function footerball_format_lifestyle_text_figures($html) {
    $html = (string) $html;
    if ($html === '' || stripos($html, '<figure') === false) {
        return $html;
    }

    return preg_replace_callback(
        '/<figure\b[^>]*>(.*?)<\/figure>/is',
        static function ($matches) {
            $figure_html = $matches[0];
            $inner_html = $matches[1];

            if (preg_match('/<(?:img|picture|svg|iframe|video)\b/i', $inner_html)) {
                return $figure_html;
            }

            $caption_html = '';
            if (preg_match('/<figcaption\b[^>]*>(.*?)<\/figcaption>/is', $inner_html, $caption_match)) {
                $caption_html = trim($caption_match[1]);
                $inner_html = str_replace($caption_match[0], '', $inner_html);
            }

            $text = preg_replace('/<br\s*\/?>/i', "\n", $inner_html);
            $text = wp_strip_all_tags($text);
            $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, get_bloginfo('charset') ?: 'UTF-8');
            $lines = preg_split('/\r\n|\r|\n/', $text);
            $lines = array_values(array_filter(array_map(
                static function ($line) {
                    return trim((string) $line);
                },
                is_array($lines) ? $lines : array()
            )));

            if (count($lines) < 2) {
                return $figure_html;
            }

            $grid_html = '<div class="blog-formula-card__grid">';
            foreach ($lines as $line) {
                $grid_html .= '<span>' . esc_html($line) . '</span>';
            }
            $grid_html .= '</div>';

            $caption = $caption_html !== '' ? '<figcaption>' . wp_kses_post($caption_html) . '</figcaption>' : '';

            return '<figure class="wp-block-image blog-formula-card">' . $grid_html . $caption . '</figure>';
        },
        $html
    );
}

function footerball_clean_lifestyle_article_content($html, $fallback_alt = '') {
    $html = (string) $html;
    if ($html === '') {
        return '';
    }

    /*
     * Lifestyle singles read ACF `article.post_content_main` first; REST often exposes only `post_content`.
     * Legacy/generated drafts may still carry this block only in ACF, so strip it at render time.
     */
    $html = preg_replace('/<div\b[^>]*\baffiliate-disclosure\b[^>]*>.*?<\/div>/is', '', $html);
    $html = preg_replace('/<aside\b[^>]*\baffiliate-disclosure\b[^>]*>.*?<\/aside>/is', '', $html);
    $html = preg_replace(
        '/<p\b[^>]*>\s*<strong>\s*Disclosure:\s*<\/strong>\s*[\s\S]*?affiliate\s+links[\s\S]*?DHgate[\s\S]*?<\/p>/iu',
        '',
        $html
    );

    $html = preg_replace('/<p\b[^>]*>.*?(?:e-commerce SEO keyword classifier|Return JSONL|### Task For each query).*?<\/p>/is', '', $html);
    $html = preg_replace('/<h1\b[^>]*>\s*ads\s*(?:<img\b[^>]*>\s*)?<\/h1>/is', '', $html);
    $html = preg_replace('/<h1\b([^>]*)>/i', '<h2$1>', $html);
    $html = preg_replace('/<\/h1>/i', '</h2>', $html);
    $html = footerball_format_lifestyle_text_figures($html);

    return footerball_optimize_html_images($html, $fallback_alt);
}

function footerball_attachment_alt_fallback($attr, $attachment, $size) {
    if (!empty($attr['alt'])) {
        return $attr;
    }

    $alt = get_post_meta($attachment->ID, '_wp_attachment_image_alt', true);
    if (!$alt) {
        $alt = $attachment->post_title;
    }
    if (!$alt) {
        $alt = get_bloginfo('name');
    }

    $attr['alt'] = footerball_clean_meta_text($alt);
    return $attr;
}
add_filter('wp_get_attachment_image_attributes', 'footerball_attachment_alt_fallback', 10, 3);

/* ✅ Players page styles */
function my_player_styles() {
    if (is_post_type_archive('players')) {
        wp_enqueue_style(
            'players-style',
            get_stylesheet_directory_uri() . '/css/players.css',
            array('main-style'),
            THEME_VERSION
        );
    }

    if (is_singular('players')) {
        wp_enqueue_style(
            'players-profile-style',
            get_stylesheet_directory_uri() . '/css/players-profile.css',
            array('main-style'),
            THEME_VERSION
        );

        wp_enqueue_script(
            'chart-js',
            'https://cdn.jsdelivr.net/npm/chart.js',
            array(),
            '4.4.7',
            array(
                'strategy'  => 'defer',
                'in_footer' => true,
            )
        );
    }
}
add_action('wp_enqueue_scripts', 'my_player_styles');

/* ✅ Lifestyle page styles */
function my_lifestyle_styles() {
    if (is_singular('lifestyle') || is_post_type_archive('lifestyle')) {
        wp_enqueue_style(
            'lifestyle-style',
            get_stylesheet_directory_uri() . '/css/lifestyle.css',
            array('main-style'),
            THEME_VERSION
        );
    }
}
add_action('wp_enqueue_scripts', 'my_lifestyle_styles');

/* ✅ Search page styles */
function footerball_search_styles() {
    if (is_search()) {
        wp_enqueue_style(
            'search-style',
            get_stylesheet_directory_uri() . '/css/search.css',
            array('main-style'),
            THEME_VERSION
        );
    }
}
add_action('wp_enqueue_scripts', 'footerball_search_styles');

/* ✅ Teams page styles */
function my_teams_styles() {
    if (is_singular('teams') || get_post_type() === 'teams' || is_post_type_archive('teams')) {
        wp_enqueue_style(
            'teams-style',
            get_stylesheet_directory_uri() . '/css/teams.css',
            array('main-style'),
            THEME_VERSION
        );
    }
}
add_action('wp_enqueue_scripts', 'my_teams_styles');

/* ✅ Match Center page styles */
function footerball_matches_page_styles() {
    if ((int) get_query_var('footerball_matches_page') === 1 || is_post_type_archive('matches') || is_singular('matches')) {
        wp_enqueue_style(
            'matches-style',
            get_stylesheet_directory_uri() . '/css/matches.css',
            array('main-style'),
            THEME_VERSION
        );
    }
}
add_action('wp_enqueue_scripts', 'footerball_matches_page_styles');

/* ✅ Host cities page styles */
function my_host_cities_styles() {
    if (is_singular('host_cities') || get_post_type() === 'host_cities' || is_post_type_archive('host_cities')) {
        wp_enqueue_style(
            'host-cities-style',
            add_query_arg('build', 'city-news', get_stylesheet_directory_uri() . '/css/host-cities.css'),
            array('main-style'),
            THEME_VERSION
        );
    }
}
add_action('wp_enqueue_scripts', 'my_host_cities_styles');

/* ✅ About Us page styles */
function footerball_about_us_styles() {
    if (footerball_is_about_us_request()) {
        wp_enqueue_style(
            'footerball-about-us',
            get_stylesheet_directory_uri() . '/css/about-us.css',
            array('main-style'),
            THEME_VERSION
        );
    }
}
add_action('wp_enqueue_scripts', 'footerball_about_us_styles');

/**
 * Vertical DHgate promo (list + detail sidebars).
 */
function footerball_enqueue_dhgate_promo_styles() {
	$load = is_post_type_archive(array('players', 'teams', 'lifestyle', 'host_cities'))
		|| (int) get_query_var('footerball_matches_page') === 1
		|| is_post_type_archive('matches')
		|| is_singular(array('players', 'teams', 'lifestyle', 'host_cities'));
	if (!$load) {
		return;
	}
	wp_enqueue_style(
		'footerball-dhgate-promo',
		get_stylesheet_directory_uri() . '/css/dhgate-sidebar-ad.css',
		array('main-style'),
		THEME_VERSION
	);
}
add_action('wp_enqueue_scripts', 'footerball_enqueue_dhgate_promo_styles');

/* ✅ Error page styles */
function my_error_page_styles() {
    $error_code = (int) get_query_var('footerball_error_code');
    if (is_404() || $error_code >= 500) {
        wp_enqueue_style(
            'error-page-style',
            get_stylesheet_directory_uri() . '/css/error-pages.css',
            array('main-style'),
            THEME_VERSION
        );
    }
}
add_action('wp_enqueue_scripts', 'my_error_page_styles');

/* ============================================
   ✅ ACF Options Page
   ============================================ */

if (function_exists('acf_add_options_page')) {
    $home_settings_page = array(
        'page_title' => 'Home Settings',
        'menu_title' => 'Home Settings',
        'menu_slug'  => 'home-settings',
        'capability' => 'edit_posts',
        'redirect'   => false
    );
    acf_add_options_page($home_settings_page);

    $player_gear_settings_page = array(
        'page_title' => 'Player Gear Settings',
        'menu_title' => 'Player Gear Settings',
        'menu_slug'  => 'player-gear-settings',
        'capability' => 'edit_posts',
        'redirect'   => false
    );
    acf_add_options_page($player_gear_settings_page);
}

/* ============================================
   ✅ Register Teams Custom Post Type
   ============================================ */

function register_teams_cpt() {
    register_post_type('teams', array(
        'label'         => 'Teams',
        'labels'        => array(
            'name'          => 'Teams',
            'singular_name' => 'Team',
            'add_new'       => 'Add New Team',
            'edit_item'     => 'Edit Team',
            'view_item'     => 'View Team',
            'all_items'     => 'All Teams',
        ),
        'public'        => true,
        'has_archive'   => true,
        'rewrite'       => array('slug' => 'teams'),
        'supports'      => array('title', 'editor', 'thumbnail', 'excerpt'),
        'menu_icon'     => 'dashicons-flag',
        'show_in_rest'  => true,
        'menu_position' => 5,
    ));
}
add_action('init', 'register_teams_cpt');

function flush_teams_rewrite_rules() {
    footerball_register_error_routes();
    flush_rewrite_rules(false);
}
add_action('after_switch_theme', 'flush_teams_rewrite_rules');

/* Flush rewrite rules after player tab rewrite rules are registered (v2: updates tab) */
add_action('init', function () {
    if (get_option('players_tab_rewrite_flushed_v2')) {
        return;
    }
    flush_rewrite_rules(false);
    update_option('players_tab_rewrite_flushed_v2', 1);
}, 100);

/* ============================================
   ✅ Error Page Routes (Real Status Codes)
   ============================================ */
function footerball_register_error_routes() {
    add_rewrite_tag('%footerball_error_code%', '([0-9]{3})');
    add_rewrite_rule('^5xx/?$', 'index.php?footerball_error_code=500', 'top');
}
add_action('init', 'footerball_register_error_routes');

function footerball_register_matches_route() {
    add_rewrite_tag('%footerball_matches_page%', '([01])');
    add_rewrite_rule('^matches/?$', 'index.php?footerball_matches_page=1', 'top');
}
add_action('init', 'footerball_register_matches_route');

add_filter('query_vars', function ($vars) {
    $vars[] = 'footerball_error_code';
    $vars[] = 'footerball_matches_page';
    return $vars;
});

add_action('init', function () {
    if (get_option('footerball_matches_route_flushed_v1')) {
        return;
    }
    flush_rewrite_rules(false);
    update_option('footerball_matches_route_flushed_v1', 1);
}, 100);

add_action('template_redirect', function () {
    $error_code = (int) get_query_var('footerball_error_code');
    if ($error_code >= 500) {
        status_header($error_code);
        nocache_headers();
    }
});

add_action('template_redirect', function () {
    $req_uri = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '';
    $path = preg_replace('/\?.*$/', '', $req_uri);
    if (rtrim($path, '/') === '/page') {
        wp_redirect(home_url('/'), 301);
        exit;
    }
}, 1);

/* ============================================
   Team Tab Rewrite（/teams/{slug}/{tab}/）
   ============================================ */
add_action('init', function () {
    add_rewrite_tag('%team_tab%', '(profile|gear|players|news)');

    add_rewrite_rule(
        '^teams/([^/]+)/(profile|gear|players|news)/?$',
        'index.php?teams=$matches[1]&team_tab=$matches[2]',
        'top'
    );
}, 10);

add_filter('query_vars', function ($vars) {
    $vars[] = 'team_tab';
    return $vars;
});

add_action('init', function () {
    if (get_option('footerball_team_tab_rewrite_flushed_v1')) {
        return;
    }
    flush_rewrite_rules(false);
    update_option('footerball_team_tab_rewrite_flushed_v1', 1);
}, 100);

function footerball_team_tab_url($post_id, $tab) {
    $post_id = (int) $post_id;
    $slug = get_post_field('post_name', $post_id);
    $allowed_tabs = array('profile', 'gear', 'players', 'news');

    if (!$slug || $post_id <= 0 || !in_array($tab, $allowed_tabs, true)) {
        return '';
    }

    return home_url('/teams/' . $slug . '/' . $tab . '/');
}

add_action('template_redirect', function () {
    $req_uri = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '';
    if (is_singular('teams')) {
        $post = get_queried_object();
        if (!$post) {
            return;
        }

        $post_id = (int) $post->ID;
        $slug = $post->post_name;
        $tab = get_query_var('team_tab');
        $valid_tabs = array('profile', 'gear', 'players', 'news');
        $canonical_tab = in_array($tab, $valid_tabs, true) ? $tab : 'profile';
        $path_raw = preg_replace('/\?.*$/', '', $req_uri);
        $has_query_params = isset($_GET['id']) || isset($_GET['tab']);
        $real_slug = ctype_digit($slug) ? get_post_field('post_name', $post_id) : $slug;
        $std_path = '/teams/' . $real_slug . '/' . $canonical_tab;

        if (rtrim($path_raw, '/') !== $std_path || $has_query_params) {
            wp_redirect(home_url($std_path . '/'), 301);
            exit;
        }
        return;
    }

    if (preg_match('#^/teams/([^/]+)/schedule/?(?:\?.*)?$#', $req_uri, $m)) {
        $slug = sanitize_title($m[1]);
        $team = get_page_by_path($slug, OBJECT, 'teams');
        if ($team && $team->post_status === 'publish') {
            wp_redirect(footerball_team_tab_url((int) $team->ID, 'profile'), 301);
            exit;
        }
    }

    if (preg_match('#^/teams/([0-9]+)(?:/(profile|gear|players|news))?/?#', $req_uri, $m)) {
        $post_id = (int) $m[1];
        $tab = !empty($m[2]) ? $m[2] : 'profile';
        $resolved = get_post($post_id);
        if ($resolved && $resolved->post_type === 'teams' && $resolved->post_status === 'publish') {
            wp_redirect(home_url('/teams/' . $resolved->post_name . '/' . $tab . '/'), 301);
            exit;
        }
    }
}, 1);

/* ============================================
   ✅ Register Players Custom Post Type
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
   ✅ Players ACF Fields (English)
   ============================================ */
add_action('acf/init', function () {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group(array(
        'key' => 'group_player_info_full',
        'title' => 'Player Info',
        'fields' => array(
            array('key' => 'field_player_number', 'label' => 'Squad Number', 'name' => 'number', 'type' => 'text'),
            array('key' => 'field_player_country', 'label' => 'Country', 'name' => 'country', 'type' => 'text'),
            array('key' => 'field_player_position', 'label' => 'Position', 'name' => 'position', 'type' => 'text'),
            array('key' => 'field_player_height', 'label' => 'Height', 'name' => 'height', 'type' => 'text'),
            array('key' => 'field_player_weight', 'label' => 'Weight', 'name' => 'weight', 'type' => 'text'),
            array('key' => 'field_player_birthday', 'label' => 'Date of Birth', 'name' => 'birthday', 'type' => 'date_picker', 'display_format' => 'F j, Y', 'return_format' => 'Y-m-d'),
            array('key' => 'field_player_birthplace', 'label' => 'Place of Birth', 'name' => 'birthplace', 'type' => 'text'),
            array('key' => 'field_player_foot', 'label' => 'Preferred Foot', 'name' => 'foot', 'type' => 'select', 'choices' => array('Right' => 'Right', 'Left' => 'Left', 'Both' => 'Both')),
            array('key' => 'field_player_club', 'label' => 'Club', 'name' => 'club', 'type' => 'text'),
            array('key' => 'field_player_team', 'label' => 'National Team', 'name' => 'player_team', 'type' => 'post_object', 'post_type' => array('teams'), 'return_format' => 'id', 'ui' => 1),
            array('key' => 'field_player_description', 'label' => 'Short Description', 'name' => 'description', 'type' => 'textarea', 'rows' => 3),
            array('key' => 'field_player_featured_image', 'label' => 'Featured Image', 'name' => 'featured_image', 'type' => 'image', 'return_format' => 'id', 'preview_size' => 'medium'),
            array('key' => 'field_player_profile', 'label' => 'Profile / Bio', 'name' => 'profile', 'type' => 'wysiwyg', 'toolbar' => 'basic', 'media_upload' => 0),
            array('key' => 'field_player_ohm', 'label' => 'Offensive Heat Map', 'name' => 'o_h_m', 'type' => 'image', 'return_format' => 'id'),
            /* Stats */
            array('key' => 'field_player_pace', 'label' => 'Pace', 'name' => 'pace', 'type' => 'number', 'min' => 0, 'max' => 99),
            array('key' => 'field_player_shooting', 'label' => 'Shooting', 'name' => 'shooting', 'type' => 'number', 'min' => 0, 'max' => 99),
            array('key' => 'field_player_passing', 'label' => 'Passing', 'name' => 'passing', 'type' => 'number', 'min' => 0, 'max' => 99),
            array('key' => 'field_player_dribbling', 'label' => 'Dribbling', 'name' => 'dribbling', 'type' => 'number', 'min' => 0, 'max' => 99),
            array('key' => 'field_player_defending', 'label' => 'Defending', 'name' => 'defending', 'type' => 'number', 'min' => 0, 'max' => 99),
            array('key' => 'field_player_physical', 'label' => 'Physical', 'name' => 'physical', 'type' => 'number', 'min' => 0, 'max' => 99),
            /* Career */
            array('key' => 'field_player_career_goals', 'label' => 'Career Goals', 'name' => 'career_goals', 'type' => 'number'),
            array('key' => 'field_player_career_apps', 'label' => 'Career Appearances', 'name' => 'career_appearances', 'type' => 'number'),
            array('key' => 'field_player_career_mins', 'label' => 'Career Minutes', 'name' => 'career_minutes', 'type' => 'number'),
            array(
                'key' => 'field_player_season_stats',
                'label' => 'Season Stats',
                'name' => 'season_stats',
                'type' => 'repeater',
                'layout' => 'table',
                'button_label' => 'Add Season',
                'sub_fields' => array(
                    array('key' => 'field_ss_season', 'label' => 'Season', 'name' => 'season', 'type' => 'text'),
                    array('key' => 'field_ss_club', 'label' => 'Club', 'name' => 'club', 'type' => 'text'),
                    array('key' => 'field_ss_apps', 'label' => 'Apps', 'name' => 'apps', 'type' => 'number'),
                    array('key' => 'field_ss_goals', 'label' => 'Goals', 'name' => 'goals', 'type' => 'number'),
                ),
            ),
            array(
                'key' => 'field_player_matches',
                'label' => 'Recent Matches',
                'name' => 'matches',
                'type' => 'repeater',
                'layout' => 'table',
                'sub_fields' => array(
                    array('key' => 'field_pm_date', 'label' => 'Date', 'name' => 'date', 'type' => 'text'),
                    array('key' => 'field_pm_comp', 'label' => 'Competition', 'name' => 'competition', 'type' => 'text'),
                    array('key' => 'field_pm_opp', 'label' => 'Opponent', 'name' => 'opponent', 'type' => 'text'),
                    array('key' => 'field_pm_venue', 'label' => 'Venue', 'name' => 'venue', 'type' => 'text'),
                    array('key' => 'field_pm_status', 'label' => 'Status', 'name' => 'status', 'type' => 'text'),
                ),
            ),
            array(
                'key' => 'field_player_market_values',
                'label' => 'Market Value History',
                'name' => 'market_values',
                'type' => 'repeater',
                'sub_fields' => array(
                    array('key' => 'field_mv_year', 'label' => 'Year', 'name' => 'year', 'type' => 'text'),
                    array('key' => 'field_mv_value', 'label' => 'Value (€M)', 'name' => 'value', 'type' => 'number'),
                ),
            ),
            /* Gear / poll / FAQ */
            array(
                'key' => 'field_player_gear',
                'label' => 'Player Gear / Merch',
                'name' => 'player_gear',
                'type' => 'repeater',
                'sub_fields' => array(
                    array('key' => 'field_pg_name', 'label' => 'Name', 'name' => 'name', 'type' => 'text'),
                    array('key' => 'field_pg_price', 'label' => 'Price', 'name' => 'price', 'type' => 'text'),
                    array('key' => 'field_pg_link', 'label' => 'Link', 'name' => 'link', 'type' => 'url'),
                    array('key' => 'field_pg_image', 'label' => 'Image', 'name' => 'image', 'type' => 'image', 'return_format' => 'id'),
                ),
            ),
            array(
                'key' => 'field_player_poll',
                'label' => 'Fan Poll',
                'name' => 'player_poll',
                'type' => 'repeater',
                'sub_fields' => array(
                    array('key' => 'field_pp_label', 'label' => 'Label', 'name' => 'label', 'type' => 'text'),
                    array('key' => 'field_pp_pct', 'label' => 'Percent', 'name' => 'percent', 'type' => 'text'),
                ),
            ),
            array(
                'key' => 'field_player_faq',
                'label' => 'FAQ',
                'name' => 'player_faq',
                'type' => 'repeater',
                'sub_fields' => array(
                    array('key' => 'field_faq_q', 'label' => 'Question', 'name' => 'question', 'type' => 'text'),
                    array('key' => 'field_faq_a', 'label' => 'Answer', 'name' => 'answer', 'type' => 'textarea'),
                ),
            ),
            /* ============================================
               Profile Page v3 - New Fields
               ============================================ */
            array('key' => 'field_major_honors', 'label' => 'Major Honors', 'name' => 'major_honors', 'type' => 'text', 'default_value' => ''),
            array('key' => 'field_playing_style', 'label' => 'Playing Style', 'name' => 'playing_style', 'type' => 'text', 'default_value' => ''),
            array('key' => 'field_national_team_info', 'label' => 'National Team Info', 'name' => 'national_team_info', 'type' => 'text', 'default_value' => ''),
            array('key' => 'field_health_status', 'label' => 'Health Status', 'name' => 'health_status', 'type' => 'select', 'choices' => array('healthy' => 'Healthy', 'injured' => 'Injured'), 'default_value' => 'healthy'),
            array('key' => 'field_health_desc', 'label' => 'Health Description', 'name' => 'health_desc', 'type' => 'textarea', 'rows' => 2, 'default_value' => ''),
            array('key' => 'field_health_update', 'label' => 'Health Last Updated', 'name' => 'health_update', 'type' => 'date_picker', 'display_format' => 'Y-m-d', 'return_format' => 'Y-m-d'),
            array(
                'key' => 'field_wc_matches',
                'label' => 'World Cup 2026 Matches',
                'name' => 'wc_matches',
                'type' => 'repeater',
                'layout' => 'table',
                'button_label' => 'Add Match',
                'sub_fields' => array(
                    array('key' => 'field_wcm_date', 'label' => 'Date', 'name' => 'date', 'type' => 'text', 'placeholder' => '2026-06-12'),
                    array('key' => 'field_wcm_time', 'label' => 'Time', 'name' => 'time', 'type' => 'text', 'placeholder' => 'Fri 03:00'),
                    array('key' => 'field_wcm_stage', 'label' => 'Stage/Group', 'name' => 'stage', 'type' => 'text', 'placeholder' => 'Group A - Matchday 1'),
                    array('key' => 'field_wcm_opponent', 'label' => 'Opponent', 'name' => 'opponent', 'type' => 'text'),
                    array('key' => 'field_wcm_opponent_flag', 'label' => 'Opponent Flag URL', 'name' => 'opponent_flag', 'type' => 'url'),
                    array('key' => 'field_wcm_venue', 'label' => 'Venue', 'name' => 'venue', 'type' => 'text', 'placeholder' => 'MetLife Stadium (New York)'),
                    array('key' => 'field_wcm_status', 'label' => 'Status', 'name' => 'status', 'type' => 'select', 'choices' => array('upcoming' => 'Upcoming', 'pending' => 'TBD')),
                ),
            ),
            array(
                'key' => 'field_quick_links',
                'label' => 'Quick Links',
                'name' => 'quick_links',
                'type' => 'repeater',
                'layout' => 'table',
                'button_label' => 'Add Link',
                'sub_fields' => array(
                    array('key' => 'field_ql_icon', 'label' => 'Icon', 'name' => 'icon', 'type' => 'text', 'placeholder' => '🛒'),
                    array('key' => 'field_ql_text', 'label' => 'Link Text', 'name' => 'text', 'type' => 'text'),
                    array('key' => 'field_ql_url', 'label' => 'URL', 'name' => 'url', 'type' => 'url'),
                ),
            ),
            /* ============================================
               World Cup 2026 Status (Hero Section)
               ============================================ */
            array('key' => 'field_wc_qualification', 'label' => 'WC Qualification Status', 'name' => 'wc_status_qualification', 'type' => 'text'),
            array('key' => 'field_wc_role', 'label' => 'WC Expected Role', 'name' => 'wc_status_role', 'type' => 'text'),
            array('key' => 'field_wc_next_match', 'label' => 'WC Next Match', 'name' => 'wc_status_next_match', 'type' => 'text'),
            /* ============================================
               Gear Page Fields
               ============================================ */
            array('key' => 'field_gear_intro', 'label' => 'Gear Intro Text', 'name' => 'gear_intro_text', 'type' => 'textarea', 'rows' => 3),
            array(
                'key' => 'field_gear_categories',
                'label' => 'Gear Categories',
                'name' => 'gear_categories',
                'type' => 'repeater',
                'layout' => 'table',
                'button_label' => 'Add Category',
                'sub_fields' => array(
                    array('key' => 'field_gc_name', 'label' => 'Category Name', 'name' => 'category_name', 'type' => 'text'),
                    array('key' => 'field_gc_desc', 'label' => 'Description', 'name' => 'category_desc', 'type' => 'textarea', 'rows' => 2),
                    array('key' => 'field_gc_image', 'label' => 'Category Image', 'name' => 'category_image', 'type' => 'image', 'return_format' => 'id'),
                    array('key' => 'field_gc_cta', 'label' => 'CTA Text', 'name' => 'category_cta', 'type' => 'text'),
                    array('key' => 'field_gc_link', 'label' => 'Link', 'name' => 'category_link', 'type' => 'url'),
                ),
            ),
            array('key' => 'field_on_pitch_analysis', 'label' => 'On-Pitch Gear Analysis', 'name' => 'on_pitch_analysis', 'type' => 'wysiwyg', 'toolbar' => 'basic', 'media_upload' => 0),
            array(
                'key' => 'field_recommended_gear',
                'label' => 'Recommended Gear Cards',
                'name' => 'recommended_gear',
                'type' => 'repeater',
                'layout' => 'table',
                'button_label' => 'Add Gear Item',
                'sub_fields' => array(
                    array('key' => 'field_rg_name', 'label' => 'Product Name', 'name' => 'rg_name', 'type' => 'text'),
                    array('key' => 'field_rg_price', 'label' => 'Price', 'name' => 'rg_price', 'type' => 'text'),
                    array('key' => 'field_rg_old_price', 'label' => 'Old Price', 'name' => 'rg_old_price', 'type' => 'text'),
                    array('key' => 'field_rg_badge', 'label' => 'Badge', 'name' => 'rg_badge', 'type' => 'text'),
                    array('key' => 'field_rg_rating', 'label' => 'Rating / Sold', 'name' => 'rg_rating', 'type' => 'text'),
                    array('key' => 'field_rg_reason', 'label' => 'Recommendation Reason', 'name' => 'rg_reason', 'type' => 'text'),
                    array('key' => 'field_rg_link', 'label' => 'Buy Link', 'name' => 'rg_link', 'type' => 'url'),
                    array('key' => 'field_rg_image', 'label' => 'Product Image URL / Attachment ID', 'name' => 'rg_image', 'type' => 'url'),
                ),
            ),
            array('key' => 'field_auth_jersey_desc', 'label' => 'Authentic Jersey Description', 'name' => 'authentic_jersey_desc', 'type' => 'textarea', 'rows' => 3),
            array('key' => 'field_auth_jersey_link', 'label' => 'Authentic Jersey Shop Link', 'name' => 'authentic_jersey_link', 'type' => 'url'),
            array('key' => 'field_replica_jersey_desc', 'label' => 'Replica Jersey Description', 'name' => 'replica_jersey_desc', 'type' => 'textarea', 'rows' => 3),
            array('key' => 'field_replica_jersey_link', 'label' => 'Replica Jersey Shop Link', 'name' => 'replica_jersey_link', 'type' => 'url'),
            array(
                'key' => 'field_collectibles',
                'label' => 'Collectibles & Merch',
                'name' => 'collectibles',
                'type' => 'repeater',
                'layout' => 'table',
                'button_label' => 'Add Collectible',
                'sub_fields' => array(
                    array('key' => 'field_coll_name', 'label' => 'Item Name', 'name' => 'coll_name', 'type' => 'text'),
                    array('key' => 'field_coll_tag', 'label' => 'Usage Tag', 'name' => 'coll_tag', 'type' => 'text'),
                    array('key' => 'field_coll_price', 'label' => 'Price', 'name' => 'coll_price', 'type' => 'text'),
                    array('key' => 'field_coll_link', 'label' => 'Link', 'name' => 'coll_link', 'type' => 'url'),
                    array('key' => 'field_coll_image', 'label' => 'Image URL / Attachment ID', 'name' => 'coll_image', 'type' => 'url'),
                ),
            ),
            array(
                'key' => 'field_player_gear_hot_searches',
                'label' => 'Player-Specific Hot Searches',
                'name' => 'player_gear_hot_searches',
                'type' => 'repeater',
                'layout' => 'table',
                'button_label' => 'Add Search Term',
                'instructions' => 'Leave empty to use the global defaults from Player Gear Settings.',
                'sub_fields' => array(
                    array('key' => 'field_pghs_term', 'label' => 'Search Term', 'name' => 'term', 'type' => 'text'),
                ),
            ),
            /* ============================================
               Stats Page Fields
               ============================================ */
            array('key' => 'field_career_summary', 'label' => 'Career Summary Text', 'name' => 'career_summary', 'type' => 'textarea', 'rows' => 5),
            array(
                'key' => 'field_career_badges',
                'label' => 'Career Highlight Badges',
                'name' => 'career_badges',
                'type' => 'repeater',
                'layout' => 'table',
                'button_label' => 'Add Badge',
                'instructions' => 'Use exact titles so facts map correctly: e.g. Senior club apps; Senior club goals OR for goalkeepers Senior club goals conceded (values from Transfermarkt «Performance data»); Senior international caps; Senior international goals OR Senior international goals conceded for GKs.',
                'sub_fields' => array(
                    array('key' => 'field_badge_icon', 'label' => 'Icon / Emoji', 'name' => 'badge_icon', 'type' => 'text'),
                    array('key' => 'field_badge_title', 'label' => 'Title', 'name' => 'badge_title', 'type' => 'text'),
                    array('key' => 'field_badge_label', 'label' => 'Small Label', 'name' => 'badge_label', 'type' => 'text'),
                    array('key' => 'field_badge_value', 'label' => 'Value', 'name' => 'badge_value', 'type' => 'text'),
                ),
            ),
            array('key' => 'field_wc_milestone_goals', 'label' => 'WC Milestone: Total Goals', 'name' => 'wc_milestone_goals', 'type' => 'text'),
            array('key' => 'field_wc_milestone_assists', 'label' => 'WC Milestone: Total Assists', 'name' => 'wc_milestone_assists', 'type' => 'text'),
            array('key' => 'field_wc_milestone_matches', 'label' => 'WC Milestone: Matches Played', 'name' => 'wc_milestone_matches', 'type' => 'text'),
            array(
                'key' => 'field_player_honors_list',
                'label' => 'Honors List',
                'name' => 'player_honors_list',
                'type' => 'repeater',
                'layout' => 'table',
                'button_label' => 'Add Honor',
                'sub_fields' => array(
                    array('key' => 'field_phl_icon', 'label' => 'Icon / Emoji', 'name' => 'icon', 'type' => 'text'),
                    array('key' => 'field_phl_title', 'label' => 'Honor Title', 'name' => 'title', 'type' => 'text'),
                    array('key' => 'field_phl_years', 'label' => 'Years', 'name' => 'years', 'type' => 'text'),
                ),
            ),
            array(
                'key' => 'field_player_erfolge_cards',
                'label' => 'Personal Honours (TM Erfolge layout)',
                'name' => 'player_erfolge_cards',
                'type' => 'repeater',
                'layout' => 'block',
                'button_label' => 'Add title / trophy block',
                'instructions' => 'Optional: richer honours (trophy image + season/club rows), modeled on Transfermarkt erfolge pages. When this repeater has rows, it replaces the legacy "Honors List" on the site. Populate via scripts/fetch_transfermarkt_erfolge.py JSON as a reference, then enter rows in WP admin.',
                'sub_fields' => array(
                    array('key' => 'field_pec_headline', 'label' => 'Headline (e.g. 3x Spanish champion)', 'name' => 'headline', 'type' => 'text'),
                    array('key' => 'field_pec_trophy_image', 'label' => 'Trophy image URL', 'name' => 'trophy_image', 'type' => 'url'),
                    array(
                        'key' => 'field_pec_season_rows',
                        'label' => 'Season / club rows',
                        'name' => 'season_rows',
                        'type' => 'repeater',
                        'layout' => 'table',
                        'button_label' => 'Add row',
                        'sub_fields' => array(
                            array('key' => 'field_pec_sr_season', 'label' => 'Season', 'name' => 'season', 'type' => 'text'),
                            array('key' => 'field_pec_sr_club', 'label' => 'Club / team', 'name' => 'club', 'type' => 'text'),
                            array('key' => 'field_pec_sr_club_logo', 'label' => 'Club crest URL', 'name' => 'club_logo', 'type' => 'url'),
                        ),
                    ),
                ),
            ),
            array(
                'key' => 'field_transfermarkt_profil_url',
                'label' => 'Transfermarkt profil URL',
                'name' => 'transfermarkt_profil_url',
                'type' => 'url',
                'instructions' => 'Optional. Full URL to the player profile on Transfermarkt (…/profil/spieler/…). Used for auditing and can be filled automatically by sync scripts.',
            ),
            array(
                'key' => 'field_recent_tournaments',
                'label' => 'Recent Tournaments',
                'name' => 'recent_tournaments',
                'type' => 'repeater',
                'layout' => 'table',
                'button_label' => 'Add Tournament',
                'sub_fields' => array(
                    array('key' => 'field_rt_competition', 'label' => 'Competition', 'name' => 'competition', 'type' => 'text'),
                    array('key' => 'field_rt_team', 'label' => 'Team / Result', 'name' => 'team', 'type' => 'text'),
                    array('key' => 'field_rt_note', 'label' => 'Note', 'name' => 'note', 'type' => 'text'),
                    array('key' => 'field_rt_rating', 'label' => 'Rating', 'name' => 'rating', 'type' => 'text'),
                ),
            ),
            array(
                'key' => 'field_advanced_stats',
                'label' => 'Advanced Stats',
                'name' => 'advanced_stats',
                'type' => 'repeater',
                'layout' => 'table',
                'button_label' => 'Add Stat',
                'sub_fields' => array(
                    array('key' => 'field_adv_label', 'label' => 'Label', 'name' => 'label', 'type' => 'text'),
                    array('key' => 'field_adv_value', 'label' => 'Value', 'name' => 'value', 'type' => 'text'),
                ),
            ),
            array('key' => 'field_stats_heatmap_image', 'label' => 'Stats Heat Map Image', 'name' => 'stats_heatmap_image', 'type' => 'image', 'return_format' => 'id'),
            array('key' => 'field_stats_source_label', 'label' => 'Stats Source Label', 'name' => 'stats_source_label', 'type' => 'text'),
            array('key' => 'field_stats_source_url', 'label' => 'Stats Source URL', 'name' => 'stats_source_url', 'type' => 'url'),
            array('key' => 'field_stats_competition_source_label', 'label' => 'Stats Competition Source Label', 'name' => 'stats_competition_source_label', 'type' => 'text'),
            array('key' => 'field_stats_competition_source_url', 'label' => 'Stats Competition Source URL', 'name' => 'stats_competition_source_url', 'type' => 'url'),
            array(
                'key' => 'field_stats_competition_records',
                'label' => 'Stats Competition Records',
                'name' => 'stats_competition_records',
                'type' => 'repeater',
                'layout' => 'table',
                'button_label' => 'Add Competition Record',
                'sub_fields' => array(
                    array('key' => 'field_scr_year', 'label' => 'Years', 'name' => 'wms_year', 'type' => 'text'),
                    array('key' => 'field_scr_stage', 'label' => 'Record Type', 'name' => 'wms_stage', 'type' => 'text'),
                    array('key' => 'field_scr_competition', 'label' => 'Competition', 'name' => 'wms_opponent', 'type' => 'text'),
                    array('key' => 'field_scr_apps', 'label' => 'Apps', 'name' => 'wms_minutes', 'type' => 'number'),
                    array('key' => 'field_scr_goals', 'label' => 'Goals', 'name' => 'wms_goals', 'type' => 'number'),
                    array('key' => 'field_scr_source', 'label' => 'Source Label', 'name' => 'competition', 'type' => 'text'),
                ),
            ),
            array(
                'key' => 'field_wc_match_stats',
                'label' => 'World Cup Match Stats',
                'name' => 'wc_match_stats',
                'type' => 'repeater',
                'layout' => 'table',
                'button_label' => 'Add Match',
                'sub_fields' => array(
                    array('key' => 'field_wms_year', 'label' => 'Year', 'name' => 'wms_year', 'type' => 'text'),
                    array('key' => 'field_wms_stage', 'label' => 'Stage', 'name' => 'wms_stage', 'type' => 'text'),
                    array('key' => 'field_wms_opponent', 'label' => 'Opponent', 'name' => 'wms_opponent', 'type' => 'text'),
                    array('key' => 'field_wms_minutes', 'label' => 'Minutes', 'name' => 'wms_minutes', 'type' => 'number'),
                    array('key' => 'field_wms_goals', 'label' => 'Goals', 'name' => 'wms_goals', 'type' => 'number'),
                    array('key' => 'field_wms_assists', 'label' => 'Assists', 'name' => 'wms_assists', 'type' => 'number'),
                    array('key' => 'field_wms_yc', 'label' => 'Yellow Cards', 'name' => 'wms_yc', 'type' => 'number'),
                    array('key' => 'field_wms_rc', 'label' => 'Red Cards', 'name' => 'wms_rc', 'type' => 'number'),
                    array('key' => 'field_wms_competition', 'label' => 'Competition', 'name' => 'competition', 'type' => 'text'),
                ),
            ),
        ),
        'location' => array(
            array(
                array('param' => 'post_type', 'operator' => '==', 'value' => 'players'),
            ),
        ),
        'position' => 'normal',
        'style' => 'default',
        'active' => true,
    ));

    acf_add_local_field_group(array(
        'key' => 'group_player_gear_settings',
        'title' => 'Player Gear Settings',
        'fields' => array(
            array(
                'key' => 'field_default_gear_hot_searches',
                'label' => 'Default Hot Searches',
                'name' => 'default_gear_hot_searches',
                'type' => 'repeater',
                'layout' => 'table',
                'button_label' => 'Add Search Term',
                'sub_fields' => array(
                    array('key' => 'field_dghs_term', 'label' => 'Search Term', 'name' => 'term', 'type' => 'text'),
                ),
            ),
            array(
                'key' => 'field_default_gear_buying_tips',
                'label' => 'Default Buying Tips',
                'name' => 'default_gear_buying_tips',
                'type' => 'repeater',
                'layout' => 'table',
                'button_label' => 'Add Tip',
                'sub_fields' => array(
                    array('key' => 'field_dgbt_title', 'label' => 'Title', 'name' => 'title', 'type' => 'text'),
                    array('key' => 'field_dgbt_desc', 'label' => 'Description', 'name' => 'desc', 'type' => 'textarea', 'rows' => 2),
                ),
            ),
        ),
        'location' => array(
            array(
                array('param' => 'options_page', 'operator' => '==', 'value' => 'player-gear-settings'),
            ),
        ),
        'position' => 'normal',
        'style' => 'default',
        'active' => true,
    ));
});

/* ============================================
   ✅ Players Tab Rewrite（/players/{slug}/{tab}/）
   ============================================ */
add_action('init', function () {
    add_rewrite_tag('%player_tab%', '(profile|gear|stats|updates)');

    /* 1) Standard sub-path: /players/{slug}/{tab}/ */
    add_rewrite_rule(
        '^players/([^/]+)/(profile|gear|stats|updates)/?$',
        'index.php?players=$matches[1]&player_tab=$matches[2]',
        'top'
    );

    /* 2) Numeric ID sub-path: /players/{id}/{tab}/ → redirected 301 by handler */
    add_rewrite_rule(
        '^players/([0-9]+)/(profile|gear|stats|updates)/?$',
        'index.php?players=$matches[1]&player_tab=$matches[2]',
        'top'
    );

    /* 3) Pure numeric ID: /players/{id}/ → redirected 301 by handler */
    add_rewrite_rule(
        '^players/([0-9]+)/?$',
        'index.php?players=$matches[1]',
        'top'
    );

    /* 4) Legacy /news/ → 301 → /updates/ */
    add_rewrite_rule(
        '^players/([^/]+)/news/?$',
        'index.php?players=$matches[1]&player_tab_redirect=news',
        'top'
    );
}, 10);

add_filter('query_vars', function ($vars) {
    $vars[] = 'player_tab';
    return $vars;
});

/**
 * Build the standard URL from post ID + tab slug: /players/{slug}/{tab}/
 */
function footerball_player_tab_url($post_id, $tab) {
    $post_id = (int) $post_id;
    $slug    = get_post_field('post_name', $post_id);
    if (!$slug || $post_id <= 0) {
        return '';
    }
    $allowed = array('profile', 'gear', 'stats', 'updates');
    if (!in_array($tab, $allowed, true)) {
        $tab = 'profile';
    }
    return home_url('/players/' . $slug . '/' . $tab . '/');
}

/**
 * Players Tab 301 Redirect
 * - /players/{slug}/         → 301 → /players/{slug}/profile/
 * - /players/{id}/           → 301 → /players/{real-slug}/profile/
 * - /players/{id}/{tab}/     → 301 → /players/{real-slug}/{tab}/
 * - /players/{slug}/?id=&tab= → 301 → /players/{slug}/profile/
 *
 * Each tab's canonical URL is the standard sub-path, indexed separately by Google.
 */
add_action('template_redirect', function () {
    /* ---- Players Pages ---- */
    if (is_singular('players')) {
        $post       = get_queried_object();
        $post_id    = (int) $post->ID;
        $slug       = $post->post_name;
        $tab        = get_query_var('player_tab');
        $valid_tabs = array('profile', 'gear', 'stats', 'updates');

        $canonical_tab = in_array($tab, $valid_tabs, true) ? $tab : 'profile';

        /* Parse the requested raw path (without query string) */
        $req_uri     = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
        $path_raw    = preg_replace('/\?.*$/', '', $req_uri);
        $path_parts  = array_filter(explode('/', $path_raw));
        $path_parts  = array_values($path_parts);

        /* Detect legacy query-param URLs */
        $has_query_params = isset($_GET['id']) || isset($_GET['tab']);

        /* If the slug is numeric (i.e. /players/123456/), find the real post */
        $real_slug = $slug;
        $slug_is_numeric = ctype_digit($slug);
        if ($slug_is_numeric && $post_id > 0) {
            $real_post = get_post($post_id);
            if ($real_post && $real_post->post_name) {
                $real_slug = $real_post->post_name;
            }
        }

        /* Legacy /news/ → 301 → /updates/ */
        $last_part = end($path_parts);
        if ($last_part === 'news') {
            array_pop($path_parts);
            $path_parts[] = 'updates';
            $new_path = '/' . implode('/', $path_parts) . '/';
            wp_redirect(home_url($new_path), 301);
            exit;
        }

        /* Standard path */
        $std_path = '/players/' . $real_slug . '/' . $canonical_tab;

        $path_no_trail = rtrim($path_raw, '/');
        if ($path_no_trail !== $std_path || $has_query_params) {
            wp_redirect(home_url($std_path . '/'), 301);
            exit;
        }
        return;
    }

    /* ---- Intercept /players/{id}/{tab}/ or /players/{id}/ before is_singular ---- */
    $req_uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
    if (preg_match('#^/players/([0-9]+)(?:/(profile|gear|stats|updates))?/?#', $req_uri, $m)) {
        $pid   = (int) $m[1];
        $rtab  = !empty($m[2]) ? $m[2] : 'profile';
        $valid = array('profile', 'gear', 'stats', 'updates');
        if (!in_array($rtab, $valid, true)) {
            $rtab = 'profile';
        }
        $resolved = get_post($pid);
        if ($resolved && $resolved->post_type === 'players' && $resolved->post_status === 'publish') {
            $rslug = $resolved->post_name;
            wp_redirect(home_url('/players/' . $rslug . '/' . $rtab . '/'), 301);
            exit;
        }
    }

    /* ---- Legacy /news/ → /updates/ redirect ---- */
    if (preg_match('#^/players/([^/]+)/news/?$#', $req_uri, $m)) {
        $slug_part = $m[1];
        if (ctype_digit($slug_part)) {
            $resolved = get_post((int) $slug_part);
            if ($resolved && $resolved->post_type === 'players') {
                wp_redirect(home_url('/players/' . $resolved->post_name . '/updates/'), 301);
                exit;
            }
        }
        wp_redirect(home_url('/players/' . $slug_part . '/updates/'), 301);
        exit;
    }
});

/* ============================================
   ✅ Register Lifestyle Custom Post Type
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
        'taxonomies'    => array('post_tag', 'category'),
        'menu_icon'     => 'dashicons-heart',
        'show_in_rest'  => true,
        'menu_position' => 6,
    ));
}
add_action('init', 'register_lifestyle_cpt');

add_action('add_meta_boxes', function () {
    add_meta_box(
        'footerball_lifestyle_seo_meta',
        'SEO Meta (Title & Description)',
        function ($post) {
            wp_nonce_field('footerball_save_lifestyle_seo_meta', 'footerball_lifestyle_seo_meta_nonce');

            $meta_title = get_post_meta($post->ID, '_footerball_lifestyle_meta_title', true);
            $meta_description = get_post_meta($post->ID, '_footerball_lifestyle_meta_description', true);

            echo '<p style="margin:0 0 12px;color:#555;">These fields override the browser title, meta description, and OG/Twitter tags for this lifestyle article. The on-page article headline always uses the article display title. Leave blank to use the current automatic values.</p>';

            echo '<p style="margin:14px 0 6px;"><label for="footerball_lifestyle_meta_title"><strong>Meta Title</strong></label></p>';
            echo '<input id="footerball_lifestyle_meta_title" name="footerball_lifestyle_meta_title" type="text" value="' . esc_attr($meta_title) . '" style="width:100%;" maxlength="120" placeholder="Recommended: 50-60 characters">';
            echo '<p style="margin:6px 0 14px;color:#777;">Used for <code>&lt;title&gt;</code>, <code>og:title</code>, and <code>twitter:title</code> only — not the on-page <code>&lt;h1&gt;</code>.</p>';

            echo '<p style="margin:14px 0 6px;"><label for="footerball_lifestyle_meta_description"><strong>Meta Description</strong></label></p>';
            echo '<textarea id="footerball_lifestyle_meta_description" name="footerball_lifestyle_meta_description" rows="4" style="width:100%;" maxlength="240" placeholder="Recommended: 140-160 characters">' . esc_textarea($meta_description) . '</textarea>';
            echo '<p style="margin:6px 0 0;color:#777;">Used for <code>meta name=&quot;description&quot;</code> and <code>og:description</code>.</p>';
        },
        'lifestyle',
        'normal',
        'high'
    );

    add_meta_box(
        'footerball_lifestyle_related_teams_meta',
        'Related Teams',
        function ($post) {
            wp_nonce_field('footerball_save_lifestyle_related_teams', 'footerball_lifestyle_related_teams_nonce');

            $selected_team_ids = footerball_get_lifestyle_related_team_ids($post->ID);
            $teams = get_posts(array(
                'post_type'      => 'teams',
                'post_status'    => 'publish',
                'posts_per_page' => -1,
                'orderby'        => 'title',
                'order'          => 'ASC',
            ));

            echo '<p style="margin:0 0 12px;color:#555;">Select one or more teams. This lifestyle article will appear on the selected team detail News tab.</p>';

            if (empty($teams)) {
                echo '<p style="color:#777;">No published teams found.</p>';
                return;
            }

            echo '<div style="max-height:260px;overflow:auto;border:1px solid #dcdcde;border-radius:4px;padding:10px 12px;background:#fff;">';
            foreach ($teams as $team_post) {
                $team_id = (int) $team_post->ID;
                echo '<label style="display:block;margin:0 0 8px;">';
                echo '<input type="checkbox" name="footerball_related_teams[]" value="' . esc_attr($team_id) . '"' . checked(in_array($team_id, $selected_team_ids, true), true, false) . '> ';
                echo esc_html(get_the_title($team_id));
                echo '</label>';
            }
            echo '</div>';
        },
        'lifestyle',
        'side',
        'default'
    );

    add_meta_box(
        'footerball_lifestyle_related_players_meta',
        'Related Players',
        function ($post) {
            wp_nonce_field('footerball_save_lifestyle_related_players', 'footerball_lifestyle_related_players_nonce');

            $selected_player_ids = footerball_get_lifestyle_related_player_ids($post->ID);
            $players = footerball_lifestyle_admin_player_posts_for_meta_box($selected_player_ids);

            echo '<p style="margin:0 0 12px;color:#555;">Select one or more players. This lifestyle article will appear on the selected player detail Updates tab.</p>';
            echo '<p style="margin:0 0 10px;color:#826200;font-size:12px;">Large catalogs load in batches for stability; players already linked below always stay visible even if they are outside the first page of names.</p>';

            if (empty($players)) {
                echo '<p style="color:#777;">No published players found.</p>';
                return;
            }

            echo '<div style="max-height:300px;overflow:auto;border:1px solid #dcdcde;border-radius:4px;padding:10px 12px;background:#fff;">';
            foreach ($players as $player_post) {
                $player_id = (int) $player_post->ID;
                echo '<label style="display:block;margin:0 0 8px;">';
                echo '<input type="checkbox" name="footerball_related_players[]" value="' . esc_attr($player_id) . '"' . checked(in_array($player_id, $selected_player_ids, true), true, false) . '> ';
                echo esc_html(get_the_title($player_id));
                echo '</label>';
            }
            echo '</div>';
        },
        'lifestyle',
        'side',
        'default'
    );

    add_meta_box(
        'footerball_lifestyle_related_host_cities_meta',
        'Related Host Cities',
        function ($post) {
            wp_nonce_field('footerball_save_lifestyle_related_host_cities', 'footerball_lifestyle_related_host_cities_nonce');

            $selected_city_ids = footerball_get_lifestyle_related_host_city_ids($post->ID);
            $cities = get_posts(array(
                'post_type'      => 'host_cities',
                'post_status'    => 'publish',
                'posts_per_page' => -1,
                'orderby'        => 'title',
                'order'          => 'ASC',
            ));

            echo '<p style="margin:0 0 12px;color:#555;">Select one or more host cities. This lifestyle article will appear on the selected city Stadium Day Guide tab.</p>';

            if (empty($cities)) {
                echo '<p style="color:#777;">No published host cities found.</p>';
                return;
            }

            echo '<div style="max-height:300px;overflow:auto;border:1px solid #dcdcde;border-radius:4px;padding:10px 12px;background:#fff;">';
            foreach ($cities as $city_post) {
                $city_id = (int) $city_post->ID;
                echo '<label style="display:block;margin:0 0 8px;">';
                echo '<input type="checkbox" name="footerball_related_host_cities[]" value="' . esc_attr($city_id) . '"' . checked(in_array($city_id, $selected_city_ids, true), true, false) . '> ';
                echo esc_html(get_the_title($city_id));
                echo '</label>';
            }
            echo '</div>';
        },
        'lifestyle',
        'side',
        'default'
    );
});

add_action('save_post_lifestyle', function ($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['footerball_lifestyle_seo_meta_nonce']) && wp_verify_nonce($_POST['footerball_lifestyle_seo_meta_nonce'], 'footerball_save_lifestyle_seo_meta')) {
        $fields = array(
            'meta_title' => 'footerball_lifestyle_meta_title',
            'meta_description' => 'footerball_lifestyle_meta_description',
        );

        foreach ($fields as $meta_key => $post_key) {
            $value = isset($_POST[$post_key]) ? footerball_clean_meta_text(wp_unslash($_POST[$post_key])) : '';
            $storage_key = '_footerball_lifestyle_' . $meta_key;
            if ($value === '') {
                delete_post_meta($post_id, $storage_key);
            } else {
                update_post_meta($post_id, $storage_key, $value);
            }
        }
    }

    if (isset($_POST['footerball_lifestyle_related_teams_nonce']) && wp_verify_nonce($_POST['footerball_lifestyle_related_teams_nonce'], 'footerball_save_lifestyle_related_teams')) {
        $team_ids = isset($_POST['footerball_related_teams']) ? (array) wp_unslash($_POST['footerball_related_teams']) : array();
        $team_ids = array_values(array_unique(array_filter(array_map('absint', $team_ids))));
        if (empty($team_ids)) {
            delete_post_meta($post_id, '_footerball_related_teams');
            delete_post_meta($post_id, '_footerball_related_team');
        } else {
            update_post_meta($post_id, '_footerball_related_teams', array_map('strval', $team_ids));
            delete_post_meta($post_id, '_footerball_related_team');
            foreach ($team_ids as $team_id) {
                add_post_meta($post_id, '_footerball_related_team', (string) $team_id, false);
            }
        }
    }

    if (isset($_POST['footerball_lifestyle_related_players_nonce']) && wp_verify_nonce($_POST['footerball_lifestyle_related_players_nonce'], 'footerball_save_lifestyle_related_players')) {
        $player_ids = isset($_POST['footerball_related_players']) ? (array) wp_unslash($_POST['footerball_related_players']) : array();
        $player_ids = array_values(array_unique(array_filter(array_map('absint', $player_ids))));
        if (empty($player_ids)) {
            delete_post_meta($post_id, '_footerball_related_players');
            delete_post_meta($post_id, '_footerball_related_player');
        } else {
            update_post_meta($post_id, '_footerball_related_players', array_map('strval', $player_ids));
            delete_post_meta($post_id, '_footerball_related_player');
            foreach ($player_ids as $player_id) {
                add_post_meta($post_id, '_footerball_related_player', (string) $player_id, false);
            }
        }
    }

    if (isset($_POST['footerball_lifestyle_related_host_cities_nonce']) && wp_verify_nonce($_POST['footerball_lifestyle_related_host_cities_nonce'], 'footerball_save_lifestyle_related_host_cities')) {
        $city_ids = isset($_POST['footerball_related_host_cities']) ? (array) wp_unslash($_POST['footerball_related_host_cities']) : array();
        $city_ids = array_values(array_unique(array_filter(array_map('absint', $city_ids))));
        if (empty($city_ids)) {
            delete_post_meta($post_id, '_footerball_related_host_cities');
            delete_post_meta($post_id, '_footerball_related_host_city');
        } else {
            update_post_meta($post_id, '_footerball_related_host_cities', array_map('strval', $city_ids));
            delete_post_meta($post_id, '_footerball_related_host_city');
            foreach ($city_ids as $city_id) {
                add_post_meta($post_id, '_footerball_related_host_city', (string) $city_id, false);
            }
        }
    }
});

function footerball_get_lifestyle_archive_cards() {
    $theme_uri = get_stylesheet_directory_uri();
    $fallback_images = array(
        $theme_uri . '/images/home-hero-trophy.webp',
        $theme_uri . '/images/players/christian-pulisic-82fbf5d0b2.webp',
        $theme_uri . '/images/host-cities/host-city-toronto.webp',
        $theme_uri . '/images/players/lamine-yamal-06edaa0df4.webp',
        $theme_uri . '/images/players/lionel-messi-4de7433b49.webp',
        $theme_uri . '/images/host-cities/host-city-mexico-city.webp',
        $theme_uri . '/images/host-city-new-york-metlife-1600.webp',
        $theme_uri . '/images/players/united-states-f7bedd1915.webp',
    );

    $demo_posts = array(
        array('title' => 'The Road to Glory: What to Expect at World Cup 2026', 'category' => 'Featured', 'excerpt' => 'From expanded format to iconic host cities, we break down everything fans need to know about the most ambitious World Cup yet.', 'date' => 'May 24, 2025', 'read' => '8 min read', 'image' => $fallback_images[0], 'link' => home_url('/?s=' . rawurlencode('The Road to Glory: What to Expect at World Cup 2026'))),
        array('title' => 'USA vs England: Group Clash Preview & Key Battles', 'category' => 'Match Preview', 'excerpt' => 'A tactical preview of the midfield matchups, set pieces, and pressure points that could define a headline fixture.', 'date' => 'May 23, 2025', 'read' => '5 min read', 'image' => $fallback_images[1], 'link' => home_url('/?s=' . rawurlencode('USA vs England: Group Clash Preview & Key Battles'))),
        array('title' => 'Toronto: A World Cup City Ready to Welcome the World', 'category' => 'Host City Guide', 'excerpt' => 'Explore fan zones, skyline views, transport tips, and local experiences for supporters traveling north.', 'date' => 'May 22, 2025', 'read' => '6 min read', 'image' => $fallback_images[2], 'link' => home_url('/?s=' . rawurlencode('Toronto: A World Cup City Ready to Welcome the World'))),
        array('title' => '16 Young Stars to Watch in World Cup 2026', 'category' => 'Player Focus', 'excerpt' => 'The next generation is ready to shine. These are the rising talents fans should keep on their radar.', 'date' => 'May 22, 2025', 'read' => '7 min read', 'image' => $fallback_images[3], 'link' => home_url('/?s=' . rawurlencode('16 Young Stars to Watch in World Cup 2026'))),
        array('title' => 'Can Messi Make One More World Cup Run?', 'category' => 'Fan Stories', 'excerpt' => 'The conversations, hopes, and hard questions around one of footballs most watched storylines.', 'date' => 'May 21, 2025', 'read' => '4 min read', 'image' => $fallback_images[4], 'link' => home_url('/?s=' . rawurlencode('Can Messi Make One More World Cup Run?'))),
        array('title' => 'How Mexico Could Shape Their World Cup Story', 'category' => 'Tactical Analysis', 'excerpt' => 'A closer look at structure, pressing choices, and creative routes through the final third.', 'date' => 'May 20, 2025', 'read' => '6 min read', 'image' => $fallback_images[5], 'link' => home_url('/?s=' . rawurlencode('How Mexico Could Shape Their World Cup Story'))),
        array('title' => 'Fan Essentials for a Full Matchday', 'category' => 'Fan Shopping Tips', 'excerpt' => 'From jerseys to travel-friendly accessories, this checklist keeps supporters ready from kickoff to stoppage time.', 'date' => 'May 19, 2025', 'read' => '4 min read', 'image' => $fallback_images[6], 'link' => home_url('/?s=' . rawurlencode('Fan Essentials for a Full Matchday'))),
        array('title' => 'USA Squad Update: Key Injuries & Call-ups', 'category' => 'Team News', 'excerpt' => 'Latest team notes, depth-chart movement, and how the roster picture may change before the tournament.', 'date' => 'May 18, 2025', 'read' => '3 min read', 'image' => $fallback_images[7], 'link' => home_url('/?s=' . rawurlencode('USA Squad Update: Key Injuries & Call-ups'))),
    );
    foreach ($demo_posts as &$demo_post) {
        if (empty($demo_post['link']) || $demo_post['link'] === '#') {
            $demo_post['link'] = home_url('/?s=' . rawurlencode($demo_post['title']));
        }
    }
    unset($demo_post);

    $posts = get_posts(array(
        'post_type'      => 'lifestyle',
        'post_status'    => 'publish',
        'posts_per_page' => 12,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ));

    $cards = array();
    foreach ($posts as $index => $post_obj) {
        $post_id = (int) $post_obj->ID;
        $article = function_exists('get_field') ? get_field('article', $post_id) : array();
        $title = footerball_get_lifestyle_display_title($post_id, $article);
        $excerpt = footerball_get_lifestyle_list_excerpt($post_id, 22, $article);
        $image = '';

        if (!empty($article['post_thumbnail'])) {
            $image = wp_get_attachment_image_url((int) $article['post_thumbnail'], 'large');
        }
        if (!$image && has_post_thumbnail($post_id)) {
            $image = get_the_post_thumbnail_url($post_id, 'large');
        }
        if (!$image && function_exists('footerball_get_lifestyle_editorial_hero_url')) {
            $image = footerball_get_lifestyle_editorial_hero_url($post_id);
        }
        if (!$image) {
            $image = $fallback_images[$index % count($fallback_images)];
        }

        $category = 'World Cup 2026';
        if (!empty($article['post_tags']) && is_array($article['post_tags'])) {
            $category = (string) reset($article['post_tags']);
        } else {
            $terms = get_the_terms($post_id, 'category');
            if (!empty($terms) && !is_wp_error($terms)) {
                $category = $terms[0]->name;
            }
        }

        $words = str_word_count(footerball_get_lifestyle_body_plaintext($post_id, $article));
        $cards[] = array(
            'title'    => $title,
            'category' => $category,
            'excerpt'  => $excerpt,
            'date'     => !empty($article['post_date']) ? $article['post_date'] : get_the_date('M j, Y', $post_id),
            'read'     => max(3, (int) ceil($words / 220)) . ' min read',
            'image'    => $image,
            'link'     => get_permalink($post_id),
        );
    }

    return !empty($cards) ? $cards : $demo_posts;
}

function footerball_render_lifestyle_archive_page() {
    $theme_uri = get_stylesheet_directory_uri();
    $cards = footerball_get_lifestyle_archive_cards();
    $featured = $cards[0];
    $grid_cards = array_slice($cards, 1);
    $topics = array('USA Squad Updates', 'World Cup 2026 Draw', 'Host Cities Guide', 'Messi in 2026?', 'Fan Gear Deals');
    $teams = array(
        array('name' => 'USA', 'code' => 'US'),
        array('name' => 'Mexico', 'code' => 'MX'),
        array('name' => 'Brazil', 'code' => 'BR'),
        array('name' => 'England', 'code' => 'EN'),
        array('name' => 'Argentina', 'code' => 'AR'),
        array('name' => 'France', 'code' => 'FR'),
        array('name' => 'Germany', 'code' => 'DE'),
        array('name' => 'Spain', 'code' => 'ES'),
    );

    get_header();
    ?>
	    <main class="lifestyle-archive">
	        <section class="lifestyle-hero" style="--hero-bg: url('<?php echo esc_url($theme_uri . '/images/home-hero-trophy.webp'); ?>');">
            <div class="lifestyle-shell">
                <p class="lifestyle-kicker">Football 2026 Fan Hub</p>
                <h1>Latest News &amp; Insights</h1>
                <p>From match previews and tactical analysis to host city guides and fan essentials for the biggest football celebration in North America.</p>
            </div>
        </section>
        <section class="lifestyle-board">
            <div class="lifestyle-shell lifestyle-layout">
                <div class="lifestyle-main">
                    <div class="lifestyle-toolbar">
                        <div class="lifestyle-tabs" aria-label="Article filters">
                            <button class="is-active" type="button">All</button>
                            <button type="button">Match Preview</button>
                            <button type="button">Tactical Analysis</button>
                            <button type="button">Player Focus</button>
                            <button type="button">Team News</button>
                            <button type="button">Host City Guide</button>
                            <button type="button">Fan Shopping Tips</button>
                        </div>
                        <div class="lifestyle-search-row">
                            <label class="lifestyle-search"><span>Search</span><input type="search" placeholder="Search news, players, teams, topics..."></label>
                            <label class="lifestyle-sort"><span>Sort by</span><select><option>Latest</option><option>Most Popular</option><option>Editor's Picks</option></select></label>
                        </div>
                    </div>
                    <a class="lifestyle-featured" href="<?php echo esc_url($featured['link']); ?>"<?php echo footerball_internal_link_attrs($featured['link']); ?>>
                        <div class="lifestyle-featured-media">
                            <img src="<?php echo esc_url($featured['image']); ?>" alt="<?php echo esc_attr($featured['title']); ?>">
                            <span><?php echo esc_html($featured['category']); ?></span>
                        </div>
                        <div class="lifestyle-featured-body">
                            <p class="lifestyle-overline">World Cup 2026</p>
                            <h2><?php echo esc_html($featured['title']); ?></h2>
                            <p><?php echo esc_html($featured['excerpt']); ?></p>
                            <div class="lifestyle-card-meta"><span>DHgate Editorial Team</span><span><?php echo esc_html($featured['date']); ?></span><span><?php echo esc_html($featured['read']); ?></span></div>
                            <strong>Read full story &rarr;</strong>
                        </div>
                    </a>
                    <div class="lifestyle-grid">
                        <?php foreach ($grid_cards as $card) : ?>
                            <a class="lifestyle-card" href="<?php echo esc_url($card['link']); ?>"<?php echo footerball_internal_link_attrs($card['link']); ?>>
                                <div class="lifestyle-card-media">
                                    <img src="<?php echo esc_url($card['image']); ?>" alt="<?php echo esc_attr($card['title']); ?>">
                                    <span><?php echo esc_html($card['category']); ?></span>
                                </div>
                                <div class="lifestyle-card-body">
                                    <h3><?php echo esc_html($card['title']); ?></h3>
                                    <p><?php echo esc_html($card['excerpt']); ?></p>
                                    <div class="lifestyle-card-meta"><span><?php echo esc_html($card['date']); ?></span><span><?php echo esc_html($card['read']); ?></span></div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <button class="lifestyle-load" type="button">Load More Articles</button>
                </div>
                <aside class="lifestyle-rail">
                    <section class="lifestyle-panel">
                        <div class="lifestyle-panel-head"><h2>Trending Topics</h2><a href="<?php echo esc_url(home_url('/lifestyle/')); ?>"<?php echo footerball_internal_link_attrs(home_url('/lifestyle/')); ?>>View all</a></div>
                        <ol class="topic-list">
                            <?php foreach ($topics as $topic_index => $topic) : ?>
                                <li><span><?php echo esc_html($topic_index + 1); ?></span><strong><?php echo esc_html($topic); ?></strong><em><?php echo esc_html(number_format(12.4 - ($topic_index * 1.7), 1)); ?>K posts</em></li>
                            <?php endforeach; ?>
                        </ol>
	                    </section>
	                    <section class="lifestyle-panel">
	                        <?php echo footerball_render_upcoming_matches_module(array(), array('url' => home_url('/matches/'), 'class' => 'fb-upcoming-matches--panel')); ?>
	                    </section>
                    <section class="lifestyle-panel">
                        <div class="lifestyle-panel-head"><h2>Popular Teams</h2><a href="<?php echo esc_url(footerball_archive_url('teams', '/teams/')); ?>"<?php echo footerball_internal_link_attrs(footerball_archive_url('teams', '/teams/')); ?>>View all</a></div>
                        <div class="team-chip-grid">
                            <?php foreach ($teams as $team) : ?>
                                <?php $demo_team_archive = footerball_archive_url('teams', '/teams/'); ?>
                                <a href="<?php echo esc_url($demo_team_archive); ?>"<?php echo footerball_internal_link_attrs($demo_team_archive); ?>><span><?php echo esc_html($team['code']); ?></span><?php echo esc_html($team['name']); ?></a>
                            <?php endforeach; ?>
                        </div>
                    </section>
                    <section class="lifestyle-panel lifestyle-newsletter">
                        <h2>Newsletter</h2>
                        <p>Get the latest World Cup 2026 news, exclusive insights and special offers.</p>
                        <form><input type="email" placeholder="Enter your email"><button type="submit">Subscribe</button></form>
                        <small>We respect your privacy.</small>
                    </section>
                    <section class="lifestyle-panel">
                        <div class="lifestyle-panel-head"><h2>Shop Fan Essentials</h2><a href="<?php echo esc_url(footerball_dhgate_search_url('football fan essentials')); ?>" target="_blank" rel="nofollow sponsored noopener">View all</a></div>
                        <div class="shop-mini-grid">
                            <a href="<?php echo esc_url(footerball_dhgate_search_url('football replica jersey')); ?>" target="_blank" rel="nofollow sponsored noopener"><img src="<?php echo esc_url($theme_uri . '/images/dhgate-logo.png'); ?>" alt="Replica jersey"><strong>Replica Jersey</strong><span>US $29.99</span></a>
                            <a href="<?php echo esc_url(footerball_dhgate_search_url('football fan scarf')); ?>" target="_blank" rel="nofollow sponsored noopener"><img src="<?php echo esc_url($theme_uri . '/images/dhgate-logo.png'); ?>" alt="Scarf"><strong>Fan Scarf</strong><span>US $12.99</span></a>
                            <a href="<?php echo esc_url(footerball_dhgate_search_url('football cap')); ?>" target="_blank" rel="nofollow sponsored noopener"><img src="<?php echo esc_url($theme_uri . '/images/dhgate-logo.png'); ?>" alt="Cap"><strong>Cap</strong><span>US $9.99</span></a>
                        </div>
                    </section>
                </aside>
            </div>
        </section>
    </main>
    <?php
    get_footer();
}

add_action('template_redirect', function () {
    if (!is_post_type_archive('lifestyle')) {
        return;
    }

    $custom = get_stylesheet_directory() . '/archive-lifestyle.php';
    if (file_exists($custom)) {
        return;
    }

    footerball_render_lifestyle_archive_page();
    exit;
}, 20);

/* ============================================
   ✅ Register Host Cities Custom Post Type
   ============================================ */
function register_host_cities_cpt() {
    register_post_type('host_cities', array(
        'label'         => 'Host Cities',
        'labels'        => array(
            'name'          => 'Host Cities',
            'singular_name' => 'Host City',
            'add_new'       => 'Add New Host City',
            'edit_item'     => 'Edit Host City',
            'view_item'     => 'View Host City',
            'all_items'     => 'All Host Cities',
        ),
        'public'        => true,
        'has_archive'   => true,
        'rewrite'       => array('slug' => 'host-cities'),
        'supports'      => array('title', 'editor', 'thumbnail', 'excerpt'),
        'menu_icon'     => 'dashicons-location-alt',
        'show_in_rest'  => true,
        'menu_position' => 7,
    ));
}
add_action('init', 'register_host_cities_cpt');

/**
 * Host city hero / card image: JSON map (Wikimedia URLs) shipped with the theme.
 *
 * @return array<string, array<string, mixed>>
 */
function footerball_host_city_seo_image_map() {
    static $map = null;
    if ($map !== null) {
        return $map;
    }
    $path = get_stylesheet_directory() . '/images/host-cities/seo-image-metadata.json';
    if (!is_readable($path)) {
        $map = array();
        return $map;
    }
    $decoded = json_decode(file_get_contents($path), true);
    $map = is_array($decoded) ? $decoded : array();
    return $map;
}

/**
 * Banner URL for a host city: ACF image → theme file on disk → JSON remote URL → known CDN fallback.
 * Ensures images work on production even if theme /images/* binaries were not uploaded.
 *
 * @param int $post_id Host city post ID.
 * @return string
 */
function footerball_host_city_banner_url($post_id) {
    $post_id = (int) $post_id;
    if ($post_id <= 0) {
        return '';
    }

    if (function_exists('get_field')) {
        $banner_id = get_field('host_city_banner_image', $post_id);
        if ($banner_id) {
            $url = wp_get_attachment_image_url((int) $banner_id, 'full');
            if ($url) {
                return $url;
            }
        }
    }

    $slug = get_post_field('post_name', $post_id);
    $dir = get_stylesheet_directory();
    $uri = get_stylesheet_directory_uri();

    foreach (array('webp', 'jpg', 'png') as $ext) {
        $rel = '/images/host-cities/host-city-' . $slug . '.' . $ext;
        if (file_exists($dir . $rel)) {
            return $uri . $rel;
        }
    }

    $map = footerball_host_city_seo_image_map();
    if ($slug && !empty($map[$slug]['image_url'])) {
        return $map[$slug]['image_url'];
    }

    $fallback_rel = '/images/host-city-new-york-metlife-1600.webp';
    if (file_exists($dir . $fallback_rel)) {
        return $uri . $fallback_rel;
    }
    $fallback_jpg = '/images/host-city-new-york-metlife-1600.jpg';
    if (file_exists($dir . $fallback_jpg)) {
        return $uri . $fallback_jpg;
    }

    if (!empty($map['new-york']['image_url'])) {
        return $map['new-york']['image_url'];
    }

    return '';
}

/* ============================================
   ✅ Host City Tab Rewrite（/host-cities/{slug}/{tab}/）
   ============================================ */
add_action('init', function () {
    add_rewrite_tag('%host_city_tab%', '(ticket-information|fixtures|stadium-day-guide|official-gear)');

    add_rewrite_rule(
        '^host-cities/([^/]+)/(ticket-information|fixtures|stadium-day-guide|official-gear)/?$',
        'index.php?host_cities=$matches[1]&host_city_tab=$matches[2]',
        'top'
    );

    add_rewrite_rule(
        '^host-cities/([0-9]+)/(ticket-information|fixtures|stadium-day-guide|official-gear)/?$',
        'index.php?host_cities=$matches[1]&host_city_tab=$matches[2]',
        'top'
    );

    add_rewrite_rule(
        '^host-cities/([0-9]+)/?$',
        'index.php?host_cities=$matches[1]',
        'top'
    );
}, 10);

add_filter('query_vars', function ($vars) {
    $vars[] = 'host_city_tab';
    return $vars;
});

function footerball_host_city_tab_url($post_id, $tab) {
    $post_id = (int) $post_id;
    $slug = get_post_field('post_name', $post_id);
    $allowed = array('ticket-information', 'fixtures', 'stadium-day-guide', 'official-gear');
    if (!$slug || $post_id <= 0) {
        return '';
    }
    if (!in_array($tab, $allowed, true)) {
        $tab = 'ticket-information';
    }
    return home_url('/host-cities/' . $slug . '/' . $tab . '/');
}

function footerball_default_tab_permalink($permalink, $post) {
    $post = get_post($post);
    if (!$post instanceof WP_Post) {
        return $permalink;
    }

    if ($post->post_type === 'players' && function_exists('footerball_player_tab_url')) {
        $url = footerball_player_tab_url((int) $post->ID, 'profile');
        return $url ?: $permalink;
    }

    if ($post->post_type === 'teams' && function_exists('footerball_team_tab_url')) {
        $url = footerball_team_tab_url((int) $post->ID, 'profile');
        return $url ?: $permalink;
    }

    if ($post->post_type === 'host_cities' && function_exists('footerball_host_city_tab_url')) {
        $url = footerball_host_city_tab_url((int) $post->ID, 'ticket-information');
        return $url ?: $permalink;
    }

    return $permalink;
}
add_filter('post_type_link', 'footerball_default_tab_permalink', 10, 2);

add_action('template_redirect', function () {
    if (is_singular('host_cities')) {
        $post = get_queried_object();
        if (!$post) {
            return;
        }
        $post_id = (int) $post->ID;
        $slug = $post->post_name;
        $tab = get_query_var('host_city_tab');
        $valid_tabs = array('ticket-information', 'fixtures', 'stadium-day-guide', 'official-gear');
        $canonical_tab = in_array($tab, $valid_tabs, true) ? $tab : 'ticket-information';
        $req_uri = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '';
        $path_raw = preg_replace('/\?.*$/', '', $req_uri);
        $has_query_params = isset($_GET['id']) || isset($_GET['tab']);
        $real_slug = ctype_digit($slug) ? get_post_field('post_name', $post_id) : $slug;
        $std_path = '/host-cities/' . $real_slug . '/' . $canonical_tab;
        if (rtrim($path_raw, '/') !== $std_path || $has_query_params) {
            wp_redirect(home_url($std_path . '/'), 301);
            exit;
        }
        return;
    }

    $req_uri = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '';
    if (preg_match('#^/host-cities/([0-9]+)(?:/(ticket-information|fixtures|stadium-day-guide|official-gear))?/?#', $req_uri, $m)) {
        $post_id = (int) $m[1];
        $tab = !empty($m[2]) ? $m[2] : 'ticket-information';
        $resolved = get_post($post_id);
        if ($resolved && $resolved->post_type === 'host_cities' && $resolved->post_status === 'publish') {
            $target_path = '/host-cities/' . $resolved->post_name . '/' . $tab;
            wp_redirect(home_url($target_path . '/'), 301);
            exit;
        }
    }
});

/* ============================================
   ✅ Auto-Flush Rewrite Rules After Initial Host Cities Deployment
   ============================================ */
add_action('init', function () {
    if (get_option('host_cities_rewrite_flushed_v2')) {
        return;
    }
    flush_rewrite_rules(false);
    update_option('host_cities_rewrite_flushed_v2', 1);
}, 99);

/* ============================================
   ✅ Custom Template Loader
   ============================================ */

add_filter('template_include', function ($template) {
    $error_code = (int) get_query_var('footerball_error_code');
    if ($error_code >= 500) {
        status_header($error_code);
        nocache_headers();
        $custom = get_stylesheet_directory() . '/500.php';
        if (file_exists($custom)) {
            return $custom;
        }
    }

    // Virtual About Us page
    if (footerball_is_about_us_request()) {
        global $wp_query;
        if ($wp_query instanceof WP_Query) {
            $wp_query->is_404 = false;
            $wp_query->is_page = true;
            $wp_query->is_singular = true;
        }
        status_header(200);
        $custom = get_stylesheet_directory() . '/page-about-us.php';
        if (file_exists($custom)) {
            return $custom;
        }
    }

    // Match Center template
    if ((int) get_query_var('footerball_matches_page') === 1 || is_post_type_archive('matches')) {
        $custom = get_stylesheet_directory() . '/archive-matches.php';
        if (file_exists($custom)) {
            return $custom;
        }
    }

    // Teams archive template
    if (is_post_type_archive('teams')) {
        $custom = get_stylesheet_directory() . '/archive-teams.php';
        if (file_exists($custom)) {
            return $custom;
        }
    }

    // Teams single template
    if (is_singular('teams')) {
        $custom = get_stylesheet_directory() . '/single-teams.php';
        if (file_exists($custom)) {
            return $custom;
        }
    }
    
    // Players single template
    if (is_singular('players')) {
        $custom = get_stylesheet_directory() . '/single-players.php';
        if (file_exists($custom)) {
            return $custom;
        }
    }

    // Players archive template
    if (is_post_type_archive('players')) {
        $custom = get_stylesheet_directory() . '/archive-players.php';
        if (file_exists($custom)) {
            return $custom;
        }
    }
    
    // Lifestyle single template
    if (is_singular('lifestyle')) {
        $custom = get_stylesheet_directory() . '/single-lifestyle.php';
        if (file_exists($custom)) {
            return $custom;
        }
    }

    // Lifestyle archive template
    if (is_post_type_archive('lifestyle')) {
        $custom = get_stylesheet_directory() . '/archive-lifestyle.php';
        if (file_exists($custom)) {
            return $custom;
        }
    }

    // Host Cities archive template
    if (is_post_type_archive('host_cities')) {
        $custom = get_stylesheet_directory() . '/archive-host-cities.php';
        if (file_exists($custom)) {
            return $custom;
        }
    }

    // Host Cities single template
    if (is_singular('host_cities')) {
        $custom = get_stylesheet_directory() . '/single-host-cities.php';
        if (file_exists($custom)) {
            return $custom;
        }
    }
    
    return $template;
}, 99);

/* ✅ Header Mobile Menu Interaction */
function my_header_mobile_menu_script() {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var toggle = document.querySelector('.mobile-menu-toggle');
        var overlay = document.querySelector('.mobile-menu-overlay');

        if (!toggle || !overlay) return;

        function closeMenu() {
            toggle.classList.remove('active');
            overlay.classList.remove('open');
            toggle.setAttribute('aria-expanded', 'false');
        }

        toggle.addEventListener('click', function (e) {
            e.stopPropagation();
            toggle.classList.toggle('active');
            overlay.classList.toggle('open');
            toggle.setAttribute('aria-expanded', overlay.classList.contains('open') ? 'true' : 'false');
        });

        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) {
                closeMenu();
            }
        });

        var links = overlay.querySelectorAll('a');
        links.forEach(function (link) {
            link.addEventListener('click', closeMenu);
        });

        document.addEventListener('click', function (e) {
            if (!overlay.contains(e.target) && e.target !== toggle && !toggle.contains(e.target)) {
                closeMenu();
            }
        });
    });
    </script>
    <?php
}
add_action('wp_footer', 'my_header_mobile_menu_script');

/* ============================================
   ✅ Teams ACF Fields (aligned with single-teams.php)
   ============================================ */
add_action('acf/init', function () {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group(array(
        'key' => 'group_team_info_full',
        'title' => 'Team Info',
        'fields' => array(
            array('key' => 'field_team_name', 'label' => 'Name', 'name' => 'name', 'type' => 'text'),
            array('key' => 'field_team_logo', 'label' => 'Team Logo', 'name' => 'team_logo', 'type' => 'image', 'return_format' => 'id', 'preview_size' => 'medium', 'library' => 'all'),
            array('key' => 'field_team_banner_bg', 'label' => 'Team Banner Background', 'name' => 'team_banner_bg', 'type' => 'image', 'return_format' => 'id', 'preview_size' => 'medium', 'library' => 'all'),
            array('key' => 'field_team_motto', 'label' => 'Team Motto', 'name' => 'team_motto', 'type' => 'text'),
            array('key' => 'field_team_fifa_rank', 'label' => 'FIFA Rank', 'name' => 'team_fifa_rank', 'type' => 'text'),
            array('key' => 'field_team_confederation', 'label' => 'Confederation', 'name' => 'team_confederation', 'type' => 'text'),
            array('key' => 'field_team_world_cup_titles', 'label' => 'World Cup Titles', 'name' => 'team_world_cup_titles', 'type' => 'text'),
            array('key' => 'field_team_founded', 'label' => 'Founded', 'name' => 'team_founded', 'type' => 'text'),
            array('key' => 'field_team_stadium', 'label' => 'Home Stadium', 'name' => 'team_stadium', 'type' => 'text'),
            array('key' => 'field_team_coach', 'label' => 'Coach', 'name' => 'team_coach', 'type' => 'text'),
            array('key' => 'field_team_captain', 'label' => 'Captain', 'name' => 'team_captain', 'type' => 'text'),
            array('key' => 'field_team_description', 'label' => 'Team Description', 'name' => 'team_description', 'type' => 'wysiwyg', 'toolbar' => 'basic', 'media_upload' => 0),
            array('key' => 'field_team_stats_content', 'label' => 'Team Stats', 'name' => 'team_stats_content', 'type' => 'textarea', 'instructions' => 'Appears below Recent Tournament Record on the team detail page. Leave blank to auto-generate from recent tournament record data.', 'rows' => 5, 'new_lines' => 'wpautop'),
            array('key' => 'field_world_cup_prediction', 'label' => 'World Cup Prediction', 'name' => 'world_cup_prediction', 'type' => 'textarea', 'rows' => 3),
            array('key' => 'field_fifa_rank_highest', 'label' => 'FIFA Rank Highest', 'name' => 'fifa_rank_highest', 'type' => 'text'),
            array('key' => 'field_fifa_rank_lowest', 'label' => 'FIFA Rank Lowest', 'name' => 'fifa_rank_lowest', 'type' => 'text'),
            array('key' => 'field_fifa_rank_current', 'label' => 'FIFA Rank Current', 'name' => 'fifa_rank_current', 'type' => 'text'),
            array('key' => 'field_vote_option_a', 'label' => 'Vote Option A', 'name' => 'vote_option_a', 'type' => 'text'),
            array('key' => 'field_vote_option_a_pct', 'label' => 'Vote Option A %', 'name' => 'vote_option_a_pct', 'type' => 'number', 'min' => 0, 'max' => 100, 'step' => 1),
            array('key' => 'field_vote_option_b', 'label' => 'Vote Option B', 'name' => 'vote_option_b', 'type' => 'text'),
            array('key' => 'field_vote_option_b_pct', 'label' => 'Vote Option B %', 'name' => 'vote_option_b_pct', 'type' => 'number', 'min' => 0, 'max' => 100, 'step' => 1),
            array('key' => 'field_team_primary_color', 'label' => 'Primary Color', 'name' => 'team_primary_color', 'type' => 'color_picker'),
            array('key' => 'field_team_secondary_color', 'label' => 'Secondary Color', 'name' => 'team_secondary_color', 'type' => 'color_picker'),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'teams',
                ),
            ),
        ),
        'position' => 'normal',
        'style' => 'default',
        'active' => true,
    ));
});

/* ============================================
   ✅ Seed all players with default profile v3 data
   ============================================ */
add_action('init', function () {
    if (!defined('FOOTERBALL_ENABLE_LEGACY_PLAYER_SEED') || !FOOTERBALL_ENABLE_LEGACY_PLAYER_SEED || !function_exists('update_field') || get_option('players_profile_v3_seeded')) {
        return;
    }

    $players = get_posts(array(
        'post_type'      => 'players',
        'post_status'    => array('publish', 'draft', 'pending', 'future', 'private'),
        'posts_per_page' => -1,
    ));

    foreach ($players as $player) {
        $post_id = (int) $player->ID;
        $player_name = $player->post_title;

        // Set default values only if fields are empty
        $ensure_field = function ($field, $value) use ($post_id) {
            $current = get_field($field, $post_id);
            if ($current === null || $current === '' || (is_array($current) && empty($current))) {
                update_field($field, $value, $post_id);
            }
        };

        // Legacy seed is disabled by default. Keep this action available only for
        // explicit local migrations; public player data must come from sync jobs
        // or manually verified editorial fields.

        // No placeholder FAQ, match, ranking, or profile facts are seeded here.
    }

    update_option('players_profile_v3_seeded', 1);
}, 35);

/* ============================================
   ✅ Seed all teams with English data
   ============================================ */
add_action('init', function () {
    if (!function_exists('update_field') || get_option('teams_bulk_seeded_v1')) {
        return;
    }

    $teams = get_posts(array(
        'post_type'      => 'teams',
        'post_status'    => array('publish', 'draft', 'pending', 'future', 'private'),
        'posts_per_page' => -1,
    ));

    $conf_map = array(
        'argentina' => 'CONMEBOL', 'uruguay' => 'CONMEBOL', 'ecuador' => 'CONMEBOL', 'colombia' => 'CONMEBOL', 'paraguay' => 'CONMEBOL', 'brazil' => 'CONMEBOL',
        'mexico' => 'CONCACAF', 'canada' => 'CONCACAF', 'curacao' => 'CONCACAF', 'panama' => 'CONCACAF', 'haiti' => 'CONCACAF', 'usa' => 'CONCACAF',
        'netherlands' => 'UEFA', 'austria' => 'UEFA', 'czechia' => 'UEFA', 'bosnia-and-herzegovina' => 'UEFA', 'scotland' => 'UEFA', 'sweden' => 'UEFA', 'turkiye' => 'UEFA', 'switzerland' => 'UEFA', 'croatia' => 'UEFA', 'belgium' => 'UEFA', 'germany' => 'UEFA', 'norway' => 'UEFA', 'england' => 'UEFA', 'france' => 'UEFA', 'spain' => 'UEFA', 'portugal' => 'UEFA',
        'morocco' => 'CAF', 'senegal' => 'CAF', 'algeria' => 'CAF', 'cape-verde' => 'CAF', 'dr-congo' => 'CAF', 'ghana' => 'CAF', 'ivory-coast' => 'CAF', 'tunisia' => 'CAF', 'south-africa' => 'CAF', 'egypt' => 'CAF',
        'australia' => 'AFC', 'iraq' => 'AFC', 'iran' => 'AFC', 'japan' => 'AFC', 'qatar' => 'AFC', 'jordan' => 'AFC', 'saudi-arabia' => 'AFC', 'uzbekistan' => 'AFC', 'south-korea' => 'AFC',
        'new-zealand' => 'OFC',
    );

    $rank_order = array(
        'argentina','france','england','spain','portugal','netherlands','belgium','germany','croatia','brazil','uruguay','colombia',
        'morocco','mexico','usa','canada','japan','iran','switzerland','senegal','austria','sweden','scotland','turkiye',
        'australia','saudi-arabia','qatar','uzbekistan','south-korea','ghana','ivory-coast','tunisia','algeria','egypt',
        'south-africa','dr-congo','cape-verde','panama','paraguay','ecuador','curacao','haiti','new-zealand','iraq','jordan',
        'bosnia-and-herzegovina','czechia','norway'
    );
    $rank_map = array();
    foreach ($rank_order as $index => $slug) {
        $rank_map[$slug] = $index + 1;
    }

    foreach ($teams as $team) {
        $name = $team->post_title;
        $slug = $team->post_name ? $team->post_name : sanitize_title($name);
        $post_id = (int) $team->ID;
        $rank = isset($rank_map[$slug]) ? $rank_map[$slug] : 48;
        $conf = isset($conf_map[$slug]) ? $conf_map[$slug] : 'UEFA';

        update_field('team_motto', $name . ' Never Stops Fighting', $post_id);
        update_field('team_fifa_rank', (string) $rank, $post_id);
        update_field('team_confederation', $conf, $post_id);
        update_field('team_world_cup_titles', $slug === 'argentina' ? '3' : '0', $post_id);
        update_field('team_founded', '1900', $post_id);
        update_field('team_stadium', $name . ' National Stadium', $post_id);
        update_field('team_coach', $name . ' Head Coach', $post_id);
        update_field('team_captain', $name . ' Captain', $post_id);
        update_field('team_description', $name . ' is a competitive national team with a clear tactical identity and strong ambition for 2026.', $post_id);
        update_field('world_cup_prediction', 'Target: reach the knockout stage and push for a quarter-final or better.', $post_id);
        update_field('fifa_rank_highest', (string) max(1, $rank - 10), $post_id);
        update_field('fifa_rank_lowest', (string) min(80, $rank + 20), $post_id);
        update_field('fifa_rank_current', (string) $rank, $post_id);
        update_field('vote_option_a', $name, $post_id);
        update_field('vote_option_a_pct', 60, $post_id);
        update_field('vote_option_b', 'Opponent', $post_id);
        update_field('vote_option_b_pct', 40, $post_id);
        /* Only set default colors if not already set */
        $current_primary = get_field('team_primary_color', $post_id);
        $current_secondary = get_field('team_secondary_color', $post_id);
        if (empty($current_primary)) {
            update_field('team_primary_color', '#0A1940', $post_id);
        }
        if (empty($current_secondary)) {
            update_field('team_secondary_color', '#FFFFFF', $post_id);
        }

        update_field('recent_matches', array(
            array('year' => '2024', 'competition' => 'Continental Championship', 'played' => 6, 'won' => 4, 'drawn' => 1, 'lost' => 1, 'goals_for' => 11, 'goals_against' => 5, 'rank' => 'Semi-finals', 'rank_color' => 'silver'),
            array('year' => '2022', 'competition' => 'FIFA World Cup', 'played' => 4, 'won' => 2, 'drawn' => 1, 'lost' => 1, 'goals_for' => 7, 'goals_against' => 5, 'rank' => 'Round of 16', 'rank_color' => 'bronze'),
        ), $post_id);

        update_field('top_scorers', array(
            array('name' => $name . ' Captain', 'position' => 'Forward', 'goals' => 16, 'avatar' => ''),
            array('name' => $name . ' Striker', 'position' => 'Forward', 'goals' => 9, 'avatar' => ''),
        ), $post_id);

        update_field('official_merch', array(
            array('name' => $name . ' Home Jersey 2026', 'price' => '129', 'link' => 'https://www.adidas.com', 'image' => ''),
            array('name' => $name . ' Training Jacket', 'price' => '149', 'link' => 'https://www.adidas.com', 'image' => ''),
        ), $post_id);

        update_field('team_news', array(
            array('tag' => 'Official', 'title' => $name . ' announces latest squad', 'excerpt' => 'Coaching staff confirms final list for the upcoming international window.', 'date' => date('Y-m-d'), 'link' => 'https://www.fifa.com', 'thumb' => ''),
            array('tag' => 'Match', 'title' => $name . ' wins in warm-up fixture', 'excerpt' => 'Team shape and pressing intensity continue to improve ahead of the tournament.', 'date' => date('Y-m-d'), 'link' => 'https://www.fifa.com', 'thumb' => ''),
        ), $post_id);

        update_field('top_players', array(
            array('name' => $name . ' Captain', 'avatar' => '', 'link' => 'https://www.fifa.com'),
            array('name' => $name . ' Key Midfielder', 'avatar' => '', 'link' => 'https://www.fifa.com'),
            array('name' => $name . ' Goalkeeper', 'avatar' => '', 'link' => 'https://www.fifa.com'),
        ), $post_id);

        update_field('faq_items', array(
            array('question' => 'What is ' . $name . ' targeting for the 2026 World Cup?', 'answer' => 'The team targets at least the knockout stage and aims to challenge top contenders.'),
            array('question' => 'Who is the captain of ' . $name . '?', 'answer' => $name . ' Captain'),
        ), $post_id);

        update_field('team_honors', array(
            array('year' => '2024', 'title' => 'Continental Championship Semi-finalist'),
            array('year' => '2022', 'title' => 'World Cup Participant'),
        ), $post_id);
    }

    update_option('teams_bulk_seeded_v1', 1);
}, 31);

/* ============================================
   ✅ Bind imported media logos to team_logo field
   ============================================ */
/* ============================================
   ✅ Team color meta fields are managed by ACF
   (register_post_meta removed to avoid REST API conflict with ACF)
   ============================================ */

/* ============================================
   ✅ Bind imported media logos to team_logo field
   ============================================ */
add_action('init', function () {
    if (get_option('teams_logo_import_bound_v3')) {
        return;
    }

    $slug_to_media_id = array(
        'egypt' => 555,
        'croatia' => 556,
        'south-korea' => 557,
        'belgium' => 558,
        'germany' => 559,
        'usa' => 560,
        'brazil' => 561,
        'norway' => 562,
        'france' => 563,
        'england' => 564,
        'spain' => 565,
        'portugal' => 566,
        'netherlands' => 567,
        'canada' => 568,
        'morocco' => 569,
        'uruguay' => 570,
        'senegal' => 571,
        'ecuador' => 572,
        'colombia' => 573,
        'austria' => 574,
        'czechia' => 575,
        'bosnia-and-herzegovina' => 576,
        'scotland' => 577,
        'sweden' => 578,
        'turkiye' => 579,
        'switzerland' => 580,
        'australia' => 581,
        'iraq' => 582,
        'iran' => 583,
        'japan' => 584,
        'qatar' => 585,
        'jordan' => 586,
        'saudi-arabia' => 587,
        'algeria' => 588,
        'uzbekistan' => 589,
        'cape-verde' => 590,
        'ghana' => 591,
        'dr-congo' => 592,
        'ivory-coast' => 593,
        'tunisia' => 594,
        'south-africa' => 595,
        'curacao' => 596,
        'panama' => 597,
        'haiti' => 598,
        'paraguay' => 599,
        'new-zealand' => 600,
        'mexico' => 601,
        'argentina' => 602,
    );

    foreach ($slug_to_media_id as $slug => $media_id) {
        $team = get_page_by_path($slug, OBJECT, 'teams');
        if (!$team) {
            continue;
        }
        $team_id = (int) $team->ID;
        if (function_exists('update_field')) {
            update_field('team_logo', $media_id, $team_id);
        } else {
            update_post_meta($team_id, 'team_logo', $media_id);
        }
    }

    update_option('teams_logo_import_bound_v3', 1);
}, 32);

/* ============================================
   ✅ Teams Extended Data Editor (no ACF Pro required)
   ============================================ */
add_action('add_meta_boxes', function () {
    add_meta_box(
        'team_extended_data_editor',
        'Team Extended Data (Editable)',
        function ($post) {
            wp_nonce_field('save_team_extended_data', 'team_extended_data_nonce');

            $fields = array(
                'recent_matches_data' => "Recent Matches (one line per row)\nFormat: Year|Competition|Played|Won|Drawn|Lost|Goals For|Goals Against|Result|Color(gold/silver/bronze)",
                'top_scorers_data'    => "Top Scorer (only the first row is shown)\nFormat: Name|Position|Goals|Avatar URL|Link",
                'official_merch_data' => "Official Merch (one line per row)\nFormat: Name|Price|Link|Image URL",
                'team_news_data'      => "Team News (one line per row)\nFormat: Tag|Title|Excerpt|Date|Link|Thumb URL",
                'top_players_data'    => "Top Players (one line per row)\nFormat: Name|Avatar URL|Link",
                'faq_items_data'      => "FAQ Items (one line per row)\nFormat: Question|Answer",
                'team_honors_data'    => "Team Honors (one line per row)\nFormat: Year|Title",
            );

            echo '<p style="margin-bottom:12px;color:#555;">Use <code>|</code> to split columns. One item per line.</p>';

            foreach ($fields as $key => $label) {
                $value = get_post_meta($post->ID, '_team_' . $key, true);
                echo '<p style="margin:14px 0 6px;"><strong>' . esc_html($label) . '</strong></p>';
                echo '<textarea name="' . esc_attr($key) . '" rows="5" style="width:100%;font-family:monospace;">' . esc_textarea($value) . '</textarea>';
            }
        },
        'teams',
        'normal',
        'high'
    );
});

add_action('save_post_teams', function ($post_id) {
    if (!isset($_POST['team_extended_data_nonce']) || !wp_verify_nonce($_POST['team_extended_data_nonce'], 'save_team_extended_data')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $keys = array(
        'recent_matches_data',
        'top_scorers_data',
        'official_merch_data',
        'team_news_data',
        'top_players_data',
        'faq_items_data',
        'team_honors_data',
    );

    foreach ($keys as $key) {
        $val = isset($_POST[$key]) ? wp_unslash($_POST[$key]) : '';
        update_post_meta($post_id, '_team_' . $key, $val);
    }
});

/* ============================================
   ✅ Host Cities ACF Fields (English Host City Detail Page)
   ============================================ */
add_action('acf/init', function () {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group(array(
        'key' => 'group_host_city_info_full',
        'title' => 'Host City Info',
        'fields' => array(
            array('key' => 'field_host_city_order', 'label' => 'Host City Order', 'name' => 'host_city_order', 'type' => 'text'),
            array('key' => 'field_host_city_country', 'label' => 'Country / Region', 'name' => 'host_city_country', 'type' => 'text'),
            array('key' => 'field_host_city_stadium_name', 'label' => 'Stadium Name', 'name' => 'host_city_stadium_name', 'type' => 'text'),
            array('key' => 'field_host_city_subtitle', 'label' => 'Subtitle', 'name' => 'host_city_subtitle', 'type' => 'text'),
            array('key' => 'field_host_city_banner_image', 'label' => 'Banner Image', 'name' => 'host_city_banner_image', 'type' => 'image', 'return_format' => 'id', 'preview_size' => 'medium', 'library' => 'all'),
            array('key' => 'field_host_city_banner_alt', 'label' => 'Banner Alt (SEO)', 'name' => 'host_city_banner_alt', 'type' => 'text'),
            array('key' => 'field_host_city_image_credit', 'label' => 'Image Credit', 'name' => 'host_city_image_credit', 'type' => 'text'),
            array('key' => 'field_host_city_image_source_url', 'label' => 'Image Source URL', 'name' => 'host_city_image_source_url', 'type' => 'url'),
            array('key' => 'field_host_city_capacity', 'label' => 'Capacity', 'name' => 'host_city_capacity', 'type' => 'text'),
            array('key' => 'field_host_city_matches_count', 'label' => 'Matches Hosted', 'name' => 'host_city_matches_count', 'type' => 'text'),
            array('key' => 'field_host_city_surface', 'label' => 'Surface', 'name' => 'host_city_surface', 'type' => 'text'),
            array('key' => 'field_host_city_expected_visitors', 'label' => 'Expected Visitors', 'name' => 'host_city_expected_visitors', 'type' => 'text'),
            array(
                'key' => 'field_host_city_stadium_opened_year',
                'label' => 'Stadium opened (year)',
                'name' => 'host_city_stadium_opened_year',
                'type' => 'text',
                'instructions' => 'Major opening / renovation year shown on the hero (e.g. 2007). Leave empty to use the theme default for this city.',
            ),
            array('key' => 'field_host_city_tab_ticket', 'label' => 'Tab: Ticket', 'name' => 'host_city_tab_ticket', 'type' => 'text'),
            array('key' => 'field_host_city_tab_fixtures', 'label' => 'Tab: Fixtures', 'name' => 'host_city_tab_fixtures', 'type' => 'text'),
            array('key' => 'field_host_city_tab_guide', 'label' => 'Tab: Guide', 'name' => 'host_city_tab_guide', 'type' => 'text'),
            array('key' => 'field_host_city_tab_gear', 'label' => 'Tab: Official Gear', 'name' => 'host_city_tab_gear', 'type' => 'text'),
            array('key' => 'field_host_city_section_ticket', 'label' => 'Section: Ticket Title', 'name' => 'host_city_section_ticket', 'type' => 'text'),
            array('key' => 'field_host_city_section_fixtures', 'label' => 'Section: Fixtures Title', 'name' => 'host_city_section_fixtures', 'type' => 'text'),
            array('key' => 'field_host_city_section_guide', 'label' => 'Section: Guide Title', 'name' => 'host_city_section_guide', 'type' => 'text'),
            array('key' => 'field_host_city_section_gear', 'label' => 'Section: Official Gear Title', 'name' => 'host_city_section_gear', 'type' => 'text'),
            array('key' => 'field_host_city_section_journal', 'label' => 'Section: Fan Journal Title', 'name' => 'host_city_section_journal', 'type' => 'text'),
            array('key' => 'field_host_city_section_nearby', 'label' => 'Section: Nearby Tips Title', 'name' => 'host_city_section_nearby', 'type' => 'text'),
            array('key' => 'field_host_city_stadium_overview', 'label' => 'Stadium Overview', 'name' => 'host_city_stadium_overview', 'type' => 'textarea', 'rows' => 4, 'new_lines' => 'wpautop', 'instructions' => 'Appears in the STADIUM OVERVIEW card on the Ticket Information tab.'),
            array('key' => 'field_host_city_ticket_notice', 'label' => 'Ticket Notice', 'name' => 'host_city_ticket_notice', 'type' => 'textarea', 'rows' => 3),
            array(
                'key' => 'field_host_city_ticket_actions',
                'label' => 'Ticket Actions',
                'name' => 'host_city_ticket_actions',
                'type' => 'wysiwyg',
                'tabs' => 'visual,text',
                'toolbar' => 'basic',
                'media_upload' => 0,
                'instructions' => 'Each action on a new line: <code>Label|URL</code> e.g. <code>Buy Tickets|https://example.com</code>',
            ),
            array(
                'key' => 'field_host_city_fixtures',
                'label' => 'Fixtures',
                'name' => 'host_city_fixtures',
                'type' => 'wysiwyg',
                'tabs' => 'visual,text',
                'toolbar' => 'basic',
                'media_upload' => 0,
                'instructions' => 'Each fixture on a new line: <code>Date|Stage|Home Team|Away Team|Status</code> e.g. <code>2026-06-18 18:00|Group Stage|Team A|Team B|On Sale</code>',
            ),
            array(
                'key' => 'field_host_city_day_guide',
                'label' => 'Stadium Day Guide',
                'name' => 'host_city_day_guide',
                'type' => 'wysiwyg',
                'tabs' => 'visual,text',
                'toolbar' => 'full',
                'media_upload' => 1,
                'instructions' => 'Use headings and paragraphs for guide content. Use emoji or text for icons.',
            ),
            array(
                'key' => 'field_host_city_official_gear',
                'label' => 'Official Gear',
                'name' => 'host_city_official_gear',
                'type' => 'wysiwyg',
                'tabs' => 'visual,text',
                'toolbar' => 'basic',
                'media_upload' => 0,
                'instructions' => 'Each item on a new line: <code>Name|Price|Link|ImageURL</code> e.g. <code>Jersey|$85|https://store.com/jersey|https://img.com/j.jpg</code>',
            ),
            array(
                'key' => 'field_host_city_fan_journal',
                'label' => 'Fan Journal',
                'name' => 'host_city_fan_journal',
                'type' => 'wysiwyg',
                'tabs' => 'visual,text',
                'toolbar' => 'basic',
                'media_upload' => 0,
                'instructions' => 'Each entry on a new line: <code>Title|Excerpt|Link|ThumbURL</code> e.g. <code>City Guide|Explore the city|https://example.com|https://img.com/t.jpg</code>',
            ),
            array('key' => 'field_host_city_local_time', 'label' => 'Local Time Label', 'name' => 'host_city_local_time', 'type' => 'text'),
            array('key' => 'field_host_city_weather', 'label' => 'Weather Label', 'name' => 'host_city_weather', 'type' => 'text'),
            array('key' => 'field_host_city_latitude', 'label' => 'Latitude', 'name' => 'host_city_latitude', 'type' => 'text'),
            array('key' => 'field_host_city_longitude', 'label' => 'Longitude', 'name' => 'host_city_longitude', 'type' => 'text'),
            array('key' => 'field_host_city_timezone', 'label' => 'Timezone', 'name' => 'host_city_timezone', 'type' => 'text', 'instructions' => 'IANA timezone, for example America/Toronto or America/Los_Angeles.'),
            array('key' => 'field_host_city_transport_title', 'label' => 'Transport Card Title', 'name' => 'host_city_transport_title', 'type' => 'text'),
            array('key' => 'field_host_city_transport_desc', 'label' => 'Transport Card Description', 'name' => 'host_city_transport_desc', 'type' => 'textarea', 'rows' => 2),
            array('key' => 'field_host_city_transport_button', 'label' => 'Transport Button Text', 'name' => 'host_city_transport_button', 'type' => 'text'),
            array('key' => 'field_host_city_transport_link', 'label' => 'Transport Button Link', 'name' => 'host_city_transport_link', 'type' => 'url'),
            array(
                'key' => 'field_host_city_nearby_tips',
                'label' => 'Nearby Tips',
                'name' => 'host_city_nearby_tips',
                'type' => 'wysiwyg',
                'tabs' => 'visual,text',
                'toolbar' => 'basic',
                'media_upload' => 0,
                'instructions' => 'Each tip on a new line, or use an unordered list.',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'host_cities',
                ),
            ),
        ),
        'position' => 'normal',
        'style' => 'default',
        'active' => true,
    ));
});

/* ============================================
   ✅ Host city data is managed in WP admin (database)
   ============================================ */
add_action('init', function () {
    if (get_option('host_city_editor_backfill_v1')) {
        return;
    }
    if (!function_exists('update_field')) {
        return;
    }

    $cities = get_posts(array(
        'post_type'      => 'host_cities',
        'post_status'    => array('publish', 'draft', 'pending', 'future', 'private'),
        'posts_per_page' => -1,
    ));

    foreach ($cities as $city) {
        $post_id = (int) $city->ID;
        $title = get_the_title($post_id);
        $stadium = get_field('host_city_stadium_name', $post_id) ?: 'Stadium Name';

        $ensure = function ($field, $value) use ($post_id) {
            $current = get_field($field, $post_id);
            if ($current === null || $current === '' || (is_array($current) && empty($current))) {
                update_field($field, $value, $post_id);
            }
        };

        $ensure('host_city_tab_ticket', 'Ticket Information');
        $ensure('host_city_tab_fixtures', 'Fixtures');
        $ensure('host_city_tab_guide', 'Stadium Day Guide');
        $ensure('host_city_tab_gear', 'Official Gear');
        $ensure('host_city_section_ticket', 'Ticket Information');
        $ensure('host_city_section_fixtures', 'Upcoming 2026 Fixtures (' . $stadium . ')');
        $ensure('host_city_section_guide', 'Stadium Day Guide');
        $ensure('host_city_section_gear', 'Official Gear');
        $ensure('host_city_section_journal', 'Fan Journal');
        $ensure('host_city_section_nearby', 'Nearby Tips');
        $ensure('host_city_ticket_notice', 'Ticket windows open in phases. Match assignments are subject to FIFA final release; monitor official channels for exact teams.');
        $ensure('host_city_ticket_actions', array(
            array('label' => 'General Ticket Window', 'url' => 'https://www.fifa.com/en/tickets'),
            array('label' => 'Hospitality Packages', 'url' => 'https://hospitality.fifa.com/'),
            array('label' => 'Accessibility Tickets', 'url' => 'https://www.fifa.com/en/tickets'),
        ));
        $ensure('host_city_day_guide', array(
            array('icon' => '🚆', 'title' => 'Transit First', 'content' => 'Use official rail and shuttle services on matchday to avoid parking delays.'),
            array('icon' => '🎒', 'title' => 'Clear-Bag Policy', 'content' => 'Bring only approved clear bags and avoid oversized backpacks.'),
            array('icon' => '💳', 'title' => 'Cashless Venue', 'content' => 'Cards and mobile wallets are accepted across all official counters.'),
        ));
        $ensure('host_city_official_gear', array(
            array('name' => 'FIFA 2026 Tee', 'price' => '$39', 'link' => 'https://store.fifa.com/', 'image' => ''),
            array('name' => 'Host City Scarf', 'price' => '$28', 'link' => 'https://store.fifa.com/', 'image' => ''),
            array('name' => 'Matchday Cap', 'price' => '$24', 'link' => 'https://store.fifa.com/', 'image' => ''),
            array('name' => 'Collectors Pin', 'price' => '$12', 'link' => 'https://store.fifa.com/', 'image' => ''),
        ));
        $ensure('host_city_fan_journal', array(
            array('title' => 'How to reach ' . $stadium . ' quickly', 'excerpt' => 'Simple route plan from downtown and airport to the stadium district.', 'link' => footerball_host_city_tab_url($post_id, 'stadium-day-guide'), 'thumb' => ''),
            array('title' => 'Best pre-game food spots in ' . $title, 'excerpt' => 'Fan-friendly food options before kickoff.', 'link' => footerball_host_city_tab_url($post_id, 'stadium-day-guide'), 'thumb' => ''),
            array('title' => 'First-time matchday checklist', 'excerpt' => 'What to bring, what to skip, and when to arrive.', 'link' => footerball_host_city_tab_url($post_id, 'stadium-day-guide'), 'thumb' => ''),
        ));
    }

    update_option('host_city_editor_backfill_v1', 1);
}, 41);

add_action('init', function () {
    if (get_option('host_city_stadium_overview_backfill_v1')) {
        return;
    }
    if (!function_exists('update_field')) {
        return;
    }

    $cities = get_posts(array(
        'post_type'      => 'host_cities',
        'post_status'    => array('publish', 'draft', 'pending', 'future', 'private'),
        'posts_per_page' => -1,
    ));

    foreach ($cities as $city) {
        $post_id = (int) $city->ID;
        $city_name = get_the_title($post_id);
        $stadium = get_field('host_city_stadium_name', $post_id) ?: 'Stadium Name';
        $current = get_field('host_city_stadium_overview', $post_id);

        if ($current === null || trim((string) $current) === '') {
            update_field(
                'host_city_stadium_overview',
                $stadium . ' is a modern matchday venue in the heart of ' . $city_name . ', built for clear sightlines, lively fan energy, and smooth transit before and after kickoff.',
                $post_id
            );
        }
    }

    update_option('host_city_stadium_overview_backfill_v1', 1);
}, 42);

/* ============================================
   ✅ Host city data migration: array → text format (v2)
   ============================================ */
add_action('init', function () {
    if (get_option('host_city_migrate_v2')) {
        return;
    }
    if (!function_exists('update_field')) {
        return;
    }

    $cities = get_posts(array(
        'post_type'      => 'host_cities',
        'post_status'    => array('publish', 'draft', 'pending', 'future', 'private'),
        'posts_per_page' => -1,
    ));

    foreach ($cities as $city) {
        $post_id = (int) $city->ID;

        $ticket_actions = get_field('host_city_ticket_actions', $post_id);
        if (is_array($ticket_actions) && !empty($ticket_actions)) {
            $lines = array();
            foreach ($ticket_actions as $a) {
                $ticket_url = (!empty($a['url']) && $a['url'] !== '#') ? $a['url'] : 'https://www.fifa.com/en/tickets';
                $lines[] = ($a['label'] ?? '') . '|' . $ticket_url;
            }
            update_field('host_city_ticket_actions', implode("\n", $lines), $post_id);
        }

        $fixtures = get_field('host_city_fixtures', $post_id);
        if (is_array($fixtures) && !empty($fixtures)) {
            $lines = array();
            foreach ($fixtures as $f) {
                $lines[] = ($f['datetime'] ?? '') . '|' . ($f['stage'] ?? '') . '|' . ($f['home_team'] ?? '') . '|' . ($f['away_team'] ?? '') . '|' . ($f['status'] ?? '');
            }
            update_field('host_city_fixtures', implode("\n", $lines), $post_id);
        }

        $day_guide = get_field('host_city_day_guide', $post_id);
        if (is_array($day_guide) && !empty($day_guide)) {
            $html = '';
            foreach ($day_guide as $g) {
                $icon = $g['icon'] ?? '';
                $title = $g['title'] ?? '';
                $content = $g['content'] ?? '';
                $html .= "<h3>{$icon} {$title}</h3><p>{$content}</p>";
            }
            update_field('host_city_day_guide', $html, $post_id);
        }

        $official_gear = get_field('host_city_official_gear', $post_id);
        if (is_array($official_gear) && !empty($official_gear)) {
            $lines = array();
            foreach ($official_gear as $g) {
                $gear_name = $g['name'] ?? 'football gear';
                $gear_link = (!empty($g['link']) && $g['link'] !== '#') ? $g['link'] : footerball_dhgate_search_url($gear_name);
                $lines[] = $gear_name . '|' . ($g['price'] ?? '') . '|' . $gear_link . '|' . ($g['image'] ?? '');
            }
            update_field('host_city_official_gear', implode("\n", $lines), $post_id);
        }

        $fan_journal = get_field('host_city_fan_journal', $post_id);
        if (is_array($fan_journal) && !empty($fan_journal)) {
            $lines = array();
            foreach ($fan_journal as $j) {
                $journal_link = (!empty($j['link']) && $j['link'] !== '#') ? $j['link'] : footerball_host_city_tab_url($post_id, 'stadium-day-guide');
                $lines[] = ($j['title'] ?? '') . '|' . ($j['excerpt'] ?? '') . '|' . $journal_link . '|' . ($j['thumb'] ?? '');
            }
            update_field('host_city_fan_journal', implode("\n", $lines), $post_id);
        }

        $nearby_tips = get_field('host_city_nearby_tips', $post_id);
        if (is_array($nearby_tips) && !empty($nearby_tips)) {
            $html = '<ul>';
            foreach ($nearby_tips as $tip) {
                $html .= '<li>' . ($tip['text'] ?? '') . '</li>';
            }
            $html .= '</ul>';
            update_field('host_city_nearby_tips', $html, $post_id);
        }
    }

    update_option('host_city_migrate_v2', 1);
}, 42);

/* ============================================
   ✅ Host city SEO image metadata backfill
   ============================================ */
add_action('init', function () {
    if (get_option('host_city_seo_image_backfill_v1')) {
        return;
    }

    if (!function_exists('update_field')) {
        return;
    }

    $meta_file = get_stylesheet_directory() . '/images/host-cities/seo-image-metadata.json';
    if (!file_exists($meta_file)) {
        return;
    }

    $raw = file_get_contents($meta_file);
    if (!$raw) {
        return;
    }
    $map = json_decode($raw, true);
    if (!is_array($map)) {
        return;
    }

    foreach ($map as $slug => $image_meta) {
        $city = get_page_by_path($slug, OBJECT, 'host_cities');
        if (!$city) {
            continue;
        }
        $post_id = (int) $city->ID;
        $city_title = get_the_title($post_id);
        $stadium = get_field('host_city_stadium_name', $post_id) ?: ($image_meta['stadium_page_title'] ?? 'Stadium');

        $current_alt = get_field('host_city_banner_alt', $post_id);
        if (!$current_alt) {
            update_field('host_city_banner_alt', $stadium . ' in ' . $city_title . ' for FIFA World Cup 2026 host city page', $post_id);
        }

        $current_credit = get_field('host_city_image_credit', $post_id);
        if (!$current_credit && !empty($image_meta['credit'])) {
            update_field('host_city_image_credit', $image_meta['credit'], $post_id);
        }

        $current_source = get_field('host_city_image_source_url', $post_id);
        if (!$current_source && !empty($image_meta['source_url'])) {
            update_field('host_city_image_source_url', $image_meta['source_url'], $post_id);
        }
    }

    update_option('host_city_seo_image_backfill_v1', 1);
}, 42);

/**
 * Search DHgate products by keyword
 * 
 * @param string $word Search keyword (player name or team name)
 * @param string $pool Pool type (Blue or Advantage)
 * @return array Product search results
 */
function dhgate_search_products($word, $pool = 'Blue') {
    $cache_key = 'dhgate_products_' . md5($word . '_' . $pool);
    $cached = get_transient($cache_key);
    
    if ($cached !== false) {
        return $cached;
    }
    
    $url = 'http://172.19.223.27:8001/dhgate/product/search/customer/';
    
    $pool_ids = [];
    if ('Blue' === $pool) {
        $pool_ids = [2, 3, 4];
    } elseif ('Advantage' === $pool) {
        $pool_ids = [1];
    }
    
    $payload = [
        'keyword' => $word,
        'categoryid' => '',
        'freecountry' => 'us',
        'noTree' => true,
        'pageno' => 0,
        'pagesize' => 50,
        'param25' => 'ad657fd3a2924a2f9745c0ff2304967',
        'param31' => 'us',
        'param4' => 'app',
        'param41' => 'gHmmiKrTxzgP3RPZxsJFHVg1L1LA6uBqI',
        'paramJson' => [
            'offline' => true,
            'sourceType' => 'LIST'
        ],
        'searchType' => 'LIST',
        'sortType' => 'bestmatch',
        'visitIP' => '172.19.223.27'
    ];
    
    if (!empty($pool_ids)) {
        $payload['filter'] = [
            'IN_blueplusflag' => implode(',', $pool_ids)
        ];
    }
    
    $response = wp_remote_post($url, [
        'body' => json_encode($payload),
        'headers' => [
            'Content-Type' => 'application/json'
        ],
        'timeout' => 5
    ]);
    
    if (is_wp_error($response)) {
        return [];
    }
    
    $body = wp_remote_retrieve_body($response);
    $result = json_decode($body, true);
    
    if (!is_array($result)) {
        return [];
    }

    if (isset($result['resultList']) && is_array($result['resultList'])) {
        $products = [];
        foreach ($result['resultList'] as $item) {
            $products[] = footerball_normalize_dhgate_product($item);
        }

        $products = array_values(array_filter($products));
        set_transient($cache_key, $products, HOUR_IN_SECONDS);
        return $products;
    }

    $item_codes = footerball_extract_dhgate_item_codes($result);
    if (empty($item_codes)) {
        return [];
    }

    $products = [];
    foreach (array_slice($item_codes, 0, 12) as $item_code) {
        $product = dhgate_get_product_details($item_code);
        if (!empty($product)) {
            $products[] = $product;
        }
    }

    set_transient($cache_key, $products, HOUR_IN_SECONDS);
    return $products;
}

function footerball_extract_dhgate_item_codes($result) {
    $codes = [];

    foreach ((array) $result as $item) {
        if (is_scalar($item)) {
            $code = preg_replace('/\D+/', '', (string) $item);
            if ($code !== '') {
                $codes[] = $code;
            }
            continue;
        }

        if (is_array($item)) {
            foreach (array('itemCode', 'item_code', 'ic', 'sku') as $key) {
                if (!empty($item[$key])) {
                    $code = preg_replace('/\D+/', '', (string) $item[$key]);
                    if ($code !== '') {
                        $codes[] = $code;
                    }
                    break;
                }
            }
        }
    }

    return array_values(array_unique($codes));
}

function footerball_normalize_dhgate_product($item) {
    if (!is_array($item)) {
        return [];
    }

    $item_code = $item['itemCode'] ?? $item['item_code'] ?? $item['sku'] ?? '';
    $image = $item['imageUrl'] ?? $item['image'] ?? '';
    if (is_array($image)) {
        $image = reset($image);
    }

    $offers = isset($item['offers']) && is_array($item['offers']) ? $item['offers'] : [];
    $price = $item['price'] ?? $offers['price'] ?? '';
    $url = $item['link'] ?? $item['url'] ?? $offers['url'] ?? '';

    if (!$url && $item_code) {
        $url = 'https://www.dhgate.com/product/name/' . rawurlencode((string) $item_code) . '.html';
    }

    $rating = '';
    if (!empty($item['aggregateRating']) && is_array($item['aggregateRating'])) {
        $rating_value = $item['aggregateRating']['ratingValue'] ?? '';
        $review_count = $item['aggregateRating']['reviewCount'] ?? '';
        $rating = trim($rating_value . ($review_count ? ' / ' . number_format((float) $review_count) . ' reviews' : ''));
    }

    return array(
        'name'      => footerball_clean_dhgate_product_name($item['title'] ?? $item['name'] ?? ''),
        'price'     => footerball_format_dhgate_usd_price($price),
        'link'      => $url,
        'image'     => (string) $image,
        'item_code' => (string) $item_code,
        'rating'    => $rating,
    );
}

function footerball_clean_dhgate_product_name($name) {
    $name = html_entity_decode(wp_strip_all_tags((string) $name), ENT_QUOTES, 'UTF-8');
    $name = preg_replace('/\s*From\s+[^|]+(?:\|\s*DHgate\.Com)?$/i', '', $name);
    $name = str_replace(array('&Price;', '| DHgate.Com'), '', $name);
    $name = preg_replace('/\s+/', ' ', $name);
    return trim($name);
}

function footerball_format_dhgate_usd_price($price) {
    if (is_array($price)) {
        $price = reset($price);
    }

    $price = trim((string) $price);
    if ($price === '' || stripos($price, 'price') !== false) {
        return '';
    }

    if (preg_match('/^\$|^US\s*\$/i', $price)) {
        return preg_replace('/^US\s*/i', 'US ', $price);
    }

    if (is_numeric($price)) {
        return 'US $' . number_format((float) $price, 2);
    }

    if (preg_match('/[\d.]+/', $price, $match)) {
        return 'US $' . $match[0];
    }

    return $price;
}

function footerball_dhgate_search_url($term) {
    return 'https://www.dhgate.com/wholesale/search.do?searchkey=' . rawurlencode((string) $term);
}

function footerball_product_image_html($image, $alt, $size = 'medium') {
    if (empty($image)) {
        return '';
    }

    if (is_numeric($image)) {
        return wp_get_attachment_image((int) $image, $size, false, array('alt' => esc_attr($alt)));
    }

    return '<img src="' . esc_url($image) . '" alt="' . esc_attr($alt) . '">';
}

function footerball_dhgate_product_line($product) {
    if (empty($product) || !is_array($product)) {
        return '';
    }

    return implode('|', array(
        str_replace('|', ' ', $product['name'] ?? ''),
        str_replace('|', ' ', $product['price'] ?? ''),
        str_replace('|', '', $product['link'] ?? ''),
        str_replace('|', '', $product['image'] ?? ''),
    ));
}
    
/**
 * Get DHgate product details by item code
 * 
 * @param string $item_code DHgate item code
 * @return array|null Product details
 */
function dhgate_get_product_details($item_code) {
    $cache_key = 'dhgate_product_' . $item_code;
    $cached = get_transient($cache_key);
    
    if ($cached !== false) {
        return $cached;
    }
    
    $url = 'https://www.dhgate.com/product/name/' . rawurlencode((string) $item_code) . '.html';
    
    $response = wp_remote_get($url, [
        'headers' => [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36'
        ],
        'timeout' => 10
    ]);
    
    if (is_wp_error($response)) {
        return null;
    }
    
    $body = wp_remote_retrieve_body($response);
    
    // Parse JSON-LD data
    preg_match_all('/<script type="application\/ld\+json">(.*?)<\/script>/s', $body, $matches);
    
    if (empty($matches[1])) {
        return null;
    }
    
    foreach ($matches[1] as $json) {
        $data = json_decode($json, true);
        $entries = (isset($data['@type']) || isset($data['name'])) ? array($data) : (array) $data;
        foreach ($entries as $entry) {
            if (is_array($entry) && isset($entry['@type']) && $entry['@type'] === 'Product') {
                $entry['sku'] = $entry['sku'] ?? $item_code;
                if (empty($entry['url'])) {
                    $entry['url'] = $url;
                }
                $product = footerball_normalize_dhgate_product($entry);

                if (!empty($product['name'])) {
                    set_transient($cache_key, $product, HOUR_IN_SECONDS);
                    return $product;
                }
            }
        }
    }
    
    return null;
}

/**
 * Get AI SPR (Special Promotion) products by keyword
 * Calls DHgate product search API with specified keywords and returns formatted product data
 * 
 * @param string|array $keywords Product search keyword(s). Can be single keyword or array of keywords
 * @param int $limit Max number of products to return per keyword (default: 1)
 * @param string $pool Product pool: 'Blue' (default), 'Advantage'
 * @return array Formatted product data for display
 */
function get_ai_spr_product($keywords = '', $limit = 1, $pool = 'Blue') {
    if (empty($keywords)) {
        return array();
    }
    
    // Support single keyword or array of keywords
    if (is_string($keywords)) {
        $keywords = array($keywords);
    }
    
    $all_products = array();
    
    foreach ($keywords as $keyword) {
        $keyword = trim($keyword);
        if (empty($keyword)) {
            continue;
        }
        
        // Call DHgate search API
        $search_results = dhgate_search_products($keyword, $pool);
        
        if (empty($search_results)) {
            continue;
        }
        
        // Take top N results (sorted by bestmatch/orders by default from API)
        $top_results = array_slice($search_results, 0, $limit);
        
        foreach ($top_results as $product) {
            $all_products[] = array(
                'title' => $product['name'] ?? '',
                'subtitle' => 'DHgate Special',
                'price' => $product['price'] ?? '',
                'old' => '',
                'image' => $product['image'] ?? '',
                'alt' => $product['name'] ?? 'Product image',
                'link' => (!empty($product['link']) && $product['link'] !== '#') ? $product['link'] : footerball_dhgate_search_url($product['name'] ?? $keyword),
                'rating' => !empty($product['rating']) ? $product['rating'] : generate_rating(),
                'item_code' => $product['item_code'] ?? '',
            );
        }
        
        // Stop if we have enough products
        if (count($all_products) >= count($keywords) * $limit) {
            break;
        }
    }
    
    return $all_products;
}

/**
 * Format DHgate price to display format (¥XXX)
 */
function format_dhgate_price($price_str) {
    if (empty($price_str)) {
        return '¥0';
    }
    
    // Extract numeric price from string like "US $29.99 - $59.99"
    preg_match('/[\d\.]+/', $price_str, $matches);
    if (empty($matches)) {
        return '¥0';
    }
    
    $usd_price = floatval($matches[0]);
    $cny_price = round($usd_price * 7.2); // Approximate USD to CNY conversion
    return '¥' . number_format($cny_price, 0);
}

/**
 * Calculate old price (original price before discount)
 */
function calculate_old_price($price_str) {
    if (empty($price_str)) {
        return '¥0';
    }
    
    preg_match('/[\d\.]+/', $price_str, $matches);
    if (empty($matches)) {
        return '¥0';
    }
    
    $usd_price = floatval($matches[0]);
    $cny_price = round($usd_price * 7.2 * 1.3); // Old price is ~30% higher
    return '¥' . number_format($cny_price, 0);
}

/**
 * Generate rating for product
 */
function generate_rating() {
    return number_format(mt_rand(45, 49) / 10, 1);
}
