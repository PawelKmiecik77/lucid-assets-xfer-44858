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

/* ============ placeholder render ============ */
add_filter('render_block', function ($content, $block) {
  if (strpos($content, 'LUCID_INTEG') !== false) {
    $content = str_replace('LUCID_INTEG', lucid_integ_html(get_queried_object_id()), $content);
  }
  if (strpos($content, 'LUCID_PARTNERS') !== false) {
    $content = str_replace('LUCID_PARTNERS', lucid_partners_html(get_queried_object_id()), $content);
  }
  return $content;
}, 10, 2);
