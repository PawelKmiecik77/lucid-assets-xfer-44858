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
  $rows = function_exists('get_field') ? get_field('integ_items', $page_id) : null;
  if (!is_array($rows) || !$rows) return '';
  $h = '<div class="integ-figs">';
  foreach ($rows as $r) {
    $img = !empty($r['image']) ? $r['image'] : '';
    $label = isset($r['label']) ? $r['label'] : '';
    $title = isset($r['title']) ? $r['title'] : '';
    $desc = isset($r['description']) ? $r['description'] : '';
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

/* ============ placeholder render ============ */
add_filter('render_block', function ($content, $block) {
  if (strpos($content, 'LUCID_INTEG') !== false) {
    $content = str_replace('LUCID_INTEG', lucid_integ_html(get_queried_object_id()), $content);
  }
  return $content;
}, 10, 2);
