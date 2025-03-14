<?php
/**
 * General Functions
 * // @echo HEADER
 */

/*******************************************************
  Settings
*******************************************************/

/**
 * Get Settings Value
 *
 * @author       	Alimir
 * @since           1.0
 * @return			Void
 */
if( ! function_exists( 'wp_ulike_get_setting' ) ){
	function wp_ulike_get_setting( $setting, $option = false, $default = false ) {
		$setting = get_option( $setting );
		if ( is_array( $setting ) ) {
			if ( $option ) {
				return isset( $setting[$option] ) ? wp_ulike_settings::parse_multi( $setting[$option] ) : $default;
			}
			foreach ( $setting as $k => $v ) {
				$setting[$k] = wp_ulike_settings::parse_multi( $v );
			}

			return $setting;
		}
		return $option ? $default : $setting;
	}
}

/**
 * Delete all likes number by ajax process.
 *
 * @author       	Alimir
 * @since           2.2
 * @return			Void
 */
if( ! function_exists( 'wp_ulike_delete_all_data' ) ){
	function wp_ulike_delete_all_data() {
		global $wpdb;
		$get_action = $_POST['action'];
		//$wpdb->hide_errors();

		if($get_action == 'wp_ulike_posts_delete_data'){
			$meta_table = $wpdb->prefix."postmeta";
			$meta_key   = '_liked';
		} else if($get_action == 'wp_ulike_comments_delete_data'){
			$meta_table = $wpdb->prefix."commentmeta";
			$meta_key   = '_commentliked';
		} else if($get_action == 'wp_ulike_buddypress_delete_data'){
			$meta_table = $wpdb->prefix."bp_activity_meta";
			$meta_key   = '_activityliked';
		} else if($get_action == 'wp_ulike_bbpress_delete_data'){
			$meta_table = $wpdb->prefix."postmeta";
			$meta_key   = '_topicliked';
		}

		$do_action 		= $wpdb->delete($meta_table, array( 'meta_key' => $meta_key ));

		if ($do_action === FALSE) {
			wp_send_json_error( __( 'Failed! An Error Has Occurred While Deleting All ULike Logs/Data', WP_ULIKE_SLUG ));
		} else {
			wp_send_json_success( __( 'Success! All ULike Logs/Data Have Been Deleted', WP_ULIKE_SLUG ) );
		}
	}
}

/**
 * Delete all the users likes logs by ajax process.
 *
 * @author       	Alimir
 * @since           2.2
 * @return			Void
 */
if( ! function_exists( 'wp_ulike_delete_all_logs' ) ){
	function wp_ulike_delete_all_logs() {
		global $wpdb;
		$get_action = $_POST['action'];
		//$wpdb->hide_errors();

		if($get_action == 'wp_ulike_posts_delete_logs'){
			$logs_table = $wpdb->prefix."ulike";
		} else if($get_action == 'wp_ulike_comments_delete_logs'){
			$logs_table = $wpdb->prefix."ulike_comments";
		} else if($get_action == 'wp_ulike_buddypress_delete_logs'){
			$logs_table = $wpdb->prefix."ulike_activities";
		} else if($get_action == 'wp_ulike_bbpress_delete_logs'){
			$logs_table = $wpdb->prefix."ulike_forums";
		}

		if ($wpdb->query("TRUNCATE TABLE $logs_table") === FALSE) {
			wp_send_json_error( __( 'Failed! An Error Has Occurred While Deleting All ULike Logs/Data', WP_ULIKE_SLUG ) );
		} else {
			wp_send_json_success( __( 'Success! All ULike Logs/Data Have Been Deleted', WP_ULIKE_SLUG ) );
		}
	}
}


/**
 * Generate templates list
 *
 * @author       	Alimir
 * @since           2.8
 * @return			Array
 */
if( ! function_exists( 'wp_ulike_generate_templates_list' ) ){
	function wp_ulike_generate_templates_list(){
		$default = array(
			'wpulike-default' => array(
				'name'     => __('Default', WP_ULIKE_SLUG),
				'callback' => 'wp_ulike_set_default_template',
				'symbol'   => WP_ULIKE_ASSETS_URL . '/img/svg/default.svg'
			),
			'wpulike-heart' => array(
				'name'     => __('Heart', WP_ULIKE_SLUG),
				'callback' => 'wp_ulike_set_simple_heart_template',
				'symbol'   => WP_ULIKE_ASSETS_URL . '/img/svg/heart.svg'
			),
			'wpulike-robeen' => array(
				'name'     => __('Robeen', WP_ULIKE_SLUG),
				'callback' => 'wp_ulike_set_robeen_template',
				'symbol'   => WP_ULIKE_ASSETS_URL . '/img/svg/twitter.svg'
			),
			'wpulike-animated-heart' => array(
				'name'     => __('Animated Heart', WP_ULIKE_SLUG),
				'callback' => 'wp_ulike_set_animated_heart_template',
				'symbol'   => WP_ULIKE_ASSETS_URL . '/img/svg/animated-heart.svg'
			)
		);

		return apply_filters( 'wp_ulike_add_templates_list', $default );
	}
}

/**
 * Generate options info list
 *
 * @author       	Alimir
 * @since           3.1
 * @return			Array
 */
if( ! function_exists( 'wp_ulike_get_options_info' ) ){
	function wp_ulike_get_options_info( $type, $result = array() ) {

		switch ( $type ) {
			case 'general':
				$result = array(
					'title'  => '<i class="dashicons-before dashicons-admin-settings"></i>' . ' ' . __( 'General',WP_ULIKE_SLUG),
					'fields' => array(
						'button_type' => array(
							'type'    => 'visual-select',
							'label'   => __( 'Button Type', WP_ULIKE_SLUG),
							'default' => 'image',
							'options' => array(
								'image' => array(
									'name'   => __('Icon', WP_ULIKE_SLUG),
									'symbol' => WP_ULIKE_ASSETS_URL . '/img/svg/icon.svg'
								),
								'text' => array(
									'name'   => __('Text', WP_ULIKE_SLUG),
									'symbol' => WP_ULIKE_ASSETS_URL . '/img/svg/text.svg'
								)
							)
						),
						'button_text' => array(
							'default' => __('Like',WP_ULIKE_SLUG),
							'label'   => __( 'Button Text', WP_ULIKE_SLUG) . ' (' . __('Like', WP_ULIKE_SLUG) .')',
						),
						'button_text_u' => array(
							'default' => __('Liked',WP_ULIKE_SLUG),
							'label'   => __( 'Button Text', WP_ULIKE_SLUG) . ' (' . __('Unlike', WP_ULIKE_SLUG) .')',
						),
						'button_url' => array(
							'type'        => 'media',
							'label'       => __( 'Button Icon', WP_ULIKE_SLUG) . ' (' . __('Like', WP_ULIKE_SLUG) .')',
							'description' => __( 'Best size: 16x16',WP_ULIKE_SLUG)
						),
						'button_url_u' => array(
							'type'        => 'media',
							'label'       => __( 'Button Icon', WP_ULIKE_SLUG) . ' (' . __('Unlike', WP_ULIKE_SLUG) .')',
							'description' => __( 'Best size: 16x16',WP_ULIKE_SLUG)
						),
						'permission_text' => array(
							'default' => __('You have not permission to unlike',WP_ULIKE_SLUG),
							'label'   => __( 'Permission Text', WP_ULIKE_SLUG)
						),
						'login_type' => array(
							'type'    => 'radio',
							'label'   => __( 'Users Login Type',WP_ULIKE_SLUG),
							'default' => 'button',
							'options' => array(
								'alert'  => __('Alert Box', WP_ULIKE_SLUG),
								'button' => __('Like Button', WP_ULIKE_SLUG)
						  	)
						),
						'login_text' => array(
							'default' => __('You Should Login To Submit Your Like',WP_ULIKE_SLUG),
							'label'   => __( 'Users Login Text', WP_ULIKE_SLUG)
						),
						'plugin_files' => array(
							'type'        => 'multi',
							'label'       => __( 'Disable Plugin Files',WP_ULIKE_SLUG ),
							'options'     => array(
								'home'        => __('Home', WP_ULIKE_SLUG),
								'single'      => __('Single Posts', WP_ULIKE_SLUG),
								'page'        => __('Pages', WP_ULIKE_SLUG),
								'archive'     => __('Archives', WP_ULIKE_SLUG),
								'category'    => __('Categories', WP_ULIKE_SLUG),
								'search'      => __('Search Results', WP_ULIKE_SLUG),
								'tag'         => __('Tags', WP_ULIKE_SLUG),
								'author'      => __('Author Page', WP_ULIKE_SLUG),
								'buddypress'  => __('BuddyPress Pages', WP_ULIKE_SLUG),
								'bbpress'     => __('bbPress Pages', WP_ULIKE_SLUG),
								'woocommerce' => __('WooCommerce Pages', WP_ULIKE_SLUG)
							),
							'description' => __('Remove the plugin\'s css and js file on these pages.', WP_ULIKE_SLUG)
					    ),
						'format_number' => array(
							'type'          => 'checkbox',
							'default'       => 0,
							'label'         => __('Format Number', WP_ULIKE_SLUG),
							'checkboxlabel' => __('Activate', WP_ULIKE_SLUG),
							'description'   => __('Convert numbers of Likes with string (kilobyte) format.', WP_ULIKE_SLUG) . '<strong> (WHEN? likes>=1000)</strong>'
						),
						'notifications' => array(
							'type'          => 'checkbox',
							'default'       => 1,
							'label'         => __('Notifications', WP_ULIKE_SLUG),
							'checkboxlabel' => __('Activate', WP_ULIKE_SLUG),
							'description'   => __('Custom toast messages after each activity', WP_ULIKE_SLUG)
						),
						'anonymise'     => array(
							'type'          => 'checkbox',
							'default'       => 0,
							'label'         => __('Anonymize IP', WP_ULIKE_SLUG),
							'checkboxlabel' => __('Activate', WP_ULIKE_SLUG),
							'description'   => __('Anonymize the IP address for GDPR Compliance', WP_ULIKE_SLUG)
						),
						'like_notice' => array(
							'default' => __('Thanks! You Liked This.',WP_ULIKE_SLUG),
							'label'   => __( 'Liked Notice Message', WP_ULIKE_SLUG)
						),
						'unlike_notice' => array(
							'default' => __('Sorry! You unliked this.',WP_ULIKE_SLUG),
							'label'   => __( 'Unliked Notice Message', WP_ULIKE_SLUG)
						)
					)
				);//end wp_ulike_general
				break;

			case 'posts':
				$result = array(
					'title' => '<i class="dashicons-before dashicons-admin-post"></i>' . ' ' . __( 'Posts',WP_ULIKE_SLUG),
					'fields' => array(
						'theme' => array(
							'type'    => 'visual-select',
							'default' => 'wpulike-default',
							'label'   => __( 'Themes',WP_ULIKE_SLUG),
							'options' => call_user_func('wp_ulike_generate_templates_list')
						),
						'auto_display' => array(
							'type'          => 'checkbox',
							'default'       => 1,
							'label'         => __('Automatic display', WP_ULIKE_SLUG),
							'checkboxlabel' => __('Activate', WP_ULIKE_SLUG)
						),
						'auto_display_position' => array(
							'type'    => 'radio',
							'label'   => __( 'Auto Display Position',WP_ULIKE_SLUG),
							'default' => 'bottom',
							'options' => array(
								'top'        => __('Top of Content', WP_ULIKE_SLUG),
								'bottom'     => __('Bottom of Content', WP_ULIKE_SLUG),
								'top_bottom' => __('Top and Bottom', WP_ULIKE_SLUG)
							)
						),
						'auto_display_filter' => array(
							'type'    => 'multi',
							'label'   => __( 'Auto Display Filter',WP_ULIKE_SLUG ),
							'options' => array(
								'home'     => __('Home', WP_ULIKE_SLUG),
								'single'   => __('Single Posts', WP_ULIKE_SLUG),
								'page'     => __('Pages', WP_ULIKE_SLUG),
								'archive'  => __('Archives', WP_ULIKE_SLUG),
								'category' => __('Categories', WP_ULIKE_SLUG),
								'search'   => __('Search Results', WP_ULIKE_SLUG),
								'tag'      => __('Tags', WP_ULIKE_SLUG),
								'author'   => __('Author Page', WP_ULIKE_SLUG)
							),
							'description' => __('You can filter theses pages on auto display option.', WP_ULIKE_SLUG)
						),
						'google_rich_snippets' => array(
							'type'          => 'checkbox',
							'default'       => 0,
							'label'         => '*' . __('Google Rich Snippets', WP_ULIKE_SLUG),
							'checkboxlabel' => __('Activate', WP_ULIKE_SLUG),
							'description'   => __('Add rich snippet for ratings in form of schema.org', WP_ULIKE_SLUG)
						),
						'only_registered_users' => array(
							'type'          => 'checkbox',
							'default'       => 0,
							'label'         => __('Only registered Users', WP_ULIKE_SLUG),
							'checkboxlabel' => __('Activate', WP_ULIKE_SLUG),
							'description'   => __('<strong>Only</strong> registered users have permission to like posts.', WP_ULIKE_SLUG)
						),
						'logging_method' => array(
							'type'    => 'select',
							'default' => 'by_username',
							'label'   => __( 'Logging Method',WP_ULIKE_SLUG),
							'options' => array(
								'do_not_log'  => __('Do Not Log', WP_ULIKE_SLUG),
								'by_cookie'   => __('Logged By Cookie', WP_ULIKE_SLUG),
								'by_ip'       => __('Logged By IP', WP_ULIKE_SLUG),
								'by_username' => __('Logged By Username', WP_ULIKE_SLUG)
							)
						),
						'users_liked_box' => array(
							'type'          => 'checkbox',
							'default'       => 1,
							'label'         => __('Display Likers Box', WP_ULIKE_SLUG),
							'checkboxlabel' => __('Activate', WP_ULIKE_SLUG)
						),
						'disable_likers_pophover' => array(
							'type'          => 'checkbox',
							'default'       => 0,
							'label'         => __('Disable Popover In Likers Box', WP_ULIKE_SLUG),
							'checkboxlabel' => __('Disable', WP_ULIKE_SLUG),
							'description'   => __('Active this option to show liked users avatars in the bottom of button like.', WP_ULIKE_SLUG)
						),
						'users_liked_box_avatar_size' => array(
							'type'        => 'number',
							'default'     => 64,
							'label'       => __( 'Size of Gravatars', WP_ULIKE_SLUG),
							'description' => __('Size of Gravatars to return (max is 512)', WP_ULIKE_SLUG)
						),
						'number_of_users' => array(
							'type'        => 'number',
							'default'     => 10,
							'label'       => __( 'Number Of The Users', WP_ULIKE_SLUG),
							'description' => __('The number of users to show in the users liked box', WP_ULIKE_SLUG)
						),
						'users_liked_box_template' => array(
							'type'        => 'textarea',
							'default'     => '<div class="wp-ulike-likers-list">%START_WHILE%<span class="wp-ulike-liker"><a href="#" title="%USER_NAME%">%USER_AVATAR%</a></span>%END_WHILE%</div>',
							'label'       => __('Users Like Box Template', WP_ULIKE_SLUG),
							'description' => __('Allowed Variables:', WP_ULIKE_SLUG) . ' <code>%USER_AVATAR%</code> , <code>%BP_PROFILE_URL%</code> , <code>%UM_PROFILE_URL%</code> , <code>%USER_NAME%</code> , <code>%START_WHILE%</code> , <code>%END_WHILE%</code>'
						),
						'delete_logs' => array(
							'type'        => 'action',
							'label'       => __( 'Delete All Logs', WP_ULIKE_SLUG ),
							'description' => __( 'You Are About To Delete All Likes Logs. This Action Is Not Reversible.', WP_ULIKE_SLUG),
							'action'      => 'wp_ulike_delete_all_logs'
						),
						'delete_data' => array(
							'type'        => 'action',
							'label'       => __( 'Delete All Data', WP_ULIKE_SLUG ),
							'description' => __( 'You Are About To Delete All Likes Data. This Action Is Not Reversible.', WP_ULIKE_SLUG),
							'action'      => 'wp_ulike_delete_all_data'
						)
					)
				);//end wp_ulike_posts
				break;

			case 'comments':
				$result = array(
					'title'  => '<i class="dashicons-before dashicons-admin-comments"></i>' . ' ' . __( 'Comments',WP_ULIKE_SLUG),
					'fields' => array(
						'theme' => array(
							'type'    => 'visual-select',
							'default' => 'wpulike-heart',
							'label'   => __( 'Themes',WP_ULIKE_SLUG),
							'options' => call_user_func('wp_ulike_generate_templates_list')
						),
						'auto_display' => array(
							'type'          => 'checkbox',
							'default'       => 1,
							'label'         => __('Automatic display', WP_ULIKE_SLUG),
							'checkboxlabel' => __('Activate', WP_ULIKE_SLUG)
						),
						'auto_display_position' => array(
							'type'    => 'radio',
							'label'   => __( 'Auto Display Position',WP_ULIKE_SLUG),
							'default' => 'bottom',
							'options' => array(
								'top'        => __('Top of Content', WP_ULIKE_SLUG),
								'bottom'     => __('Bottom of Content', WP_ULIKE_SLUG),
								'top_bottom' => __('Top and Bottom', WP_ULIKE_SLUG)
							)
						),
						'only_registered_users' => array(
							'type'          => 'checkbox',
							'default'       => 0,
							'label'         => __('Only registered Users', WP_ULIKE_SLUG),
							'checkboxlabel' => __('Activate', WP_ULIKE_SLUG),
							'description'   => __('<strong>Only</strong> registered users have permission to like comments.', WP_ULIKE_SLUG)
						),
						'logging_method' => array(
							'type'    => 'select',
							'default' => 'by_username',
							'label'   => __( 'Logging Method',WP_ULIKE_SLUG),
							'options' => array(
								'do_not_log'  => __('Do Not Log', WP_ULIKE_SLUG),
								'by_cookie'   => __('Logged By Cookie', WP_ULIKE_SLUG),
								'by_ip'       => __('Logged By IP', WP_ULIKE_SLUG),
								'by_username' => __('Logged By Username', WP_ULIKE_SLUG)
							)
						),
						'users_liked_box' => array(
							'type'          => 'checkbox',
							'default'       => 1,
							'label'         => __('Display Likers Box', WP_ULIKE_SLUG),
							'checkboxlabel' => __('Activate', WP_ULIKE_SLUG)
						),
						'disable_likers_pophover' => array(
							'type'          => 'checkbox',
							'default'       => 0,
							'label'         => __('Disable Popover In Likers Box', WP_ULIKE_SLUG),
							'checkboxlabel' => __('Disable', WP_ULIKE_SLUG),
							'description'   => __('Active this option to show liked users avatars in the bottom of button like.', WP_ULIKE_SLUG)
						),
						'users_liked_box_avatar_size' => array(
							'type'        => 'number',
							'default'     => 64,
							'label'       => __( 'Size of Gravatars', WP_ULIKE_SLUG),
							'description' => __('Size of Gravatars to return (max is 512)', WP_ULIKE_SLUG)
						),
						'number_of_users' => array(
							'type'        => 'number',
							'default'     => 10,
							'label'       => __( 'Number Of The Users', WP_ULIKE_SLUG),
							'description' => __('The number of users to show in the users liked box', WP_ULIKE_SLUG)
						),
						'users_liked_box_template' => array(
							'type'        => 'textarea',
							'default'     => '<div class="wp-ulike-likers-list">%START_WHILE%<span class="wp-ulike-liker"><a href="#" title="%USER_NAME%">%USER_AVATAR%</a></span>%END_WHILE%</div>',
							'label'       => __('Users Like Box Template', WP_ULIKE_SLUG),
							'description' => __('Allowed Variables:', WP_ULIKE_SLUG) . ' <code>%USER_AVATAR%</code> , <code>%BP_PROFILE_URL%</code> , <code>%UM_PROFILE_URL%</code> , <code>%USER_NAME%</code> , <code>%START_WHILE%</code> , <code>%END_WHILE%</code>'
						),
						'delete_logs' => array(
							'type'        => 'action',
							'label'       => __( 'Delete All Logs', WP_ULIKE_SLUG ),
							'description' => __( 'You Are About To Delete All Likes Logs. This Action Is Not Reversible.', WP_ULIKE_SLUG),
							'action'      => 'wp_ulike_delete_all_logs'
						),
						'delete_data' => array(
							'type'        => 'action',
							'label'       => __( 'Delete All Data', WP_ULIKE_SLUG ),
							'description' => __( 'You Are About To Delete All Likes Data. This Action Is Not Reversible.', WP_ULIKE_SLUG),
							'action'      => 'wp_ulike_delete_all_data'
						)
					)
				);//end wp_ulike_comments
				break;

			case 'buddypress':
				$result = ! function_exists('is_buddypress') ?  array() : array(
					'title'  => '<i class="dashicons-before dashicons-buddypress"></i>' . ' ' . __( 'BuddyPress',WP_ULIKE_SLUG),
					'fields' => array(
						'theme' => array(
							'type'    => 'visual-select',
							'default' => 'wpulike-robeen',
							'label'   => __( 'Themes',WP_ULIKE_SLUG),
							'options' => call_user_func('wp_ulike_generate_templates_list')
						),
						'auto_display' => array(
							'type'          => 'checkbox',
							'default'       => 1,
							'label'         => __('Automatic display', WP_ULIKE_SLUG),
							'checkboxlabel' => __('Activate', WP_ULIKE_SLUG)
						),
						'auto_display_position' => array(
							'type'    => 'radio',
							'label'   => __( 'Auto Display Position',WP_ULIKE_SLUG),
							'default' => 'meta',
							'options' => array(
								'content' => __('Activity Content', WP_ULIKE_SLUG),
								'meta'    => __('Activity Meta', WP_ULIKE_SLUG)
							)
						),
						'activity_comment'=> array(
							'type'          => 'checkbox',
							'default'       => 1,
							'label'         => __('Activity Comment', WP_ULIKE_SLUG),
							'checkboxlabel' => __('Activate', WP_ULIKE_SLUG),
							'description'   => __('Add the possibility to like Buddypress comments in the activity stream', WP_ULIKE_SLUG)
						),
						'only_registered_users' => array(
							'type'          => 'checkbox',
							'default'       => 0,
							'label'         => __('Only registered Users', WP_ULIKE_SLUG),
							'checkboxlabel' => __('Activate', WP_ULIKE_SLUG),
							'description'   => __('<strong>Only</strong> registered users have permission to like activities.', WP_ULIKE_SLUG)
						),
						'logging_method' => array(
							'type'    => 'select',
							'default' => 'by_cookie_ip',
							'label'   => __( 'Logging Method',WP_ULIKE_SLUG),
							'options' => array(
								'do_not_log'  => __('Do Not Log', WP_ULIKE_SLUG),
								'by_cookie'   => __('Logged By Cookie', WP_ULIKE_SLUG),
								'by_ip'       => __('Logged By IP', WP_ULIKE_SLUG),
								'by_username' => __('Logged By Username', WP_ULIKE_SLUG)
							)
						),
						'users_liked_box' => array(
							'type'          => 'checkbox',
							'default'       => 1,
							'label'         => __('Display Likers Box', WP_ULIKE_SLUG),
							'checkboxlabel' => __('Activate', WP_ULIKE_SLUG)
						),
						'disable_likers_pophover' => array(
							'type'          => 'checkbox',
							'default'       => 0,
							'label'         => __('Disable Popover In Likers Box', WP_ULIKE_SLUG),
							'checkboxlabel' => __('Disable', WP_ULIKE_SLUG),
							'description'   => __('Active this option to show liked users avatars in the bottom of button like.', WP_ULIKE_SLUG)
						),
						'users_liked_box_avatar_size' => array(
							'type'        => 'number',
							'default'     => 64,
							'label'       => __( 'Size of Gravatars', WP_ULIKE_SLUG),
							'description' => __('Size of Gravatars to return (max is 512)', WP_ULIKE_SLUG)
						),
						'number_of_users' => array(
							'type'        => 'number',
							'default'     => 10,
							'label'       => __( 'Number Of The Users', WP_ULIKE_SLUG),
							'description' => __('The number of users to show in the users liked box', WP_ULIKE_SLUG)
						),
						'users_liked_box_template' => array(
							'type'        => 'textarea',
							'default'     => '<div class="wp-ulike-likers-list">%START_WHILE%<span class="wp-ulike-liker"><a href="#" title="%USER_NAME%">%USER_AVATAR%</a></span>%END_WHILE%</div>',
							'label'       => __('Users Like Box Template', WP_ULIKE_SLUG),
							'description' => __('Allowed Variables:', WP_ULIKE_SLUG) . ' <code>%USER_AVATAR%</code> , <code>%BP_PROFILE_URL%</code> , <code>%UM_PROFILE_URL%</code> , <code>%USER_NAME%</code> , <code>%START_WHILE%</code> , <code>%END_WHILE%</code>'
						),
						'new_likes_activity' => array(
							'type'          => 'checkbox',
							'default'       => 0,
							'label'         => __('BuddyPress Activity', WP_ULIKE_SLUG),
							'checkboxlabel' => __('Activate', WP_ULIKE_SLUG),
							'description'   => __('insert new likes in buddyPress activity page', WP_ULIKE_SLUG)
						),
						'custom_notification' => array(
							'type'          => 'checkbox',
							'default'       => 0,
							'label'         => __('BuddyPress Custom Notification', WP_ULIKE_SLUG),
							'checkboxlabel' => __('Activate', WP_ULIKE_SLUG),
							'description'   => __('Sends out notifications when you get a like from someone', WP_ULIKE_SLUG)
						),
						'bp_post_activity_add_header' => array(
							'type'        => 'textarea',
							'default'     => '<strong>%POST_LIKER%</strong> liked <a href="%POST_PERMALINK%" title="%POST_TITLE%">%POST_TITLE%</a>. (So far, This post has <span class="badge">%POST_COUNT%</span> likes)',
							'label'       => __('Post Activity Text', WP_ULIKE_SLUG),
							'description' => __('Allowed Variables:', WP_ULIKE_SLUG) . ' <code>%POST_LIKER%</code> , <code>%POST_PERMALINK%</code> , <code>%POST_COUNT%</code> , <code>%POST_TITLE%</code>'
						),
						'bp_comment_activity_add_header' => array(
							'type'        => 'textarea',
							'default'     => '<strong>%COMMENT_LIKER%</strong> liked <strong>%COMMENT_AUTHOR%</strong> comment. (So far, %COMMENT_AUTHOR% has <span class="badge">%COMMENT_COUNT%</span> likes for this comment)',
							'label'       => __('Comment Activity Text', WP_ULIKE_SLUG),
							'description' => __('Allowed Variables:', WP_ULIKE_SLUG) . ' <code>%COMMENT_LIKER%</code> , <code>%COMMENT_AUTHOR%</code> , <code>%COMMENT_COUNT%</code>, <code>%COMMENT_PERMALINK%</code>'
						),
						'delete_logs' => array(
							'type'        => 'action',
							'label'       => __( 'Delete All Logs', WP_ULIKE_SLUG ),
							'description' => __( 'You Are About To Delete All Likes Logs. This Action Is Not Reversible.', WP_ULIKE_SLUG),
							'action'      => 'wp_ulike_delete_all_logs'
						),
						'delete_data' => array(
							'type'        => 'action',
							'label'       => __( 'Delete All Data', WP_ULIKE_SLUG ),
							'description' => __( 'You Are About To Delete All Likes Data. This Action Is Not Reversible.', WP_ULIKE_SLUG),
							'action'      => 'wp_ulike_delete_all_data'
						)
					)
				);//end wp_ulike_buddypress
				break;

			case 'bbpress':
				$result	= ! function_exists('is_bbpress') ? array() : array(
					'title'  => '<i class="dashicons-before dashicons-bbpress"></i>' . ' ' . __( 'bbPress',WP_ULIKE_SLUG),
					'fields' => array(
						'theme' => array(
							'type'    => 'visual-select',
							'default' => 'wpulike-default',
							'label'   => __( 'Themes',WP_ULIKE_SLUG),
							'options' => call_user_func('wp_ulike_generate_templates_list')
						),
						'auto_display' => array(
							'type'          => 'checkbox',
							'default'       => 1,
							'label'         => __('Automatic display', WP_ULIKE_SLUG),
							'checkboxlabel' => __('Activate', WP_ULIKE_SLUG)
						),
						'auto_display_position' => array(
							'type'    => 'radio',
							'label'   => __( 'Auto Display Position',WP_ULIKE_SLUG),
							'default' => 'bottom',
							'options' => array(
								'top'    => __('Top of Content', WP_ULIKE_SLUG),
								'bottom' => __('Bottom of Content', WP_ULIKE_SLUG)
							)
						),
						'only_registered_users' => array(
							'type'          => 'checkbox',
							'default'       => 0,
							'label'         => __('Only registered Users', WP_ULIKE_SLUG),
							'checkboxlabel' => __('Activate', WP_ULIKE_SLUG),
							'description'   => __('<strong>Only</strong> registered users have permission to like Topics.', WP_ULIKE_SLUG)
						),
						'logging_method' => array(
							'type'    => 'select',
							'default' => 'by_cookie_ip',
							'label'   => __( 'Logging Method',WP_ULIKE_SLUG),
							'options' => array(
								'do_not_log'  => __('Do Not Log', WP_ULIKE_SLUG),
								'by_cookie'   => __('Logged By Cookie', WP_ULIKE_SLUG),
								'by_ip'       => __('Logged By IP', WP_ULIKE_SLUG),
								'by_username' => __('Logged By Username', WP_ULIKE_SLUG)
							)
						),
						'users_liked_box' => array(
							'type'          => 'checkbox',
							'default'       => 1,
							'label'         => __('Display Likers Box', WP_ULIKE_SLUG),
							'checkboxlabel' => __('Activate', WP_ULIKE_SLUG)
						),
						'disable_likers_pophover' => array(
							'type'          => 'checkbox',
							'default'       => 0,
							'label'         => __('Disable Popover In Likers Box', WP_ULIKE_SLUG),
							'checkboxlabel' => __('Disable', WP_ULIKE_SLUG),
							'description'   => __('Active this option to show liked users avatars in the bottom of button like.', WP_ULIKE_SLUG)
						),
						'users_liked_box_avatar_size' => array(
							'type'        => 'number',
							'default'     => 64,
							'label'       => __( 'Size of Gravatars', WP_ULIKE_SLUG),
							'description' => __('Size of Gravatars to return (max is 512)', WP_ULIKE_SLUG)
						),
						'number_of_users' => array(
							'type'        => 'number',
							'default'     => 10,
							'label'       => __( 'Number Of The Users', WP_ULIKE_SLUG),
							'description' => __('The number of users to show in the users liked box', WP_ULIKE_SLUG)
						),
						'users_liked_box_template' => array(
							'type'        => 'textarea',
							'default'     => '<div class="wp-ulike-likers-list">%START_WHILE%<span class="wp-ulike-liker"><a href="#" title="%USER_NAME%">%USER_AVATAR%</a></span>%END_WHILE%</div>',
							'label'       => __('Users Like Box Template', WP_ULIKE_SLUG),
							'description' => __('Allowed Variables:', WP_ULIKE_SLUG) . ' <code>%USER_AVATAR%</code> , <code>%BP_PROFILE_URL%</code> , <code>%UM_PROFILE_URL%</code> , <code>%USER_NAME%</code> , <code>%START_WHILE%</code> , <code>%END_WHILE%</code>'
						),
						'delete_logs' => array(
							'type'        => 'action',
							'label'       => __( 'Delete All Logs', WP_ULIKE_SLUG ),
							'description' => __( 'You Are About To Delete All Likes Logs. This Action Is Not Reversible.', WP_ULIKE_SLUG),
							'action'      => 'wp_ulike_delete_all_logs'
						),
						'delete_data' => array(
							'type'        => 'action',
							'label'       => __( 'Delete All Data', WP_ULIKE_SLUG ),
							'description' => __( 'You Are About To Delete All Likes Data. This Action Is Not Reversible.', WP_ULIKE_SLUG),
							'action'      => 'wp_ulike_delete_all_data'
						)
					)
				);//end wp_ulike_buddypress
				break;

			case 'customizer':
				$result = array(
					'title'  => '<i class="dashicons-before dashicons-art"></i>' . ' ' . __( 'Customize',WP_ULIKE_SLUG),
					'fields' => array(
						'custom_style' => array(
							'type'          => 'checkbox',
							'default'       => 0,
							'label'         => __('Custom Style', WP_ULIKE_SLUG),
							'checkboxlabel' => __('Activate', WP_ULIKE_SLUG),
							'attributes'    => array(
								'class' => 'wp_ulike_custom_style_activation'
							),
							'description' 	=> __('Active this option to see the custom style settings.', WP_ULIKE_SLUG)
						),
						'btn_bg' => array(
							'type'        => 'color',
							'label'       => __('Button style', WP_ULIKE_SLUG),
							'description' => __('Background', WP_ULIKE_SLUG)
						),
						'btn_border' => array(
							'type'        => 'color',
							'description' => __('Border Color', WP_ULIKE_SLUG)
						),
						'btn_color' => array(
							'type'        => 'color',
							'description' => __('Text Color', WP_ULIKE_SLUG)
						),
						'counter_bg' => array(
							'type'        => 'color',
							'label'       => __( 'Counter Style', WP_ULIKE_SLUG),
							'description' => __('Background', WP_ULIKE_SLUG)
						),
						'counter_border' => array(
							'type'        => 'color',
							'description' => __('Border Color', WP_ULIKE_SLUG)
						),
						'counter_color' => array(
							'type'        => 'color',
							'description' => __('Text Color', WP_ULIKE_SLUG)
						),
						'loading_animation' => array(
							'type'        => 'media',
							'label'       => __( 'Loading Animation', WP_ULIKE_SLUG) . ' (.GIF)',
							'description' => __( 'Best size: 16x16',WP_ULIKE_SLUG)
						),
						'custom_css' => array(
							'type'  => 'textarea',
							'label' => __('Custom CSS', WP_ULIKE_SLUG),
						)
					)
				);//end wp_ulike_customize
			break;
		}

		return $result;
	}
}


/*******************************************************
  Posts
*******************************************************/

/**
 * Display Like button for posts
 *
 * @author       	Alimir
 * @param           String 	$type
 * @param           Array 	$args
 * @since           1.0
 * @return			String
 */
if( ! function_exists( 'wp_ulike' ) ){
	function wp_ulike( $type = 'get', $args = array() ) {
		//global variables
		global $post;

		$post_ID       = isset( $args['id'] ) ? $args['id'] : $post->ID;
		$get_post_meta = get_post_meta( $post_ID, '_liked', true );
		$get_like      = empty( $get_post_meta ) ? 0 : $get_post_meta;
		$attributes    = apply_filters( 'wp_ulike_posts_add_attr', null );
		$style         = wp_ulike_get_setting( 'wp_ulike_posts', 'theme', 'wpulike-default' );
		$button_type   = wp_ulike_get_setting( 'wp_ulike_general', 'button_type', 'image' );

		//Main data
		$defaults      = array(
			"id"            => $post_ID,				//Post ID
			"get_like"      => $get_like,				//Number Of Likes
			"method"        => 'likeThis',				//JavaScript method
			"setting"       => 'wp_ulike_posts',		//Setting Key
			"type"          => 'post',					//Function type (post/process)
			"table"         => 'ulike',					//posts table
			"column"        => 'post_id',				//ulike table column name
			"key"           => '_liked',				//meta key
			"cookie"        => 'liked-',				//Cookie Name
			"slug"          => 'post',					//Slug Name
			"style"         => $style,					//Get Default Theme
			"attributes"    => $attributes,				//Get Attributes Filter
			"wrapper_class" => '',						//Extra Wrapper class
			"button_type"   => $button_type				//Button Type
		);

		$parsed_args = wp_parse_args( $args, $defaults );
		// Output templayte
		$output      = wp_ulike_display_button( $parsed_args, wp_ulike_get_setting( 'wp_ulike_posts', 'only_registered_users' ) );
		// Select retrun or print
        if( $type === 'put' ) {
        	return $output;
        } else {
        	echo $output;
        }

	}
}

/**
 * Check wp ulike callback
 *
 * @author       	Alimir
 * @param           Array 	$options
 * @param           Array   $args
 * @since           1.9
 * @return			boolean
 */
if( ! function_exists( 'is_wp_ulike' ) ){
	function is_wp_ulike( $options, $args = array() ){

		$defaults = array(
			'is_home'     => is_front_page() && is_home() && $options['home'] == '1',
			'is_single'   => is_single() && $options['single'] == '1',
			'is_page'     => is_page() && $options['page'] == '1',
			'is_archive'  => is_archive() && $options['archive'] == '1',
			'is_category' => is_category() && $options['category'] == '1',
			'is_search'   => is_search() && $options['search'] == '1',
			'is_tag'      => is_tag() && $options['tag'] == '1',
			'is_author'   => is_author() && $options['author'] == '1',
			'is_bp'       => function_exists('is_buddypress') && is_buddypress() && isset( $options['buddypress'] ) && $options['buddypress'] == '1',
			'is_bbpress'  => function_exists('is_bbpress') && is_bbpress() && isset( $options['bbpress'] ) && $options['bbpress'] == '1',
			'is_wc'       => function_exists('is_woocommerce') && is_woocommerce() && isset( $options['woocommerce'] ) && $options['woocommerce'] == '1',
		);

		$parsed_args = wp_parse_args( $args, $defaults );

		foreach ( $parsed_args as $key => $value ) {
			if( $value ) {
				return false;
			}
		}

		return true;
	}
}


/**
 * Get Single Post likes number
 *
 * @author       	Alimir
 * @param           Integer $post_ID
 * @since           1.7
 * @return          String
 */
if( ! function_exists( 'wp_ulike_get_post_likes' ) ){
	function wp_ulike_get_post_likes($post_ID){
		$val = get_post_meta($post_ID, '_liked', true);
		return wp_ulike_format_number($val);
	}
}

/**
 * Calculate rating value by user logs & date_time
 *
 * @author       	Alimir
 * @param           Integer $post_ID
 * @param           Boolean $is_decimal
 * @since           2.7
 * @return          String
 */
if( ! function_exists( 'wp_ulike_get_rating_value' ) ){
	function wp_ulike_get_rating_value($post_ID, $is_decimal = true){
		global $wpdb;
		if (false === ($rating_value = wp_cache_get($cache_key = 'get_rich_rating_value_' . $post_ID, $cache_group = 'wp_ulike'))) {
			//get the average, likes count & date_time columns by $post_ID
			$request =  "SELECT
							FORMAT(
								(
								SELECT
									AVG(counted.total)
								FROM
									(
									SELECT
										COUNT(*) AS total
									FROM
										".$wpdb->prefix."ulike AS ulike
									GROUP BY
										ulike.post_id
								) AS counted
							),
							0
							) AS average,
							COUNT(ulike.post_id) AS counter,
							posts.post_date AS post_date
						FROM
							".$wpdb->prefix."ulike AS ulike
						JOIN
							".$wpdb->prefix."posts AS posts
						ON
							ulike.post_id = ".$post_ID." AND posts.ID = ulike.post_id;";
			//get columns in a row
			$likes 	= $wpdb->get_row($request);
			$avg 	= $likes->average;
			$count 	= $likes->counter;
			$date 	= strtotime($likes->post_date);

			// if there is no log data, set $rating_value = 5
			if( $count == 0 || $avg == 0 ){
				$rating_value = 5;
			} else {
				$decimal = 0;
				if( $is_decimal ){
					list( $whole, $decimal ) = explode( '.', number_format( ( $count*100 / ( $avg * 2 ) ), 1 ) );
					$decimal = (int)$decimal;
				}
				if( $date > strtotime('-1 month')) {
					if($count < $avg) $rating_value = 4 + ".$decimal";
					else $rating_value = 5;
				} else if(($date <= strtotime('-1 month')) && ($date > strtotime('-6 month'))) {
					if($count < $avg) $rating_value = 3 + ".$decimal";
					else if(($count >= $avg) && ($count < ($avg*3/2))) $rating_value = 4 + ".$decimal";
					else $rating_value = 5;
				} else {
					if($count < ($avg/2)) $rating_value = 1 + ".$decimal";
					else if(($count >= ($avg/2)) && ($count < $avg)) $rating_value = 2 + ".$decimal";
					else if(($count >= $avg) && ($count < ($avg*3/2))) $rating_value = 3 + ".$decimal";
					else if(($count >= ($avg*3/2)) && ($count < ($avg*2))) $rating_value = 4 + ".$decimal";
					else $rating_value = 5;
				}
			}

			wp_cache_add($cache_key, $rating_value, $cache_group, HOUR_IN_SECONDS);
		}

		return apply_filters( 'wp_ulike_rating_value', $rating_value, $post_ID );
	}
}


/*******************************************************
  Comments
*******************************************************/

/**
 * wp_ulike_comments function for comments like/unlike display
 *
 * @author       	Alimir
 * @param           String 	$type
 * @param           Array 	$args
 * @since           1.6
 * @return			String
 */
if( ! function_exists( 'wp_ulike_comments' ) ){
	function wp_ulike_comments( $type = 'get', $args = array() ) {

		$comment_ID    = isset( $args['id'] ) ? $args['id'] : get_comment_ID();
		$comment_meta  = get_comment_meta( $comment_ID, '_commentliked', true );
		$get_like      = empty( $comment_meta ) ? 0 : $comment_meta;
		$attributes    = apply_filters( 'wp_ulike_comments_add_attr', null );
		$style         = wp_ulike_get_setting( 'wp_ulike_comments', 'theme', 'wpulike-default' );
		$button_type   = wp_ulike_get_setting( 'wp_ulike_general', 'button_type', 'image' );

		//Main Data
		$defaults      = array(
			"id"            => $comment_ID,				//Comment ID
			"get_like"      => $get_like,				//Number Of Likes
			"method"        => 'likeThisComment',		//JavaScript method
			"setting"       => 'wp_ulike_comments',		//Setting Key
			"type"          => 'post',					//Function type (post/process)
			"table"         => 'ulike_comments',		//Comments table
			"column"        => 'comment_id',			//ulike_comments table column name
			"key"           => '_commentliked',			//meta key
			"cookie"        => 'comment-liked-',		//Cookie Name
			"slug"          => 'comment',				//Slug Name
			"style"         => $style,					//Get Default Theme
			"attributes"    => $attributes,				//Get Attributes Filter
			"wrapper_class" => '',						//Extra Wrapper class
			"button_type"   => $button_type				//Button Type
		);

		$parsed_args = wp_parse_args( $args, $defaults );
		// Output templayte
		$output      = wp_ulike_display_button( $parsed_args, wp_ulike_get_setting( 'wp_ulike_comments', 'only_registered_users' ) );
		// Select retrun or print
        if( $type === 'put' ) {
        	return $output;
        } else {
        	echo $output;
        }

	}
}

/**
 * Get the number of likes on a single comment
 *
 * @author          Alimir & Wacław Jacek
 * @param           Integer $commentID
 * @since           2.5
 * @return          String
 */
if( ! function_exists( 'wp_ulike_get_comment_likes' ) ){
	function wp_ulike_get_comment_likes( $comment_ID ){
		$val = get_comment_meta($comment_ID, '_commentliked', true);
		return wp_ulike_format_number($val);
	}
}

/*******************************************************
  BuddyPress
*******************************************************/

/**
 * wp_ulike_buddypress function for activities like/unlike display
 *
 * @author       	Alimir
 * @param           String 	$type
 * @param           Array 	$args
 * @since           1.7
 * @return			String
 */
if( ! function_exists( 'wp_ulike_buddypress' ) ){
	function wp_ulike_buddypress( $type = 'get', $args = array() ) {

        if ( bp_get_activity_comment_id() != null ){
			$activityID 	= isset( $args['id'] ) ? $args['id'] : bp_get_activity_comment_id();
		} else {
			$activityID 	= isset( $args['id'] ) ? $args['id'] : bp_get_activity_id();
		}

		$bp_get_meta   = bp_activity_get_meta($activityID, '_activityliked');
		$get_like      = empty( $bp_get_meta ) ? 0 : $bp_get_meta;
		$attributes    = apply_filters( 'wp_ulike_activities_add_attr', null );
		$style         = wp_ulike_get_setting( 'wp_ulike_buddypress', 'theme', 'wpulike-default' );
		$button_type   = wp_ulike_get_setting( 'wp_ulike_general', 'button_type', 'image' );

		//Main Data
		$defaults      = array(
			"id"            => $activityID,				//Activity ID
			"get_like"      => $get_like,				//Number Of Likes
			"method"        => 'likeThisActivity',		//JavaScript method
			"setting"       => 'wp_ulike_buddypress',	//Setting Key
			"type"          => 'post',					//Function type (post/process)
			"table"         => 'ulike_activities',		//Activities table
			"column"        => 'activity_id',			//ulike_activities table column name
			"key"           => '_activityliked',		//meta key
			"cookie"        => 'activity-liked-',		//Cookie Name
			"slug"          => 'activity',				//Slug Name
			"style"         => $style,					//Get Default Theme
			"attributes"    => $attributes,				//Get Attributes Filter
			"wrapper_class" => '',						//Extra Wrapper class
			"button_type"   => $button_type				//Button Type
		);

		$parsed_args = wp_parse_args( $args, $defaults );
		// Output templayte
		$output      = wp_ulike_display_button( $parsed_args, wp_ulike_get_setting( 'wp_ulike_buddypress', 'only_registered_users' ) );
		// Select retrun or print
        if( $type === 'put' ) {
        	return $output;
        } else {
        	echo $output;
        }

	}
}

/**
 * Get auther ID by the ulike types
 *
 * @author       	Alimir
 * @param           Integer $cp_ID (Post/Comment/... ID)
 * @param           String 	$type (Get ulike Type)
 * @since           2.5
 * @return          String
 */
if( ! function_exists( 'wp_ulike_get_auhtor_id' ) ){
	function wp_ulike_get_auhtor_id($cp_ID,$type) {
		if($type == '_liked' || $type == '_topicliked'){
			$post_tmp = get_post($cp_ID);
			return $post_tmp->post_author;
		}
		else if($type == '_commentliked'){
			$comment = get_comment( $cp_ID );
			return $comment->user_id;
		}
		else if( $type == '_activityliked' ){
			$activity = bp_activity_get_specific( array( 'activity_ids' => $cp_ID, 'display_comments'  => true ) );
			return $activity['activities'][0]->user_id;
		}
		else return;
	}
}

/**
 * Wrapper for bbp_format_buddypress_notifications function as it is not returning $action
 *
 * @author       	Alimir
 * @since           2.5.1
 * @return          String
 */
if( ! function_exists( 'wp_ulike_bbp_format_buddypress_notifications' ) ) {
	function wp_ulike_bbp_format_buddypress_notifications( $action, $item_id, $secondary_item_id, $total_items, $format = 'string' ) {

		if ( ! defined( 'BP_VERSION' ) ) {
			return;
		}

		$result = bbp_format_buddypress_notifications(
			$action,
			$item_id,
			$secondary_item_id,
			$total_items,
			$format
		);

		if ( ! $result ) {
			$result = $action;
		}

		return $result;
	}
}


/**
 * Check the buddypress notification component existence
 *
 * @author       	Alimir
 * @since           2.5.1
 * @return          integer
 */
if( ! function_exists( 'wp_ulike_bbp_is_component_exist' ) ) {
	function wp_ulike_bbp_is_component_exist( $component_name ){
		global $wpdb;
		$bp = buddypress();

		return $wpdb->get_var(
				$wpdb->prepare(
					"SELECT COUNT(*) FROM {$bp->notifications->table_name} WHERE component_action = %s",
					$component_name
				)
			);
	}
}

/*******************************************************
  bbPress
*******************************************************/
/**
 * wp_ulike_bbpress function for topics like/unlike display
 *
 * @author       	Alimir
 * @param           String 	$type
 * @param           Array 	$args
 * @since           2.2
 * @return			String
 */
if( ! function_exists( 'wp_ulike_bbpress' ) ){
	function wp_ulike_bbpress( $type = 'get', $args = array() ) {
		//global variables
		global $post;

        //Thanks to @Yehonal for this commit
		$replyID       = bbp_get_reply_id();
		$post_ID       = !$replyID ? $post->ID : $replyID;
		$post_ID       = isset( $args['id'] ) ? $args['id'] : $post_ID;

		$get_post_meta = get_post_meta( $post_ID, '_topicliked', true );
		$get_like      = empty( $get_post_meta ) ? 0 : $get_post_meta;
		$attributes    = apply_filters( 'wp_ulike_topics_add_attr', null );
		$style         = wp_ulike_get_setting( 'wp_ulike_bbpress', 'theme', 'wpulike-default' );
		$button_type   = wp_ulike_get_setting( 'wp_ulike_general', 'button_type', 'image' );

		//Main Data
		$defaults      = array(
			"id"            => $post_ID,				//Post ID
			"get_like"      => $get_like,				//Number Of Likes
			"method"        => 'likeThisTopic',			//JavaScript method
			"setting"       => 'wp_ulike_bbpress',		//Setting Key
			"type"          => 'post',					//Function type (post/process)
			"table"         => 'ulike_forums',			//posts table
			"column"        => 'topic_id',				//ulike table column name
			"key"           => '_topicliked',			//meta key
			"cookie"        => 'topic-liked-',			//Cookie Name
			"slug"          => 'topic',					//Slug Name
			"style"         => $style,					//Get Default Theme
			"attributes"    => $attributes,				//Get Attributes Filter
			"wrapper_class" => '',						//Extra Wrapper class
			"button_type"   => $button_type,			//Button Type
		);

		$parsed_args = wp_parse_args( $args, $defaults );
		// Output templayte
		$output      = wp_ulike_display_button( $parsed_args, wp_ulike_get_setting( 'wp_ulike_bbpress', 'only_registered_users' ) );
		// Select retrun or print
        if( $type === 'put' ) {
        	return $output;
        } else {
        	echo $output;
        }

	}
}

/*******************************************************
  General
*******************************************************/

/**
 * Convert numbers of Likes with string (kilobyte) format
 *
 * @author       	Alimir
 * @param           Array  		$parsed_args
 * @param           Integer 	$only_registered_users
 * @since           3.4
 * @return          String
 */
if( ! function_exists( 'wp_ulike_get_post_settings_by_type' ) ){
	function wp_ulike_get_post_settings_by_type( $post_type, $post_ID ){
		switch ( $post_type ) {
			case 'likeThis':
				$settings = array(
					'get_meta_data' => get_post_meta( $post_ID, '_liked', true ),
					'setting_key'   => 'wp_ulike_posts',
					'table_name'    => 'ulike',
					'column_name'   => 'post_id',
					'meta_key'      => '_liked',
					'cookie_name'   => 'liked-'
				);
				break;

			case 'likeThisComment':
				$settings = array(
					'get_meta_data' => get_comment_meta( $post_ID, '_commentliked', true ),
					'setting_key'   => 'wp_ulike_comments',
					'table_name'    => 'ulike_comments',
					'column_name'   => 'comment_id',
					'meta_key'      => '_commentliked',
					'cookie_name'   => 'comment-liked-'
				);
				break;

			case 'likeThisActivity':
				$settings = array(
					'get_meta_data' => bp_activity_get_meta( $post_ID, '_activityliked' ),
					'setting_key'   => 'wp_ulike_buddypress',
					'table_name'    => 'ulike_activities',
					'column_name'   => 'activity_id',
					'meta_key'      => '_activityliked',
					'cookie_name'   => 'activity-liked-',
				);
				break;

			case 'likeThisTopic':
				$settings = array(
					'get_meta_data' => get_post_meta( $post_ID, '_topicliked', true ),
					'setting_key'   => 'wp_ulike_bbpress',
					'table_name'    => 'ulike_forums',
					'column_name'   => 'topic_id',
					'meta_key'      => '_topicliked',
					'cookie_name'   => 'topic-liked-'
				);
				break;

			default:
				$settings = array();
		}

		return $settings;
	}
}

/**
 * Get template between
 *
 * @author       	Alimir
 * @param           String $string
 * @param           String $start
 * @param           String $end
 * @since           2.0
 * @return			String
 */
if( ! function_exists( 'wp_ulike_get_likers_list_per_post' ) ){
	function wp_ulike_get_likers_list_per_post( $table_name, $column_name, $post_ID, $limit_num = 10 ){
		// Global wordpress database object
		global $wpdb;
		// Get likers list
		return $wpdb->get_results( "SELECT user_id
						FROM   ".$wpdb->prefix."$table_name
						WHERE  $column_name = '$post_ID'
						       AND status   = 'like'
						       AND user_id BETWEEN 1 AND 999999
						GROUP  BY user_id
						LIMIT  $limit_num"
					);
	}
}


/**
 * Get likers box template
 *
 * @author       	Alimir
 * @param           String $table_name
 * @param           String $column_name
 * @param           String $post_ID
 * @param           String $setting_key
 * @since           2.0
 * @return			String
 */
if( ! function_exists( 'wp_ulike_get_likers_template' ) ){
	function wp_ulike_get_likers_template( $table_name, $column_name, $post_ID, $setting_key ){

		// Get user's limit number value
		$limit_num  = wp_ulike_get_setting( $setting_key, 'number_of_users', 10 );
		// Get likers list
		$get_users  = wp_ulike_get_likers_list_per_post( $table_name, $column_name, $post_ID, $limit_num );
		// Bulk user list
		$users_list = '';

		if( ! empty( $get_users ) ) {

			// Get likers html template
			$get_template 	= wp_ulike_get_setting( $setting_key, 'users_liked_box_template', '<div class="wp-ulike-likers-list">%START_WHILE%<span class="wp-ulike-liker"><a href="#" title="%USER_NAME%">%USER_AVATAR%</a></span>%END_WHILE%</div>' );

			$inner_template = wp_ulike_get_template_between( $get_template, "%START_WHILE%", "%END_WHILE%" );

			foreach ( $get_users as $get_user ) {
				$user_info 		= get_userdata( $get_user->user_id );
				$out_template 	= $inner_template;
				if ( $user_info ):
					if( strpos( $out_template, '%USER_AVATAR%' ) !== false ) {
						$avatar_size 	= wp_ulike_get_setting( $setting_key, 'users_liked_box_avatar_size' );
						$USER_AVATAR 	= get_avatar( $user_info->user_email, $avatar_size, '' , 'avatar' );
						$out_template 	= str_replace( "%USER_AVATAR%", $USER_AVATAR, $out_template );
					}
					if( strpos( $out_template, '%USER_NAME%' ) !== false) {
						$USER_NAME 		= $user_info->display_name;
						$out_template 	= str_replace( "%USER_NAME%", $USER_NAME, $out_template );
					}
					if( strpos( $out_template, '%UM_PROFILE_URL%' ) !== false && function_exists('um_fetch_user') ) {
						global $ultimatemember;
						um_fetch_user( $user_info->ID );
						$UM_PROFILE_URL = um_user_profile_url();
						$out_template 	= str_replace( "%UM_PROFILE_URL%", $UM_PROFILE_URL, $out_template );
					}
					if( strpos( $out_template, '%BP_PROFILE_URL%' ) !== false && function_exists('bp_core_get_user_domain') ) {
						$BP_PROFILE_URL = bp_core_get_user_domain( $user_info->ID );
						$out_template 	= str_replace( "%BP_PROFILE_URL%", $BP_PROFILE_URL, $out_template );
					}
					$users_list .= $out_template;
				endif;
			}

			if( ! empty( $users_list ) ) {
				return wp_ulike_put_template_between( $get_template, $users_list, "%START_WHILE%", "%END_WHILE%" );
			}
		}

		return NULL;
	}
}

/**
 * Get template between
 *
 * @author       	Alimir
 * @param           String $string
 * @param           String $start
 * @param           String $end
 * @since           2.0
 * @return			String
 */
if( ! function_exists( 'wp_ulike_get_template_between' ) ){
	function wp_ulike_get_template_between( $string, $start, $end ){
		$string 	= " ".$string;
		$ini 		= strpos($string,$start);
		if ( $ini == 0 ){
			return "";
		}
		$ini 		+= strlen($start);
		$len 		= strpos($string,$end,$ini) - $ini;

		return substr( $string, $ini, $len );
	}
}

/**
 * Put template between
 *
 * @author       	Alimir
 * @param           String $string
 * @param           String $inner_string
 * @param           String $start
 * @param           String $end
 * @since           2.0
 * @return			String
 */
if( ! function_exists( 'wp_ulike_put_template_between' ) ){
	function wp_ulike_put_template_between( $string, $inner_string, $start, $end ){
		$string 	= " ".$string;
		$ini 		= strpos($string,$start);
		if ($ini == 0){
			return "";
		}

		$ini 		+= strlen($start);
		$len		= strpos($string,$end,$ini) - $ini;
		$newstr 	= substr_replace($string,$inner_string,$ini,$len);

		return str_replace(
			array( "%START_WHILE%", "%END_WHILE%" ),
			array( "", "" ),
			$newstr
		);
	}
}

/**
 * Convert numbers of Likes with string (kilobyte) format
 *
 * @author       	Alimir
 * @param           Array  		$parsed_args
 * @param           Integer 	$only_registered_users
 * @since           3.4
 * @return          String
 */
if( ! function_exists( 'wp_ulike_display_button' ) ){
	function wp_ulike_display_button( array $parsed_args, $only_registered_users ){
		global $wp_ulike_class;

		if( $only_registered_users != '1' || ( $only_registered_users == '1' && is_user_logged_in() ) ) {
			// Return ulike template
			return $wp_ulike_class->wp_get_ulike( $parsed_args );
		} else {
			if( wp_ulike_get_setting( 'wp_ulike_general', 'login_type') === 'button' ){
				return $wp_ulike_class->get_template( $parsed_args, 0 );
			} else {
				return apply_filters( 'wp_ulike_login_alert_template',
					sprintf( '<p class="alert alert-info fade in" role="alert">%s<a href="%s">%s</a></p>',
					__('You need to login in order to like this post: ',WP_ULIKE_SLUG),
					wp_login_url( get_permalink() ),
					__('click here',WP_ULIKE_SLUG)
					)
				);
			}
		}
	}
}

/**
 * Get custom style setting from customize options
 *
 * @author       	Alimir
 * @since           1.3
 * @return          Void (Print new CSS styles)
 */
if( ! function_exists( 'wp_ulike_get_custom_style' ) ){
	function wp_ulike_get_custom_style( $return_style = null ){

		// Like Icon
		if( $get_like_icon = wp_get_attachment_url( wp_ulike_get_setting( 'wp_ulike_general', 'button_url' ) ) ) {
			$return_style .= '.wp_ulike_btn.wp_ulike_put_image:after { background-image: url('.$get_like_icon.') !important; }';
		}

		// Unlike Icon
		if( $get_like_icon = wp_get_attachment_url( wp_ulike_get_setting( 'wp_ulike_general', 'button_url_u' ) ) ) {
			$return_style .= '.wp_ulike_btn.wp_ulike_put_image.image-unlike:after { background-image: url('.$get_like_icon.') !important; filter: none; }';
		}

		if( wp_ulike_get_setting( 'wp_ulike_customize', 'custom_style' ) ) {

			//get custom options
			$customstyle   = get_option( 'wp_ulike_customize' );
			$btn_style     = '';
			$counter_style = '';
			$before_style  = '';

			// Button Style
			if( isset( $customstyle['btn_bg'] ) && ! empty( $customstyle['btn_bg'] ) ) {
				$btn_style .= "background-color:".$customstyle['btn_bg'].";";
			}
			if( isset( $customstyle['btn_border'] ) && ! empty( $customstyle['btn_border'] ) ) {
				$btn_style .= "box-shadow: 0 0 0 1px ".$customstyle['btn_border']." inset; ";
			}
			if( isset( $customstyle['btn_color'] ) && ! empty( $customstyle['btn_color'] ) ) {
				$btn_style .= "color:".$customstyle['btn_color'].";";
			}

			if( $btn_style != '' ){
				$return_style .= '.wpulike-default .wp_ulike_btn, .wpulike-default .wp_ulike_btn:hover, #bbpress-forums .wpulike-default .wp_ulike_btn, #bbpress-forums .wpulike-default .wp_ulike_btn:hover{'.$btn_style.'}.wpulike-heart .wp_ulike_general_class{'.$btn_style.'}';
			}

			// Counter Style
			if( isset( $customstyle['counter_bg'] ) && ! empty( $customstyle['counter_bg'] ) ) {
				$counter_style .= "background-color:".$customstyle['counter_bg'].";";
			}
			if( isset( $customstyle['counter_border'] ) && ! empty( $customstyle['counter_border'] ) ) {
				$counter_style .= "box-shadow: 0 0 0 1px ".$customstyle['counter_border']." inset; ";
				$before_style  = "background-color:".$customstyle['counter_bg']."; border-color:transparent; border-bottom-color:".$customstyle['counter_border']."; border-left-color:".$customstyle['counter_border'].";";
			}
			if( isset( $customstyle['counter_color'] ) && ! empty( $customstyle['counter_color'] ) ) {
				$counter_style .= "color:".$customstyle['counter_color'].";";
			}

			if( $counter_style != '' ){
				$return_style .= '.wpulike-default .count-box,.wpulike-default .count-box{'.$counter_style.'}.wpulike-default .count-box:before{'.$before_style.'}';
			}

			// Loading Spinner
			if( isset( $customstyle['loading_animation'] ) && ! empty( $customstyle['loading_animation'] ) ) {
				$return_style .= '.wpulike .wp_ulike_is_loading .wp_ulike_btn, #buddypress .activity-content .wpulike .wp_ulike_is_loading .wp_ulike_btn, #bbpress-forums .bbp-reply-content .wpulike .wp_ulike_is_loading .wp_ulike_btn {background-image: url('.wp_get_attachment_url( $customstyle['loading_animation'] ).') !important;}';
			}

			// Custom Styles
			if( isset( $customstyle['custom_css'] ) && ! empty( $customstyle['custom_css'] ) ) {
				$return_style .= $customstyle['custom_css'];
			}

		}

		return $return_style;
	}

}

/**
 * Convert numbers of Likes with string (kilobyte) format
 *
 * @author       	Alimir
 * @param           Integer $num (get like number)
 * @since           1.5
 * @return          String
 */
if( ! function_exists( 'wp_ulike_format_number' ) ){
	function wp_ulike_format_number($num){
		$plus = $num != 0 ? '+' : '';
		if ($num >= 1000 && wp_ulike_get_setting( 'wp_ulike_general', 'format_number' ) == '1')
		$value = round($num/1000, 2) . 'K' . $plus;
		else
		$value = $num . $plus;
		$value = apply_filters( 'wp_ulike_format_number', $value, $num, $plus);
		return $value;
	}
}


/**
 * Date in localized format
 *
 * @author       	Alimir
 * @param           String (Date)
 * @since           2.3
 * @return          String
 */
if( ! function_exists( 'wp_ulike_date_i18n' ) ){
	function wp_ulike_date_i18n($date){
		return date_i18n(
			get_option( 'date_format' ) . ' @ ' . get_option( 'time_format' ),
			strtotime($date)
		);
	}
}

/**
 * Convert IP to a integer value
 *
 * @author       	Alimir
 * @param           String $user_ip
 * @since           3.4
 * @return          String
 */
if( ! function_exists( 'wp_ulike_generate_user_id' ) ){
	function wp_ulike_generate_user_id( $user_ip ) {

		if( filter_var( $user_ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {
		    return ip2long( $user_ip );
		} else {
		    $binary_val = '';
		    foreach ( unpack( 'C*', inet_pton( $user_ip ) ) as $byte ) {
		        $binary_val .= decbin( $byte );
		    }
		    return base_convert( ltrim( $binary_val, '0' ), 2, 10 );
		}

	}
}


if( ! function_exists( 'wp_ulike_is_user_liked' ) ) {
	/**
	 * A simple function to check if user has been liked post or not
	 *
	 * @param integer $item_ID
	 * @param integer $user_ID
	 * @param string $type
	 * @return bool
	 */
	function wp_ulike_is_user_liked( $item_ID, $user_ID,  $type = 'likeThis' ) {
		global $wpdb;
		// Get ULike settings
		$get_settings = wp_ulike_get_post_settings_by_type( $type, $item_ID );

		$query  = sprintf( "
			SELECT COUNT(*)
			FROM %s
			WHERE `%s` = %s
			AND `status` = 'like'
			And `user_id` = %s",
			esc_sql( $wpdb->prefix . $get_settings['table_name'] ),
			esc_html( $get_settings['column_name'] ),
			esc_html( $item_ID ),
			esc_html( $user_ID )
		);

		return $wpdb->get_var( $query );
	}
}

/*******************************************************
  Templates
*******************************************************/

/**
 * Create simple default template
 *
 * @author       	Alimir
 * @since           2.8
 * @return			Void
 */
if( ! function_exists( 'wp_ulike_set_default_template' ) ){
	function wp_ulike_set_default_template( array $wp_ulike_template ){
		//This function will turn output buffering on
		ob_start();
		do_action( 'wp_ulike_before_template' );
		// Extract input array
		extract( $wp_ulike_template );
	?>
		<div class="wpulike wpulike-default <?php echo $wrapper_class; ?>" <?php echo $attributes; ?>>
			<div class="<?php echo $general_class; ?>">
				<button type="button"
					data-ulike-id="<?php echo $ID; ?>"
					data-ulike-nonce="<?php echo wp_create_nonce( $type . $ID ); ?>"
					data-ulike-type="<?php echo $type; ?>"
					data-ulike-status="<?php echo $status; ?>" class="<?php echo $button_class; ?>">
					<?php
						if($button_type == 'text'){
							echo '<span>' . $button_text . '</span>';
						}
					?>
				</button>
				<?php echo $counter; ?>
			</div>
		<?php
			do_action( 'wp_ulike_inside_template', $wp_ulike_template );
		?>
		</div>
	<?php
		do_action( 'wp_ulike_after_template' );
		return ob_get_clean(); // data is now in here
	}
}

	/**
	 * Create simple heart template
	 *
	 * @author       	Alimir
	 * @since           2.8
	 * @return			Void
	 */
if( ! function_exists( 'wp_ulike_set_simple_heart_template' ) ){
	function wp_ulike_set_simple_heart_template( array $wp_ulike_template ){
		//This function will turn output buffering on
		ob_start();
		do_action( 'wp_ulike_before_template' );
		// Extract input array
		extract( $wp_ulike_template );
	?>
		<div class="wpulike wpulike-heart <?php echo $wrapper_class; ?>" <?php echo $attributes; ?>>
			<div class="<?php echo $general_class; ?>">
				<button type="button"
					data-ulike-id="<?php echo $ID; ?>"
					data-ulike-nonce="<?php echo wp_create_nonce( $type  . $ID ); ?>"
					data-ulike-type="<?php echo $type; ?>"
					data-ulike-status="<?php echo $status; ?>" class="<?php echo $button_class; ?>">
					<?php
						if($button_type == 'text'){
							echo '<span>' . $button_text . '</span>';
						}
					?>
				</button>
				<?php echo $counter; ?>
			</div>
		<?php
			do_action( 'wp_ulike_inside_template', $wp_ulike_template );
		?>
		</div>
	<?php
		do_action( 'wp_ulike_after_template' );
		return ob_get_clean(); // data is now in here
	}
}

/**
 * Create Robeen (Animated Heart) template
 *
 * @author       	Alimir
 * @since           2.8
 * @return			Void
 */
if( ! function_exists( 'wp_ulike_set_robeen_template' ) ){
	function wp_ulike_set_robeen_template( array $wp_ulike_template ){
		//This function will turn output buffering on
		ob_start();
		do_action( 'wp_ulike_before_template' );
		// Extract input array
		extract( $wp_ulike_template );
	?>
		<div class="wpulike wpulike-robeen <?php echo $wrapper_class; ?>" <?php echo $attributes; ?>>
			<div class="<?php echo $general_class; ?>">
					<label title="<?php echo esc_attr( 'like this' . $type ); ?>">
					<input 	type="checkbox"
							data-ulike-id="<?php echo $ID; ?>"
							data-ulike-nonce="<?php echo wp_create_nonce( $type . $ID ); ?>"
							data-ulike-type="<?php echo $type; ?>"
							data-ulike-status="<?php echo $status; ?>"
							class="<?php echo $button_class; ?>"
							<?php echo  $status == 2  ? 'checked="checked"' : ''; ?> />

					<svg class="heart-svg" viewBox="467 392 58 57" xmlns="http://www.w3.org/2000/svg">
					    <g class="Group" fill="none" fill-rule="evenodd" transform="translate(467 392)">
					        <path d="M29.144 20.773c-.063-.13-4.227-8.67-11.44-2.59C7.63 28.795 28.94 43.256 29.143 43.394c.204-.138 21.513-14.6 11.44-25.213-7.214-6.08-11.377 2.46-11.44 2.59z" class="heart" fill="#AAB8C2" />
					        <circle class="main-circ" fill="#E2264D" opacity="0" cx="29.5" cy="29.5" r="1.5" />
					        <g class="grp7" opacity="0" transform="translate(7 6)">
					            <circle class="oval1" fill="#9CD8C3" cx="2" cy="6" r="2" />
					            <circle class="oval2" fill="#8CE8C3" cx="5" cy="2" r="2" />
					        </g>
					        <g class="grp6" opacity="0" transform="translate(0 28)">
					            <circle class="oval1" fill="#CC8EF5" cx="2" cy="7" r="2" />
					            <circle class="oval2" fill="#91D2FA" cx="3" cy="2" r="2" />
					        </g>
					        <g class="grp3" opacity="0" transform="translate(52 28)">
					            <circle class="oval2" fill="#9CD8C3" cx="2" cy="7" r="2" />
					            <circle class="oval1" fill="#8CE8C3" cx="4" cy="2" r="2" />
					        </g>
					        <g class="grp2" opacity="0" transform="translate(44 6)" fill="#CC8EF5">
					            <circle class="oval2" transform="matrix(-1 0 0 1 10 0)" cx="5" cy="6" r="2" />
					            <circle class="oval1" transform="matrix(-1 0 0 1 4 0)" cx="2" cy="2" r="2" />
					        </g>
					        <g class="grp5" opacity="0" transform="translate(14 50)" fill="#91D2FA">
					            <circle class="oval1" transform="matrix(-1 0 0 1 12 0)" cx="6" cy="5" r="2" />
					            <circle class="oval2" transform="matrix(-1 0 0 1 4 0)" cx="2" cy="2" r="2" />
					        </g>
					        <g class="grp4" opacity="0" transform="translate(35 50)" fill="#F48EA7">
					            <circle class="oval1" transform="matrix(-1 0 0 1 12 0)" cx="6" cy="5" r="2" />
					            <circle class="oval2" transform="matrix(-1 0 0 1 4 0)" cx="2" cy="2" r="2" />
					        </g>
					        <g class="grp1" opacity="0" transform="translate(24)" fill="#9FC7FA">
					            <circle class="oval1" cx="2.5" cy="3" r="2" />
					            <circle class="oval2" cx="7.5" cy="2" r="2" />
					        </g>
					    </g>
					</svg>
					<?php echo $counter; ?>
					</label>
			</div>
		<?php
			do_action( 'wp_ulike_inside_template', $wp_ulike_template );
		?>
		</div>
	<?php
		do_action( 'wp_ulike_after_template' );
		return ob_get_clean(); // data is now in here
	}
}

/**
 * Create Animated Heart template
 *
 * @author       	Alimir
 * @since           3.6.2
 * @return			Void
 */
if( ! function_exists( 'wp_ulike_set_animated_heart_template' ) ){
	function wp_ulike_set_animated_heart_template( array $wp_ulike_template ){
		//This function will turn output buffering on
		ob_start();
		do_action( 'wp_ulike_before_template' );
		// Extract input array
		extract( $wp_ulike_template );
	?>
		<div class="wpulike wpulike-animated-heart <?php echo $wrapper_class; ?>" <?php echo $attributes; ?>>
			<div class="<?php echo $general_class; ?>">
				<button type="button"
					data-ulike-id="<?php echo $ID; ?>"
					data-ulike-nonce="<?php echo wp_create_nonce( $type  . $ID ); ?>"
					data-ulike-type="<?php echo $type; ?>"
					data-ulike-append="<?php echo htmlspecialchars( '<svg class="wpulike-svg-heart wpulike-svg-heart-pop one" viewBox="0 0 32 29.6"><path d="M23.6,0c-3.4,0-6.3,2.7-7.6,5.6C14.7,2.7,11.8,0,8.4,0C3.8,0,0,3.8,0,8.4c0,9.4,9.5,11.9,16,21.2c6.1-9.3,16-12.1,16-21.2C32,3.8,28.2,0,23.6,0z"/></svg><svg class="wpulike-svg-heart wpulike-svg-heart-pop two" viewBox="0 0 32 29.6"><path d="M23.6,0c-3.4,0-6.3,2.7-7.6,5.6C14.7,2.7,11.8,0,8.4,0C3.8,0,0,3.8,0,8.4c0,9.4,9.5,11.9,16,21.2c6.1-9.3,16-12.1,16-21.2C32,3.8,28.2,0,23.6,0z"/></svg><svg class="wpulike-svg-heart wpulike-svg-heart-pop three" viewBox="0 0 32 29.6"><path d="M23.6,0c-3.4,0-6.3,2.7-7.6,5.6C14.7,2.7,11.8,0,8.4,0C3.8,0,0,3.8,0,8.4c0,9.4,9.5,11.9,16,21.2c6.1-9.3,16-12.1,16-21.2C32,3.8,28.2,0,23.6,0z"/></svg><svg class="wpulike-svg-heart wpulike-svg-heart-pop four" viewBox="0 0 32 29.6"><path d="M23.6,0c-3.4,0-6.3,2.7-7.6,5.6C14.7,2.7,11.8,0,8.4,0C3.8,0,0,3.8,0,8.4c0,9.4,9.5,11.9,16,21.2c6.1-9.3,16-12.1,16-21.2C32,3.8,28.2,0,23.6,0z"/></svg><svg class="wpulike-svg-heart wpulike-svg-heart-pop five" viewBox="0 0 32 29.6"><path d="M23.6,0c-3.4,0-6.3,2.7-7.6,5.6C14.7,2.7,11.8,0,8.4,0C3.8,0,0,3.8,0,8.4c0,9.4,9.5,11.9,16,21.2c6.1-9.3,16-12.1,16-21.2C32,3.8,28.2,0,23.6,0z"/></svg><svg class="wpulike-svg-heart wpulike-svg-heart-pop six" viewBox="0 0 32 29.6"><path d="M23.6,0c-3.4,0-6.3,2.7-7.6,5.6C14.7,2.7,11.8,0,8.4,0C3.8,0,0,3.8,0,8.4c0,9.4,9.5,11.9,16,21.2c6.1-9.3,16-12.1,16-21.2C32,3.8,28.2,0,23.6,0z"/></svg><svg class="wpulike-svg-heart wpulike-svg-heart-pop seven" viewBox="0 0 32 29.6"><path d="M23.6,0c-3.4,0-6.3,2.7-7.6,5.6C14.7,2.7,11.8,0,8.4,0C3.8,0,0,3.8,0,8.4c0,9.4,9.5,11.9,16,21.2c6.1-9.3,16-12.1,16-21.2C32,3.8,28.2,0,23.6,0z"/></svg><svg class="wpulike-svg-heart wpulike-svg-heart-pop eight" viewBox="0 0 32 29.6"><path d="M23.6,0c-3.4,0-6.3,2.7-7.6,5.6C14.7,2.7,11.8,0,8.4,0C3.8,0,0,3.8,0,8.4c0,9.4,9.5,11.9,16,21.2c6.1-9.3,16-12.1,16-21.2C32,3.8,28.2,0,23.6,0z"/></svg><svg class="wpulike-svg-heart wpulike-svg-heart-pop nine" viewBox="0 0 32 29.6"><path d="M23.6,0c-3.4,0-6.3,2.7-7.6,5.6C14.7,2.7,11.8,0,8.4,0C3.8,0,0,3.8,0,8.4c0,9.4,9.5,11.9,16,21.2c6.1-9.3,16-12.1,16-21.2C32,3.8,28.2,0,23.6,0z"/></svg>' ); ?>"
					data-ulike-status="<?php echo $status; ?>" class="<?php echo $button_class; ?>">
					<svg class="wpulike-svg-heart wpulike-svg-heart-icon" viewBox="0 -28 512.00002 512" xmlns="http://www.w3.org/2000/svg">
					<path
						d="m471.382812 44.578125c-26.503906-28.746094-62.871093-44.578125-102.410156-44.578125-29.554687 0-56.621094 9.34375-80.449218 27.769531-12.023438 9.300781-22.917969 20.679688-32.523438 33.960938-9.601562-13.277344-20.5-24.660157-32.527344-33.960938-23.824218-18.425781-50.890625-27.769531-80.445312-27.769531-39.539063 0-75.910156 15.832031-102.414063 44.578125-26.1875 28.410156-40.613281 67.222656-40.613281 109.292969 0 43.300781 16.136719 82.9375 50.78125 124.742187 30.992188 37.394531 75.535156 75.355469 127.117188 119.3125 17.613281 15.011719 37.578124 32.027344 58.308593 50.152344 5.476563 4.796875 12.503907 7.4375 19.792969 7.4375 7.285156 0 14.316406-2.640625 19.785156-7.429687 20.730469-18.128907 40.707032-35.152344 58.328125-50.171876 51.574219-43.949218 96.117188-81.90625 127.109375-119.304687 34.644532-41.800781 50.777344-81.4375 50.777344-124.742187 0-42.066407-14.425781-80.878907-40.617188-109.289063zm0 0" />
					</svg>
				</button>
				<?php echo $counter; ?>
			</div>
		<?php
			do_action( 'wp_ulike_inside_template', $wp_ulike_template );
		?>
		</div>
	<?php
		do_action( 'wp_ulike_after_template' );
		return ob_get_clean(); // data is now in here
	}
}