<?php
add_action('wp_enqueue_scripts', function () {
  wp_enqueue_style('lucid-fonts', 'https://fonts.googleapis.com/css2?family=Red+Hat+Display:wght@400;500;600;700&family=Red+Hat+Text:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap', array(), null);
  $b = get_stylesheet_directory_uri() . '/astro';
  wp_enqueue_style('lucid-astro-base', $b . '/styles.css', array(), '2.3');
  wp_enqueue_style('lucid-astro-home', $b . '/home.css', array('lucid-astro-base'), '2.3');
  wp_enqueue_style('lucid-astro-reel', $b . '/reel.css', array('lucid-astro-base'), '2.3');
  wp_enqueue_style('lucid-astro-pillar', $b . '/pillar.css', array('lucid-astro-base'), '2.3');
  wp_enqueue_style('lucid-astro-insights', $b . '/insights.css', array('lucid-astro-base'), '2.3');
  wp_enqueue_style('lucid-astro-scoped', $b . '/scoped.css', array('lucid-astro-base'), '2.3');
  wp_enqueue_script('lucid-js', get_stylesheet_directory_uri() . '/js/lucid.js', array(), '2.3', array('in_footer' => true, 'strategy' => 'defer'));
}, 20);

// --- i18n: locale-aware shared chrome + homepage (Polylang) ---
function lucid_locale() {
  $l = function_exists('pll_current_language') ? pll_current_language() : '';
  return $l ? $l : 'en';
}
function lucid_inc($base) {
  $d = get_stylesheet_directory() . '/inc/';
  $loc = lucid_locale();
  $f = $d . $base . '-' . $loc . '.html';
  if (!is_readable($f)) $f = $d . $base . '.html';
  return is_readable($f) ? file_get_contents($f) : '';
}
// Dynamic, per-page language switcher (Polylang). Built to match the static
// .lang-switch markup from the Astro header, so it drops straight in.
function lucid_langswitch() {
  if (!function_exists('pll_the_languages')) return '';
  $ls = pll_the_languages(array('raw' => 1, 'hide_if_no_translation' => 0));
  if (!is_array($ls) || !$ls) return '';
  $o = '<div class="lang-switch" role="group" aria-label="Language">';
  foreach ($ls as $l) {
    $a = !empty($l['current_lang']) ? ' class="active"' : '';
    $o .= '<a' . $a . ' href="' . esc_url($l['url']) . '" hreflang="' . esc_attr($l['slug']) . '">' . strtoupper($l['slug']) . '</a>';
  }
  return $o . '</div>';
}
add_action('wp_body_open', function () {
  if (is_admin()) return;
  $h = lucid_inc('header');
  // swap the static switcher for the live, per-page one
  $h = preg_replace_callback('#<div class="lang-switch".*?</div>#s', function () {
    return lucid_langswitch();
  }, $h);
  // swap the static logo for the WP Custom Logo (Customizer → Site Identity) when set
  $logo_id = get_theme_mod('custom_logo');
  if ($logo_id) {
    $logo_url = wp_get_attachment_image_url($logo_id, 'full');
    if ($logo_url) $h = preg_replace('#src="/assets/logo-lucid[^"]*"#', 'src="' . esc_url($logo_url) . '"', $h);
  }
  echo $h;
});
add_action('wp_footer', function () {
  if (is_admin()) return;
  echo lucid_inc('footer');
}, 5);
// Front page renders a placeholder inside a wp:html block; swap it for the
// locale-appropriate home markup at render time (raw, no wpautop).
add_filter('render_block', function ($content, $block) {
  if (strpos($content, 'LUCID_HOME_PLACEHOLDER') !== false) {
    return str_replace('LUCID_HOME_PLACEHOLDER', lucid_inc('home'), $content);
  }
  return $content;
}, 10, 2);
