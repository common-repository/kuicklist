<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       kuick.co
 * @since      1.0.0
 *
 * @package    KuickList
 * @subpackage KuickList/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    KuickList
 * @subpackage KuickList/admin
 * @author     KuickList <support@kuicklist.com>
 */
class KuickList_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

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
		 * defined in KuickList_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The KuickList_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/kuicklist-admin.css', array(), $this->version, 'all' );

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
		 * defined in KuickList_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The KuickList_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/kuicklist-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Register the menu for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function kuicklist_admin_plugin_menu() {

		// Add Plugin Menu
		add_menu_page( 'KuickList' . ' - Dashboard', 'KuickList', 'manage_options', 'kuicklist', array($this, 'kuicklist_page_dashboard'), plugin_dir_url( __FILE__ ) . 'images/favicon.png', '64.419' );
	}

	/**
	 * Plugin dashboard page
	 *
	 * @since    1.0.0
	 */
	public function kuicklist_page_dashboard() {
		$api_key = get_option('_kuick_list_api_key');
		require_once plugin_dir_path( __FILE__ ) . 'partials/kuicklist-admin-display.php';
	}




	/**
	 * Add meta box for KuickList Checklists
	 * @return null
	 */
	public function kuicklist_add_meta_box()
	{
        // we'll render it on Pages and Posts
        $render_area_types = array('page', 'post');
        add_meta_box(
            'kuicklist-checklists',
            __( 'KuickList', 'kuicklist' ),
            array($this, 'kuicklist_meta_box_callback'),
            $render_area_types, 'side','high'
        );
	}



	/**
	 * Call back function for meta box
	 * 
	 * @param  $post
	 * @return string
	 */
	public function kuicklist_meta_box_callback($post = null)
	{
		$kuicklist_checklist_id = get_post_meta(get_the_ID(), '_kuick_list_checklist_id', true );

		$kuicklist_no_optin = get_post_meta(get_the_ID(), '_kuicklist_no_optin', true );

		$checked = '';

		if($kuicklist_no_optin) {
			$checked = 'checked';
		} 

		$api_key = get_option('_kuick_list_api_key');

		if($api_key) {

		    $url = KUICKLIST_API_URL.'checklists?group_by_folder=1';

		    // Get from Transient Cache
		    $response = get_transient('_kuicklist_checklists_data');

		    if ($response === false) {
				$response = $this->_wpRemoteRequestAPI($api_key, $url, 'GET');
		        // echo "Stroing in Cache";
		        set_transient('_kuicklist_checklists_data', $response, 300);
		    }
			
			// Append Dropdown
			if(!empty($response) && isset($response->data) && count($response->data) > 0) {

				$dd_html = '';

                $dd_html .= '<div class="kuicklist_checklists_dd">';
                $dd_html .= '<label>Select a Checklist</label><select name="_kuick_list_checklist_id" class="form-control kuicklist_checklists_dd" style="width: 89%; margin-top:10px; margin-bottom: 10px;">';

                $selected_kuicklist = '';

                $dd_html .= '<option value="">Select</option>';

                // Group by folders
                foreach ($response->data as $key => $value) {
                    if(isset($value->checklists)) {
                        $dd_html .= '<optgroup label="'.esc_attr($value->name).'">';
                        foreach ($value->checklists as $checklist) {
                            $dd_html .= '<option value="'.esc_attr($checklist->en_id).'" '.($kuicklist_checklist_id == $checklist->en_id ? 'selected' : '').' >'.esc_attr($checklist->name).'</option>';
                        }

                        $dd_html .= '</optgroup>';
                    } else {
                        // fallback if user has cache the data as without folders structure
                        $dd_html .= '<option value="'.esc_attr($value->en_id).'" '.($kuicklist_checklist_id == $value->en_id ? 'selected' : '').' >'.esc_attr($value->name).'</option>';
                    }
                }

                $dd_html .= '</select>';
                $dd_html .= ' <a href="javascript:void(0);" class="kuicklist_checklists kuicklist_refresh_icon" style="vertical-align: middle !important; top: 5px !important; position: relative !important; text-decoration: none !important; color: #555d65 !important;" title="Fetch New Data"><span class="dashicons dashicons-update"></span></a></div>';
                $kl_allowed_dd_html = array(
                    'div'      => array(
                        'class'  => array(),
                    ),
                    'label'     => array(),
                    'select'     => array(
                        'class'  => array(),
                        'name'  => array(),
                        'style'  => array(),
                    ),
                    'option' => array(
                        'value'  => array(),
                        'selected'  => array(),
                    ),
                    'optgroup' => array(
                        'label'     => array()
                    ),
                    'a'     => array(
                        'class'  => array(),
                        'style'  => array(),
                        'title'  => array(),
                    ),
                    'span'   => array(
                        'class'  => array(),
                    )
                );
                _e(wp_kses( $dd_html, $kl_allowed_dd_html ));

			} else {
				echo '<p>No Checklist was found!</p>';
			}
		} else {
			echo '<p>Your KuickList account has not been connected to your website. Click <a href="'.admin_url('admin.php?page=kuicklist&act=1').'">here</a> to connect.</p>';
		}

	}


	/**
	 * Save KuickList checklists id as meta key on the post save
	 * 
	 * @param  $post_id
	 * @return null
	 */
	public function kuicklist_save_checklist($post_id)
	{

		if ( isset($_POST['_kuick_list_checklist_id']) ) {
		    $checklist_id = sanitize_text_field($_POST['_kuick_list_checklist_id']);
			update_post_meta($post_id, '_kuick_list_checklist_id', $checklist_id);
		}

		if ( isset($_POST['_kuicklist_no_optin']) ) {
			update_post_meta($post_id, '_kuicklist_no_optin', 1);
		} else {
			update_post_meta($post_id, '_kuicklist_no_optin', 0);
		}
		
	}

	/**
	 * Verify API Key KuickList and save it to DB
	 * 
	 * @param 
	 * @return null
	 */
	public function kuicklist_verfiy_api_key() {
	    
	    $api_key = sanitize_text_field($_POST['_kuick_list_api_key']);

	    $url = KUICKLIST_API_URL.'verify-api-key';

	    $data = array(
	    	'_kuick_list_api_key' => $api_key
	    );

		$response = $this->_wpRemoteRequestAPI($api_key, $url, 'POST', $data);

		// $response->message = 'Success';
		// print_r($response); exit();
		if($response->success && $response->success == true) {

			$option_name = '_kuick_list_api_key' ;
			$new_value = $api_key;
			 
			if(get_option( $option_name ) !== false ) {
			    // The option already exists, so update it.
			    update_option( $option_name, $new_value );
			 
			} else {
			    // The option hasn't been created yet'.
			    add_option( $option_name, $new_value);
			}

			// Delete cached kuicklist data
			delete_transient('_kuicklist_checklists_data');

			wp_safe_redirect( add_query_arg( array( 'page' => 'kuicklist'), admin_url( 'plugins.php' ) ) );
		} else {
			wp_safe_redirect( add_query_arg( array( 'page' => 'kuicklist&act=1', 'res' => 'inv' ), admin_url( 'plugins.php' ) ) );
		}
		
        exit;
	}

	/**
	 * centraize wp_remote curl request for API
	 * @param  [type] $url    [description]
	 * @param  [type] $method [description]
	 * @param  array  $data   [description]
	 * @return [type]         [description]
	 */
	private function _wpRemoteRequestAPI($api_key, $url, $method, $data = array())
	{
        $response = wp_remote_post( $url, array(
			'method' => $method,
			'timeout' => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking' => true,
			'headers' => array('Kuick-List-Api-Key' => $api_key),
			'body' => $data,
			'cookies' => array(),
            'sslverify' => false
		    )
		);

		$response = json_decode($response['body']);
		return $response;
	}

	/**
	 * Delete kuicklists from cache and append new kuicklists dropdowm
	 * @return [type] [description]
	 */
	public function refresh_checklists()
	{
		$response = delete_transient('_kuicklist_checklists_data');

		echo $this->kuicklist_meta_box_callback();
		exit();
	}

	/**
	 * Delete All Cache Data
	 * @return [type] [description]
	 */
	public function kuicklist_clear_all_cache_data()
	{
		sleep(1);
		global $wpdb;

        // delete all "kuicklist namespace" transients
        $sql = "
            DELETE 
            FROM {$wpdb->options}
            WHERE option_name like '\_transient_kuicklist_checklists_data%'
            OR option_name like '\_transient__kuicklist_checklists_data%'
            OR option_name like '\_transient__kuicklist_checklist_data_%'
            OR option_name like '\_transient_kuicklist_checklist_data_%'
            OR option_name like '\_transient_timeout_kuicklist_checklists_data%'
            OR 	option_name like '\_transient_timeout__kuicklist_checklist_data_%'
            OR option_name like '\_transient_timeout__kuicklist_checklists_data%'
            OR option_name like '\_transient_kuicklist%'
            OR option_name like '\_transient__kuicklist%'
        ";

        $wpdb->query($sql);
		
		exit('success');
	}

}
