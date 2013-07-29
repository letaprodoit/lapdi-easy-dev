<?php	
if ( !class_exists( 'TSP_Easy_Dev' ) )
{
	require_once( 'class.easy-dev-data.php' );
	require_once( 'class.easy-dev-settings.php' );
	require_once( 'class.easy-dev-widget.php' );
	
	/**
	 * API implementations for TSP Easy Dev Pro, Use TSP Easy Dev package to easily create, manage and display wordpress plugins
	 * @package 	TSP_Easy_Dev
	 * @author 		sharrondenice, thesoftwarepeople
	 * @author 		Sharron Denice, The Software People
	 * @copyright 	2013 The Software People
	 * @license 	APACHE v2.0 (http://www.apache.org/licenses/LICENSE-2.0)
	 * @version 	1.0
	 */
	class TSP_Easy_Dev
	{
		/**
		 * An array of CSS URLs to include in the admin area
		 *
		 * @var array
		 */
		private $admin_css_files		= array();
		/**
		 * An array of JS URLs to include in the admin area
		 *
		 * @var array
		 */
		private $admin_js_files		= array();
		/**
		 * An array of CSS URLs to include in the user front-end
		 *
		 * @var array
		 */
		private $user_css_files		= array();
		/**
		 * An array of JS URLs to include in the user front-end
		 *
		 * @var array
		 */
		private $user_js_files		= array();
		/**
		 * The extended TSP_Easy_Dev_Settings class, must be instantiated (ie $my_plugin->settings_class = new TSP_Easy_Dev_Settings_MY_PLUGIN ( $settings );)
         *
         * @api
		 *
		 * @var TSP_Easy_Dev_Settings
		 */
		private $settings_class;
		/**
		 * The name of the widget class created by the user, a placeholder because logic can not be handled  
		 * by this class, the widget class has to be static and and called statically by WordPress
         *
         * @api
		 *
		 * @var string
		 */
		private $widget_class; //TODO: There was no way to aggregate a class for widget it has to be handled by WordPress via a hook, look into this with newer versions of WordPress
		/**
		 * The array of global values for the plugin, provided by the USER on instantiation
		 *
		 * @var array
		 */
		protected $settings 		= array();
		/**
		 * The version of WordPress that this plugin requires
         *
         * @api
		 *
		 * @var string
		 */
		public $required_wordpress_version;
		/**
		 * Does the plugin use shortcodes?
         *
         * @api
		 *
		 * @var boolean
		 */
		public $uses_shortcodes 		= false;
		/**
		 * Does the plugin require Smarty?
         *
         * @api
		 *
		 * @var boolean
		 */
		public $uses_smarty 			= false;
				
		/**
		 * Constructor
		 *
		 * @since 1.0
		 *
		 * @param array $globals Required - Sets the global settings for the plugin
		 *
		 * @return none
		 */
		public function __construct( $globals ) 
		{
			// Only use the default globals if they are none in the database
			
			$this->settings	= $globals;
		}//end __construct
		

		/**
		 * After all the settings are initialized, run the plugin
		 *
		 * @api
		 *
		 * @since 1.0
		 *
		 * @param string $plugin Required - The file name of the plugin, __FILE__
		 *
		 * @return none
		 */
		 public function run( $plugin )
		 {
			// register install/uninstall hooks
			register_activation_hook( $plugin, 		array( 'TSP_Easy_Dev', 'install') );
			register_deactivation_hook( $plugin, 	array( 'TSP_Easy_Dev', 'deactivate') );
			register_uninstall_hook( $plugin, 		array( 'TSP_Easy_Dev', 'uninstall' ) );
			
			add_action( 'init', 					array( $this, 'init' ) );
			add_action( 'admin_init', 				array( $this, 'init' ) );
			add_action( 'deactivate_' . $plugin, 	array( $this, 'de_init' ) );
			
			add_action('admin_enqueue_scripts', 	array( $this, 'enqueue_admin_scripts' ));
			add_action('wp_enqueue_scripts', 		array( $this, 'enqueue_user_scripts' ));

			// If the plugin uses settings add them
			if ( $this->settings_class )
			{
				$this->settings_class->init( $this->settings );
			}//end if
			
			if ( $this->settings_class || $this->widget_class )
			{
				$this->uses_smarty = true; // Smarty required to display HTML on settings and widget pages
			}//end if
			
			if ( $this->uses_smarty )
			{
				require_once( 'class.easy-dev-smarty.php' );
			}//end if
		 }//end setup


		/**
		 * Function to initialize the plugin on install or activation
		 *
		 * @ignore - Must be public because used in WordPress hooks
		 *
		 * @since 1.0
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
						
						$message =  $this->settings['Title'] . " requires WordPress version <strong>{$this->required_wordpress_version} or higher</strong>.<br>You have version <strong>$wp_version</strong> installed.";
					    ?>
					    <div class="error">
					        <p><?php _e( $message, $this->settings['name']  ); ?></p>
					    </div>
					    <?php
					} );
					
					deactivate_plugins($this->settings['name'] . DS . $this->settings['name'].'.php');
					
					return;
				}//endif
			}//endif
			
			
			$message = "";

			// If the plugin requries smarty create cache and compiled directories
			if ( $this->uses_smarty )
			{
				$smarty_cache_dir = $this->settings['smarty_cache_dir'];
				$smarty_compiled_dir = $this->settings['smarty_compiled_dir'];
				
				if ( !file_exists( $smarty_cache_dir ) )
				{
					if (!@mkdir( $smarty_cache_dir, 0777, true ))
					{
						$message .= "<br>Unable to create $smarty_cache_dir directory. Please create this directory manually via FTP or cPanel.";
					}//end if
				}//end if

				if ( !file_exists( $smarty_compiled_dir ) )
				{
					if (!!@mkdir( $smarty_compiled_dir, 0777, true ))
					{
						$message .= "<br>Unable to create $smarty_compiled_dir directory. Please create this directory manually via FTP or cPanel.";
					}//end if
				}//end if
			}//end if

			return $message;
		}
		
		/**
		 * Method to intialize the settings class for this plugin
		 *
		 * @since 1.0
		 *
		 * @param TSP_Easy_Dev_Settings $settings_class Required The settings handler class for this plugin
		 *
		 * @return none
		 */
		public function set_settings_handler( $settings_class ) 
		{
			if ( is_subclass_of( $settings_class, 'TSP_Easy_Dev_Settings' ) )
			{
				$this->settings_class = $settings_class;
			}//end if
			else
			{
				wp_die ( "The settings handler must be a subclass of TSP_Easy_Dev_Settings." );
			}//end else
		}//end set_settings_handler
		
		
		/**
		 * Method to return the name of the widget class handler
		 *
		 * @since 1.0
		 *
		 * @param none
		 *
		 * @return TSP_Easy_Dev_Settings object reference
		 */
		public function get_settings_handler() 
		{
			return $this->settings_class;
		}//end get_settings_handler
		
		/**
		 * Method to intialize the settings class for this plugin
		 *
		 * @since 1.0
		 *
		 * @param string $widget_class Required The NAME of the widget handler class for this plugin
		 *
		 * @return none
		 */
		public function set_widget_handler( $widget_class ) 
		{
			$this->widget_class = $widget_class;
		}//end set_widget_handler
		
		/**
		 * Method to return the name of the widget class handler
		 *
		 * @since 1.0
		 *
		 * @param none
		 *
		 * @return string Widget class name
		 */
		public function get_widget_handler() 
		{
			return $this->widget_class;
		}//end get_widget_handler

		/**
		 * Function to de-initialize the plugin on uninstall
		 *
		 * @ignore - Must be public because used in WordPress hooks
		 *
		 * @since 1.0
		 *
		 * @param none
		 *
		 * @return none (extend to add additional checks)
		 */
		public function de_init()
		{
			if ( $this->settings_class )
				$this->settings_class->deregister_settings();

			$message = "";
			
			// If the plugin requries smarty create cache and compiled directories
			if ( $this->uses_smarty )
			{
				$smarty_cache_dir = $this->settings['smarty_cache_dir'];
				$smarty_compiled_dir = $this->settings['smarty_compiled_dir'];

				if ( file_exists( $smarty_cache_dir ) )
				{
					if (!@rmdir( $smarty_cache_dir ))
					{
						$message .= "<br>Unable to remove $smarty_cache_dir directory. Please remove this directory manually via FTP or cPanel.";
					}//end if
				}//end if
				
				if ( file_exists( $smarty_compiled_dir ) )
				{
					if (!@rmdir( $smarty_compiled_dir ))
					{
						$message .= "<br>Unable to remove $smarty_compiled_dir directory. Please remove this directory manually via FTP or cPanel.";
					}//end if
				}//end if
			}//end if
			
			return $message;
		}//end deinit

		/**
		 * Add styles to the queue
		 *
		 * @api
		 *
		 * @since 1.0
		 *
		 * @param string $css Required - The full URL of the css file
		 * @param boolean $admin Optonal - Is the style for the admin or user interface
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
		 * Add user scripts to the queue
		 *
		 * @api
		 *
		 * @since 1.0
		 *
		 * @param string $script Required - The full URL of the script file
		 * @param array $required_scripts Optonal - Array of required script tags (ie 'jquery','jquery-ui-widget')
		 * @param boolean $admin Optonal - Is the style for the admin or user interface
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
		 * @api
		 *
		 * @since 1.0
		 *
		 * @param string $tag Required - the tag name of the shortcode
		 *
		 * @return none
		 */
		 public function add_shortcode( $tag )
		 {
			if ( $this->uses_shortcodes )
			{
				$this->settings['shortcodes'][] = $tag;
			}//endif
		 }//end add_shortcode

		/**
		 * Set the plugin icon (used by settings on run)
		 *
		 * @api
		 *
		 * @since 1.0
		 *
		 * @param string $icon Required - The full URL of the icon file
		 *
		 * @return none
		 */
		public function set_plugin_icon( $icon )
		{
			$this->settings['plugin_icon'] =  $icon;
		}//end set_plugin_icon

		/**
		 *  Implementation to queue user scripts and stylesheets
		 *
		 * @ignore - Must be public because used in WordPress hooks
		 *
		 * @since 1.0
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
		 * @ignore - Must be public because used in WordPress hooks
		 *
		 * @since 1.0
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
		 * Return the current global settings
		 *
		 * @api
		 *
		 * @since 1.0
		 *
		 * @param none
		 *
		 * @return array $this->settings current global settings
		 */
		 public function get_settings()
		 {
		 	return $this->settings;
		 }//end get_settings
		 
		/**
		 * Optional implementation to install plugin - can be extended by subclasses, not to be called directly but extended by subclasses
		 *
		 * @api
		 *
		 * @since 1.0
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
		 * Optional implementation to uninstall plugin - can be extended by subclasses, not to be called directly but extended by subclasses
		 *
		 * @api
		 *
		 * @since 1.0
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
		 * Optional implementation to activate plugin - can be extended by subclasses, not to be called directly but extended by subclasses
		 *
		 * @api
		 *
		 * @since 1.0
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
		 * Optional implementation to deactivate plugin - can be extended by subclasses, not to be called directly but extended by subclasses
		 *
		 * @api
		 *
		 * @since 1.0
		 *
		 * @param none
		 *
		 * @return string $message Optional any messages generated by deactivation
		 */
		static public function deactivate()
		{
			return;
		}//end deactivate
		
	}//end TSP_Easy_Dev
}//endif	
?>