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
		add_action( 'wp_ajax_futurewordpress/project/quizandfiltersearch/action/get_autocomplete', [ $this, 'get_autocomplete' ], 10, 0 );
		add_action( 'wp_ajax_nopriv_futurewordpress/project/quizandfiltersearch/action/get_autocomplete', [ $this, 'get_autocomplete' ], 10, 0 );

		add_action( 'wp_ajax_futurewordpress/project/ajax/search/category', [ $this, 'search_category' ], 10, 0 );
		add_action( 'wp_ajax_nopriv_futurewordpress/project/ajax/search/category', [ $this, 'search_category' ], 10, 0 );
		add_action( 'wp_ajax_futurewordpress/project/ajax/submit/popup', [ $this, 'submit_popup' ], 10, 0 );
		add_action( 'wp_ajax_nopriv_futurewordpress/project/ajax/submit/popup', [ $this, 'submit_popup' ], 10, 0 );

		add_action( 'wp_ajax_futurewordpress/project/ajax/edit/category', [ $this, 'edit_category' ], 10, 0 );
		add_action( 'wp_ajax_futurewordpress/project/ajax/save/category', [ $this, 'save_category' ], 10, 0 );
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
		$dataset = isset($_POST['dataset'])?$_POST['dataset']:'{}';
		// echo $dataset;
		$dataset = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', stripslashes(html_entity_decode($dataset))), true);
		// print_r($dataset);

		$dataset['wpdb'] = $wpdb->get_results(
			$wpdb->prepare(
				'SELECT ttx.term_id, trm.name FROM '.$wpdb->prefix.'term_taxonomy ttx left join '.$wpdb->prefix.'terms trm on trm.term_id=ttx.term_id where ttx.taxonomy=%s and trm.name like "%s" order by ttx.term_id desc limit 0, 1;', 'listing_category', $dataset['keyword_search']
			)
		);
		$dataset['category'] = ($dataset['wpdb']&&isset($dataset['wpdb'][0]))?$dataset['wpdb'][0]->term_id:0;
		$json = [
			'hooks' => [ 'gotcategorypopupresult' ],
			'header' => [
				'category_photo' => 'https://eu-bark-media.s3.eu-west-1.amazonaws.com/category_header_photos/74-1530804797752.jpg'
			],
			'user' => [
				'sellerLoggedIn' => is_user_logged_in(),
				'telephone' => null,
				'userLoggedIn' => is_user_logged_in(),
				'userName' => is_user_logged_in()?wp_get_current_user()->display_name:null
			],
			'country' => false,
			'category' => [
				'id' => 74,
				'is_parent' => false,
				'name' => 'Personal Trainers',
				'toast' => '<strong>52</strong> people requested this service in the last 10 minutes!',
				'thumbnail' => [
					'1x' => 'https://d1vbfnp4jhzk1f.cloudfront.net/s/modal-thumbnail/74-15656973291985616552.png!d=wNNiR2',
					'2x' => 'https://d1vbfnp4jhzk1f.cloudfront.net/s/modal-thumbnail/74-15656973291985616552.png!d=yNNhEJ',
				],
				'custom_fields' => [
					[
						'required' => true,
						'type' => 'radio',
						'slug' => 'what-is-your-gender',
						'label' => 'What is your gender?',
						'label_extra' => 'Achieve your 2023 fitness goals with an Expert Personal Trainer! ',
						'info' => '',
						'test_display' => true,
						'is_conditional' => false,
						'rules' => [],
						'options' => [
							['label' => 'Male','name' => 'male','forward' => 'what-is-your-hobby'],
							['label' => 'Female','name' => 'female','forward' => false],
							['label' => 'Other (e.g. couple, group)','name' => 'other-eg-couple-group','input' => 'others','forward' => false],
						]
					],
					[
						'required' => true,
						'type' => 'text',
						'slug' => 'what-is-your-mothername',
						'label' => 'What is your Mother name?',
						'label_extra' => 'Give here your mother name so that we could verify your identity.',
						'info' => '',
						'test_display' => true,
						'is_conditional' => false,
						'rules' => []
					],
					[
						'required' => true,
						'type' => 'checkbox',
						'slug' => 'what-is-your-hobby',
						'label' => 'What is your hobby?',
						'label_extra' => 'Choose your hobby so that we could suggest you favourite video.',
						'info' => '',
						'test_display' => true,
						'is_conditional' => false,
						'rules' => [],
						'options' => [
							['label' => 'Cricket','name' => 'cricket','forward' => false],
							['label' => 'Soccar','name' => 'soccar','forward' => false],
							['label' => 'Tennis ball','name' => 'tennis','forward' => false],
							['label' => 'Others','name' => 'others','forward' => false],
						]
					],
				]
			]
		];

		$result = [];
		$result['category'] = get_term_meta($dataset['category'],'_categorypopup',true);
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
		$json = [
			'hooks' => ['popup_submitting_done'],
			'message' => __( 'Popup submitted successfully. Hold on unil you\'re redirecting to searh results.', 'domain' )
		];
		wp_send_json_success( $json, 200 );
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
		$fieldID = 8888;
		if(!$fields||$fields=='') {return $fields;}
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
			'header' => 'Great - we\'ve got pros ready and available.',
			'icon'	=> '<span class="fa fa-check"></span>',
			'headerbgurl' => false,
		];
		$fieldID++;
		$fields[] = [
			'fieldID' => $fieldID,
			'type' => 'text',
			'headerbg' => '',
			'heading' => 'What email address would you like quotes sent to?',
			'subtitle' => 'Give here your email address that will help us to create an account for you',
			'placeholder' => 'Email-address',
			'headerbgurl' => false,
		];
		$fieldID++;
		$fields[] = [
			'fieldID' => $fieldID,
			'type' => 'text',
			'headerbg' => '',
			'heading' => 'Your full name?',
			'subtitle' => 'Your full name that will be public and according to your name, this account will be created.',
			'placeholder' => 'Eg. Jhon Due',
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
		return $fields;
	}
}
