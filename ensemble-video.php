<?php
/*
Plugin Name: Ensemble Video
Description: Easily embed ensemble videos in your site
Version: 1.0
Author: Sam Margulies
*/

class Ensemble_Video {
	
	// constructor
	function Ensemble_Video() {		

		// add our shortcode
		add_shortcode('ensemblevideo', array(&$this, 'ensemblevideo_shortcode'));
		
		// set default options
		if ( get_site_option('ensemble_video') === false ) {
			if( $this->is_network_activated() ) {
				update_site_option('ensemble_video', $this->default_options());
			} else {
				update_option('ensemble_video', $this->default_options());
			}
		}
		
		if( is_admin() ) {
			// add media button 
			add_action('media_buttons_context', array(&$this, 'add_media_button'), 999);
			// add media button scripts and styles
			add_action('admin_enqueue_scripts', array(&$this, 'admin_enqueue_scripts'));
			
			
			// add admin page
			add_action('admin_menu', array(&$this, 'admin_menu'));
            add_action('admin_init', array(&$this, 'admin_init'));
			
			// add network admin page
			add_action('network_admin_menu', array(&$this, 'admin_menu'));
			// save settings for network admin
			add_action('network_admin_edit_ensemble_video', array( &$this, 'save_network_settings' ) );
			// return message for update settings
			add_action('network_admin_notices',	array( &$this, 'network_admin_notices' ) );
			// add ajax function render shortcodes
			add_action('wp_ajax_ensemblevideo_render_shortcode', array( &$this, 'render_shortcode_callback') );
			
			add_action('wp_ajax_ensemblevideo_proxy_api', array( &$this, 'proxy_api_callback') );
			

		}
	}
	
	function admin_enqueue_scripts() {
		// TODO: restrict to pages with post editor
		
		wp_enqueue_script( 'ensemble-video', plugins_url('/js/ensemble-video.js', __FILE__) );
		wp_enqueue_style( 'ensemble-video-styles', plugins_url('/css/ensemble-video.css', __FILE__) );
	}
	
    function add_media_button($context) {

        $image_btn = plugins_url( '/img/ensemble-button-bw.png', __FILE__ );

		$out = '<a href="#TB_inline?width=240&height=240&inlineId=ensemble-video" class="thickbox" id="add-ensemble-video" title="' . __("Add Ensemble Video", 'ensemble-video') . '"><img src="'.$image_btn.'" alt="' . __("Add Ensemble Video", 'ensemble-video') . '" /></a>';
		return $context . $out;
	
	}
	
	// test to see if we are network activated
	function is_network_activated() {
		
	    // Makes sure the plugin is defined before trying to use it
		if ( ! function_exists( 'is_plugin_active_for_network' ) )
		    require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
			
		return is_multisite() && is_plugin_active_for_network( plugin_basename( __FILE__ ));
	}
	
	// add the menu to our site or network
	function admin_menu() {
		if ( $this->is_network_activated() ) {
			add_submenu_page( 'settings.php',__('Ensemble Video Settings','ensemble-video'), __('Ensemble Video','ensemble-video'), 'manage_options', 'ensemble_video', array(&$this, 'display_options_page') );
		} else {
			add_options_page( __('Ensemble Video Settings','ensemble-video'), __('Ensemble Video','ensemble-video'), 'manage_options', 'ensemble_video', array(&$this, 'display_options_page') );
		}
	}
	
	// register Settings API settings
	function admin_init() {
		register_setting('ensemble_video_options_group','ensemble_video',array(&$this, 'validate_options'));
        add_settings_section('ensemble_video','General Settings', array(&$this, 'display_options_description'),'ensemble_video');
		add_settings_field('ensemble_video_ensemble_url','Ensemble Video URL',array(&$this, 'display_ensemble_url_option'),'ensemble_video','ensemble_video');
	}
	
	function default_options() {
		return array(
			'ensemble_url' => 'https://cloud.ensemblevideo.com',
		);
	}
	
	function validate_options($input) {
						
		$options = $this->get_options();
		
		// sanitize url
		$ensemble_url = esc_url_raw( $input['ensemble_url'] );
			
		// replace http urls with https, since that is all ensemble supports
		// we are running this after our first sanitization in case they didn't enter a protocol
		$ensemble_url = esc_url_raw( str_replace('http://', 'https://', $ensemble_url ), array('https') );
		
		$ensemble_url = untrailingslashit($ensemble_url);
		
		if( empty($ensemble_url) ) {
			
			add_settings_error('ensemble_video_ensemble_url', 'ensemble_invald_url', __('Please enter a valid Ensemble Video URL.', 'ensemble-video'));
			
		} else {
			$options['ensemble_url'] = $ensemble_url;
		}
				
		return $options;
	}
	
	function display_options_description() {
		?>
		<!-- <p>Configure your Ensemble Video embed defaults.</p> -->
		<?php
	}
	
	function display_ensemble_url_option() {
		
		$options = $this->get_options();
		
		?>
		<input id="ensemble_video_ensemble_url" name="ensemble_video[ensemble_url]" class="regular-text" value="<?php echo $options['ensemble_url']; ?>" />
		<p class="description">This is the URL of your ensemble video site, eg. https://ensemble.example.com.</p>
		<?php
	}
	
	function display_options_page() {
		
		$options = $this->get_options();
		
		$post_page = $this->is_network_activated() ? 'edit.php?action=ensemble_video' : 'options.php';
		
		?>
		<div class="wrap">
			<?php screen_icon("options-general"); ?>
			<h2>Ensemble Video Settings</h2>
			<form action="<?php echo $post_page; ?>" method="post">
				<?php settings_fields('ensemble_video_options_group'); ?>
				<?php do_settings_sections('ensemble_video'); ?>
				<p class="submit">
					<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
				</p>
			</form>
		</div>
         <?php
	}

	// get options for current site, or network if network activated
	function get_options() {
		if ( $this->is_network_activated() ) {
			return get_site_option('ensemble_video');
		} 
	
		return get_option('ensemble_video');
	}
	
	// update options for current site, or network if network activated
	function update_options( $options ) {
		
		if ( $this->is_network_activated() ) {
			return update_site_option('ensemble_video', $options);
		} 
	
		return update_option('ensemble_video', $options);
	}
	
	// Save network settings
	function save_network_settings() {
		
		if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'ensemble_video_options_group-options' ) )
			wp_die( 'Sorry, you failed the nonce test.' );
		
		// validate options
		$input = $this->validate_options( $_POST['ensemble_video'] );
		
		// update options
		$this->update_options( $input );
		
		// redirect to settings page in network
		wp_redirect(
			add_query_arg(
			array( 'page' => 'ensemble_video', 'updated' => 'true' ),
				network_admin_url( 'settings.php' )
			)
		);
		exit();
	}
	
	// Retrun string for update message
	function network_admin_notices() {
		
		$screen = get_current_screen();
				
		// if updated and the right page
		if ( isset( $_GET['updated'] ) && 
			'settings_page_ensemble_video-network' === $screen->id
			) {
				
			$message = __( 'Options saved.', 'ensemble_video' );
			echo '<div id="message" class="updated"><p>' . $message . '</p></div>';
		}
	}
	
	function ensemblevideo_shortcode($atts){
		
		$options = $this->get_options();
		
		$embed_defaults = wp_embed_defaults();
	
		$atts = shortcode_atts( array(		

			'url' 								=> $options['ensemble_url'],
			
			'contentid' 						=> '',
			
			'audio'								=> false,
			
			'width' 							=> $embed_defaults["width"],
			'height' 							=> $embed_defaults["height"],
			'iframe' 							=> 'true',
			'title' 							=> 'false',
			'autoplay' 							=> 'false',
			'showcaptions' 						=> 'false',
			'hidecontrols' 						=> 'false',
			
			'destinationid' 					=> '',
			
			'displayshowcase' 					=> false,
			'featuredcontentorderbydirection' 	=> 'desc',
			'displaycategorylist'				=> 'true',
			'categoryorientation'				=> 'horizontal',
			
			'displayembedcode'					=> 'false',
			'displaystatistics'					=> 'false',
			'displayattachments'				=> 'false',
			'displaylinks'						=> 'false',
			'displaycredits'					=> 'false',
			
		), $atts);
		
		
		if( $atts['width'] == $embed_defaults['width'] && $atts['height'] == $embed_defaults['height'] ) {
				
			// expand videos to be the biggest they can and still have the right proportions
			// but only for single videos, leave web destinations at maximum embed size
			if( !empty($atts['contentid']) ) {

				list( $width, $height ) = wp_expand_dimensions( 480, 300, $atts['width'], $atts['height'] );
			}
			
		}  else {
			$width = $atts['width'];
			$height = $atts['height'];
		}
		
		if( $atts['audio'] == true ) {
			$height = '40';
		}
		
		$output =  '<p><div id="ensembleEmbeddedContent';
		$output .= !empty($atts['contentid']) ? $atts['contentid'] : $atts['destinationid'];
		$output .= '" class="ensembleEmbeddedContent" style="width: ' . $width . 'px; height: ' . ($height - 10) . 'px;margin-left:-8px;margin-top:-8px;"><script type="text/javascript" src="' . $atts['url'] . '/ensemble/app/plugin/plugin.aspx?';
		if( !empty($atts['contentid']) ) {
			$output .= 'contentID=' . $atts['contentid'];
			$output .= '&displayTitle=' . $atts['title'];
			$output .= '&autoPlay=' . $atts['autoplay'];
			$output .= '&hideControls=' . $atts['hidecontrols'];
			$output .= '&showCaptions=' . $atts['showcaptions'];
			$output .= '&width=' . $width;
			if( $atts['audio'] == false ) {
				$output .= '&height=' . ($height - 30);
			}
			$output .= '&embed=true';
			$output .= '&startTime=0';
		} else {
			$output .= 'DestinationID=' . $atts['destinationid'];
			
			$output .= '&maxContentWidth=' . $width;
			
			if( $atts['displayshowcase'] !== false ) {
				$output .= '&displayShowcase=' . $atts['displayshowcase'];
				$output .= '&featuredContentOrderByDirection=' . $atts['featuredcontentorderbydirection'];
				$output .= '&displayCategoryList=' . $atts['displaycategorylist'];
				$output .= '&categoryOrientation=' . $atts['categoryorientation'];
			}
			
			$output .= '&displayEmbedCode='	 . $atts['displayembedcode'];
			$output .= '&displayStatistics='	 . $atts['displaystatistics'];
			$output .= '&displayAttachments=' . $atts['displayattachments'];
			$output .= '&displayLinks='		 . $atts['displaylinks'];
			$output .= '&displayCredits='	 . $atts['displaycredits'];
		}
		$output .= '&useIFrame=' . $atts['iframe'];
		$output .= '"></script></div></p>';
		
		return $output;
	}
	
	function render_shortcode_callback() {
		// TODO: get working, add nonce
		$data = array();
				
		$data['shortcode'] = do_shortcode( $_POST['shortcode'] );
		
		echo json_encode($data);
		
		die(); // this is required to return a proper result
	}

	function proxy_api_callback() {
		// TODO: get working, add nonce
		
		$options = $this->get_options();
		
		//$api_base = $options['ensemble_url'] . '/api/';
		
		$api_base = 'http://cloud-test.ensemblevideo.com/api/';
		
		$api_call = esc_attr( $_POST['api_call'] );
		
		$request_args = array(
			'headers' => array(
				'Authorization' => 'Basic ' . base64_encode( $_POST['username'] . ':' . $_POST['password'] )
		) );
		
		$response = wp_remote_retrieve_body( wp_remote_request($api_base . $api_call, $request_args) );
		
		echo $response;
		
		die(); // this is required to return a proper result
	}
	
}

/* Initialise outselves */
$GLOBALS['ensemble_video'] = new Ensemble_Video();
