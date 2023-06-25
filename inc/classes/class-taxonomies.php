<?php
/**
 * Register Custom Taxonomies
 *
 * @package QuizAndFilterSearch
 */
namespace QUIZ_AND_FILTER_SEARCH\inc;
use QUIZ_AND_FILTER_SEARCH\inc\Traits\Singleton;
class Taxonomies {
	use Singleton;
	protected function __construct() {
		// load class.
		$this->setup_hooks();
	}
	protected function setup_hooks() {
		/**
		 * Actions.
		 */
		// add_action( 'init', [ $this, 'create_genre_taxonomy' ] );
		$this->mediaConfig = [
			'title'						=> __( 'Select Icon. Recomment to keep image around of 40px/40px.', 'domain' ),
			'library'					=> [
				'type'					=> 'image'
			],
			'button'					=> [
				'text'					=> __( 'Use this Icon', 'domain' ),
			],
			'multiple'				=> false
		];
		$taxonomy = 'listing_category';
		add_action( "{$taxonomy}_add_form_fields", [ $this, "{$taxonomy}_add_form_fields" ], 10, 1 );
		add_action( "{$taxonomy}_edit_form_fields", [ $this, "{$taxonomy}_edit_form_fields" ], 10, 2 );
		// add_action( "created_{$taxonomy}", [ $this, "save_{$taxonomy}" ], 10, 1 );
		// add_action( "edited_{$taxonomy}", [ $this, "save_{$taxonomy}" ], 10, 1 );
	}
	// Register Taxonomy Genre
	public function create_genre_taxonomy() {
		$labels = [
			'name'              => _x( 'Genres', 'taxonomy general name', 'quiz-and-filter-search-domain' ),
			'singular_name'     => _x( 'Genre', 'taxonomy singular name', 'quiz-and-filter-search-domain' ),
			'search_items'      => __( 'Search Genres', 'quiz-and-filter-search-domain' ),
			'all_items'         => __( 'All Genres', 'quiz-and-filter-search-domain' ),
			'parent_item'       => __( 'Parent Genre', 'quiz-and-filter-search-domain' ),
			'parent_item_colon' => __( 'Parent Genre:', 'quiz-and-filter-search-domain' ),
			'edit_item'         => __( 'Edit Genre', 'quiz-and-filter-search-domain' ),
			'update_item'       => __( 'Update Genre', 'quiz-and-filter-search-domain' ),
			'add_new_item'      => __( 'Add New Genre', 'quiz-and-filter-search-domain' ),
			'new_item_name'     => __( 'New Genre Name', 'quiz-and-filter-search-domain' ),
			'menu_name'         => __( 'Genre', 'quiz-and-filter-search-domain' ),
		];
		$args   = [
			'labels'             => $labels,
			'description'        => __( 'Movie Genre', 'quiz-and-filter-search-domain' ),
			'hierarchical'       => true,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_nav_menus'  => true,
			'show_tagcloud'      => true,
			'show_in_quick_edit' => true,
			'show_admin_column'  => true,
			'show_in_rest'       => true,
		];
		register_taxonomy( 'genre', [ 'movies' ], $args );
	}

	public function listing_category_add_form_fields( $taxonomy ) {
		$this->listing_category_form_fields__popupspopup( false, $taxonomy );
	}
	public function listing_category_edit_form_fields( $term, $taxonomy ) {
		// 
		?>
		<tr class="form-field form-requi-red term-name-wrap">
			<th scope="row"><label><?php esc_html_e( 'Setup Popups', 'domain' ); ?></label></th>
			<td>
				<?php $this->listing_category_form_fields__popupspopup( $term, $taxonomy ); ?>
				<p class="description"><?php esc_html_e( 'Setup your popup and it\'s related data by clicking this button.', 'domain' ); ?></p>
			</td>
		</tr>
		<?php
	}
	private function listing_category_form_fields__popupspopup( $term, $taxonomy ) {
		?>
			<div class="fwppopspopup-button-wrap">
				<button type="button" class="button btn fwppopspopup-open" title="<?php esc_attr_e( 'Customize Popup', 'domain' ); ?>"><?php esc_html_e( 'Customize Popup', 'domain' ); ?></button>
			</div>
		<?php
	}
}
