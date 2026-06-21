<?php
/**
 * Plugin Name: Lucid Projects
 * Description: Projects portfolio — CPT + ACF + Astro-fidelity rendering, Media Library backed, Polylang-aware.
 */
if (!defined('ABSPATH')) exit;

/* ---------- 1. Custom Post Type ---------- */
add_action('init', function () {
  register_post_type('project', array(
    'labels' => array(
      'name' => 'Projekty', 'singular_name' => 'Projekt',
      'add_new' => 'Dodaj nowy', 'add_new_item' => 'Dodaj projekt',
      'edit_item' => 'Edytuj projekt', 'new_item' => 'Nowy projekt',
      'view_item' => 'Zobacz projekt', 'all_items' => 'Wszystkie projekty',
      'menu_name' => 'Projekty', 'search_items' => 'Szukaj projektów',
    ),
    'public' => true,
    'has_archive' => false,
    'menu_icon' => 'dashicons-portfolio',
    'menu_position' => 5,
    'supports' => array('title', 'thumbnail', 'page-attributes'),
    'rewrite' => array('slug' => 'projects', 'with_front' => false),
    'show_in_rest' => true,
  ));
});

/* ---------- 2. ACF fields ---------- */
add_action('acf/init', function () {
  if (!function_exists('acf_add_local_field_group')) return;
  acf_add_local_field_group(array(
    'key' => 'group_lucid_project',
    'title' => 'Dane projektu',
    'fields' => array(
      array('key'=>'field_lp_code','label'=>'Kod','name'=>'code','type'=>'text','wrapper'=>array('width'=>'25'),'instructions'=>'np. LMM.01'),
      array('key'=>'field_lp_discipline','label'=>'Dyscyplina','name'=>'discipline','type'=>'select','wrapper'=>array('width'=>'75'),'choices'=>array('Lighting Consulting'=>'Lighting Consulting','Signage & Wayfinding'=>'Signage & Wayfinding'),'default_value'=>'Lighting Consulting'),
      array('key'=>'field_lp_loc','label'=>'Lokalizacja','name'=>'loc','type'=>'text','wrapper'=>array('width'=>'50')),
      array('key'=>'field_lp_scope','label'=>'Zakres','name'=>'scope','type'=>'text','wrapper'=>array('width'=>'50')),
      array('key'=>'field_lp_client','label'=>'Klient','name'=>'client','type'=>'text','wrapper'=>array('width'=>'50')),
      array('key'=>'field_lp_architects','label'=>'Architekci','name'=>'architects','type'=>'text','wrapper'=>array('width'=>'50')),
      array('key'=>'field_lp_description','label'=>'Opis (lede w hero)','name'=>'description','type'=>'textarea','rows'=>4),
      array('key'=>'field_lp_outcome','label'=>'Rezultat (outcome)','name'=>'outcome','type'=>'textarea','rows'=>3),
      array('key'=>'field_lp_gallery','label'=>'Galeria (poza Obrazkiem wyróżniającym)','name'=>'gallery','type'=>'gallery','return_format'=>'array','preview_size'=>'medium','instructions'=>'Zdjęcie wiodące = Obrazek wyróżniający (panel po prawej). Tutaj dodaj pozostałe zdjęcia galerii.'),
    ),
    'location' => array(array(array('param'=>'post_type','operator'=>'==','value'=>'project'))),
    'position' => 'normal',
  ));
});

/* ---------- 3. Helpers ---------- */
function lucid_proj_locale($id) {
  // Render labels by the VIEWING page language (projects are a single EN-data set
  // surfaced on both the EN and PL index; detail pages render under /projects/ = EN).
  if (function_exists('pll_current_language')) { $l = pll_current_language('slug'); if ($l) return $l; }
  return 'en';
}
function lucid_proj_tagline($disc) { return $disc === 'Signage & Wayfinding' ? 'Architects of the Way' : 'Architects of Light'; }
function lucid_proj_disc_url($disc, $loc) {
  if ($disc === 'Signage & Wayfinding') return $loc === 'pl' ? '/pl/kompetencje/oznakowanie-i-nawigacja/' : '/expertise/signage-wayfinding/';
  return $loc === 'pl' ? '/pl/kompetencje/projektowanie-oswietlenia/' : '/expertise/lighting-design/';
}
function lucid_proj_page_url($which, $loc) {
  if ($which === 'contact') return $loc === 'pl' ? '/pl/kontakt/' : '/contact/';
  return $loc === 'pl' ? '/pl/projekty/' : '/projects/';
}
function lucid_proj_images($id) {
  $imgs = array();
  $fid = get_post_thumbnail_id($id);
  if ($fid) { $u = wp_get_attachment_image_url($fid, 'full'); if ($u) $imgs[] = $u; }
  $gal = get_field('gallery', $id);
  if (is_array($gal)) foreach ($gal as $g) {
    $u = is_array($g) ? (!empty($g['url']) ? $g['url'] : '') : wp_get_attachment_image_url($g, 'full');
    if ($u) $imgs[] = $u;
  }
  return $imgs;
}
function lucid_proj_ui($loc) {
  $en = array('breadcrumb'=>'Projects','allProjects'=>'← All projects','discussSimilar'=>'Discuss a similar project','gallery'=>'— Gallery','galleryTitle'=>'Project visualisations.','galleryIntro'=>'From the discipline portfolio. Click any image to enlarge.','factLocation'=>'Location','factScope'=>'Scope','factClient'=>'Client','factArchitects'=>'Architects','facts'=>'— Project Facts','factsTitle'=>'Project facts.','discipline'=>'— Discipline','explore'=>'Explore','view'=>'View','viewProject'=>'View project','ctaEyebrow'=>'— Conversation','ctaTitle'=>'Planning a similar project in Europe or the GCC?','ctaText'=>"Let's define the right integrated scope, workflow and delivery logic for your project.",'ctaBtn'=>'Start a Conversation','ctaMeta'=>'Reply within 2 business days · EN / PL / AR');
  if ($loc === 'pl') return array('breadcrumb'=>'Projekty','allProjects'=>'← Wszystkie projekty','discussSimilar'=>'Omów podobny projekt','gallery'=>'— Galeria','galleryTitle'=>'Wizualizacje projektowe.','galleryIntro'=>'Z portfolio dyscypliny. Kliknij dowolny obraz, by powiększyć.','factLocation'=>'Lokalizacja','factScope'=>'Zakres','factClient'=>'Klient','factArchitects'=>'Architekci','facts'=>'— Fakty projektowe','factsTitle'=>'Fakty projektowe.','discipline'=>'— Dyscyplina','explore'=>'Zobacz','view'=>'Zobacz','viewProject'=>'Zobacz projekt','ctaEyebrow'=>'— Rozmowa','ctaTitle'=>'Planujesz podobny projekt w Europie lub regionie GCC?','ctaText'=>'Zdefiniujmy właściwy zintegrowany zakres, proces i logikę realizacji dla Twojego projektu.','ctaBtn'=>'Zacznij rozmowę','ctaMeta'=>'Odpowiedź w ciągu 2 dni roboczych · EN / PL / AR');
  return $en;
}

/* ---------- 4. Card (index) ---------- */
function lucid_project_card($id) {
  $loc = lucid_proj_locale($id);
  $ui = lucid_proj_ui($loc);
  $imgs = lucid_proj_images($id);
  $img0 = $imgs ? $imgs[0] : '';
  $disc = (string) get_field('discipline', $id);
  $locv = (string) get_field('loc', $id);
  $scope = (string) get_field('scope', $id);
  $h  = '<a class="cs-card" href="' . esc_url(get_permalink($id)) . '">';
  $h .= '<div class="cs-image" style="background-image:url(\'' . esc_url($img0) . '\');background-size:cover;background-position:center"><div class="corner-tl"></div><div class="corner-br"></div></div>';
  $h .= '<div class="cs-body"><div class="cs-meta"><span>' . esc_html($disc) . '</span></div>';
  $h .= '<h3 class="cs-title">' . esc_html(get_the_title($id)) . '</h3>';
  $h .= '<p class="cs-loc">' . esc_html($locv) . ' · ' . esc_html($scope) . '</p>';
  $h .= '<div class="cs-foot"><span>' . esc_html($ui['viewProject']) . '</span><span class="cs-arrow">→</span></div></div></a>';
  return $h;
}

/* ---------- 5. Index grid ---------- */
function lucid_projects_index_html() {
  $q = new WP_Query(array('post_type'=>'project','posts_per_page'=>-1,'orderby'=>array('menu_order'=>'ASC','date'=>'ASC'),'post_status'=>'publish','lang'=>''));
  $h = '<div class="cs-grid">';
  while ($q->have_posts()) { $q->the_post(); $h .= lucid_project_card(get_the_ID()); }
  wp_reset_postdata();
  return $h . '</div>';
}

/* ---------- 6. Detail ---------- */
function lucid_project_detail($id) {
  if (!$id) return '';
  $loc = lucid_proj_locale($id);
  $ui = lucid_proj_ui($loc);
  $imgs = lucid_proj_images($id);
  $n = count($imgs);
  $disc = (string) get_field('discipline', $id);
  $title = get_the_title($id);
  $description = (string) get_field('description', $id);
  $outcome = (string) get_field('outcome', $id);
  $locv = (string) get_field('loc', $id);
  $scope = (string) get_field('scope', $id);
  $client = (string) get_field('client', $id);
  $architects = (string) get_field('architects', $id);
  $facts = array();
  if ($locv && $locv !== '—') $facts[$ui['factLocation']] = $locv;
  if ($scope && $scope !== '—') $facts[$ui['factScope']] = $scope;
  if ($client && $client !== '—') $facts[$ui['factClient']] = $client;
  if ($architects && $architects !== '—') $facts[$ui['factArchitects']] = $architects;
  $pad = function ($x) { return str_pad((string) $x, 2, '0', STR_PAD_LEFT); };
  $discUrl = lucid_proj_disc_url($disc, $loc);
  $projUrl = lucid_proj_page_url('projects', $loc);
  $contactUrl = lucid_proj_page_url('contact', $loc);

  $h  = '<div data-project data-images=\'' . esc_attr(wp_json_encode($imgs)) . '\'>';
  $h .= '<section class="pillar-hero"><div class="shell-wide">';
  $h .= '<div class="breadcrumb eyebrow">— ' . esc_html($ui['breadcrumb']) . '</div>';
  $h .= '<div class="disc-tier-row"><span class="disc-tier-badge is-lead">' . esc_html($disc) . '</span><span class="disc-tagline">' . esc_html(lucid_proj_tagline($disc)) . '</span></div>';
  $h .= '<div class="pillar-hero-grid"><div><h1 class="h1" style="max-width:14ch">' . esc_html($title) . '</h1></div>';
  $h .= '<div><p class="lede">' . esc_html($description) . '</p>';
  $h .= '<div style="margin-top:32px;display:flex;gap:14px;flex-wrap:wrap"><a class="btn btn-ghost" href="' . esc_url($projUrl) . '">' . esc_html($ui['allProjects']) . '<span class="arr"></span></a><a class="btn btn-primary" href="' . esc_url($contactUrl) . '">' . esc_html($ui['discussSimilar']) . '<span class="arr"></span></a></div></div></div>';
  $h .= '<div class="pillar-hero-meta"><span>· ' . esc_html($locv) . '</span><span>· ' . esc_html($scope) . '</span></div>';
  $h .= '</div></section>';

  if ($n > 0) {
    $h .= '<section><div class="shell-wide"><div class="sec-head"><div class="num">' . esc_html($ui['gallery']) . '</div><div><h2 class="h2">' . esc_html($ui['galleryTitle']) . '</h2></div><p style="max-width:520px;margin:0">' . esc_html($ui['galleryIntro']) . '</p></div>';
    $h .= '<div class="proj-gallery"><button class="pg-lead" data-lb="0" style="background-image:url(\'' . esc_url($imgs[0]) . '\')" aria-label="View image 1"><span class="pg-zoom">⤢ ' . esc_html($ui['view']) . '</span><span class="pg-index">01 / ' . $pad($n) . '</span></button>';
    $h .= '<div class="pg-thumbs">';
    for ($i = 1; $i < $n; $i++) {
      $h .= '<button class="pg-thumb" data-lb="' . $i . '" style="background-image:url(\'' . esc_url($imgs[$i]) . '\')" aria-label="View image ' . ($i + 1) . '"><span class="pg-zoom">⤢</span></button>';
    }
    $h .= '</div></div></div></section>';
  }

  if ($facts) {
    $h .= '<section style="background:var(--paper-2);border-top:1px solid var(--rule);border-bottom:1px solid var(--rule)"><div class="shell-wide"><div class="sec-head"><div class="num">' . esc_html($ui['facts']) . '</div><div><h2 class="h2">' . esc_html($ui['factsTitle']) . '</h2></div><p style="max-width:520px;margin:0">' . esc_html($disc) . '</p></div>';
    $h .= '<div class="matrix" style="grid-template-columns:repeat(' . count($facts) . ',1fr)">';
    foreach ($facts as $label => $val) {
      $h .= '<div class="matrix-cell"><div class="matrix-num">— ' . esc_html(function_exists('mb_strtoupper') ? mb_strtoupper($label) : strtoupper($label)) . '</div><div class="matrix-label">' . esc_html($val) . '</div></div>';
    }
    $h .= '</div></div></section>';
  }

  if ($outcome) {
    $h .= '<section><div class="shell-wide"><div class="body-grid"><div class="eyebrow">' . esc_html($ui['discipline']) . '</div><div>';
    $h .= '<p style="font-family:var(--font-display);font-size:clamp(20px,1.8vw,26px);font-weight:400;color:var(--ink);max-width:60ch;line-height:1.4;letter-spacing:-0.005em;margin:0 0 24px">' . esc_html($outcome) . '</p>';
    $h .= '<a class="hover-line" href="' . esc_url($discUrl) . '" style="color:var(--ink);font-family:var(--font-mono);font-size:13px;letter-spacing:0.06em;text-transform:uppercase">' . esc_html($ui['explore']) . ' ' . esc_html($disc) . ' →</a>';
    $h .= '</div></div></div></section>';
  }

  $h .= '<section class="cta-closing"><div class="shell"><div><div style="font-family:var(--font-mono);font-size:13px;letter-spacing:0.18em;color:var(--ink-3);text-transform:uppercase;margin-bottom:24px">' . esc_html($ui['ctaEyebrow']) . '</div><h2>' . esc_html($ui['ctaTitle']) . '</h2></div>';
  $h .= '<div class="right"><p>' . esc_html($ui['ctaText']) . '</p><a class="btn btn-primary" href="' . esc_url($contactUrl) . '">' . esc_html($ui['ctaBtn']) . '<span class="arr"></span></a><div class="meta">' . esc_html($ui['ctaMeta']) . '</div></div></div></section>';

  $h .= '<div class="lightbox" style="display:none"><button class="lb-close" aria-label="Close gallery">×</button><button class="lb-nav lb-prev" aria-label="Previous image">‹</button><img class="lb-img" src="" alt="" /><button class="lb-nav lb-next" aria-label="Next image">›</button><div class="lb-count"></div></div>';
  $h .= '</div>';
  return $h;
}

/* ---------- 7. Placeholders (index + detail) ---------- */
add_filter('render_block', function ($content, $block) {
  if (strpos($content, 'LUCID_PROJECTS_INDEX') !== false) {
    $content = str_replace('LUCID_PROJECTS_INDEX', lucid_projects_index_html(), $content);
  }
  if (strpos($content, 'LUCID_PROJECT_DETAIL') !== false) {
    $content = str_replace('LUCID_PROJECT_DETAIL', lucid_project_detail(get_queried_object_id()), $content);
  }
  return $content;
}, 10, 2);

/* ---------- 8. Lightbox JS (only on single project) ---------- */
add_action('wp_enqueue_scripts', function () {
  if (!is_singular('project')) return;
  $js = "(function(){var root=document.querySelector('[data-project]');if(!root)return;var images=[];try{images=JSON.parse(root.getAttribute('data-images')||'[]');}catch(e){}var lb=root.querySelector('.lightbox');if(!lb)return;var lbImg=lb.querySelector('.lb-img');var lbCount=lb.querySelector('.lb-count');function pad(n){return String(n).padStart(2,'0');}var idx=-1;function show(i){idx=(i+images.length)%images.length;lbImg.src=images[idx];lbCount.textContent=pad(idx+1)+' / '+pad(images.length);lb.style.display='flex';}function close(){lb.style.display='none';idx=-1;}root.querySelectorAll('[data-lb]').forEach(function(b){b.addEventListener('click',function(){show(Number(b.getAttribute('data-lb')));});});lb.querySelector('.lb-prev').addEventListener('click',function(e){e.stopPropagation();show(idx-1);});lb.querySelector('.lb-next').addEventListener('click',function(e){e.stopPropagation();show(idx+1);});lb.querySelector('.lb-close').addEventListener('click',close);lb.addEventListener('click',function(e){if(e.target===lb)close();});document.addEventListener('keydown',function(e){if(idx<0)return;if(e.key==='Escape')close();if(e.key==='ArrowRight')show(idx+1);if(e.key==='ArrowLeft')show(idx-1);});})();";
  if (wp_script_is('lucid-js', 'enqueued') || wp_script_is('lucid-js', 'registered')) {
    wp_add_inline_script('lucid-js', $js);
  } else {
    wp_register_script('lucid-proj-lightbox', '', array(), '1.0', true);
    wp_enqueue_script('lucid-proj-lightbox');
    wp_add_inline_script('lucid-proj-lightbox', $js);
  }
}, 30);

/* ---------- 9. Polylang: make CPT translatable ---------- */
add_filter('pll_get_post_types', function ($types, $is_settings) {
  $types['project'] = 'project';
  return $types;
}, 10, 2);
