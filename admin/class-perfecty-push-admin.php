<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/rwngallego
 * @since      1.0.0
 *
 * @package    Perfecty_Push
 * @subpackage Perfecty_Push/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Perfecty_Push
 * @subpackage Perfecty_Push/admin
 * @author     Rowinson Gallego <rwn.gallego@gmail.com>
 */
class Perfecty_Push_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Perfecty_Push_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Perfecty_Push_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/perfecty-push-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Perfecty_Push_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Perfecty_Push_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/perfecty-push-admin.js', array( 'jquery' ), $this->version, false );
	}

	 /**
	  * Register the plugin page in the admin menu
	  *
	  * @since 1.0.0
	  */
	public function register_admin_menu() {
		add_menu_page(
			'Perfecty Push',
			'Perfecty Push',
			'manage_options',
			'perfecty-push',
			array( $this, 'print_dashboard_page' )
		);

		add_submenu_page(
			'perfecty-push',
			'Dashboard',
			'Dashboard',
			'manage_options',
			'perfecty-push',
			array( $this, 'print_dashboard_page' )
		);

		add_submenu_page(
			'perfecty-push',
			'Send notification',
			'Send notification',
			'manage_options',
			'perfecty-push-send-notification',
			array( $this, 'print_send_notification_page' )
		);

		add_submenu_page(
			'perfecty-push',
			'Notifications',
			'Notifications',
			'manage_options',
			'perfecty-push-notifications',
			array( $this, 'print_notifications_page' )
		);

		add_submenu_page(
			'perfecty-push',
			'Users',
			'Users',
			'manage_options',
			'perfecty-push-users',
			array( $this, 'print_users_page' )
		);

		add_submenu_page(
			'perfecty-push',
			'Settings',
			'Settings',
			'manage_options',
			'perfecty-push-options',
			array( $this, 'print_options_page' )
		);

		add_submenu_page(
			'perfecty-push',
			'About',
			'About',
			'manage_options',
			'perfecty-push-about',
			array( $this, 'print_about_page' )
		);
	}

	/**
	 * Register the plugin options
	 *
	 * @since 1.0.0
	 */
	public function register_options() {
		register_setting(
			'perfecty_group',
			'perfecty_push',
			array( $this, 'sanitize' )
		);

		add_settings_section(
			'perfecty_push_dialog_settings', // id
			'Change the appearance', // title
			array( $this, 'print_dialog_section' ), // callback
			'perfecty-push-options' // page
		);

		add_settings_field(
			'dialog_title', // id
			'Title', // title
			array( $this, 'print_dialog_title' ), // callback
			'perfecty-push-options', // page
			'perfecty_push_dialog_settings' // section
		);

		add_settings_field(
			'dialog_submit', // id
			'Continue Button', // title
			array( $this, 'print_dialog_submit' ), // callback
			'perfecty-push-options', // page
			'perfecty_push_dialog_settings' // section
		);

		add_settings_field(
			'dialog_cancel', // id
			'Cancel Button', // title
			array( $this, 'print_dialog_cancel' ), // callback
			'perfecty-push-options', // page
			'perfecty_push_dialog_settings' // section
		);

		add_settings_section(
			'perfecty_push_self_hosted_settings', // id
			'Self-hosted server', // title
			array( $this, 'print_self_hosted_section' ), // callback
			'perfecty-push-options' // page
		);

		add_settings_field(
			'vapid_public_key', // id
			'Vapid Public Key', // title
			array( $this, 'print_vapid_public_key' ), // callback
			'perfecty-push-options', // page
			'perfecty_push_self_hosted_settings' // section
		);

		add_settings_field(
			'server_url', // id
			'Server Url', // title
			array( $this, 'print_server_url' ), // callback
			'perfecty-push-options', // page
			'perfecty_push_self_hosted_settings' // section
		);
	}

	/**
	 * Execute the next broadcast batch
	 * This is a WordPress action called from wp-cron
	 *
	 * @param $notification_id int Notification ID
	 */
	public function execute_broadcast_batch( $notification_id ) {
		Perfecty_Push_Lib_Push_Server::execute_broadcast_batch( $notification_id );
	}

	/**
	 * Register the metaboxes
	 */
	public function register_metaboxes() {
		add_meta_box( 'perfecty_push_post_metabox', 'Perfecty Push Notifications', array( $this, 'display_post_metabox' ), 'post', 'side', 'high' );
	}

	/**
	 * Displays the metabox in the post
	 *
	 * @param $post object Contains the post
	 */
	public function display_post_metabox( $post ) {
		wp_nonce_field( 'perfecty_push_post_metabox', 'perfecty_push_post_metabox_nonce' );
		$send_notification = ! empty( get_post_meta( $post->ID, '_perfecty_push_send_on_publish', true ) );

		require_once plugin_dir_path( __FILE__ ) . 'partials/perfecty-push-admin-post-metabox.php';
	}

	/**
	 * Actions triggered when saving posts
	 * Hook triggered when saving the posts
	 *
	 * @param $post_id
	 */
	public function on_save_post( $post_id ) {
		// check the nonce
		if ( ! isset( $_POST['perfecty_push_post_metabox_nonce'] ) ) {
			return $post_id;
		}
		$nonce = $_POST['perfecty_push_post_metabox_nonce'];
		if ( ! wp_verify_nonce( $nonce, 'perfecty_push_post_metabox' ) ) {
			return $post_id;
		}

		// nothing on auto save
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		// check capabilities
		if ( ! current_user_can( 'edit_post', $post_id ) || ! current_user_can( 'edit_page', $post_id ) ) {
			return $post_id;
		}

		$send_notification = ! empty( $_POST['perfecty_push_send_on_publish'] );
		update_post_meta( $post_id, '_perfecty_push_send_on_publish', $send_notification );
	}

	/**
	 * Action triggered when a post changes status
	 *
	 * @param $new_status string New status
	 * @param $old_status string Old status
	 * @param $post WP_Post Post
	 */
	public function on_transition_post_status( $new_status, $old_status, $post ) {
		// related: https://github.com/WordPress/gutenberg/issues/15094
		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			return;
		}

		$send_notification = false;
		if ( isset( $_POST['perfecty_push_post_metabox_nonce'] ) &&
			wp_verify_nonce( $_POST['perfecty_push_post_metabox_nonce'], 'perfecty_push_post_metabox' ) ) {
			// we do this because on_transition_post_status is triggered before on_save_post by WordPress
			$send_notification = ! empty( $_POST['perfecty_push_send_on_publish'] );
		} else {
			$send_notification = ! empty( get_post_meta( $post->ID, '_perfecty_push_send_on_publish', true ) );
		}

		if ( 'publish' == $new_status && $send_notification ) {
			$body        = get_the_title( $post );
			$url_to_open = get_the_permalink( $post );
			$payload     = Perfecty_Push_Lib_Payload::build( $body, '', '', $url_to_open );
			$result      = Perfecty_Push_Lib_Push_Server::schedule_broadcast_async( $payload );

			if ( $result === false ) {
				error_log( 'Could not schedule the broadcast async, check the logs' );
				$notice = array(
					'type'    => 'error',
					'message' => 'Could not send the notification',
				);
			} else {
				$notice = array(
					'type'    => 'success',
					'message' => 'Notification message was scheduled',
				);
				if ( isset( $_POST['perfecty_push_post_metabox_nonce'] ) ) {
					// once we sent the notification, we reset the checkbox when the
					// hook was triggered after clicking the save button
					unset( $_POST['perfecty_push_send_on_publish'] );
				}
				update_post_meta( $post->ID, '_perfecty_push_send_on_publish', false );
			}

			set_transient( 'perfecty_push_admin_notice', $notice );
		}
	}

	/**
	 * Gets a Payload
	 */
	public function get_payload() {
	}

	/**
	 * Show the admin notices
	 *
	 * Gets the transient 'perfecty_push_admin_notice' which is an array with the type as key and a message as value
	 */
	public function show_admin_notice() {
		$notice = get_transient( 'perfecty_push_admin_notice' );

		if ( $notice && is_array( $notice ) ) {
			$type    = $notice['type'];
			$message = $notice['message'];

			if ( $type === 'error' ) {
				printf( '<div class="notice notice-error is-dismissible"><p>%s</p></div>', $message );
			} else {
				printf( '<div class="notice notice-success is-dismissible"><p>%s</p></div>', $message );
			}

			delete_transient( 'perfecty_push_admin_notice' );
		}
	}

	/**
	 * Renders the dashboard page
	 *
	 * @since 1.0.0
	 */
	public function print_dashboard_page() {
		require_once plugin_dir_path( __FILE__ ) . 'partials/perfecty-push-admin-dashboard.php';
	}

	/**
	 * Renders the options page
	 *
	 * @since 1.0.0
	 */
	public function print_options_page() {
		require_once plugin_dir_path( __FILE__ ) . 'partials/perfecty-push-admin-options.php';
	}

	/**
	 * Renders the notifications page
	 *
	 * @since 1.0.0
	 */
	public function print_notifications_page() {
		if ( isset( $_GET['id'] ) && ! empty( $_GET['id'] ) &&
			isset( $_GET['action'] ) && $_GET['action'] == 'view' ) {
			$id   = intval( $_REQUEST['id'] );
			$item = Perfecty_Push_Lib_Db::get_notification( $id );

			$item->payload = json_decode( $item->payload );

			require_once plugin_dir_path( __FILE__ ) . 'partials/perfecty-push-admin-notifications-view.php';
			return true;
		}

		$table    = new Perfecty_Push_Admin_Notifications_Table();
		$affected = $table->prepare_items();
		require_once plugin_dir_path( __FILE__ ) . 'partials/perfecty-push-admin-notifications.php';
	}

	/**
	 * Renders the users page
	 *
	 * @since 1.0.0
	 */
	public function print_users_page() {
		if ( isset( $_GET['id'] ) && ! empty( $_GET['id'] ) &&
			isset( $_GET['action'] ) && $_GET['action'] == 'view' ) {
			$id   = intval( $_REQUEST['id'] );
			$item = Perfecty_Push_Lib_Db::get_user( $id );

			require_once plugin_dir_path( __FILE__ ) . 'partials/perfecty-push-admin-users-view.php';
			return true;
		}

		$table    = new Perfecty_Push_Admin_Users_Table();
		$affected = $table->prepare_items();
		require_once plugin_dir_path( __FILE__ ) . 'partials/perfecty-push-admin-users.php';
	}

	/**
	 * Renders the send notification page
	 *
	 * @since 1.0.0
	 */
	public function print_send_notification_page() {
		$message = '';
		$notice  = '';

		$default = array(
			'title'       => '',
			'message'     => '',
			'url_to_open' => '',
			'image'       => '',
		);

		if ( isset( $_REQUEST['nonce'] ) && wp_verify_nonce( $_REQUEST['nonce'], 'perfecty_push_send_notification' ) ) {
			$item = shortcode_atts( $default, $_REQUEST );

			$validation_result = $this->validate_notification_message( $item );
			if ( $validation_result === true ) {
				// filter
				$item['title']       = sanitize_text_field( $item['title'] );
				$item['message']     = sanitize_textarea_field( $item['message'] );
				$item['url_to_open'] = sanitize_text_field( $item['url_to_open'] );
				$item['image']       = sanitize_text_field( $item['image'] );

				$payload = Perfecty_Push_Lib_Payload::build( $item['message'], $item['title'], $item['image'], $item['url_to_open'] );

				// send notification
				$result = Perfecty_Push_Lib_Push_Server::schedule_broadcast_async( $payload );
				if ( $result === false ) {
					  $notice = 'Could not schedule the notification, check the logs';
				} else {
					$message = 'The notification job has started';

					// we clear the form
					$item = $default;
				}
			} else {
				$notice = $validation_result;
			}
		} else {
			$item = $default;
		}

		add_meta_box(
			'perfecty_push_send_notification_meta_box',
			'Notification details',
			array( $this, 'print_send_notification_metabox' ),
			'perfecty-push-send-notification',
			'normal'
		);

		require_once plugin_dir_path( __FILE__ ) . 'partials/perfecty-push-admin-send-notification.php';
	}

	/**
	 * Validates the notification details
	 *
	 * @param array $item Contains the entry
	 */
	public function validate_notification_message( $item ) {
		$messages = array();

		if ( empty( $item['title'] ) ) {
			$messages[] = 'The title is required';
		}

		if ( empty( $item['message'] ) ) {
			$messages[] = 'The message is required';
		}

		if ( empty( $messages ) ) {
			return true;
		} else {
			return implode( '<br />', $messages );
		}
	}

	/**
	 * Renders the send notification metabox
	 *
	 * @since 1.0.0
	 */
	public function print_send_notification_metabox( $item ) {
		require_once plugin_dir_path( __FILE__ ) . 'partials/perfecty-push-admin-send-notification-metabox.php';
	}

	/**
	 * Renders the about page
	 *
	 * @since 1.0.0
	 */
	public function print_about_page() {
		require_once plugin_dir_path( __FILE__ ) . 'partials/perfecty-push-admin-about.php';
	}

	/**
	 * Sanitize the settings
	 *
	 * @param string $input Contains the settings
	 * @return array
	 */
	public function sanitize( string $input ) {
		$new_input = array();
		if ( isset( $input['vapid_public_key'] ) ) {
			$new_input['vapid_public_key'] = sanitize_text_field( $input['vapid_public_key'] );
		}
		if ( isset( $input['server_url'] ) ) {
			$new_input['server_url'] = sanitize_text_field( $input['server_url'] );
		}

		return $new_input;
	}

	/**
	 * Print the general section info
	 *
	 * @since 1.0.0
	 */
	public function print_dialog_section() {
		print 'Change the messages shown in the notification dialog.';
	}

	/**
	 * Print the self hosted section info
	 *
	 * @since 1.0.0
	 */
	public function print_self_hosted_section() {
		print 'Configure how to connect your website with your self-hosted Perfecty Push Server.';
	}

	/**
	 * Print the Vapid public key
	 *
	 * @since 1.0.0
	 */
	public function print_vapid_public_key() {
		$options = get_option( 'perfecty_push' );
		$value   = isset( $options['vapid_public_key'] ) ? esc_attr( $options['vapid_public_key'] ) : '';

		printf(
			'<input type="text" id="perfecty_push[vapid_public_key]"' .
			'name="perfecty_push[vapid_public_key]" value="%s" />',
			$value
		);
	}

	/**
	 * Print the server_url option
	 *
	 * @since 1.0.0
	 */
	public function print_server_url() {
		$options = get_option( 'perfecty_push' );
		$value   = isset( $options['server_url'] ) ? esc_attr( $options['server_url'] ) : '';

		printf(
			'<input type="text" id="perfecty_push[server_url]"' .
			'name="perfecty_push[server_url]" value="%s" placeholder="127.0.0.1:8777"/>',
			$value
		);
	}

	/**
	 * Print the dialog_title option
	 *
	 * @since 1.0.0
	 */
	public function print_dialog_title() {
		$options = get_option( 'perfecty_push' );
		$value   = isset( $options['dialog_title'] ) ? esc_attr( $options['dialog_title'] ) : '';

		printf(
			'<input type="text" id="perfecty_push[dialog_title]"' .
			'name="perfecty_push[dialog_title]" value="%s" />',
			$value
		);
	}

	/**
	 * Print the dialog_submit option
	 *
	 * @since 1.0.0
	 */
	public function print_dialog_submit() {
		$options = get_option( 'perfecty_push' );
		$value   = isset( $options['dialog_submit'] ) ? esc_attr( $options['dialog_submit'] ) : '';

		printf(
			'<input type="text" id="perfecty_push[dialog_submit]"' .
			'name="perfecty_push[dialog_submit]" value="%s" />',
			$value
		);
	}

	/**
	 * Print the dialog_cancel option
	 *
	 * @since 1.0.0
	 */
	public function print_dialog_cancel() {
		$options = get_option( 'perfecty_push' );
		$value   = isset( $options['dialog_cancel'] ) ? esc_attr( $options['dialog_cancel'] ) : '';

		printf(
			'<input type="text" id="perfecty_push[dialog_cancel]"' .
			'name="perfecty_push[dialog_cancel]" value="%s" />',
			$value
		);
	}
}
