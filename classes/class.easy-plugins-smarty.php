<?php	
if ( !class_exists( 'TSP_Easy_Plugins_Smarty' ) )
{
	if (!class_exists('Smarty'))
	{
	    if (file_exists( plugin_dir_path( __FILE__ ) . 'lib/Smarty/Smarty.class.php' ))
	        require_once plugin_dir_path( __FILE__ ) . 'lib/Smarty/Smarty.class.php';
	}//endif
	
	/**
	 * TSP_Easy_Plugins_Smarty - Wrapper for the Smarty class
	 * @package TSP_Easy_Plugins
	 * @author sharrondenice, thesoftwarepeople
	 * @author Sharron Denice, The Software People
	 * @copyright 2013 The Software People
	 * @license APACHE v2.0 (http://www.apache.org/licenses/LICENSE-2.0)
	 * @version $Id: [FILE] [] [DATE] [TIME] [USER] $
	 */
	final class TSP_Easy_Plugins_Smarty extends Smarty
	{
		/**
		 * Constructor
		 *
		 * @param array $template_dirs Optional array of template directories
		 * @param string $cache_dir Optional directory for cache
		 * @param string $compiled_dir Optional directory for cache
		 * @param boolean $form Optional are we displaying a form or not
		 *
		 */
		public function __construct( $template_dirs = null, $cache_dir = null, $compiled_dir = null, $form = false ) 
		{
			$this->smarty = $this;
			
			// Only use the default globals if they are none in the database
			
			if ( !empty( $template_dirs ))
				$this->setTemplateDir( $template_dirs );
			
			if ( !empty( $cache_dir ))
				$this->setCompileDir( $cache_dir );
			
			if ( !empty( $compiled_dir ))
				$this->setCacheDir( $compiled_dir );
			
			if ( $form )
			{
				$this->assign( 'EASY_PLUGIN_FORM_FIELDS',	'easy-plugin-field.tpl' );
				$this->assign( 'class',						'');
			}//end if
		}//end __construct
	}//end TSP_Easy_Plugins_Smarty
}//end if