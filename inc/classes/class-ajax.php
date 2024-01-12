<?php
/**
 * Block Patterns
 *
 * @package QuizAndFilterSearch
 */

namespace QUIZ_AND_FILTER_SEARCH\inc;

use QUIZ_AND_FILTER_SEARCH\inc\Traits\Singleton;

class Ajax {
	use Singleton;
	protected function __construct() {
		$this->setup_hooks();
	}
	protected function setup_hooks() {
		add_action('wp_ajax_futurewordpress/project/quizandfiltersearch/action/get_autocomplete', [$this, 'get_autocomplete'], 10, 0);
		add_action('wp_ajax_nopriv_futurewordpress/project/quizandfiltersearch/action/get_autocomplete', [$this, 'get_autocomplete'], 10, 0);

		add_action('wp_ajax_futurewordpress/project/ajax/search/category', [$this, 'search_category'], 10, 0);
		add_action('wp_ajax_nopriv_futurewordpress/project/ajax/search/category', [$this, 'search_category'], 10, 0);
		add_action('wp_ajax_futurewordpress/project/ajax/submit/popup', [$this, 'submit_popup'], 10, 0);
		add_action('wp_ajax_nopriv_futurewordpress/project/ajax/submit/popup', [$this, 'submit_popup'], 10, 0);

		add_action('wp_ajax_futurewordpress/project/ajax/edit/category', [$this, 'edit_category'], 10, 0);
		add_action('wp_ajax_futurewordpress/project/ajax/save/category', [$this, 'save_category'], 10, 0);

		add_action('wp_ajax_futurewordpress/project/ajax/search/popup', [$this, 'search_popup'], 10, 0);
		add_action('wp_ajax_nopriv_futurewordpress/project/ajax/search/popup', [$this, 'search_popup'], 10, 0);
	}
	public function get_autocomplete() {
		global $wpdb;
		switch ($_GET['term']) {
			case 'category':
				$_GET['query'] = '%'.$_GET['query'].'%';
				$res = $wpdb->get_results(
					"SELECT ttx.term_id, trm.name FROM {$wpdb->prefix}term_taxonomy ttx left join {$wpdb->prefix}terms trm on trm.term_id=ttx.term_id where ttx.taxonomy='listing_category' and trm.name like '%a%' order by ttx.term_id desc limit 0, 50;"
				);
				break;
			case 'location':
				$_GET['query'] = '%'.$_GET['query'].'%';
				$res = $wpdb->get_results(
					"SELECT post.post_title, pstm.meta_value as name FROM {$wpdb->prefix}posts post left join {$wpdb->prefix}postmeta pstm on pstm.post_id=post.ID WHERE post.post_type='listing' and pstm.meta_key='_friendly_address' and pstm.meta_value like '{$_GET['query']}' order by post.post_title desc limit 0, 50;"
				);
				break;
			default:
				$res = [];
				break;
		}
		
		// $res = [];for ($i=0; $i < 10; $i++) {$res[] = ['name'=>'Result '.$i,'value'=>'result_'.$i];}
		wp_send_json_success( $res, 200 );
	}
	public function search_category() {
		global $wpdb;
		// check_ajax_referer('futurewordpress/project/quizandfiltersearch/verify/nonce', '_nonce', true);
		$dataset = isset($_POST['dataset'])?$_POST['dataset']:'{}';
		$dataset = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', stripslashes(html_entity_decode($dataset))), true);

		$dataset['wpdb'] = $wpdb->get_results(
			$wpdb->prepare(
				// 'SELECT ttx.term_id, trm.name FROM '.$wpdb->prefix.'term_taxonomy ttx left join '.$wpdb->prefix.'terms trm on trm.term_id=ttx.term_id where ttx.taxonomy=%s and trm.name like "%s" order by ttx.term_id desc limit 0, 1;', 'listing_category', $dataset['keyword_search'],

				"SELECT ttx.term_id, trm.name
				FROM {$wpdb->prefix}term_taxonomy ttx
				LEFT JOIN {$wpdb->prefix}terms trm ON trm.term_id = ttx.term_id
				LEFT JOIN {$wpdb->prefix}term_relationships tr ON tr.term_taxonomy_id = ttx.term_taxonomy_id
				LEFT JOIN {$wpdb->prefix}posts post ON post.ID = tr.object_id
				LEFT JOIN {$wpdb->prefix}postmeta pstm ON pstm.post_id = post.ID
				WHERE ttx.taxonomy = 'listing_category'
				  AND trm.name LIKE '%s'
				  AND post.post_type = 'listing'
				  AND pstm.meta_key = '_friendly_address'
				  AND pstm.meta_value LIKE '%s'
				ORDER BY ttx.term_id DESC
				LIMIT 0, 1;",
				$dataset['keyword_search'],
				$dataset['location_search']
			)
		);
		$dataset['category'] = ($dataset['wpdb']&&isset($dataset['wpdb'][0]))?$dataset['wpdb'][0]->term_id:0;

		$result = [];
		$result['category'] = get_term_meta($dataset['category'],'_categorypopup',true);
		
		$requested = get_term_meta($dataset['category'],'_category_reqs',true);
		$requested = (!$requested||$requested=='')?[]:(array)$requested;
		foreach($requested as $ip => $time) {
			if($time < strtotime('-10 minutes', time())) {
				unset($requested[$ip]);
			}
		}
		$requested[$_SERVER['REMOTE_ADDR']] = time();
		update_term_meta($dataset['category'],'_category_reqs',$requested);

		$json = [
			'hooks' => [ 'gotcategorypopupresult' ],
			'header' => [
				'category_photo' => false // 'https://eu-bark-media.s3.eu-west-1.amazonaws.com/category_header_photos/74-1530804797752.jpg'
			],
			'user' => [
				'sellerLoggedIn' => is_user_logged_in(),
				'telephone' => null,
				'userLoggedIn' => is_user_logged_in(),
				'userName' => is_user_logged_in()?wp_get_current_user()->display_name:null
			],
			'country' => false,
			'category' => [
				'id' => $dataset['category'],
				'is_parent' => false,
				'name' => $dataset['keyword_search'],
				'toast' => false, // '<strong>' . count($requested) . '</strong> people requested this service in the last 10 minutes!',
				'thumbnail' => [
					'1x' => '', // 'https://d1vbfnp4jhzk1f.cloudfront.net/s/modal-thumbnail/74-15656973291985616552.png!d=wNNiR2',
					'2x' => '', // 'https://d1vbfnp4jhzk1f.cloudfront.net/s/modal-thumbnail/74-15656973291985616552.png!d=yNNhEJ',
				],
				'custom_fields' => [
				]
			],
		];


		$result['hooks'] = ['gotcategorypopupresult'];
		foreach($result['category'] as $i => $cat) {
			$result['category'][$i]['headerbgurl'] = ($cat['headerbg']=='')?false:wp_get_attachment_url($cat['headerbg']);
		}
		$json['category']['custom_fields'] = $this->merge_customfields($result['category']);
		
		$json['request'] = $_POST;
		$json['request']['dataset'] = $dataset;
		wp_send_json_success( $json, 200 );
	}
	public function submit_popup() {
		// check_ajax_referer('futurewordpress/project/quizandfiltersearch/verify/nonce', '_nonce', true);
		$request = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', stripslashes(html_entity_decode($_POST['dataset']))), true);
		$json = [
			'hooks' => ['popup_submitting_done'],
			'message' => __( 'Popup submitted successfully. Hold on unil you\'re redirecting to searh results.', 'domain' )
		];
		
		if(isset($request['category']) && !empty($request['category'])) {
			$request['category'] = (int) $request['category'];
			$term_link = get_term_link($request['category'], 'listing_category');
			if(!$term_link || is_wp_error($term_link)) {$term_link = false;}
			$json['redirectedTo'] = $term_link;
		}
		if(isset($request['field']["9002"]) && ! is_user_logged_in()) {
			$user_email = $request['field']["9002"];
			$user_name = $request['field']["9003"];
			$user_pass = $request['field']["9004"];
			$user = get_user_by_email($user_email);
			if($user) {
				$user_id = $user->ID;
				wp_set_current_user($user_id, $user->user_login);
				wp_set_auth_cookie($user_id);
				do_action('wp_login', $user->user_login, $user);
			} else {
				$user_id = username_exists($user_name);
				if(!$user_id && false == email_exists($user_email)) {
					$user_id = wp_create_user($user_name, $user_pass, $user_email );
					if(!is_wp_error($user_id)) {
						$user = get_user_by('id', $user_id);
						wp_set_current_user($user_id, $user->user_login);
						wp_set_auth_cookie($user_id);
						do_action('wp_login', $user->user_login, $user);
					}
					
				} else {
					$random_password = __( 'User already exists.  Password inherited.', 'textdomain' );
				}
				
			}
		}

		

		wp_send_json_success( $json, 200 );
	}
	public function search_popup() {
		global $wpdb;
		$json = ['hooks' => ['popup_searching_done']];
		// check_ajax_referer('futurewordpress/project/quizandfiltersearch/verify/nonce', '_nonce', true);
		$request = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', stripslashes(html_entity_decode($_POST['formdata']))), true);
		// $fields = $this->merge_customfields([[]]);
		// unset($fields[0]);
		// foreach($fields as $i => $field) {
		// 	if($field['fieldID'] == 9000) {
		// 		// IS for loaction. But no need.
		// 	}
		// }
		$category_string = $request['field']['category'];
		$location_string = $request['field'][9000];
		$result = (array) $wpdb->get_row(
			$wpdb->prepare(
				"SELECT ttx.term_id, trm.name
				FROM {$wpdb->prefix}term_taxonomy ttx
				LEFT JOIN {$wpdb->prefix}terms trm ON trm.term_id = ttx.term_id
				LEFT JOIN {$wpdb->prefix}term_relationships tr ON tr.term_taxonomy_id = ttx.term_taxonomy_id
				LEFT JOIN {$wpdb->prefix}posts post ON post.ID = tr.object_id
				LEFT JOIN {$wpdb->prefix}postmeta pstm ON pstm.post_id = post.ID
				WHERE ttx.taxonomy = 'listing_category'
				  AND trm.name LIKE '%s'
				  AND post.post_type = 'listing'
				  AND pstm.meta_key = '_friendly_address'
				  AND pstm.meta_value LIKE '%s'
				ORDER BY ttx.term_id DESC
				LIMIT 0, 1;",
				$category_string,
				$location_string
			)
		);
		if(!$result || is_wp_error($result) || !isset($result['term_id'])) {
			// Nothing found!
			$args = [
				'post_title'   => $category_string.' in '.$location_string,
				'post_content' => '',
				'post_status'  => 'publish',
				'post_type'	   => 'logs',
				'post_author'  => is_user_logged_in()?get_current_user_id():1,
				// 'tax_input'    => [
				// 	'hierarchical_tax'     => $hierarchical_tax,
				// 	'non_hierarchical_tax' => $non_hierarchical_terms
				// ],
				'meta_input'   => [
					'_popup_steps' => ['category' => $category_string, 'location' => $location_string]
				]
			];
			$post_id = wp_insert_post($args);
			if($post_id && !is_wp_error($post_id)) {
				$json['message'] = sprintf(
					__('We can\'t find "%s" service around %s.', 'domain'),
					$category_string, $location_string
				);
				wp_send_json_success($json, 200);
			}
		}
		
		// $json['request'] = $request;
		// $json['fields'] = $fields;
		// $json['fields'] = $fields;
		$json['message'] = sprintf(
			__('Congrats! We just find "%s" around %s.', 'domain'),
			$category_string, $location_string
		);
		wp_send_json_error($json);
	}
	public function save_category() {
		$result = [];
		$result['hooks'] = ['category_updated'];$result['message'] = __( 'Popup updated Successfully', 'domain' );
		$request = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', stripslashes(html_entity_decode($_POST['dataset']))), true);
		$result['json'] = $request;
		$term_id = $_POST['category_id'];
		update_term_meta( $term_id, '_categorypopup', $request );
		wp_send_json_success($result, 200);
	}
	public function edit_category() {
		$result = [];
		$result['category'] = get_term_meta($_POST['category_id'],'_categorypopup',true);
		$result['hooks'] = ['gotcategorypopupresult'];
		foreach($result['category'] as $i => $cat) {
			$result['category'][$i]['headerbgurl'] = wp_get_attachment_url($cat['headerbg']);
		}

		wp_send_json_success($result, 200);
	}
	public function merge_customfields($fields) {
		$fieldID = 9000;
		if(!$fields || $fields == "") {return $fields;}
		$fields = (array) $fields;
		$fields[] = [
			'fieldID' => $fieldID,
			'type' => 'text',
			'headerbg' => '',
			'heading' => 'Where do you need the {{category.name}}?',
			'subtitle' => 'The postcode or town for the address where you want the {{category.name}}.',
			'placeholder' => 'Enter your postcode or town',
			'headerbgurl' => false,
		];
		$fieldID++;
		$fields[] = [
			'fieldID' => $fieldID,
			'type' => 'confirm',
			'headerbg' => '',
			'heading' => 'Great - we\'ve got pros ready and available.',
			'icon'	=> '<span class="fa fa-check"></span>',
			'headerbgurl' => false,
		];
		if(!is_user_logged_in()) {
			$fieldID++;
			$fields[] = [
				'fieldID' => $fieldID,
				'type' => 'text',
				'headerbg' => '',
				'heading' => 'Which email address would you like quotes sent to?',
				'subtitle' => 'Give here your email address that will help us to create an account for you or auto login for an existing account.',
				'placeholder' => 'Email-address',
				'headerbgurl' => false,
			];
			$fieldID++;
			$fields[] = [
				'fieldID' => $fieldID,
				'type' => 'text',
				'headerbg' => '',
				'heading' => 'Your full name?',
				'subtitle' => '',
				'label' => 'Please tell us your name:',
				'placeholder' => 'Full name',
				'footer'	=> sprintf(__('By continuing, you confirm your agreement to our %sTerms & Conditions%s', 'domain'), '<a href="'.esc_url(get_privacy_policy_url()).'" target="_blank">', '</a>'),
				'headerbgurl' => false,
				'extra_fields' => [
					[
						'fieldID' => ($fieldID+1),
						'type' => 'checkbox',
						'subtitle' => '',
						'headerbgurl' => false,
						'options'	=> [
							['label' => 'I am happy to receive occasional marketing emails.', 'input' => false, 'next' => false]
						]
					]
				]
			];
			$fieldID++;$fieldID++;
			$fields[] = [
				'fieldID' => $fieldID,
				'type' => 'password',
				'headerbg' => '',
				'heading' => 'Give here the password',
				'subtitle' => 'Password help to keep your account secure for anonymouse attack and third party tracking.',
				'placeholder' => '%^8;fd&!87"af$',
				'headerbgurl' => false,
			];
			$fieldID++;
			$fields[] = [
				'fieldID' => $fieldID,
				'type' => 'text',
				'headerbg' => '',
				'heading' => 'What is your phone number?',
				'subtitle' => 'Professionals will be able to contact you directly to find out more about your request.',
				'placeholder' => 'Phone number. Eg. +880 1814-118 328',
				'headerbgurl' => false,
			];
		} else {
			$fieldID = ($fieldID + 4);
		}
		return $fields;
	}
}
