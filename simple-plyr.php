<?php
/**
 *
 * @link              https://github.com/jcchikikomori/simple-plyr
 * @since             0.0.2
 * @package           Simple Plyr (PHP7)
 *
 * @wordpress-plugin
 * Plugin Name:       Simple Plyr
 * Plugin URI:        https://github.com/jcchikikomori/simple-plyr
 * Description:       Simple Plyr Video Player
 * Version:           0.0.2
 * Author:            Bestony
 * Author URI:        https://github.com/jcchikikomori
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       plyrio
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) { die; }

use RicardoFiorani\Matcher\VideoServiceMatcher;
require __DIR__ . '/vendor/autoload.php';

/**
 * Add Shortcode
 * @param $atts
 *
 * @return string
 * @throws \RicardoFiorani\Exception\ServiceNotAvailableException
 */
function plyr_filter( $atts ) {

    // Attributes
    $atts = shortcode_atts(
        array(
            'url' => 'https://www.youtube.com/watch?v=gM0qOa_H-rs', // (c) Nikon Europe. C.C.
            'poster' => '/path/to/poster.jpg',
        ),
        $atts,
        'plyr'
    );

    $vsm = new VideoServiceMatcher();

    // Detects which service the url belongs to and returns the service's implementation of RicardoFiorani\Adapter\VideoAdapterInterface
    $video = $vsm->parse($atts['url']);

    // if video was from YouTube or Vimeo
    if ($video instanceof RicardoFiorani\Adapter\Youtube\YoutubeServiceAdapter ||
        $video instanceof RicardoFiorani\Adapter\Vimeo\VimeoServiceAdapter ) {
        $str = sprintf("<div class='plyr__video-embed plyr-embed' id='plyr-player' poster='%s'>
                                   <iframe src='%s' allowfullscreen allowtransparency allow=\"autoplay\"></iframe>
                               </div>", $atts['poster'], $atts['url']);
    }
    // use HTML5 video code
    else {
        $str = sprintf("<video id='plyr-player' poster='%s' controls><source src='%s' type='video/mp4'></video>",$atts['poster'], $atts['url']);
    }

    return $str;
}


function plyr_assets() {
    $plugin_url = plugin_dir_url( __FILE__ );

    wp_register_style('plyr-style', $plugin_url . 'assets/plyr.css');
    wp_enqueue_style('plyr-style');

    wp_register_script('plyr-script', $plugin_url . 'assets/plyr.min.js');
    wp_enqueue_script('plyr-script');

    wp_register_script('plyr-loader-script', $plugin_url . 'assets/loader.js');
    wp_enqueue_script('plyr-loader-script');
}

// Add Quicktags
function plyr_quicktags() {

    if ( wp_script_is( 'quicktags' ) ) {
    ?>
    <script type="text/javascript">
    QTags.addButton( 'plyr', 'Simple Plyr', '[plyr url="/path/to/video.mp4" poster="/path/to/poster.jpg"]', '', '', 'Plyr Video Player', 141 );
    </script>
    <?php
    }

}

add_shortcode( 'plyr', 'plyr_filter' );
add_action( 'wp_enqueue_scripts', 'plyr_assets' );
add_action( 'admin_print_footer_scripts', 'plyr_quicktags' );