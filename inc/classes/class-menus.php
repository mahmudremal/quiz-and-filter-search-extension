<?php
/**
 * Register Menus
 *
 * @package QuizAndFilterSearch
 */
namespace QUIZ_AND_FILTER_SEARCH\inc;
use QUIZ_AND_FILTER_SEARCH\inc\Traits\Singleton;
class Menus {
	use Singleton;
	protected function __construct() {
		// load class.
		$this->setup_hooks();
	}
	protected function setup_hooks() {
		/**
		 * Actions.
		 */
		// add_action( 'init', [ $this, 'register_menus' ] );
		
    add_filter( 'futurewordpress/project/quizandfiltersearch/settings/general', [ $this, 'general' ], 10, 1 );
    add_filter( 'futurewordpress/project/quizandfiltersearch/settings/fields', [ $this, 'menus' ], 10, 1 );
		add_action( 'in_admin_header', [ $this, 'in_admin_header' ], 100, 0 );
	}
	public function register_menus() {
		register_nav_menus([
			'aquila-header-menu' => esc_html__( 'Header Menu', 'quiz-and-filter-search-domain' ),
			'aquila-footer-menu' => esc_html__( 'Footer Menu', 'quiz-and-filter-search-domain' ),
		]);
	}
	/**
	 * Get the menu id by menu location.
	 *
	 * @param string $location
	 *
	 * @return integer
	 */
	public function get_menu_id( $location ) {
		// Get all locations
		$locations = get_nav_menu_locations();
		// Get object id by location.
		$menu_id = ! empty($locations[$location]) ? $locations[$location] : '';
		return ! empty( $menu_id ) ? $menu_id : '';
	}
	/**
	 * Get all child menus that has given parent menu id.
	 *
	 * @param array   $menu_array Menu array.
	 * @param integer $parent_id Parent menu id.
	 *
	 * @return array Child menu array.
	 */
	public function get_child_menu_items( $menu_array, $parent_id ) {
		$child_menus = [];
		if ( ! empty( $menu_array ) && is_array( $menu_array ) ) {
			foreach ( $menu_array as $menu ) {
				if ( intval( $menu->menu_item_parent ) === $parent_id ) {
					array_push( $child_menus, $menu );
				}
			}
		}
		return $child_menus;
	}
	public function in_admin_header() {
		if( ! isset( $_GET[ 'page' ] ) || $_GET[ 'page' ] != 'crm_dashboard' ) {return;}
		
		remove_all_actions('admin_notices');
		remove_all_actions('all_admin_notices');
		// add_action('admin_notices', function () {echo 'My notice';});
	}
	/**
	 * Supply necessry tags that could be replace on frontend.
	 * 
	 * @return string
	 * @return array
	 */
	public function commontags( $html = false ) {
		$arg = [];$tags = [
			'username', 'sitename', 
		];
		if( $html === false ) {return $tags;}
		foreach( $tags as $tag ) {
			$arg[] = sprintf( "%s{$tag}%s", '<code>{', '}</code>' );
		}
		return implode( ', ', $arg );
	}
	public function contractTags( $tags ) {
		$arg = [];
		foreach( $tags as $tag ) {
			$arg[] = sprintf( "%s{$tag}%s", '<code>{', '}</code>' );
		}
		return implode( ', ', $arg );
	}

  /**
   * WordPress Option page.
   * 
   * @return array
   */
	public function general( $args ) {
		return $args;
	}
	public function menus( $args ) {
    // get_FwpOption( 'key', 'default' ) | apply_filters( 'futurewordpress/project/quizandfiltersearch/system/getoption', 'key', 'default' )
		// is_FwpActive( 'key' ) | apply_filters( 'futurewordpress/project/quizandfiltersearch/system/isactive', 'key' )
		$args = [];
		$args['standard'] 		= [
			'title'							=> __( 'General', 'quiz-and-filter-search-domain' ),
			'description'				=> __( 'Generel fields comst commonly used to changed.', 'quiz-and-filter-search-domain' ),
			'fields'						=> [
				[
					'id' 						=> 'general-enable',
					'label'					=> __( 'Enable', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'Mark to enable function of this Plugin.', 'quiz-and-filter-search-domain' ),
					'type'					=> 'checkbox',
					'default'				=> true
				],
				[
					'id' 						=> 'general-address',
					'label'					=> __( 'Address', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'Company address, that might be used on invoice and any public place if needed.', 'quiz-and-filter-search-domain' ),
					'type'					=> 'text',
					'default'				=> ''
				],
				[
					'id' 						=> 'general-archivedelete',
					'label'					=> __( 'Archive delete', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'Enable archive delete permission on frontend, so that user can delete archive files and data from their profile.', 'quiz-and-filter-search-domain' ),
					'type'					=> 'checkbox',
					'default'				=> ''
				],
				[
					'id' 						=> 'general-leaddelete',
					'label'					=> __( 'Delete User', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'Enable this option to apear a possibility to delete user/lead with one click. If it\'s disabled, then user delete option on list and single user details page will gone until turn it on.', 'quiz-and-filter-search-domain' ),
					'type'					=> 'checkbox',
					'default'				=> false
				],
			]
		];
		$args['permalink'] 		= [
			'title'						=> __( 'Permalink', 'quiz-and-filter-search-domain' ),
			'description'			=> __( 'Setup some permalink like dashboard and like this kind of things.', 'quiz-and-filter-search-domain' ),
			'fields'					=> [
				[
					'id' 							=> 'permalink-dashboard',
					'label'						=> __( 'Dashboard Slug', 'quiz-and-filter-search-domain' ),
					'description'			=> __( 'Enable dashboard parent Slug. By default it is "/dashboard". Each time you change this field you\'ve to re-save permalink settings.', 'quiz-and-filter-search-domain' ),
					'type'						=> 'text',
					'default'					=> 'dashboard'
				],
				[
					'id' 						=> 'permalink-userby',
					'label'					=> __( 'Dashboard Slug', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'Enable dashboard parent Slug. By default it is "/dashboard".', 'quiz-and-filter-search-domain' ),
					'type'					=> 'radio',
					'default'				=> 'id',
					'options'				=> [ 'id' => __( 'User ID', 'quiz-and-filter-search-domain' ), 'slug' => __( 'User Unique Name', 'quiz-and-filter-search-domain' ) ]
				],
			]
		];
		$args['dashboard'] 		= [
			'title'							=> __( 'Dashboard', 'quiz-and-filter-search-domain' ),
			'description'				=> __( 'Dashboard necessery fields, text and settings can configure here. Some tags on usable fields can be replace from here.', 'quiz-and-filter-search-domain' ) . $this->commontags( true ),
			'fields'						=> [
				[
					'id' 						=> 'dashboard-disablemyaccount',
					'label'					=> __( 'Disable My Account', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'Disable WooCommerce My Account dashboard and form redirect user to new dashboard. If you enable it, it\'ll apply. But be aware, WooCommerce orders and paid downloads are listed on My Account page.', 'quiz-and-filter-search-domain' ),
					'type'					=> 'checkbox',
					'default'				=> false
				],
				[
					'id' 						=> 'dashboard-title',
					'label'					=> __( 'Dashboard title', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'The title on dahsboard page. make sure you user tags properly.', 'quiz-and-filter-search-domain' ),
					'type'					=> 'text',
					'default'				=> sprintf( __( 'Client Dashoard | %s | %s', 'quiz-and-filter-search-domain' ), '{username}', '{sitename}' )
				],
				[
					'id' 						=> 'dashboard-yearstart',
					'label'					=> __( 'Year Starts', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'The Year range on dashboard starts from.', 'quiz-and-filter-search-domain' ),
					'type'					=> 'number',
					'default'				=> date( 'Y' )
				],
				[
					'id' 						=> 'dashboard-yearend',
					'label'					=> __( 'Yeah Ends with', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'The Year range on dashboard ends on.', 'quiz-and-filter-search-domain' ),
					'type'					=> 'number',
					'default'				=> ( date( 'Y' ) + 3 )
				],
				[
					'id' 						=> 'dashboard-headerbg',
					'label'					=> __( 'Header Background', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'Dashboard header background image url.', 'quiz-and-filter-search-domain' ),
					'type'					=> 'text',
					'default'				=> ''
				],
			]
		];
		$args['links'] 		= [
			'title'							=> __( 'Links', 'quiz-and-filter-search-domain' ),
			'description'				=> __( 'Documentation feature and their links can be change from here. If you leave blank anything then these "Learn More" never display.', 'quiz-and-filter-search-domain' ) . $this->commontags( true ),
			'fields'						=> [
				[
					'id' 						=> 'docs-monthlyretainer',
					'label'					=> __( 'Monthly Retainer', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'Your Monthly retainer that could be chaged anytime. Once you\'ve changed this amount, will be sync with your stripe account.', 'quiz-and-filter-search-domain' ),
					'type'					=> 'text',
					'default'				=> ''
				],
				[
					'id' 						=> 'docs-monthlyretainerurl',
					'label'					=> __( 'Learn more', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'The URL to place on Learn more.', 'quiz-and-filter-search-domain' ),
					'type'					=> 'url',
					'default'				=> ''
				],
				[
					'id' 						=> 'docs-contentcalendly',
					'label'					=> __( 'Content Calendar', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'See your content calendar on Calendly.', 'quiz-and-filter-search-domain' ),
					'type'					=> 'text',
					'default'				=> ''
				],
				[
					'id' 						=> 'docs-contentcalendlyurl',
					'label'					=> __( 'Learn more', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'The URL to place on Learn more.', 'quiz-and-filter-search-domain' ),
					'type'					=> 'url',
					'default'				=> ''
				],
				[
					'id' 						=> 'docs-contentlibrary',
					'label'					=> __( 'Content Library', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'Open content library from here.', 'quiz-and-filter-search-domain' ),
					'type'					=> 'text',
					'default'				=> ''
				],
				[
					'id' 						=> 'docs-contentlibraryurl',
					'label'					=> __( 'Learn more', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'The URL to place on Learn more.', 'quiz-and-filter-search-domain' ),
					'type'					=> 'url',
					'default'				=> ''
				],
				[
					'id' 						=> 'docs-clientrowvideos',
					'label'					=> __( 'Client Raw Video Archive', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'All of the video files are here. Click on the buton to open all archive list.', 'quiz-and-filter-search-domain' ),
					'type'					=> 'text',
					'default'				=> ''
				],
				[
					'id' 						=> 'docs-clientrowvideosurl',
					'label'					=> __( 'Learn more', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'The URL to place on Learn more.', 'quiz-and-filter-search-domain' ),
					'type'					=> 'url',
					'default'				=> ''
				],
				[
					'id' 						=> 'docs-manageretainer',
					'label'					=> __( 'Manage your Retainer', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'Manage your retainer from here. You can pause or cancel it from here.', 'quiz-and-filter-search-domain' ),
					'type'					=> 'text',
					'default'				=> ''
				],
				[
					'id' 						=> 'docs-manageretainerurl',
					'label'					=> __( 'Learn more', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'The URL to place on Learn more.', 'quiz-and-filter-search-domain' ),
					'type'					=> 'url',
					'default'				=> ''
				],
				[
					'id' 						=> 'docs-paymenthistory',
					'label'					=> __( 'Payment History', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'Payment history is synced form your stripe account since you started subscription.', 'quiz-and-filter-search-domain' ),
					'type'					=> 'text',
					'default'				=> ''
				],
				[
					'id' 						=> 'docs-paymenthistoryurl',
					'label'					=> __( 'Learn more', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'The URL to place on Learn more.', 'quiz-and-filter-search-domain' ),
					'type'					=> 'url',
					'default'				=> ''
				],
				[
					'id' 						=> 'docs-changepassword',
					'label'					=> __( 'Payment History', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'Change your password from here. This won\'t store on our database. Only encrypted password we store and make sure you\'ve saved your password on a safe place.', 'quiz-and-filter-search-domain' ),
					'type'					=> 'text',
					'default'				=> ''
				],
				[
					'id' 						=> 'docs-changepasswordurl',
					'label'					=> __( 'Learn more', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'The URL to place on Learn more.', 'quiz-and-filter-search-domain' ),
					'type'					=> 'url',
					'default'				=> ''
				],
				[
					'id' 						=> 'docs-emailaddress',
					'label'					=> __( 'Email Address', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'Email address required. Don\'t worry, we won\'t sent spam.', 'quiz-and-filter-search-domain' ),
					'type'					=> 'text',
					'default'				=> ''
				],
				[
					'id' 						=> 'docs-emailaddressurl',
					'label'					=> __( 'Learn more', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'The URL to place on Learn more.', 'quiz-and-filter-search-domain' ),
					'type'					=> 'url',
					'default'				=> ''
				],
				[
					'id' 						=> 'docs-contactnumber',
					'label'					=> __( 'Contact Number', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'Your conatct number is necessery in case if you need to communicate with you.', 'quiz-and-filter-search-domain' ),
					'type'					=> 'text',
					'default'				=> ''
				],
				[
					'id' 						=> 'docs-contactnumberurl',
					'label'					=> __( 'Learn more', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'The URL to place on Learn more.', 'quiz-and-filter-search-domain' ),
					'type'					=> 'url',
					'default'				=> ''
				],
				[
					'id' 						=> 'docs-website',
					'label'					=> __( 'Website URL', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'Give here you websute url if you have. Some case we might need to get idea about your and your company information.', 'quiz-and-filter-search-domain' ),
					'type'					=> 'text',
					'default'				=> ''
				],
				[
					'id' 						=> 'docs-websiteurl',
					'label'					=> __( 'Learn more', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'The URL to place on Learn more.', 'quiz-and-filter-search-domain' ),
					'type'					=> 'url',
					'default'				=> ''
				],
			]
		];
		$args['rest'] 		= [
			'title'							=> __( 'Rest API', 'quiz-and-filter-search-domain' ),
			'description'				=> __( 'Setup what happened when a rest api request fired on this site.', 'quiz-and-filter-search-domain' ),
			'fields'						=> [
				[
					'id' 						=> 'rest-createprofile',
					'label'					=> __( 'Create profile', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'When a request email doesn\'t match any account, so will it create a new user account?.', 'quiz-and-filter-search-domain' ),
					'type'					=> 'checkbox',
					'default'				=> true
				],
				[
					'id' 						=> 'rest-updateprofile',
					'label'					=> __( 'Update profile', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'When a request email detected an account, so will it update profile with requested information?.', 'quiz-and-filter-search-domain' ),
					'type'					=> 'checkbox',
					'default'				=> false
				],
				[
					'id' 						=> 'rest-preventemail',
					'label'					=> __( 'Prevent Email', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'Creating an account will send an email by default. Would you like to prevent sending email from rest request operation?', 'quiz-and-filter-search-domain' ),
					'type'					=> 'checkbox',
					'default'				=> true
				],
				[
					'id' 						=> 'rest-defaultpass',
					'label'					=> __( 'Default Password', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'The default password will be applied if any request contains emoty password or doesn\'t. Default value is random number.', 'quiz-and-filter-search-domain' ),
					'type'					=> 'text',
					'default'				=> ''
				],
			]
		];
		$args['auth'] 		= [
			'title'							=> __( 'Social Auth', 'quiz-and-filter-search-domain' ),
			'description'				=> __( 'Social anuthentication requeired provider API keys and some essential information. Claim them and setup here. Every API has an expiry date. So further if you face any problem with social authentication, make sure if api validity expired.', 'quiz-and-filter-search-domain' ),
			'fields'						=> [
				[
					'id' 						=> 'auth-enable',
					'label'					=> __( 'Enable Social Authetication', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'Mark this field to run social authentication. Once you disable from here, social authentication will be disabled from everywhere.', 'quiz-and-filter-search-domain' ),
					'type'					=> 'checkbox',
					'default'				=> true
				],
				[
					'id' 						=> 'auth-google',
					'label'					=> __( 'Enable Google Authetication', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'If you don\'t want to enable google authentication, you can disable this function from here.', 'quiz-and-filter-search-domain' ),
					'type'					=> 'checkbox',
					'default'				=> true
				],
				[
					'id' 						=> 'auth-connectdrive',
					'label'					=> __( 'Connect with Google Drive?', 'quiz-and-filter-search-domain' ),
					'description'		=> sprintf( __( 'Click on this %slink%s and allow access to connect with it.', 'quiz-and-filter-search-domain' ), '<a href="'. site_url( '/auth/drive/redirect/' ) . '" target="_blank">', '</a>' ),
					'type'					=> 'textcontent'
				],
				[
					'id' 						=> 'auth-googleclientid',
					'label'					=> __( 'Google Client ID', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'Your Google client or App ID, that you created for Authenticate.', 'quiz-and-filter-search-domain' ),
					'type'					=> 'text',
					'default'				=> ''
				],
				[
					'id' 						=> 'auth-googleclientsecret',
					'label'					=> __( 'Google Client Secret', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'Your Google client or App Secret. Is required here.', 'quiz-and-filter-search-domain' ),
					'type'					=> 'text',
					'default'				=> ''
				],
				[
					'id' 						=> 'auth-googledrivefolder',
					'label'					=> __( 'Storage Folder ID', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'ID of that specific folder where you want to sync files.', 'quiz-and-filter-search-domain' ),
					'type'					=> 'text',
					'default'				=> ''
				],
				[
					'id' 						=> 'auth-googleclientredirect',
					'label'					=> __( 'Google App Redirect', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'Place this link on Google Auth Callback or Redirect field on your Google App.', 'quiz-and-filter-search-domain' ) . '<code>' . apply_filters( 'futurewordpress/project/quizandfiltersearch/socialauth/redirect', '/handle/google', 'google' ) . '</code>',
					'type'					=> 'textcontent'
				],
				[
					'id' 						=> 'auth-googleauthlink',
					'label'					=> __( 'Google Auth Link', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'Use this link on your "Login with Google" button.', 'quiz-and-filter-search-domain' ) . '<code>' . apply_filters( 'futurewordpress/project/quizandfiltersearch/socialauth/link', '/auth/google', 'google' ) . '</code>',
					'type'					=> 'textcontent'
				],
			]
		];
		$args['social'] 		= [
			'title'							=> __( 'Social', 'quiz-and-filter-search-domain' ),
			'description'				=> __( 'Setup your social links her for client dashboard only. Only people who loggedin, can access these social links.', 'quiz-and-filter-search-domain' ),
			'fields'						=> [
				[
					'id' 						=> 'social-contact',
					'label'					=> __( 'Enable Contact', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'Enable contact now tab on client dashboard.', 'quiz-and-filter-search-domain' ),
					'type'					=> 'checkbox',
					'default'				=> true
				],
				[
					'id' 						=> 'social-telegram',
					'label'					=> __( 'Telegram', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'Provide Telegram messanger link here.', 'quiz-and-filter-search-domain' ),
					'type'					=> 'url',
					'default'				=> ''
				],
				[
					'id' 						=> 'social-whatsapp',
					'label'					=> __( 'WhatsApp', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'Provide WhatsApp messanger link here.', 'quiz-and-filter-search-domain' ),
					'type'					=> 'url',
					'default'				=> ''
				],
				[
					'id' 						=> 'social-email',
					'label'					=> __( 'Email', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'Email address for instant support.', 'quiz-and-filter-search-domain' ),
					'type'					=> 'email',
					'default'				=> ''
				],
				[
					'id' 						=> 'social-contactus',
					'label'					=> __( 'Contact Us', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'Place the Contact Us page link here.', 'quiz-and-filter-search-domain' ),
					'type'					=> 'url',
					'default'				=> ''
				],
			]
		];
		$args['signature'] 		= [
			'title'							=> __( 'E-Signature', 'quiz-and-filter-search-domain' ),
			'description'				=> __( 'Setup e-signature plugin some customize settings from here. Four tags for Contract is given below.', 'quiz-and-filter-search-domain' ) . $this->contractTags( ['{client_name}','{client_address}','{todays_date}','{retainer_amount}'] ),
			'fields'						=> [
				[
					'id' 						=> 'signature-addressplaceholder',
					'label'					=> __( 'Address Placeholder', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'What shouldbe replace if address1 & address2 both are empty. If you leave it blank, then it\'ll be blank.', 'quiz-and-filter-search-domain' ),
					'type'					=> 'text',
					'default'				=> 'N/A'
				],
				[
					'id' 						=> 'signature-dateformat',
					'label'					=> __( 'Date formate', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'The date format which will apply on {{todays_date}} place.', 'quiz-and-filter-search-domain' ),
					'type'					=> 'text',
					'default'				=> get_option('date_format')
				],
				[
					'id' 						=> 'signature-emptyrrtainer',
					'label'					=> __( 'Empty Retainer amount', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'if anytime we found empty retainer amount, so what will be replace there?', 'quiz-and-filter-search-domain' ),
					'type'					=> 'text',
					'default'				=> 'N/A'
				],
				[
					'id' 						=> 'signature-defaultcontract',
					'label'					=> __( 'Default contract form', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'When admin doesn\'t select a registration from before sending it to client, user is taken to this contract. It should be a page where a simple wp-form will apear with client name, service type, retainer amount if necessery.', 'quiz-and-filter-search-domain' ),
					'type'					=> 'url',
					'default'				=> ''
				],
			]
		];
		$args['email'] 		= [
			'title'							=> __( 'E-Mail', 'quiz-and-filter-search-domain' ),
			'description'				=> __( 'Setup email configuration here', 'quiz-and-filter-search-domain' ) . $this->contractTags( ['{client_name}','{client_address}','{todays_date}','{retainer_amount}', '{registration_link}', '{{site_name}}', '{{passwordreset_link}}' ] ),
			'fields'						=> [
				// [
				// 	'id' 						=> 'email-registationlink',
				// 	'label'					=> __( 'Registration Link', 'quiz-and-filter-search-domain' ),
				// 	'description'		=> __( 'Registration link that contains WP-Form registration form.', 'quiz-and-filter-search-domain' ),
				// 	'type'					=> 'text',
				// 	'default'				=> ""
				// ],
				[
					'id' 						=> 'email-registationsubject',
					'label'					=> __( 'Subject', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'The Subject, used on registration link sending mail.', 'quiz-and-filter-search-domain' ),
					'type'					=> 'text',
					'default'				=> "Invitation to Register for [Event/Service/Product]"
				],
				[
					'id' 						=> 'email-sendername',
					'label'					=> __( 'Sender name', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'Sender name that should be on mail metadata..', 'quiz-and-filter-search-domain' ),
					'type'					=> 'text',
					'default'				=> "Invitation to Register for [Event/Service/Product]"
				],
				[
					'id' 						=> 'email-registationbody',
					'label'					=> __( 'Registration link Template', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'The template, used on registration link sending mail.', 'quiz-and-filter-search-domain' ),
					'type'					=> 'textarea',
					'default'				=> "Dear [Name],\nWe are delighted to invite you to join us for [Event/Service/Product], a [brief description of event/service/product].\n[Event/Service/Product] offers [brief summary of benefits or features]. As a valued member of our community, we would like to extend a special invitation for you to be part of this exciting opportunity.\nTo register, simply click on the link below:\n[Registration link]\nShould you have any questions or require additional information, please do not hesitate to contact us at [contact information].\nWe look forward to seeing you at [Event/Service/Product].\nBest regards,\n[Your Name/Company Name]",
					'attr'					=> [ 'data-a-tinymce' => true ]
				],
				[
					'id' 						=> 'email-passresetsubject',
					'label'					=> __( 'Password Reset Subject', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'The email subject on password reset mail.', 'quiz-and-filter-search-domain' ),
					'type'					=> 'text',
					'default'				=> __( 'Password Reset Request',   'quiz-and-filter-search-domain' )
				],
				[
					'id' 						=> 'email-passresetbody',
					'label'					=> __( 'Password Reset Template', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'The template, used on password reset link sending mail.', 'quiz-and-filter-search-domain' ),
					'type'					=> 'textarea',
					'default'				=> "Dear {{client_name}},\n\nYou recently requested to reset your password for your {{site_name}} account. Please follow the link below to reset your password:\n\n{{passwordreset_link}}\n\nIf you did not make this request, you can safely ignore this email.\n\nBest regards,\n{{site_name}} Team"
				],
			]
		];
		$args['stripe'] 		= [
			'title'							=> __( 'Stripe', 'quiz-and-filter-search-domain' ),
			'description'				=> __( 'Stripe payment system configuration process should be do carefully. Here some field is importent to work with no inturrupt. Such as API key or secret key, if it\'s expired on your stripe id, it won\'t work here. New user could face problem fo that reason.', 'quiz-and-filter-search-domain' ),
			'fields'						=> [
				[
					'id' 						=> 'stripe-cancelsubscription',
					'label'					=> __( 'Cancellation', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'Enable it to make a possibility to user to cancel subscription from client dashboard.', 'quiz-and-filter-search-domain' ),
					'type'					=> 'checkbox',
					'default'				=> false
				],
				[
					'id' 						=> 'stripe-publishablekey',
					'label'					=> __( 'Publishable Key', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'The key which is secure, could import into JS, and is safe evenif any thirdparty got those code. Note that, secret key is not a publishable key.', 'quiz-and-filter-search-domain' ),
					'type'					=> 'text',
					'default'				=> ''
				],
				[
					'id' 						=> 'stripe-secretkey',
					'label'					=> __( 'Secret Key', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'The secret key that never share with any kind of frontend functionalities and is ofr backend purpose. Is required.', 'quiz-and-filter-search-domain' ),
					'type'					=> 'text',
					'default'				=> ''
				],
				[
					'id' 						=> 'stripe-currency',
					'label'					=> __( 'Currency', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'Default currency which will use to create payment link.', 'quiz-and-filter-search-domain' ),
					'type'					=> 'text',
					'default'				=> 'usd'
				],
				[
					'id' 						=> 'stripe-productname',
					'label'					=> __( 'Product name text', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'A text to show on product name place on checkout sanbox.', 'quiz-and-filter-search-domain' ),
					'type'					=> 'text',
					'default'				=> __( 'Subscription',   'quiz-and-filter-search-domain' )
				],
				[
					'id' 						=> 'stripe-productdesc',
					'label'					=> __( 'Product Description', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'Some text to show on product description field.', 'quiz-and-filter-search-domain' ),
					'type'					=> 'text',
					'default'				=> __( 'Payment for',   'quiz-and-filter-search-domain' ) . ' ' . get_option( 'blogname', 'We Make Content' )
				],
				[
					'id' 						=> 'stripe-productimg',
					'label'					=> __( 'Product Image', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'A valid image url for product. If image url are wrong or image doesn\'t detect by stripe, process will fail.', 'quiz-and-filter-search-domain' ),
					'type'					=> 'url',
					'default'				=> esc_url( QUIZ_AND_FILTER_SEARCH_BUILD_URI . '/icons/Online payment_Flatline.svg' )
				],
				[
					'id' 						=> 'stripe-paymentmethod',
					'label'					=> __( 'Payment Method', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'Select which payment method you will love to get payment.', 'quiz-and-filter-search-domain' ),
					'type'					=> 'select',
					'default'				=> 'card',
					'options'				=> apply_filters( 'futurewordpress/project/quizandfiltersearch/payment/stripe/payment_methods', [] )
				],
			]
		];
		$args['regis'] 		= [
			'title'							=> __( 'Registrations', 'quiz-and-filter-search-domain' ),
			'description'				=> sprintf( __( 'Setup registration link and WP-forms information here. %s will replace with a unique number to avoid cache.', 'quiz-and-filter-search-domain' ), '<code>{{nonce}}</code>' ),
			'fields'						=> [
				[
					'id' 						=> 'regis-rows',
					'label'					=> __( 'Rows', 'quiz-and-filter-search-domain' ),
					'description'		=> __( 'How many registration links do you have.', 'quiz-and-filter-search-domain' ),
					'type'					=> 'number',
					'default'				=> 2
				],
			]
		];
		for( $i = 1;$i <= apply_filters( 'futurewordpress/project/quizandfiltersearch/system/getoption', 'regis-rows', 3 ); $i++ ) {
			$args['regis'][ 'fields' ][] = [
				'id' 						=> 'regis-link-title-' . $i,
				'label'					=> __( 'Link title #' . $i, 'quiz-and-filter-search-domain' ),
				'description'		=> '',
				'type'					=> 'text',
				'default'				=> 'Link #' . $i
			];
			$args['regis'][ 'fields' ][] = [
				'id' 						=> 'regis-link-url-' . $i,
				'label'					=> __( 'Link URL #' . $i, 'quiz-and-filter-search-domain' ),
				'description'		=> '',
				'type'					=> 'url',
				'default'				=> ''
			];
			$args['regis'][ 'fields' ][] = [
				'id' 						=> 'regis-link-pageid-' . $i,
				'label'					=> __( 'Page ID#' . $i, 'quiz-and-filter-search-domain' ),
				'description'		=> __( 'Registration Page ID, leave it blank if you don\'t want to disable it without invitation.', 'quiz-and-filter-search-domain' ),
				'type'					=> 'text',
				'default'				=> ''
			];
		}
		$args['docs'] 		= [
			'title'							=> __( 'Documentations', 'quiz-and-filter-search-domain' ),
			'description'				=> __( 'The workprocess is tring to explain here.', 'quiz-and-filter-search-domain' ),
			'fields'						=> [
				[
					'id' 						=> 'auth-brifing',
					'label'					=> __( 'How to setup thank you page?', 'quiz-and-filter-search-domain' ),
					'description'		=> sprintf( __( 'first go to %sthis link%s Create or Edit an "Stand Alone" document. Give your thankyou custom page link here %s', 'quiz-and-filter-search-domain' ), '<a href="'. admin_url( 'admin.php?page=esign-docs&document_status=stand_alone' ) . '" target="_blank">', '</a>', '<img src="' . QUIZ_AND_FILTER_SEARCH_DIR_URI . '/docs/Stand-alone-esign-metabox.PNG' . '" alt="" />' ),
					'type'					=> 'textcontent'
				],
			]
		];
		return $args;
	}
}

/**
 * {{client_name}}, {{client_address}}, {{todays_date}}, {{retainer_amount}}
 */
