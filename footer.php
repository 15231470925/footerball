    </main>

    <!-- Build DH chat bot params -->
    <script>
    function getCookie(name) {
        const value = "; " + document.cookie;
        const parts = value.split("; " + name + "=");
        if (parts.length === 2) {
            const rawValue = parts.pop().split(";").shift();
            try {
                return decodeURIComponent(rawValue);
            } catch (error) {
                return rawValue;
            }
        }
        return "";
    }

    window.visitorParams = {
        ref_f: getCookie('ref_f') || 'seo|smart-shopping|football|organic||http|',
        vid: getCookie('vid') || '-1',
        buyerId: "d9534de5c924dee4"
    };
    </script>

    <div class="site-floating-actions" aria-label="Floating page actions">
        <button class="site-floating-action site-floating-action--top" id="site-back-top-button" type="button" aria-label="Back to top">
            <span aria-hidden="true">↑</span>
        </button>

        <!-- AI Chat Button -->
        <div id="ai-chat-button-wrapper" class="site-floating-action site-floating-action--ai">
            <button id="ai-chat-icon-close" type="button" aria-label="Hide AI assistant">&#x2715;</button>

            <button id="ai-chat-button" type="button" aria-label="Open AI shopping assistant">
                <img src="https://js.dhresource.com/dhgate/aimarketingrobot/assets/images/avatar-BIaCWoJ_.png" alt="AI Shopping Assistant" width="80" height="80" loading="lazy" decoding="async">
            </button>
        </div>
    </div>

    <div id="ai-chat-container">
        <button id="ai-chat-close" type="button" aria-label="Close AI shopping assistant">&#x2715;</button>
        <iframe id="ai-chat-iframe"
            title="AI shopping assistant"
            width="100%"
            height="100%"
            style="border:0;"
            allowfullscreen
            loading="lazy">
        </iframe>
    </div>

    <div class="ai-guide-box" id="aiGuide">
        <div class="ai-guide-close" onclick="event.stopPropagation();document.getElementById('aiGuide').style.display='none'">
            &#x2715;
        </div>
        <div class="ai-guide-header" id="aiGuideText"></div>
        <div class="ai-guide-images" id="aiGuideImages"></div>
        <div class="ai-guide-arrow"></div>
    </div>

    <footer class="site-footer teams-dir-footer">
        <div class="teams-dir-shell teams-dir-footer__grid">
            <div>
	                <a class="teams-dir-brand teams-dir-brand--footer" href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php echo esc_attr(get_bloginfo('name')); ?> home">
	                    <picture class="teams-dir-brand__picture">
	                        <?php
	                        $footerball_brand_logo = function_exists('footerball_logo_image_url')
	                            ? add_query_arg('v', rawurlencode(defined('THEME_VERSION') ? (string) THEME_VERSION : ''), footerball_logo_image_url('webp'))
	                            : get_stylesheet_directory_uri() . '/images/football-2026-dhgate-logo.webp';
	                        ?>
	                        <img class="teams-dir-brand__logo" src="<?php echo esc_url($footerball_brand_logo); ?>" alt="Football 2026 DHgate" width="196" height="50" loading="lazy" decoding="async">
	                    </picture>
	                </a>
            </div>

            <div>
                <h3>QUICK LINKS</h3>
                <a href="<?php echo esc_url(home_url('/')); ?>">Home</a>
                <a href="<?php echo esc_url(home_url('/about-us/')); ?>">About Us</a>
                <a href="<?php echo esc_url(get_post_type_archive_link('teams') ?: home_url('/teams/')); ?>">Teams</a>
                <a href="<?php echo esc_url(get_post_type_archive_link('players') ?: home_url('/players/')); ?>">Players</a>
                <a href="<?php echo esc_url(home_url('/matches/')); ?>">Matches</a>
                <a href="<?php echo esc_url(get_post_type_archive_link('host_cities') ?: home_url('/host-cities/')); ?>">Host Cities</a>
                <a href="<?php echo esc_url(get_post_type_archive_link('lifestyle') ?: home_url('/lifestyle/')); ?>">Lifestyle</a>
            </div>

            <div class="teams-dir-footer__intro">
                <p>Step into the premier destination for the world's biggest football festival in 2026. We are dedicated to connecting global fans with every aspect of the game. Explore our rich data hubs to discover in-depth player biographies, team statistics, and real-time tournament updates. Whether you are traveling to the host cities and need local LBS guides, or you are looking for the perfect fan zone outfit and lifestyle inspiration, our comprehensive guides have you covered. Celebrate the passion, the culture, and the spirit of global football with us in one unified platform.</p>
            </div>
        </div>

        <p class="teams-dir-disclaimer">
            Disclaimer: DHgate.com is an independent marketplace and is not affiliated with, sponsored by, or an official partner of FIFA or any national football association. All product names, logos, and brands are property of their respective owners.
        </p>
        <p class="teams-dir-copy">
            &copy; <?php echo esc_html(date('Y')); ?> DHgate. Football 2026 fan content and shopping inspiration for DHgate users.
        </p>
    </footer>

    <?php
    ob_start();
    wp_footer();
    $footerball_footer_output = ob_get_clean();
    $footerball_footer_output = preg_replace_callback('/<iframe\b(?![^>]*\btitle=)([^>]*)>/i', function ($matches) {
        $attrs = $matches[1];
        $title = 'Embedded third-party content';
        if (stripos($attrs, 'googletagmanager.com') !== false) {
            $title = 'Google Tag Manager';
        } elseif (stripos($attrs, 'dhresource.com') !== false || stripos($attrs, 'dhgate.com') !== false) {
            $title = 'DHgate service frame';
        } elseif (stripos($attrs, 'aimarketingbot') !== false || stripos($attrs, 'aishipgo.com') !== false) {
            $title = 'AI shopping assistant';
        }
        return '<iframe title="' . esc_attr($title) . '"' . $attrs . '>';
    }, $footerball_footer_output);
    echo $footerball_footer_output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    ?>
    <script>document.body.setAttribute("spm-b", "football-2026");</script>
    <script>
    (function () {
        function loadDhgateTracking() {
            if (document.querySelector('script[data-dhgate-track-sdk]')) return;
            var script = document.createElement('script');
            script.src = 'https://js.dhresource.com/buyer/common/track/trackwebsdk.js';
            script.defer = true;
            script.dataset.dhgateTrackSdk = '1';
            document.head.appendChild(script);
        }

        if ('requestIdleCallback' in window) {
            requestIdleCallback(loadDhgateTracking, { timeout: 5000 });
        } else {
            window.addEventListener('load', function () {
                window.setTimeout(loadDhgateTracking, 3000);
            }, { once: true });
        }
    }());
    </script>
</body>
</html>
