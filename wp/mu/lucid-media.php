<?php
/**
 * Plugin Name: Lucid Media
 * Description: Editable image + caption sections (ACF), faithful to the Astro design. Media Library backed. Reels stay HTML.
 */
if (!defined('ABSPATH')) exit;

/* ============ INTEGRATED-DESIGN — "What it looks like" 3-up ============ */
add_action('acf/init', function () {
  if (!function_exists('acf_add_local_field_group')) return;
  acf_add_local_field_group(array(
    'key' => 'group_lucid_integ',
    'title' => 'Wizualizacje integracji (siatka „What it looks like")',
    'fields' => array(
      array('key'=>'field_integ_items','label'=>'Wizualizacje','name'=>'integ_items','type'=>'repeater','layout'=>'block','button_label'=>'Dodaj wizualizację','sub_fields'=>array(
        array('key'=>'field_integ_img','label'=>'Obraz','name'=>'image','type'=>'image','return_format'=>'url','preview_size'=>'medium','wrapper'=>array('width'=>'30'),'instructions'=>'Puste = placeholder „Visual in production".'),
        array('key'=>'field_integ_label','label'=>'Etykieta','name'=>'label','type'=>'text','wrapper'=>array('width'=>'70'),'placeholder'=>'01 · Integration study'),
        array('key'=>'field_integ_title','label'=>'Tytuł','name'=>'title','type'=>'text'),
        array('key'=>'field_integ_desc','label'=>'Opis','name'=>'description','type'=>'textarea','rows'=>2),
      )),
    ),
    'location' => array(
      array(array('param'=>'post','operator'=>'==','value'=>'12')),
      array(array('param'=>'post','operator'=>'==','value'=>'59')),
    ),
  ));
});

function lucid_integ_html($page_id) {
  // Read the ACF repeater rows from raw meta (robust against local sub-field
  // name-lookup quirks); the ACF admin UI still edits these fields normally.
  $n = (int) get_post_meta($page_id, 'integ_items', true);
  if ($n < 1) return '';
  $h = '<div class="integ-figs">';
  for ($idx = 0; $idx < $n; $idx++) {
    $img = get_post_meta($page_id, 'integ_items_' . $idx . '_image', true);
    if ($img && is_numeric($img)) $img = wp_get_attachment_image_url((int) $img, 'large');
    $label = (string) get_post_meta($page_id, 'integ_items_' . $idx . '_label', true);
    $title = (string) get_post_meta($page_id, 'integ_items_' . $idx . '_title', true);
    $desc = (string) get_post_meta($page_id, 'integ_items_' . $idx . '_description', true);
    $h .= '<figure class="integ-fig">';
    if ($img) {
      $h .= '<div class="integ-frame" style="background-image:url(\'' . esc_url($img) . '\');background-size:cover;background-position:center"><span class="corner-tl"></span><span class="corner-br"></span></div>';
    } else {
      $h .= '<div class="integ-frame"><span class="corner-tl"></span><span class="corner-br"></span><div class="integ-label">' . esc_html($label) . '</div><div class="integ-soon">Visual in production</div></div>';
    }
    $h .= '<figcaption><b>' . esc_html($title) . '</b> ' . esc_html($desc) . '</figcaption>';
    $h .= '</figure>';
  }
  return $h . '</div>';
}

/* ============ PARTNERS — Associate Partners ============ */
add_action('acf/init', function () {
  if (!function_exists('acf_add_local_field_group')) return;
  acf_add_local_field_group(array(
    'key' => 'group_lucid_partners',
    'title' => 'Partnerzy (Associate Partners)',
    'fields' => array(
      array('key'=>'field_partners','label'=>'Partnerzy','name'=>'partners','type'=>'repeater','layout'=>'block','button_label'=>'Dodaj partnera','sub_fields'=>array(
        array('key'=>'field_p_portrait','label'=>'Portret','name'=>'portrait','type'=>'image','return_format'=>'id','preview_size'=>'medium','wrapper'=>array('width'=>'25')),
        array('key'=>'field_p_region','label'=>'Region (eyebrow)','name'=>'region','type'=>'text','wrapper'=>array('width'=>'75'),'placeholder'=>'— Associate Partner · Saudi Arabia & the Gulf'),
        array('key'=>'field_p_name','label'=>'Imię i nazwisko','name'=>'name','type'=>'text','wrapper'=>array('width'=>'50')),
        array('key'=>'field_p_scope','label'=>'Firma · zakres','name'=>'scope','type'=>'text','wrapper'=>array('width'=>'50')),
        array('key'=>'field_p_bio','label'=>'Opis','name'=>'bio','type'=>'textarea','rows'=>4),
        array('key'=>'field_p_linktext','label'=>'Tekst linku','name'=>'link_text','type'=>'text','wrapper'=>array('width'=>'50')),
        array('key'=>'field_p_linkurl','label'=>'URL linku','name'=>'link_url','type'=>'url','wrapper'=>array('width'=>'50')),
      )),
    ),
    'location' => array(
      array(array('param'=>'post','operator'=>'==','value'=>'15')),
      array(array('param'=>'post','operator'=>'==','value'=>'65')),
    ),
  ));
});

function lucid_partners_html($page_id) {
  $n = (int) get_post_meta($page_id, 'partners', true);
  if ($n < 1) return '';
  $h = '';
  for ($i = 0; $i < $n; $i++) {
    $k = 'partners_' . $i . '_';
    $region = (string) get_post_meta($page_id, $k . 'region', true);
    $name = (string) get_post_meta($page_id, $k . 'name', true);
    $scope = (string) get_post_meta($page_id, $k . 'scope', true);
    $bio = (string) get_post_meta($page_id, $k . 'bio', true);
    $ltext = (string) get_post_meta($page_id, $k . 'link_text', true);
    $lurl = (string) get_post_meta($page_id, $k . 'link_url', true);
    $img = get_post_meta($page_id, $k . 'portrait', true);
    if ($img && is_numeric($img)) $img = wp_get_attachment_image_url((int) $img, 'large');
    $bg = ($i % 2 === 0) ? 'background:var(--paper)' : 'border-top:1px solid var(--rule);background:var(--paper-2)';
    $h .= '<section class="partner-band" style="' . $bg . '"><div class="shell-wide"><div class="partner-grid">';
    $h .= '<div class="partner-info">';
    $h .= '<div class="partner-region">' . esc_html($region) . '</div>';
    $h .= '<h2 class="partner-name">' . esc_html($name) . '</h2>';
    $h .= '<div class="partner-scope">' . esc_html($scope) . '</div>';
    $h .= '<p class="partner-bio">' . esc_html($bio) . '</p>';
    if ($ltext) $h .= '<a class="partner-link" href="' . esc_url($lurl) . '" target="_blank" rel="noopener">' . esc_html($ltext) . '<span class="arr"></span></a>';
    $h .= '</div><div class="partner-portrait">';
    if ($img) $h .= '<img src="' . esc_url($img) . '" alt="' . esc_attr($name) . '" loading="lazy">';
    $h .= '<span class="corner-tl"></span><span class="corner-br"></span></div>';
    $h .= '</div></div></section>';
  }
  return $h;
}

/* ============ WAYDRAFTER — software window / film slot ============ */
add_action('acf/init', function () {
  if (!function_exists('acf_add_local_field_group')) return;
  acf_add_local_field_group(array(
    'key' => 'group_lucid_swfilm',
    'title' => 'WayDrafter — okno / film',
    'fields' => array(
      array('key'=>'field_sw_image','label'=>'Obraz w oknie','name'=>'sw_image','type'=>'image','return_format'=>'id','preview_size'=>'medium','wrapper'=>array('width'=>'50'),'instructions'=>'Puste = placeholder z przyciskiem play.'),
      array('key'=>'field_sw_video','label'=>'Wideo (URL YouTube/Vimeo/MP4)','name'=>'sw_video','type'=>'url','wrapper'=>array('width'=>'50'),'instructions'=>'Jeśli podane, zastępuje obraz.'),
      array('key'=>'field_sw_title','label'=>'Tytuł okna','name'=>'sw_title','type'=>'text','wrapper'=>array('width'=>'50')),
      array('key'=>'field_sw_status','label'=>'Status (np. PREVIEW)','name'=>'sw_status','type'=>'text','wrapper'=>array('width'=>'50')),
      array('key'=>'field_sw_phlabel','label'=>'Placeholder — etykieta','name'=>'sw_ph_label','type'=>'text','wrapper'=>array('width'=>'50')),
      array('key'=>'field_sw_phdim','label'=>'Placeholder — podtytuł','name'=>'sw_ph_dim','type'=>'text','wrapper'=>array('width'=>'50')),
      array('key'=>'field_sw_caption','label'=>'Podpis pod oknem','name'=>'sw_caption','type'=>'text'),
      array('key'=>'field_sw_ctatext','label'=>'CTA — tekst','name'=>'sw_cta_text','type'=>'text','wrapper'=>array('width'=>'50')),
      array('key'=>'field_sw_ctaurl','label'=>'CTA — URL','name'=>'sw_cta_url','type'=>'text','wrapper'=>array('width'=>'50')),
    ),
    'location' => array(
      array(array('param'=>'post','operator'=>'==','value'=>'13')),
      array(array('param'=>'post','operator'=>'==','value'=>'61')),
      array(array('param'=>'post','operator'=>'==','value'=>'19')),
      array(array('param'=>'post','operator'=>'==','value'=>'71')),
      array(array('param'=>'post','operator'=>'==','value'=>'20')),
      array(array('param'=>'post','operator'=>'==','value'=>'73')),
    ),
  ));
});

function lucid_swfilm_html($page_id) {
  $img = get_post_meta($page_id, 'sw_image', true);
  if ($img && is_numeric($img)) $img = wp_get_attachment_image_url((int) $img, 'large');
  $video = trim((string) get_post_meta($page_id, 'sw_video', true));
  $title = (string) get_post_meta($page_id, 'sw_title', true);
  $status = (string) get_post_meta($page_id, 'sw_status', true);
  $phl = (string) get_post_meta($page_id, 'sw_ph_label', true);
  $phd = (string) get_post_meta($page_id, 'sw_ph_dim', true);
  $cap = (string) get_post_meta($page_id, 'sw_caption', true);
  $ctat = (string) get_post_meta($page_id, 'sw_cta_text', true);
  $ctau = (string) get_post_meta($page_id, 'sw_cta_url', true);
  $h = '<div class="sw-window">';
  $h .= '<div class="sw-bar"><span class="sw-dots"><i></i><i></i><i></i></span><span class="sw-title">' . esc_html($title) . '</span><span class="sw-status">' . esc_html($status) . '</span></div>';
  $h .= '<div class="sw-screen">';
  if ($video) {
    $embed = function_exists('wp_oembed_get') ? wp_oembed_get($video) : false;
    if ($embed) $h .= '<div class="sw-embed" style="width:100%;height:100%">' . $embed . '</div>';
    else $h .= '<video class="sw-video" src="' . esc_url($video) . '" controls playsinline style="width:100%;height:100%;object-fit:cover"></video>';
  } elseif ($img) {
    $h .= '<div class="sw-poster" style="background-image:url(\'' . esc_url($img) . '\');background-size:cover;background-position:center;width:100%;height:100%"></div>';
  } else {
    $h .= '<div class="sw-placeholder"><div class="sw-corner tl"></div><div class="sw-corner tr"></div><div class="sw-corner bl"></div><div class="sw-corner br"></div><div class="sw-play"><span></span></div><div class="sw-ph-label">' . esc_html($phl) . '</div><div class="sw-ph-dim">' . esc_html($phd) . '</div></div>';
  }
  $h .= '</div>';
  $h .= '<div class="sw-caption">' . esc_html($cap) . '</div>';
  $h .= '</div>';
  if ($ctat) $h .= '<a class="software-cta" href="' . esc_url($ctau) . '">' . esc_html($ctat) . '<span class="arr"></span></a>';
  return $h;
}

/* ============ STUDIO — team grid ============ */
add_action('acf/init', function () {
  if (!function_exists('acf_add_local_field_group')) return;
  acf_add_local_field_group(array(
    'key' => 'group_lucid_team',
    'title' => 'Zespół (Founding Partners)',
    'fields' => array(
      array('key'=>'field_team','label'=>'Zespół','name'=>'team','type'=>'repeater','layout'=>'block','button_label'=>'Dodaj osobę','sub_fields'=>array(
        array('key'=>'field_t_photo','label'=>'Zdjęcie','name'=>'photo','type'=>'image','return_format'=>'id','preview_size'=>'thumbnail','wrapper'=>array('width'=>'20')),
        array('key'=>'field_t_initials','label'=>'Inicjały','name'=>'initials','type'=>'text','wrapper'=>array('width'=>'15')),
        array('key'=>'field_t_name','label'=>'Imię i nazwisko','name'=>'name','type'=>'text','wrapper'=>array('width'=>'25')),
        array('key'=>'field_t_role','label'=>'Rola','name'=>'role','type'=>'text','wrapper'=>array('width'=>'20')),
        array('key'=>'field_t_focus','label'=>'Specjalizacja','name'=>'focus','type'=>'text','wrapper'=>array('width'=>'20')),
      )),
    ),
    'location' => array(
      array(array('param'=>'post','operator'=>'==','value'=>'14')),
      array(array('param'=>'post','operator'=>'==','value'=>'63')),
    ),
  ));
  acf_add_local_field_group(array(
    'key' => 'group_lucid_team2',
    'title' => 'Zespół projektowy (Design team)',
    'fields' => array(
      array('key'=>'field_team2','label'=>'Zespół projektowy','name'=>'team2','type'=>'repeater','layout'=>'block','button_label'=>'Dodaj osobę','sub_fields'=>array(
        array('key'=>'field_t2_photo','label'=>'Zdjęcie','name'=>'photo','type'=>'image','return_format'=>'id','preview_size'=>'thumbnail','wrapper'=>array('width'=>'20')),
        array('key'=>'field_t2_initials','label'=>'Inicjały','name'=>'initials','type'=>'text','wrapper'=>array('width'=>'15')),
        array('key'=>'field_t2_name','label'=>'Imię i nazwisko','name'=>'name','type'=>'text','wrapper'=>array('width'=>'25')),
        array('key'=>'field_t2_role','label'=>'Rola','name'=>'role','type'=>'text','wrapper'=>array('width'=>'20')),
        array('key'=>'field_t2_focus','label'=>'Specjalizacja','name'=>'focus','type'=>'text','wrapper'=>array('width'=>'20')),
      )),
    ),
    'location' => array(
      array(array('param'=>'post','operator'=>'==','value'=>'14')),
      array(array('param'=>'post','operator'=>'==','value'=>'63')),
    ),
  ));
});

function lucid_team_html($page_id, $field = 'team') {
  $n = (int) get_post_meta($page_id, $field, true);
  if ($n < 1) return '';
  $h = '<div class="team-grid team-grid-4">';
  for ($i = 0; $i < $n; $i++) {
    $k = $field . '_' . $i . '_';
    $photo = get_post_meta($page_id, $k . 'photo', true);
    if ($photo && is_numeric($photo)) $photo = wp_get_attachment_image_url((int) $photo, 'medium');
    $initials = (string) get_post_meta($page_id, $k . 'initials', true);
    $name = (string) get_post_meta($page_id, $k . 'name', true);
    $role = (string) get_post_meta($page_id, $k . 'role', true);
    $focus = (string) get_post_meta($page_id, $k . 'focus', true);
    $h .= '<div class="team-card"><div class="team-photo" style="position:relative"><span>' . esc_html($initials) . '</span>';
    if ($photo) $h .= '<img src="' . esc_url($photo) . '" alt="' . esc_attr($name) . '" loading="lazy" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover" onerror="this.remove()">';
    $h .= '</div><div class="team-role">' . esc_html($role) . '</div><h3 class="team-name">' . esc_html($name) . '</h3><div class="team-focus">' . esc_html($focus) . '</div></div>';
  }
  return $h . '</div>';
}

/* ============ placeholder render ============ */
add_filter('render_block', function ($content, $block) {
  if (strpos($content, 'LUCID_INTEG') !== false) {
    $content = str_replace('LUCID_INTEG', lucid_integ_html(get_queried_object_id()), $content);
  }
  if (strpos($content, 'LUCID_PARTNERS') !== false) {
    $content = str_replace('LUCID_PARTNERS', lucid_partners_html(get_queried_object_id()), $content);
  }
  if (strpos($content, 'LUCID_SWFILM') !== false) {
    $content = str_replace('LUCID_SWFILM', lucid_swfilm_html(get_queried_object_id()), $content);
  }
  if (strpos($content, 'LUCID_TEAM2') !== false) {
    $content = str_replace('LUCID_TEAM2', lucid_team_html(get_queried_object_id(), 'team2'), $content);
  }
  if (strpos($content, 'LUCID_TEAM') !== false) {
    $content = str_replace('LUCID_TEAM', lucid_team_html(get_queried_object_id(), 'team'), $content);
  }
  return $content;
}, 10, 2);

/* ============ layout CSS (non-scoped) — restores Astro scoped styles lost in the editable renders ============ */
add_action('wp_enqueue_scripts', function () {
  $css = <<<'LCSS'
.partner-band{padding:clamp(44px,6vw,84px) 0}
.partner-grid{display:grid;grid-template-columns:1fr 240px;gap:clamp(28px,4vw,56px);align-items:center}
.partner-region{font-family:var(--font-mono);font-size:13px;letter-spacing:0.14em;text-transform:uppercase;color:var(--ink-3);margin-bottom:16px}
.partner-name{font-family:var(--font-display);font-size:clamp(28px,3vw,44px);font-weight:500;letter-spacing:-0.015em;line-height:1.1;margin:0}
.partner-scope{font-family:var(--font-mono);font-size:13px;letter-spacing:0.06em;text-transform:uppercase;color:var(--accent);margin:14px 0 22px}
.partner-bio{font-size:15px;line-height:1.7;color:var(--ink-2);max-width:54ch;margin:0 0 26px}
.partner-link{color:var(--ink);font-family:var(--font-mono);font-size:13px;letter-spacing:0.08em;text-transform:uppercase;display:inline-flex;align-items:center;gap:8px;border-bottom:1px solid var(--ink);padding-bottom:4px}
.partner-portrait{position:relative;width:240px;max-width:100%}
.partner-portrait img{width:100%;aspect-ratio:4/5;object-fit:cover;object-position:center top;display:block;border:1px solid var(--rule)}
.partner-portrait .corner-tl,.partner-portrait .corner-br{position:absolute;width:13px;height:13px;border-top:1px solid var(--ink);border-left:1px solid var(--ink);top:-6px;left:-6px;z-index:2}
.partner-portrait .corner-br{top:auto;left:auto;bottom:-6px;right:-6px;border-top:0;border-left:0;border-bottom:1px solid var(--ink);border-right:1px solid var(--ink)}
.integ-figs{display:grid;grid-template-columns:repeat(3,1fr);gap:clamp(20px,2.4vw,36px);margin-top:4px}
.integ-frame{position:relative;aspect-ratio:4/3;border:1px solid var(--rule);background:var(--paper-2);display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;overflow:hidden;background-image:linear-gradient(var(--rule) 1px,transparent 1px),linear-gradient(90deg,var(--rule) 1px,transparent 1px);background-size:32px 32px;background-position:center}
.integ-frame .integ-label{font-family:var(--font-mono);font-size:12px;letter-spacing:0.12em;text-transform:uppercase;color:var(--ink-3);z-index:1}
.integ-frame .integ-soon{font-family:var(--font-mono);font-size:11px;letter-spacing:0.1em;text-transform:uppercase;color:var(--accent);z-index:1}
.integ-frame .corner-tl,.integ-frame .corner-br{position:absolute;width:13px;height:13px;border-top:1px solid var(--ink);border-left:1px solid var(--ink);top:8px;left:8px;z-index:2}
.integ-frame .corner-br{top:auto;left:auto;bottom:8px;right:8px;border-top:0;border-left:0;border-bottom:1px solid var(--ink);border-right:1px solid var(--ink)}
.integ-fig figcaption{font-size:14px;line-height:1.6;color:var(--ink-2);margin-top:14px}
.integ-fig figcaption b{color:var(--ink);font-weight:600;display:block;margin-bottom:4px}
@media(max-width:860px){.integ-figs{grid-template-columns:1fr}.partner-grid{grid-template-columns:1fr;gap:28px}}
LCSS;
  wp_add_inline_style('lucid-astro-base', $css);
}, 25);
