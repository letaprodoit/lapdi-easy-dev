<?php	
if ( !class_exists( 'TSP_Easy_Plugins_Smarty' ) )
{
	/**
	 * TSP_Easy_Plugins_Smarty - Wrapper for the Smarty class
	 * @package TSP_Easy_Plugins
	 * @author sharrondenice, thesoftwarepeople
	 * @author Sharron Denice, The Software People
	 * @copyright 2013 The Software People
	 * @license APACHE v2.0 (http://www.apache.org/licenses/LICENSE-2.0)
	 * @version $Id: [FILE] [] [DATE] [TIME] [USER] $
	 */
	final class TSP_Easy_Plugins_Smarty
	{
		/**
		 * Function to instantiate a smarty with default settings
		 *
		 * @since 1.0.0
		 *
		 * @param array $template_dirs Optional array of template directories
		 * @param string $cache_dir Optional directory for cache
		 * @param string $compiled_dir Optional directory for cache
		 * @param boolean $form Optional are we displaying a form or not
		 *
		 * @return smarty object
		 */
		static public function get_smarty ( $template_dirs = null, $cache_dir = null, $compiled_dir = null, $form = false )
		{
	 		$smarty = new Smarty();
			$smarty->setTemplateDir( $template_dirs );
			
			$smarty->setCompileDir( $cache_dir );
			$smarty->setCacheDir( $compiled_dir );
			
			if ( $form )
			{
				$smarty->assign( 'EASY_PLUGIN_FORM_FIELDS',	'easy-plugin-field.tpl' );
				$smarty->assign('class',					'');
			}//end if
			
			return $smarty;
		}//end get_smarty
	}//end TSP_Easy_Plugins_Smarty
}//end if