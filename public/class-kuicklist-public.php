<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       kuick.co
 * @since      1.0.0
 *
 * @package    KuickList
 * @subpackage KuickList/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    KuickList
 * @subpackage KuickList/public
 * @author     KuickList <support@kuicklist.com>
 */
class KuickList_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/kuicklist-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/kuicklist-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 *	Switch to leadkitpro template 
	 * 
	 * @return string
	 */
	public function show_kuicklist_template($template)
	{
		global $post;
		global $kuicklist_checklist_id;
		global $kuicklist_no_optin;
		global $kuicklist_checklist;
        global $kuicklist_iframe_url;

        $kuicklist_checklist_id = get_post_meta(get_the_ID(), '_kuick_list_checklist_id', true );

		$kuicklist_no_optin = get_post_meta(get_the_ID(), '_kuicklist_no_optin', true );
		
		$url = KUICKLIST_API_URL.'checklist/'.$kuicklist_checklist_id;
		
		if($kuicklist_checklist_id && !empty($kuicklist_checklist_id)) {

			// Get from Transient Cache
		    $kuicklist_checklist = get_transient('_kuicklist_checklist_data_'.$kuicklist_checklist_id);

		    if ($kuicklist_checklist === false) {
				$kuicklist_checklist = $this->_wpRemoteRequestAPI($url, 'GET');
		        set_transient('_kuicklist_checklist_data_'.$kuicklist_checklist_id, $kuicklist_checklist, 300);
		    }

            // set iFrame drc URL
            $kuicklist_iframe_url = $kuicklist_checklist->data->domain.'/c/'.(isset($kuicklist_checklist_id) ? $kuicklist_checklist_id : null);

			$page_template = KUICKLIST_PLUGIN_DIR . '/public/partials/kuicklist-public-display.php';
			
			if( file_exists($page_template)) {
			  return $page_template;
			}
		}

		return $template;
	}

	/**
	 * centraize wp_remote curl request for API
	 * @param  [type] $url    [description]
	 * @param  [type] $method [description]
	 * @param  array  $data   [description]
	 * @return [type]         [description]
	 */
	private function _wpRemoteRequestAPI($url, $method, $data = array())
	{
		$api_key = get_option('_kuicklist_api_key');
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

}
