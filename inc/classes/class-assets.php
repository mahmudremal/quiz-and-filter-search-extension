<?php
/**
 * Enqueue theme assets
 *
 * @package QuizAndFilterSearch
 */


namespace QUIZ_AND_FILTER_SEARCH\inc;

use QUIZ_AND_FILTER_SEARCH\inc\Traits\Singleton;

class Assets {
	use Singleton;

	protected function __construct() {
		// load class.
		$this->setup_hooks();
	}

	protected function setup_hooks() {
		add_action( 'wp_enqueue_scripts', [ $this, 'register_styles' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'register_scripts' ] );
		
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ], 10, 1 );

		add_filter( 'futurewordpress/project/quizandfiltersearch/javascript/siteconfig', [ $this, 'siteConfig' ], 1, 1 );
		// add_filter( 'style_loader_src', [$this, 'style_loader_src'], 10, 2 );
		// add_filter( 'script_loader_src', [$this, 'style_loader_src'], 10, 2 );
	}

	public function register_styles() {
		// Register styles.
		$version = $this->filemtime( QUIZ_AND_FILTER_SEARCH_BUILD_CSS_DIR_PATH . '/public.css' );
		wp_register_style( 'quizandfiler-public', QUIZ_AND_FILTER_SEARCH_BUILD_CSS_URI . '/public.css', [], $version, 'all' );
		// Enqueue Styles.
		wp_enqueue_style( 'quizandfiler-public' );
		// if( $this->allow_enqueue() ) {}
	}

	public function register_scripts() {
		// Register scripts.
		$version = $this->filemtime(QUIZ_AND_FILTER_SEARCH_BUILD_JS_DIR_PATH.'/public.js');
		wp_register_script( 'quizandfiler-public', QUIZ_AND_FILTER_SEARCH_BUILD_JS_URI . '/public.js', ['jquery'], $version, true );

		wp_enqueue_script( 'quizandfiler-public' );
		wp_localize_script( 'quizandfiler-public', 'fwpSiteConfig', apply_filters( 'futurewordpress/project/quizandfiltersearch/javascript/siteconfig', [] ) );
	}
	private function allow_enqueue() {
		return ( function_exists( 'is_checkout' ) && ( is_checkout() || is_order_received_page() || is_wc_endpoint_url( 'order-received' ) ) );
	}

	/**
	 * Enqueue editor scripts and styles.
	 */
	public function enqueue_editor_assets() {

		$asset_config_file = sprintf( '%s/assets.php', QUIZ_AND_FILTER_SEARCH_BUILD_PATH );

		if ( ! file_exists( $asset_config_file ) ) {
			return;
		}

		$asset_config = require_once $asset_config_file;

		if ( empty( $asset_config['js/editor.js'] ) ) {
			return;
		}

		$editor_asset    = $asset_config['js/editor.js'];
		$js_dependencies = ( ! empty( $editor_asset['dependencies'] ) ) ? $editor_asset['dependencies'] : [];
		$version         = ( ! empty( $editor_asset['version'] ) ) ? $editor_asset['version'] : $this->filemtime( $asset_config_file );

		// Theme Gutenberg blocks JS.
		if ( is_admin() ) {
			wp_enqueue_script(
				'aquila-blocks-js',
				QUIZ_AND_FILTER_SEARCH_BUILD_JS_URI . '/blocks.js',
				$js_dependencies,
				$version,
				true
			);
		}

		// Theme Gutenberg blocks CSS.
		$css_dependencies = [
			'wp-block-library-theme',
			'wp-block-library',
		];

		wp_enqueue_style(
			'aquila-blocks-css',
			QUIZ_AND_FILTER_SEARCH_BUILD_CSS_URI . '/blocks.css',
			$css_dependencies,
			$this->filemtime( QUIZ_AND_FILTER_SEARCH_BUILD_CSS_DIR_PATH . '/blocks.css' ),
			'all'
		);

	}
	public function admin_enqueue_scripts( $curr_page ) {
		global $post;
		if( ! in_array( $curr_page, [ 'term.php' ] ) ) {return;}
		wp_register_style( 'quizandfiler-admin', QUIZ_AND_FILTER_SEARCH_BUILD_CSS_URI . '/admin.css', [], $this->filemtime( QUIZ_AND_FILTER_SEARCH_BUILD_CSS_DIR_PATH . '/admin.css' ), 'all' );
		wp_register_script( 'quizandfiler-admin', QUIZ_AND_FILTER_SEARCH_BUILD_JS_URI . '/admin.js', [ 'jquery' ], $this->filemtime( QUIZ_AND_FILTER_SEARCH_BUILD_JS_DIR_PATH . '/admin.js' ), true );
		
		wp_enqueue_style('quizandfiler-admin');
		wp_enqueue_script('quizandfiler-admin');

		wp_enqueue_style('quizandfiler-public');wp_enqueue_script('quizandfiler-admin');

		wp_localize_script('quizandfiler-admin','fwpSiteConfig',apply_filters('futurewordpress/project/quizandfiltersearch/javascript/siteconfig',[
			'config' => [
				'category_id' => isset($_GET['tag_ID'])?(int) $_GET['tag_ID']:get_query_var('tag_ID',false)
			]
		]));
	}
	private function filemtime($path) {
		return (file_exists($path)&&!is_dir($path))?filemtime($path):false;
	}
	public function siteConfig( $args ) {
		return wp_parse_args( [
			'ajaxUrl'    		=> admin_url( 'admin-ajax.php' ),
			'ajax_nonce' 		=> wp_create_nonce( 'futurewordpress/project/quizandfiltersearch/verify/nonce' ),
			'is_admin' 			=> is_admin(),
			'buildPath'  		=> QUIZ_AND_FILTER_SEARCH_BUILD_URI,
			'siteUrl'  			=> site_url('/'),
			'videoClips'  		=> ( function_exists( 'WC' ) && WC()->session !== null ) ? (array) WC()->session->get( 'uploaded_files_to_archive' ) : [],
			'i18n'					=> [
				'pls_wait'			=> __( 'Please wait...', 'quiz-and-filter-search-domain' ),
			],
			'leadStatus'		=> apply_filters( 'futurewordpress/project/quizandfiltersearch/action/statuses', ['no-action' => __( 'No action fetched', 'quiz-and-filter-search-domain' )], false ),
			
		], (array) $args );
	}
	public function wp_denqueue_scripts() {}
	public function admin_denqueue_scripts() {
		if( ! isset( $_GET[ 'page' ] ) ||  $_GET[ 'page' ] !='crm_dashboard' ) {return;}
		wp_dequeue_script( 'qode-tax-js' );
	}
	public function style_loader_src($src, $handle) {
		if ($handle === 'quizandfiler-public') {
			$version = $this->filemtime(str_replace(site_url('/'),ABSPATH,$src));
			// $src = add_query_arg('ver', $version, $src);
			$src = $src.'v'.$version;
		}
		return $src;
	}

}
