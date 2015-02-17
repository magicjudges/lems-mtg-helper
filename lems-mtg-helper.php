<?php
/*
Plugin Name: Lems MTG Helper
Plugin URI:  http://www.jasonlemahieu.com
Description: Easily create MtG tooltips and display card images
Version: 0.3
Author: Jason Lemahieu, Joel Krebs
Author URI: http://www.jasonlemahieu.com
*/

/* 
	CREDIT:  Based HEAVILY on the work by Sebastian Sebald (http://www.distractedbysquirrels.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

include 'lems-mtg-helper-cardfinder.php';

if (!defined('WP_CONTENT_URL'))
	define('WP_CONTENT_URL', get_option('url') . '/wp-content');
if (!defined('WP_CONTENT_DIR'))
	define('WP_CONTENT_DIR', ABSPATH . 'wp-content');
if (!defined('WP_CONTENT_FOLDER'))
	define('WP_CONTENT_FOLDER', str_replace(ABSPATH, '/', WP_CONTENT_DIR));
define('LEMS_MTG_DIR', WP_CONTENT_FOLDER . '/plugins/lems-mtg-helper');

if (!is_admin()) {
	add_action('init', 'mtgh_init');
}

function mtgh_init()
{
	if (function_exists('wp_enqueue_script')) {
		wp_enqueue_script('mtgh', get_bloginfo('wpurl') . LEMS_MTG_DIR . '/js/jtip.js', array('jquery'), '2.13a.4');
		wp_enqueue_style('mtg_helper_style', get_bloginfo('wpurl') . LEMS_MTG_DIR . '/css/lems-mtg-helper.css', array(), '1.0');
	} else {
		add_action('wp_head', 'init_header');
	}
}

function init_header()
{
	$tooltip_url = get_bloginfo('wpurl') . LEMS_MTG_DIR . '/js/jtip.js';
	$dimension_url = get_bloginfo('wpurl') . LEMS_MTG_DIR . '/js/jquery.dimensions.min.js.js';
	$jquery_url = get_bloginfo('wpurl') . '/wp-includes/js/jquery/jquery.js';
	$css_url = get_bloginfo('wpurl') . LEMS_MTG_DIR . '/css/lems-mtg-helper.css';
	?>
	<script type="text/javascript" src="<?php echo $tooltip_url ?>"></script>
	<script type="text/javascript" src="<?php echo $jquery_url ?>"></script>
	<script type="text/javascript" src="<?php echo $dimension_url ?>"></script>
	<link type="text/css" rel="stylesheet" href="<?php echo $css_url ?>" media="screen"/>
<?php
}

function cache_cards($names)
{
	$images = array();
	if (is_string($names)) {
		$images[] = "<img src='" . get_source_from_name($names) . "' style='display:none;width:1px;height:1px;' />";
	} else {
		foreach ($names as $cardname) {
			$images[] = "<img src='" . get_source_from_name($cardname) . "' style='display:none;width:1px;height:1px;' />";
		}
	}
	return implode("", $images);
}

function lems_mtg_quicktags()
{
	if (wp_script_is('quicktags')) {
		?>
		<script type="text/javascript">
			QTags.addButton('card', 'MTG Hover', '[card]', '[/card]');
			QTags.addButton('cardimg', 'MTG Image', '[cardimg align=none]', '[/cardimg]');
		</script>
	<?php
	}
}

function lems_mtg_shortcode_cardimg($atts, $content = null)
{
	extract(shortcode_atts(array(
		'align' => '',
		'set' => ''
	), $atts));
	$align = strtolower($align);

	if ($align && in_array($align, array('left', 'right', 'center'))) {
		$align_html = " style='float:" . esc_html($align) . "' ";
	} else {
		$align_html = '';
	}

	$src = get_source_from_name(sanitize_text_field($content), sanitize_text_field($set));
	return "<p><img $align_html class='lems-mtg-cardimg' src='" . $src . "'></p>";
}

function mtg_card_hover($atts, $content)
{
	extract(shortcode_atts(array(
		'text' => $content,
		'set' => ''
	), $atts));

	$query = array(
		'find' => sanitize_text_field($content),
		'width' => 223,
		'height' => 310
	);
	if ($set) {
		$query['set'] = sanitize_text_field($set);
	}

	return '<a href="' . get_bloginfo('wpurl') . LEMS_MTG_DIR
	. '/lems-mtg-helper-cardfinder.php?' . http_build_query($query) . '" class="jTip" name="">'
	. wp_kses_post($text) . '</a>';
}

add_action('admin_print_footer_scripts', 'lems_mtg_quicktags', 100);
add_shortcode('cardimg', 'lems_mtg_shortcode_cardimg');
add_shortcode('card', 'mtg_card_hover');