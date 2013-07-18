<?php	
if ( !class_exists( 'TSP_Easy_Plugins' ) )
{
	require_once( 'class.easy-plugins-data.php' );
	require_once( 'class.easy-plugins-settings.php' );
	require_once( 'class.easy-plugins-widget.php' );
	require_once( 'class.easy-plugins-smarty.php' );
	
	/**
	 * TSP_Easy_Plugins - API implementations for TSP Easy Plugins
	 * @package TSP_Easy_Plugins
	 * @author sharrondenice, thesoftwarepeople
	 * @author Sharron Denice, The Software People
	 * @copyright 2013 The Software People
	 * @license APACHE v2.0 (http://www.apache.org/licenses/LICENSE-2.0)
	 * @version $Id: [FILE] [COMMIT] [DATE] [TIME] [USER] $
	 */
	
	/**
	 * Use TSP Easy Plugins package to easily create, manage and display wordpress plugins
	 *
	 * original author: Sharron Denice
	 */
	class TSP_Easy_Plugins
	{
		protected $plugin_icon			= null;
		protected $admin_css_files		= array();
		protected $admin_js_files		= array();
		protected $user_css_files		= array();
		protected $user_js_files		= array();
		protected $plugin_globals 		= null;
		
		public $settings;
		public $required_wordpress_version;

		public $uses_shortcodes 		= false;
		public $uses_smarty 			= false;
				
		
		/**
		 * PHP4 constructor
		 */
		public function TSP_Easy_Plugins( $globals ) 
		{
			TSP_Easy_Plugins::__construct( $globals );
		}//end TSP_Plugin_Widget
	
		/**
		 * PHP5 constructor
		 */
		public function __construct( $globals ) 
		{
			// Only use the default globals if they are none in the database
			
			$this->plugin_globals	= $globals;
		}//end __construct
		

		/**
		 * Register Wordpress hooks for this pluginx
		 *
		 * @since 1.0.0
		 *
		 * @param string - The file name of the plugin
		 *
		 * @return none
		 */
		 public function run( $plugin )
		 {
			// register install/uninstall hooks
			register_activation_hook( $plugin, 		array( 'TSP_Easy_Plugins', 'install') );
			register_deactivation_hook( $plugin, 	array( 'TSP_Easy_Plugins', 'deactivate') );
			register_uninstall_hook( $plugin, 		array( 'TSP_Easy_Plugins', 'uninstall' ) );
			
			add_action( 'init', 					array( $this, 'init' ) );
			add_action( 'admin_init', 				array( $this, 'init' ) );
			add_action( 'deactivate_' . $plugin, 	array( $this, 'deinit' ) );
			
			add_action('admin_enqueue_scripts', 	array( $this, 'enqueue_admin_scripts' ));
			add_action('wp_enqueue_scripts', 		array( $this, 'enqueue_user_scripts' ));

			// If the plugin uses settings add them
			if ( $this->settings )
			{
				$this->settings->init( $this->plugin_globals );
				$this->settings->set_menu_icon($this->plugin_icon);

			}//end if

			if ( $this->uses_smarty )
			{
				if (!class_exists('Smarty'))
				{
				    if (file_exists( plugin_dir_path( __FILE__ ) . 'lib' . DS. 'Smarty' . DS . 'Smarty.class.php' ))
				        require_once plugin_dir_path( __FILE__ ) . 'lib' . DS. 'Smarty' . DS . 'Smarty.class.php';
				}//endif
			}//end if
		 }//end setup


		/**
		 * Function to initialize the plugin
		 *
		 * @since 1.0.0
		 *
		 * @param none
		 *
		 * @return none (extend to add additional checks)
		 */
		public function init() 
		{
			if ( $this->required_wordpress_version )
			{
				global $wp_version;
								
				if (version_compare($wp_version, $this->required_wordpress_version, "<"))
				{
			
					add_action( 'admin_notices', function (){
						global $wp_version;
						
						$message =  $this->plugin_globals['Title'] . " requires WordPress version <strong>{$this->required_wordpress_version} or higher</strong>.<br>You have version <strong>$wp_version</strong> installed.";
					    ?>
					    <div class="error">
					        <p><?php _e( $message, $this->plugin_globals['name']  ); ?></p>
					    </div>
					    <?php
					} );
					
					deactivate_plugins($this->plugin_globals['name'] . DS . $this->plugin_globals['name'].'.php');
					
					return;
				}//endif
			}//endif
			
			
			$message = "";

			// If the plugin requries smarty create cache and compiled directories
			if ( $this->uses_smarty )
			{
				if (!wp_mkdir_p( $this->plugin_globals['smarty_cache'] ))
					$message .= "<br>Unable to create " . $this->plugin_globals['smarty_cache'] . " directory. Please create this directory manually via FTP or cPanel.";
				else
					@chmod( $this->plugin_globals['smarty_cache'], 0777 );
				
				
				if (!wp_mkdir_p( $this->plugin_globals['smarty_compiled'] ))
					$message .= "<br>Unable to create " . $this->plugin_globals['smarty_compiled'] . " directory. Please create this directory manually via FTP or cPanel.";
				else
					@chmod( $this->plugin_globals['smarty_compiled'], 0777 );
			}//end if

			return $message;
		}
		
		public function de_init()
		{
			if ( $this->settings)
				$this->settings->deregister_settings();

			$message = "";
			
			// If the plugin requries smarty create cache and compiled directories
			if ( $this->uses_smarty )
			{
				if (!@rmdir( $this->plugin_globals['smarty_cache'] ))
					$message .= "<br>Unable to remove " . $this->plugin_globals['smarty_cache'] . " directory. Please remove this directory manually via FTP or cPanel.";
				
				if (!@rmdir( $this->plugin_globals['smarty_compiled'] ))
					$message .= "<br>Unable to remove " . $this->plugin_globals['smarty_compiled'] . " directory. Please remove this directory manually via FTP or cPanel.";
			}//end if
			
			return $message;
		}//end deinit

		/**
		 * Add user styles to the queue
		 *
		 * @since 1.0.0
		 *
		 * @param string - The file name of the css file
		 *
		 * @return none
		 */
		 public function add_css( $css, $admin = false )
		 {
			if ( $admin )
			{
				$this->admin_css_files[]  	= $css;
			}//endif
			else
			{
				$this->user_css_files[] 	= $css;
			}//end else
		 }//end add_css


		/**
		 * Add user styles to the queue
		 *
		 * @since 1.0.0
		 *
		 * @param string - The file name of the css file
		 *
		 * @return none
		 */
		 public function add_script( $script, $required_scripts = array(), $admin = false )
		 {
			if ( $admin )
			{
				$this->admin_js_files[$script] 	= $required_scripts;
			}//endif
			else
			{
				$this->user_js_files[$script] 	= $required_scripts;
			}//end else
		 }//end add_css

		/**
		 * Add short codes for processing to the widget
		 *
		 * @since 1.0.0
		 *
		 * @param string $tag Required the tag name of the shortcode
		 *
		 * @return none
		 */
		 public function add_shortcode( $tag )
		 {
			if ( $this->uses_shortcodes )
			{
				$this->plugin_globals['shortcodes'][] = $tag;
			}//endif
		 }//end add_shortcode

		/**
		 * Set the plugin icon (used by settings on run)
		 *
		 * @since 1.0.0
		 *
		 * @param none
		 *
		 * @return none (extend to add submenus to the parent menu)
		 */
		public function set_plugin_icon( $icon )
		{
			$this->plugin_icon =  $icon;
		}//end set_plugin_icon

		/**
		 *  Implementation to queue user scripts and stylesheets
		 *
		 * @since 1.0.0
		 *
		 * @param none
		 *
		 * @return none
		 */
		public function enqueue_user_scripts()
		{
			foreach ($this->user_css_files as $style)
			{
				$tag = basename($style);
				$tag = preg_replace( "/\.css/", "", $tag);
				$tag = preg_replace( "/-|\./", "_", $tag);
				$tag = "tsp_css_" . $tag;
				
				wp_register_style( $tag, $style );
				wp_enqueue_style( $tag );
			}//endforeach
			
			foreach ($this->user_js_files as $script => $requires)
			{
				$tag = basename($script);
				$tag = preg_replace( "/\.js/", "", $tag);
				$tag = preg_replace( "/-|\./", "_", $tag);
				$tag = "tsp_js_" . $tag;
				
				wp_register_script( $tag, $script, $requires );
				wp_enqueue_script( $tag );
			}//endforeach

		}//end  enqueue_styles
		
		/**
		 *  Implementation to queue admin scripts and stylesheets
		 *
		 * @since 1.0.0
		 *
		 * @param none
		 *
		 * @return none
		 */
		public function enqueue_admin_scripts()
		{
			foreach ($this->admin_css_files as $style)
			{
				$tag = basename($style);
				$tag = preg_replace( "/\.css/", "", $tag);
				$tag = preg_replace( "/-|\./", "_", $tag);
				$tag = "tsp_css_" . $tag;
				
				wp_register_style( $tag, $style );
				wp_enqueue_style( $tag );
			}//endforeach
			
			foreach ($this->admin_js_files as $script => $requires)
			{
				$tag = basename($script);
				$tag = preg_replace( "/\.js/", "", $tag);
				$tag = preg_replace( "/-|\./", "_", $tag);
				$tag = "tsp_js_" . $tag;
				
				wp_register_script( $tag, $script, $requires );
				wp_enqueue_script( $tag );
			}//endforeach
		}//end enqueue_scripts

		/**
		 * Optional implementation to install plugin
		 *
		 * @since 1.0.0
		 *
		 * @param none
		 *
		 * @return string $message Optional any messages generated by install
		 */
		static public function install()
		{
			return;
		}//end install
		
		/**
		 * Optional implementation to uninstall plugin
		 *
		 * @since 1.0.0
		 *
		 * @param none
		 *
		 * @return string $message Optional any messages generated by uninstall
		 */
		static public function uninstall()
		{
			return;
		}//end uninstall
		
		/**
		 * Optional implementation to activate plugin
		 *
		 * @since 1.0.0
		 *
		 * @param none
		 *
		 * @return string $message Optional any messages generated by deactivation
		 */
		static public function activate()
		{
			return;
		}//end activate
		
		/**
		 * Optional implementation to deactivate plugin
		 *
		 * @since 1.0.0
		 *
		 * @param none
		 *
		 * @return string $message Optional any messages generated by deactivation
		 */
		static public function deactivate()
		{
			return;
		}//end deactivate
		
	}//end TSP_Easy_Plugins
}//endif	
?>