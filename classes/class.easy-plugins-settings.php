<?php	
if ( !class_exists( 'TSP_Easy_Plugins_Settings' ) )
{
	/**
	 * TSP_Easy_Plugins_Settings - Class to display admin settings in admin area
	 * @package TSP_Easy_Plugins
	 * @author sharrondenice, thesoftwarepeople
	 * @author Sharron Denice, The Software People
	 * @copyright 2013 The Software People
	 * @license APACHE v2.0 (http://www.apache.org/licenses/LICENSE-2.0)
	 * @version $Id: [FILE] [] [DATE] [TIME] [USER] $
	 */
	abstract class TSP_Easy_Plugins_Settings
	{
		protected $plugin_globals = null; // sub-classes can call directly
		
		private $menu_icon = null;
				
		/**
		 * PHP4 constructor
		 */
		public function TSP_Easy_Plugins_Settings() 
		{
			TSP_Easy_Plugins_Settings::__construct();
		}//end TSP_Plugin_Settings
	
		/**
		 * PHP5 constructor
		 */
		public function __construct() 
		{
		}//end __construct
		
					
		/**
		 * Create settings entry in database
		 *
		 * @since 1.0.0
		 *
		 * @param none
		 *
		 * @return none
		 */
		public function register_settings ()
		{
			// Remove old plugin settigns
			if( get_option( $this->plugin_globals['option_name_old'] ) ) 
			{
				delete_option( $this->plugin_globals['option_name_old'] );
			}//end if

			// if option was not found this means the plugin is being installed
			if( !get_option( $this->plugin_globals['option_name'] ) ) 
			{
				add_option( $this->plugin_globals['option_name'], $this->plugin_globals );
			}//end if
		}//end register_settings

					
		/**
		 * Remove settings entry in database
		 *
		 * @since 1.0.0
		 *
		 * @param none
		 *
		 * @return none
		 */
		public function deregister_settings ()
		{
			// install the option defaults
			if( get_option( $this->plugin_globals['option_name'] ) ) 
			{
				delete_option( $this->plugin_globals['option_name'] );
			}//end if
		}//end deregister_settings

				
		/**
		 * Intialize the settings class
		 *
		 * @since 1.0.0
		 *
		 * @param array $globals Required the default plugin settings
		 *
		 * @return none
		 */
		public function init ($globals)
		{
			$this->plugin_globals				= $globals;

			add_action( 'admin_menu', 			array( $this, 'add_admin_menu' ) );
			add_filter( 'plugin_action_links', 	array( $this, 'add_settings_link'), 10, 2 );
			
			$this->register_settings();
		}//end register_settings

		/**
		 * Add settings links to the plugin option links (on plugins page)
		 *
		 * @since 1.0.0
		 *
		 * @param none
		 *
		 * @return none (can be overriden to remove settings links if they are not required)
		 */
		public function add_settings_link( $links, $file ) 
		{
			//Static so we don't call plugin_basename on every plugin row.
			static $this_plugin;
			if ( ! $this_plugin ) $this_plugin = $this->plugin_globals['file'];
		
			if ( $file == $this_plugin ){
					 $config_link = '<a href="admin.php?page=' . $this->plugin_globals['name'] . '.php">' . __( 'Settings', $this->plugin_globals['name'] ) . '</a>';
					 array_unshift( $links, $config_link );
			}
			
			return $links;
		} // end function plugin_action_links

		/**
		 * Add the default setting tab to the side menu to display TSP plugins
		 *
		 * @since 1.0.0
		 *
		 * @param none
		 *
		 * @return none (extend to add submenus to the parent menu)
		 */
		public function add_admin_menu()
		{
			$parent_slug = $this->plugin_globals['parent_name'];
			$menu_slug = $this->plugin_globals['name'].'.php';

			if ( !menu_page_url( $parent_slug, false ) )
			{
				// Make sure that each setting is nested into a company
				// menu area
				add_menu_page( $this->plugin_globals['parent_title'], 
					$this->plugin_globals['parent_title'], 
					'manage_options', 
					$parent_slug, 
					array( $this, 'display_parent_page' ), 
					$this->menu_icon, 
					$this->plugin_globals['parent_menu']);
			}//endif
					
			if ( !menu_page_url( $menu_slug, false ) )
			{				
				// Add child menu
				add_submenu_page($this->plugin_globals['parent_name'],
					 __( $this->plugin_globals['title_short'], $this->plugin_globals['name'] ), 
					 __( $this->plugin_globals['title_short'], $this->plugin_globals['name'] ), 
					 'manage_options', 
					 $menu_slug, 
					 array( $this, 'display_plugin_settings_page' ));
			}//endif
		}//end add_admin_menu
		
		/**
		 * Add the menu icon to the settings menu
		 *
		 * @since 1.0.0
		 *
		 * @param none
		 *
		 * @return none (extend to add submenus to the parent menu)
		 */
		public function set_menu_icon( $icon )
		{
			$this->menu_icon = $icon;
		}
		
		/**
		 * Must be implemented by the plugin to include a settings page for the plugin, if not required implement empty
		 *
		 * @since 1.0.0
		 *
		 * @param none
		 *
		 * @return none
		 */
		abstract public function display_parent_page();

		/**
		 * Must be implemented by the plugin to include a settings page for the plugin, if not required implement empty
		 *
		 * @since 1.0.0
		 *
		 * @param none
		 *
		 * @return none
		 */
		abstract public function display_plugin_settings_page();
		
	}//end TSP_Easy_Plugins_Settings
}//endif
?>