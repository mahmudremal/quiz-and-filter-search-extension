<?php
/**
 * Blocks
 *
 * @package QuizAndFilterSearch
 */

namespace QUIZ_AND_FILTER_SEARCH\inc;

use QUIZ_AND_FILTER_SEARCH\inc\Traits\Singleton;

class Blocks {
	use Singleton;

	protected function __construct() {

		$this->setup_hooks();
	}

	protected function setup_hooks() {

		/**
		 * Actions.
		 */
		add_filter( 'block_categories_all', [ $this, 'add_block_categories' ] );
	}

	/**
	 * Add a block category
	 *
	 * @param array $categories Block categories.
	 *
	 * @return array
	 */
	public function add_block_categories( $categories ) {

		$category_slugs = wp_list_pluck( $categories, 'slug' );

		return in_array( 'aquila', $category_slugs, true ) ? $categories : array_merge(
			$categories,
			[
				[
					'slug'  => 'aquila',
					'title' => __( 'Aquila Blocks', 'quiz-and-filter-search-domain' ),
					'icon'  => 'table-row-after',
				],
			]
		);

	}

}
