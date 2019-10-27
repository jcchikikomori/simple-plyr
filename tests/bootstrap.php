<?php
/**
 * PHPUnit bootstrap file
 *
 * @package Simple_Plyr
 */

$simple_plyrsimple_plyr_tests_dir = getenv( 'WPsimple_plyr_tests_dir' );

if ( ! $simple_plyr_tests_dir ) {
	$simple_plyr_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

if ( ! file_exists( $simple_plyr_tests_dir . '/includes/functions.php' ) ) {
	echo "Could not find $simple_plyr_tests_dir/includes/functions.php, have you run bin/install-wp-tests.sh ?" . PHP_EOL; // WPCS: XSS ok.
	exit( 1 );
}

// Give access to tests_add_filter() function.
require_once $simple_plyr_tests_dir . '/includes/functions.php';

/**
 * Manually load the plugin being tested.
 */
function simple_plyr_manually_load_plugin() {
	include dirname( dirname( __FILE__ ) ) . '/simple-plyr.php';
}
tests_add_filter( 'muplugins_loaded', 'simple_plyr_manually_load_plugin' );

// Start up the WP testing environment.
require $simple_plyr_tests_dir . '/includes/bootstrap.php';
