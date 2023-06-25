<?php
/**
 * This plugin ordered by a client and done by Remal Mahmud (fiverr.com/mahmud_remal). Authority dedicated to that cient.
 *
 * @wordpress-plugin
 * Plugin Name:       Quiz and filter Search
 * Plugin URI:        https://github.com/mahmudremal/quiz-and-filter-search-extension/
 * Description:       Quiz and lead registeration management with advanced filter search.
 * Version:           1.0.2
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Remal Mahmud
 * Author URI:        https://github.com/mahmudremal/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       quiz-and-filter-search-domain
 * Domain Path:       /languages
 * 
 * @package QuizAndFilterSearch
 * @author  Remal Mahmud (https://github.com/mahmudremal)
 * @version 1.0.2
 * @link https://github.com/mahmudremalquiz-and-filter-search-extension
 * @category	WooComerce Plugin
 * @copyright	Copyright (c) 2023-25
 * 
 */

/**
 * Bootstrap the plugin.
 */



defined( 'QUIZ_AND_FILTER_SEARCH__FILE__' ) || define( 'QUIZ_AND_FILTER_SEARCH__FILE__', untrailingslashit( __FILE__ ) );
defined( 'QUIZ_AND_FILTER_SEARCH_DIR_PATH' ) || define( 'QUIZ_AND_FILTER_SEARCH_DIR_PATH', untrailingslashit( plugin_dir_path( QUIZ_AND_FILTER_SEARCH__FILE__ ) ) );
defined( 'QUIZ_AND_FILTER_SEARCH_DIR_URI' ) || define( 'QUIZ_AND_FILTER_SEARCH_DIR_URI', untrailingslashit( plugin_dir_url( QUIZ_AND_FILTER_SEARCH__FILE__ ) ) );
defined( 'QUIZ_AND_FILTER_SEARCH_BUILD_URI' ) || define( 'QUIZ_AND_FILTER_SEARCH_BUILD_URI', untrailingslashit( QUIZ_AND_FILTER_SEARCH_DIR_URI ) . '/assets/build' );
defined( 'QUIZ_AND_FILTER_SEARCH_BUILD_PATH' ) || define( 'QUIZ_AND_FILTER_SEARCH_BUILD_PATH', untrailingslashit( QUIZ_AND_FILTER_SEARCH_DIR_PATH ) . '/assets/build' );
defined( 'QUIZ_AND_FILTER_SEARCH_BUILD_JS_URI' ) || define( 'QUIZ_AND_FILTER_SEARCH_BUILD_JS_URI', untrailingslashit( QUIZ_AND_FILTER_SEARCH_DIR_URI ) . '/assets/build/js' );
defined( 'QUIZ_AND_FILTER_SEARCH_BUILD_JS_DIR_PATH' ) || define( 'QUIZ_AND_FILTER_SEARCH_BUILD_JS_DIR_PATH', untrailingslashit( QUIZ_AND_FILTER_SEARCH_DIR_PATH ) . '/assets/build/js' );
defined( 'QUIZ_AND_FILTER_SEARCH_BUILD_IMG_URI' ) || define( 'QUIZ_AND_FILTER_SEARCH_BUILD_IMG_URI', untrailingslashit( QUIZ_AND_FILTER_SEARCH_DIR_URI ) . '/assets/build/src/img' );
defined( 'QUIZ_AND_FILTER_SEARCH_BUILD_CSS_URI' ) || define( 'QUIZ_AND_FILTER_SEARCH_BUILD_CSS_URI', untrailingslashit( QUIZ_AND_FILTER_SEARCH_DIR_URI ) . '/assets/build/css' );
defined( 'QUIZ_AND_FILTER_SEARCH_BUILD_CSS_DIR_PATH' ) || define( 'QUIZ_AND_FILTER_SEARCH_BUILD_CSS_DIR_PATH', untrailingslashit( QUIZ_AND_FILTER_SEARCH_DIR_PATH ) . '/assets/build/css' );
defined( 'QUIZ_AND_FILTER_SEARCH_BUILD_LIB_URI' ) || define( 'QUIZ_AND_FILTER_SEARCH_BUILD_LIB_URI', untrailingslashit( QUIZ_AND_FILTER_SEARCH_DIR_URI ) . '/assets/build/library' );
defined( 'QUIZ_AND_FILTER_SEARCH_ARCHIVE_POST_PER_PAGE' ) || define( 'QUIZ_AND_FILTER_SEARCH_ARCHIVE_POST_PER_PAGE', 9 );
defined( 'QUIZ_AND_FILTER_SEARCH_SEARCH_RESULTS_POST_PER_PAGE' ) || define( 'QUIZ_AND_FILTER_SEARCH_SEARCH_RESULTS_POST_PER_PAGE', 9 );
defined( 'QUIZ_AND_FILTER_SEARCH_OPTIONS' ) || define( 'QUIZ_AND_FILTER_SEARCH_OPTIONS', get_option( 'quiz-and-filter-search-domain' ) );

require_once QUIZ_AND_FILTER_SEARCH_DIR_PATH . '/inc/helpers/autoloader.php';
// require_once QUIZ_AND_FILTER_SEARCH_DIR_PATH . '/inc/helpers/template-tags.php';

if( ! function_exists( 'futurewordpressprojectscratch_get_theme_instance' ) ) {
	function futurewordpressprojectscratch_get_theme_instance() {\QUIZ_AND_FILTER_SEARCH\inc\Project::get_instance();}
}
futurewordpressprojectscratch_get_theme_instance();



