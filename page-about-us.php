<?php
/**
 * Virtual About Us page for DHgate Football Hub.
 */

get_header();

$theme_version = defined('THEME_VERSION') ? (string) THEME_VERSION : '';
$asset = static function ($path) use ($theme_version) {
    $url = function_exists('footerball_theme_asset_url')
        ? footerball_theme_asset_url($path)
        : get_stylesheet_directory_uri() . '/' . ltrim((string) $path, '/');

    return $theme_version !== ''
        ? add_query_arg('v', rawurlencode($theme_version), $url)
        : $url;
};
?>

<main class="fb-about-page">
    <section class="fb-about-hero">
        <div class="fb-about-shell fb-about-hero__grid">
            <div class="fb-about-hero__copy">
                <nav class="fb-about-breadcrumb" aria-label="Breadcrumb">
                    <a href="<?php echo esc_url(home_url('/')); ?>">Home</a>
                    <span>About Us</span>
                </nav>

                <p class="fb-about-kicker">Official content channel</p>
                <h1>About DHgate Football Hub</h1>
                <p class="fb-about-hero__lede">Built by DHgate for football fans worldwide.</p>
            </div>

            <figure class="fb-about-hero__media">
                <img src="<?php echo esc_url($asset('images/dhgate-football-2026-fan-gear-promo.webp')); ?>" alt="DHgate football fan gear and tournament essentials" width="960" height="640" loading="eager" decoding="async">
            </figure>
        </div>
    </section>

    <section class="fb-about-section fb-about-section--intro">
        <div class="fb-about-shell fb-about-two-col">
            <div class="fb-about-copy">
                <h2>Who We Are</h2>
                <p>DHgate Football Hub is the official football content channel operated by DHgate.com, one of the world's leading B2B and B2C cross-border e-commerce marketplaces.</p>
                <p>This site is not a third-party publication. Every article, guide, and data page on football.dhgate.com is researched, written, and edited by DHgate's in-house content team, the same organization behind dhgate.com.</p>
            </div>

            <aside class="fb-about-facts" aria-label="DHgate Football Hub facts">
                <div>
                    <strong>20+ years</strong>
                    <span>Serving football fans and retailers worldwide</span>
                </div>
                <div>
                    <strong>220+ regions</strong>
                    <span>Connecting buyers and sellers across global markets</span>
                </div>
                <div>
                    <strong>48 nations</strong>
                    <span>Team and player profiles across the qualified field</span>
                </div>
            </aside>
        </div>
    </section>

    <section class="fb-about-section">
        <div class="fb-about-shell fb-about-panel">
            <div class="fb-about-panel__copy">
                <h2>Why We Built This</h2>
                <p>DHgate has been a trusted destination for football fans and retailers worldwide for over 20 years, connecting buyers and sellers of football jerseys, equipment, and fan gear across more than 220 countries and regions.</p>
                <p>The 2026 international football tournament, hosted across the United States, Canada, and Mexico, is the largest football event in history. For DHgate, this is not just a commercial moment. It is a cultural event that our global community of buyers, sellers, and football fans has been anticipating for years.</p>
                <p>While DHgate is not an official tournament partner, we are committed to delivering accurate, timely, and fan-first coverage of the teams, players, matches, and host cities throughout the tournament.</p>
                <p>We built football.dhgate.com to serve that community with something beyond a product catalog: a genuine fan hub where people can find match information, player and team insights, host city guides, and football lifestyle content in one place.</p>
            </div>
            <figure class="fb-about-panel__media">
                <img src="<?php echo esc_url($asset('images/home-hero-trophy.webp')); ?>" alt="Football tournament trophy atmosphere" width="900" height="700" loading="lazy" decoding="async">
            </figure>
        </div>
    </section>

    <section class="fb-about-section">
        <div class="fb-about-shell fb-about-statement">
            <h2>Our Long-Term Commitment</h2>
            <p>football.dhgate.com is not a temporary campaign site. We are building a long-term football content destination for fans around the world.</p>
            <p>Beyond the 2026 tournament, our editorial team will continue to cover the football calendar year-round, including major league competitions, international fixtures, transfer windows, player developments, and the next generation of football talent.</p>
            <p>Our goal is to become a trusted, go-to resource for football fans globally, backed by DHgate's two decades of experience serving the football community through its marketplace.</p>
            <p>This is a permanent editorial investment, not a seasonal project.</p>
        </div>
    </section>

    <section class="fb-about-section">
        <div class="fb-about-shell">
            <div class="fb-about-copy fb-about-copy--wide">
                <h2>Our Editorial Team</h2>
                <p>Our content is produced by DHgate's dedicated football editorial team, a group of football writers, data analysts, and content editors working from DHgate's offices.</p>
            </div>

            <div class="fb-about-coverage" aria-label="Coverage areas">
                <article>
                    <h3>Teams and Players</h3>
                    <p>Team and player profiles across all 48 qualified nations.</p>
                </article>
                <article>
                    <h3>Match Coverage</h3>
                    <p>Match schedules, group stage analysis, and tournament updates.</p>
                </article>
                <article>
                    <h3>Host Cities</h3>
                    <p>Host city travel and fan experience guides.</p>
                </article>
                <article>
                    <h3>Fan Culture</h3>
                    <p>Football lifestyle, culture, and fan gear content.</p>
                </article>
                <article>
                    <h3>Year-Round Football</h3>
                    <p>News, league coverage, and player tracking beyond the 2026 tournament.</p>
                </article>
            </div>

            <p class="fb-about-review-note">All content goes through an internal editorial review process before publication. We are committed to accuracy, relevance, and genuine value for football fans.</p>
        </div>
    </section>

    <section class="fb-about-section">
        <div class="fb-about-shell fb-about-relationship">
            <div>
                <h2>Our Relationship with DHgate.com</h2>
                <p>football.dhgate.com is a subdomain of dhgate.com and is fully owned and operated by DHgate. The two sites share the same company, the same team, and the same commitment to serving the global football community.</p>
                <p>The purpose of this subdomain is to provide dedicated, in-depth football content that complements DHgate's marketplace, helping fans learn about the teams, the players, and the matches, while also connecting them with fan gear available on DHgate.com.</p>
                <p>We are transparent about this relationship. Where product recommendations appear on this site, they link to DHgate.com listings and are clearly labeled.</p>
            </div>
            <a class="fb-about-store-link" href="https://www.dhgate.com/" target="_blank" rel="noopener noreferrer">Visit DHgate.com</a>
        </div>
    </section>

    <section class="fb-about-section fb-about-contact-section">
        <div class="fb-about-shell fb-about-contact">
            <div>
                <h2>Contact Us</h2>
                <p>For editorial inquiries, corrections, or partnership requests, please contact the DHgate Football Hub team.</p>
            </div>

            <div class="fb-about-contact__details">
                <a href="mailto:service@DHgate.com">service@DHgate.com</a>
                <p><strong>Parent company:</strong> DHgate.com</p>
                <address>6F Dimeng Commercial Building, No. 3-2 Hua Yuan Road, Haidian District, Beijing, China 100083</address>
                <p>For DHgate marketplace support, please visit <a href="https://www.dhgate.com/" target="_blank" rel="noopener noreferrer">dhgate.com</a>.</p>
            </div>
        </div>
    </section>

<?php
get_footer();
