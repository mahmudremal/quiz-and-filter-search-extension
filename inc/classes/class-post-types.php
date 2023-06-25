<?php
/**
 * Register Post Types
 *
 * @package QuizAndFilterSearch
 */

namespace QUIZ_AND_FILTER_SEARCH\inc;

use QUIZ_AND_FILTER_SEARCH\inc\Traits\Singleton;
 
class PostTypes {
	use Singleton;

	protected function __construct() {

		// load class.
		$this->setup_hooks();
	}

	protected function setup_hooks() {

		/**
		 * Actions.
		 */
		add_action( 'init', [ $this, 'create_movie_cpt' ], 0 );

	}

	// Register Custom Post Type Movie
	public function create_movie_cpt() {

		$labels = [
			'name'                  => _x( 'Movies', 'Post Type General Name', 'quiz-and-filter-search-domain' ),
			'singular_name'         => _x( 'Movie', 'Post Type Singular Name', 'quiz-and-filter-search-domain' ),
			'menu_name'             => _x( 'Movies', 'Admin Menu text', 'quiz-and-filter-search-domain' ),
			'name_admin_bar'        => _x( 'Movie', 'Add New on Toolbar', 'quiz-and-filter-search-domain' ),
			'archives'              => __( 'Movie Archives', 'quiz-and-filter-search-domain' ),
			'attributes'            => __( 'Movie Attributes', 'quiz-and-filter-search-domain' ),
			'parent_item_colon'     => __( 'Parent Movie:', 'quiz-and-filter-search-domain' ),
			'all_items'             => __( 'All Movies', 'quiz-and-filter-search-domain' ),
			'add_new_item'          => __( 'Add New Movie', 'quiz-and-filter-search-domain' ),
			'add_new'               => __( 'Add New', 'quiz-and-filter-search-domain' ),
			'new_item'              => __( 'New Movie', 'quiz-and-filter-search-domain' ),
			'edit_item'             => __( 'Edit Movie', 'quiz-and-filter-search-domain' ),
			'update_item'           => __( 'Update Movie', 'quiz-and-filter-search-domain' ),
			'view_item'             => __( 'View Movie', 'quiz-and-filter-search-domain' ),
			'view_items'            => __( 'View Movies', 'quiz-and-filter-search-domain' ),
			'search_items'          => __( 'Search Movie', 'quiz-and-filter-search-domain' ),
			'not_found'             => __( 'Not found', 'quiz-and-filter-search-domain' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'quiz-and-filter-search-domain' ),
			'featured_image'        => __( 'Featured Image', 'quiz-and-filter-search-domain' ),
			'set_featured_image'    => __( 'Set featured image', 'quiz-and-filter-search-domain' ),
			'remove_featured_image' => __( 'Remove featured image', 'quiz-and-filter-search-domain' ),
			'use_featured_image'    => __( 'Use as featured image', 'quiz-and-filter-search-domain' ),
			'insert_into_item'      => __( 'Insert into Movie', 'quiz-and-filter-search-domain' ),
			'uploaded_to_this_item' => __( 'Uploaded to this Movie', 'quiz-and-filter-search-domain' ),
			'items_list'            => __( 'Movies list', 'quiz-and-filter-search-domain' ),
			'items_list_navigation' => __( 'Movies list navigation', 'quiz-and-filter-search-domain' ),
			'filter_items_list'     => __( 'Filter Movies list', 'quiz-and-filter-search-domain' ),
		];
		$args   = [
			'label'               => __( 'Movie', 'quiz-and-filter-search-domain' ),
			'description'         => __( 'The movies', 'quiz-and-filter-search-domain' ),
			'labels'              => $labels,
			'menu_icon'           => 'dashicons-video-alt',
			'supports'            => [
				'title',
				'editor',
				'excerpt',
				'thumbnail',
				'revisions',
				'author',
				'comments',
				'trackbacks',
				'page-attributes',
				'custom-fields',
			],
			'taxonomies'          => [],
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 5,
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => true,
			'hierarchical'        => false,
			'exclude_from_search' => false,
			'show_in_rest'        => true,
			'publicly_queryable'  => true,
			'capability_type'     => 'post',
		];

		register_post_type( 'movies', $args );

	}


}
