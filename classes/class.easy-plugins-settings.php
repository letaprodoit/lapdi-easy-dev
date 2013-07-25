<?php	
if ( !class_exists( 'TSP_Easy_Plugins_Settings' ) )
{
	/**
	 * Class to display admin settings in admin area
	 * @package 	TSP_Easy_Plugins
	 * @author 		sharrondenice, thesoftwarepeople
	 * @author 		Sharron Denice, The Software People
	 * @copyright 	2013 The Software People
	 * @license 	APACHE v2.0 (http://www.apache.org/licenses/LICENSE-2.0)
	 * @version 	1.0
	 */
	abstract class TSP_Easy_Plugins_Settings
	{
		/**
		 * The array of global values for the plugin
		 *
		 * @var array
		 */
		protected $settings = array(); // sub-classes can call directly
		/**
		 * The URL link to the settings menu icon
		 *
		 * @var string
		 */
		private $menu_icon;
				
		/**
		 * Constructor
		 *
		 * @ignore
		 *
		 * @since 1.0
		 *
		 * @param none
		 *
		 * @return none
		 */
		public function __construct() 
		{
		}//end __construct
				
		/**
		 * Intialize the settings class
		 *
		 * @since 1.0
		 *
		 * @param array $settings Required the default plugin settings
		 *
		 * @return none
		 */
		public function init ( $settings )
		{
			$this->settings				= $settings;

			$this->set_menu_icon( $this->settings['plugin_icon'] );
			
			add_action( 'admin_menu', 			array( $this, 'add_admin_menu' ) );
			add_filter( 'plugin_action_links', 	array( $this, 'add_settings_link'), 10, 2 );
			
			$this->register_settings();
		}//end register_settings
					
		/**
		 * Create settings entry in database
		 *
		 * @ignore - Must be public because used in WordPress hooks
		 *
		 * @since 1.0
		 *
		 * @param none
		 *
		 * @return none
		 */
		public function register_settings ()
		{
			// Remove old plugin settigns
			if( get_option( $this->settings['option_name_old'] ) ) 
			{
				delete_option( $this->settings['option_name_old'] );
			}//end if

			// if option was not found this means the plugin is being installed
			if( !get_option( $this->settings['option_name'] ) ) 
			{
				add_option( $this->settings['option_name'], $this->settings['plugin_data'] );
			}//end if
		}//end register_settings

					
		/**
		 * Remove settings entry in database
		 *
		 * @ignore - Must be public because used in WordPress hooks
		 *
		 * @since 1.0
		 *
		 * @param none
		 *
		 * @return none
		 */
		public function deregister_settings ()
		{
			// install the option defaults
			if( get_option( $this->settings['option_name'] ) ) 
			{
				delete_option( $this->settings['option_name'] );
			}//end if
		}//end deregister_settings

		/**
		 * Add settings links to the plugin option links (on plugins page)
		 *
		 * @ignore - Must be public because used in WordPress hooks
		 *
		 * @since 1.0
		 *
		 * @param none
		 *
		 * @return none (can be overriden to remove settings links if they are not required)
		 */
		public function add_settings_link( $links, $file ) 
		{
			//Static so we don't call plugin_basename on every plugin row.
			static $this_plugin;
			if ( ! $this_plugin ) $this_plugin = $this->settings['file'];
		
			if ( $file == $this_plugin ){
					 $config_link = '<a href="admin.php?page=' . $this->settings['name'] . '.php">' . __( 'Settings', $this->settings['name'] ) . '</a>';
					 array_unshift( $links, $config_link );
			}
			
			return $links;
		} // end function plugin_action_links

		/**
		 * Add the default setting tab to the side menu to display TSP plugins
		 *
		 * @ignore - Must be public because used in WordPress hooks
		 *
		 * @since 1.0
		 *
		 * @param none
		 *
		 * @return none (extend to add submenus to the parent menu)
		 */
		public function add_admin_menu()
		{
			$parent_slug = $this->settings['parent_name'];
			$menu_slug = $this->settings['name'].'.php';

			if ( !menu_page_url( $parent_slug, false ) )
			{
				// Make sure that each setting is nested into a company
				// menu area
				add_menu_page( $this->settings['parent_title'], 
					$this->settings['parent_title'], 
					'manage_options', 
					$parent_slug, 
					array( $this, 'display_parent_page' ), 
					$this->menu_icon, 
					$this->settings['menu_pos']);
			}//endif
					
			if ( !menu_page_url( $menu_slug, false ) )
			{				
				// If there is to be no parent menu then add the settings page as the main page
				if ( empty ( $parent_slug ) )
				{
					// Add menu as a stand-alone
					add_menu_page( __( $this->settings['title_short'], $this->settings['name'] ), 
						__( $this->settings['title_short'], $this->settings['name'] ), 
						'manage_options', 
						$menu_slug, 
						array( $this, 'display_plugin_settings_page' ), 
						$this->menu_icon, 
						$this->settings['menu_pos']);
				}//end if
				else
				{
					// Add child menu
					add_submenu_page($this->settings['parent_name'],
						 __( $this->settings['title_short'], $this->settings['name'] ), 
						 __( $this->settings['title_short'], $this->settings['name'] ), 
						 'manage_options', 
						 $menu_slug, 
						 array( $this, 'display_plugin_settings_page' ));
				}//end else
			}//endif
		}//end add_admin_menu
		
		/**
		 * Add the menu icon to the settings menu
		 *
		 * @since 1.0
		 *
		 * @param none
		 *
		 * @return none (extend to add submenus to the parent menu)
		 */
		private function set_menu_icon( $icon )
		{
			$this->menu_icon = $icon;
		}
		
		/**
		 * Must be implemented by the plugin to include a settings page for the plugin, if not required implement empty
		 *
		 * @api
		 *
		 * @since 1.0
		 *
		 * @param none
		 *
		 * @return none
		 */
		abstract public function display_parent_page();

		/**
		 * Must be implemented by the plugin to include a settings page for the plugin, if not required implement empty
		 *
		 * @api
		 *
		 * @since 1.0
		 *
		 * @param none
		 *
		 * @return none
		 */
		abstract public function display_plugin_settings_page();
		
	}//end TSP_Easy_Plugins_Settings
}//endif
?>