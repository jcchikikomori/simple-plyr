<?php
/**
 * Simple Plyr plugin updated
 * PHP version 5.6
 *
 * @category Wordpress-plugin
 * @package  Simple_Plyr
 * @author   John Cyrill Corsanes <jccorsanes@protonmail.com>
 * @license  https://www.gnu.org/licenses/gpl-3.0.txt gpl-3.0
 * @link     https://github.com/jcchikikomori/simple-plyr
 * @since    0.0.2
 *
 * Plugin Name:       Simple Plyr
 * Plugin URI:        https://github.com/jcchikikomori/simple-plyr
 * Description:       Simple Plyr Video Player
 * Version:           0.0.2
 * Author:            John Cyrill Corsanes
 * Author URI:        https://github.com/jcchikikomori
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       plyrio
 * Domain Path:       /languages
 */

if ( ! defined( 'WPINC' ) ) {
	// If this file is called directly, abort.
	die;
}

use RicardoFiorani\Matcher\VideoServiceMatcher;

require __DIR__ . '/vendor/autoload.php';

/**
 * Add Shortcode
 *
 * @param array $atts User defined attributes in shortcode tag.
 *
 * @return string
 * @throws \RicardoFiorani\Exception\ServiceNotAvailableException This will occur if URL content is not available.
 */
function simple_plyr_filter( $atts ) {
	// Attributes.
	$atts = shortcode_atts(
		array(
			// (c) Nikon Europe. C.C.
			'url'    => 'https://www.youtube.com/watch?v=gM0qOa_H-rs',
			'poster' => '/path/to/poster.jpg',
		),
		$atts,
		'plyr'
	);

	$vsm = new VideoServiceMatcher();

	// Detects which service the url belongs to and
	// returns the service's implementation of
	// RicardoFiorani\Adapter\VideoAdapterInterface.
	$video = $vsm->parse( $atts['url'] );

	// if video was from YouTube or Vime.
	if ( $video instanceof RicardoFiorani\Adapter\Youtube\YoutubeServiceAdapter
		|| $video instanceof RicardoFiorani\Adapter\Vimeo\VimeoServiceAdapter
	) {
		$str = sprintf(
			"<div class='plyr__video-embed plyr-embed' id='plyr-player' poster='%s'>
                <iframe src='%s'allowfullscreen allowtransparency allow=\"autoplay\">
                </iframe>
            </div>",
			$atts['poster'],
			$atts['url']
		);
	} else {
		// use HTML5 video code.
		$str = sprintf(
			"<video id='plyr-player' poster='%s' controls><source src='%s'
            type='video/mp4'></video>",
			$atts['poster'],
			$atts['url']
		);
	}

	return $str;
}

/**
 * Load assets
 * 
 * @return void
 */
function simple_plyr_assets() {
	$plugin_url = plugin_dir_url( __FILE__ );
	// Get the theme data.
	$theme = wp_get_theme();

	wp_register_style( 'plyr-style', $plugin_url . 'assets/plyr.css', array(), $theme->get( 'Version' ) );
	wp_register_script( 'plyr-script', $plugin_url . 'assets/plyr.min.js', array(), $theme->get( 'Version' ), true );
	wp_register_script( 'plyr-loader-script', $plugin_url . 'assets/loader.js', array(), $theme->get( 'Version' ), true );

	wp_enqueue_style( 'plyr-style' );
	wp_enqueue_script( 'plyr-script' );
	wp_enqueue_script( 'plyr-loader-script' );  
}

/**
 * Add Quicktags
 * 
 * @return void
 */
function simple_plyr_quicktags() {
	if ( wp_script_is( 'quicktags' ) ) {
		?>
		<script type="text/javascript">
			QTags.addButton(
				'plyr', 'Simple Plyr',
				'[plyr url="/path/to/video.mp4" poster="/path/to/poster.jpg"]',
				'', '', 'Plyr Video Player', 141
			);
		</script>
		<?php
	}
}

add_shortcode( 'plyr', 'simple_plyr_filter' );
add_action( 'wp_enqueue_scripts', 'simple_plyr_assets' );
add_action( 'admin_print_footer_scripts', 'simple_plyr_quicktags' );
