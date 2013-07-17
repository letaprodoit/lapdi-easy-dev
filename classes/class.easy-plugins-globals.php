<?php
if ( !class_exists( 'TSP_Easy_Plugins_Globals' ) )
{
	/**
	 * TSP_Easy_Plugins_Globals - Class to manipulate easy plugin global settings
	 * @package TSP_Easy_Plugins
	 * @author sharrondenice, thesoftwarepeople
	 * @author Sharron Denice, The Software People
	 * @copyright 2013 The Software People
	 * @license APACHE v2.0 (http://www.apache.org/licenses/LICENSE-2.0)
	 * @version $Id: [FILE] [] [DATE] [TIME] [USER] $
	 */
	
	final class TSP_Easy_Plugins_Globals
	{
		private $globals = array();
		
		/**
		 * PHP4 constructor
		 */
		public function TSP_Easy_Plugins_Globals( $globals ) 
		{
			TSP_Easy_Plugins_Globals::__construct( $globals );
		}//end TSP_Plugin_Settings
	
		/**
		 * PHP5 constructor
		 */
		public function __construct( $globals ) 
		{
			$this->set( $globals);

		}//end __construct

		/**
		 * Store global values
		 *
		 * @since 1.0.0
		 *
		 * @param array $globals Required. The value to be stored and manipulated
		 *
		 * @return none
		 */
		public function set ( $globals )
		{
			$this->globals = $globals;
		}//end load_defaults
		
		/**
		 * Get the global values
		 *
		 * @since 1.0.0
		 *
		 * @param none
		 *
		 * @return array The assigned global values
		 */
		public function get ()
		{
			return $this->globals;
		}//end load_defaults
		
		/**
		 * Get the settings only
		 *
		 * @since 1.0.0
		 *
		 * @param none
		 *
		 * @return array The assigned global settings
		 */
		public function get_form_fields ()
		{
			return $this->globals['form_fields'];
		}//end get_data

		/**
		 * Get the global settings given a key
		 *
		 * @since 1.0.0
		 *
		 * @param string $key Required - The setting key to be used to get the entire array
		 *
		 * @return object The assigned global settings with the specified key
		 */
		public function get_form_field_by_key ( $key )
		{
			$fields = array();
			
			if ( array_key_exists($key, $this->globals['form_fields'] ))
			{
				$fields = $this->globals['form_fields'][$key];
			}//end if
			
			return $fields;
		}//end get_data_by_key

		/**
		 * Get the global settings value given a key
		 *
		 * @since 1.0.0
		 *
		 * @param string $key Required - The setting key to be used to get value
		 *
		 * @return string|int The assigned global settings value with the specified key
		 */
		public function get_form_field_value_by_key ( $key )
		{
        	$value = "";
        	
			if ( array_key_exists($key, $this->globals['form_fields'] ))
			{
	        	$opts = $this->globals['form_fields'][$key];
	        	$value = $this->decode_html ( $opts['value'] );
        	}//end if

			return $value;
		}//end get_data_value_by_key

		/**
		 * Get all the global settings
		 *
		 * @since 1.0.0
		 *
		 * @param bool $form Optional - Return array that is in form format or in key/value format
		 *
		 * @return array The assigned global settings
		 */
		public function get_form_field_values ( $form = false )
		{
			$fields = array();
			
			// if form is true then 
			if ( $form )
			{
				$fields = $this->globals['form_fields'];
				
			}//endif
			
			foreach ( $this->globals['form_fields'] as $key => $opts )
			{
        		$value = $this->decode_html ( $opts['value'] );
	        		        		
	        	if ( $form )
	        	{
					$fields[$key]['id'] 	= $key;
					$fields[$key]['name'] 	= $key;
					$fields[$key]['value'] 	= $value;
	        	}//endif
	        	else
	        	{	
	        		$fields[$key] = $value;
	        	}//endelse
	        	
			}//end foreach
			
			return $fields;
		}//end get_data_values

		/**
		 * Set a global settings with specified key with value
		 *
		 * @since 1.0.0
		 *
		 * @param string $key Required - The setting key to be set
		 * @param string $value Optional - The value to set the key
		 *
		 * @return none
		 */
		public function set_form_field_value ( $key, $value = null )
		{
			if ( array_key_exists($key, $this->globals['form_fields'] ))
        	{
	        	$opts = $this->globals['form_fields'][$key];
	        	
        		$value = $this->encode_html ( $value, $this->html_ok ( $opts ) );
	        	
	        	$this->globals['form_fields'][$key]['value'] = $value;	
        	}//endif
		}//end get_data_value

		/**
		 * Replace settings with values in instance
		 *
		 * @since 1.0.0
		 *
		 * @param array $settings Required - Settings to store in globals
		 *
		 * @return none
		 */
		public function set_form_field_values ( $fields )
		{
			if ( !empty($fields) )
			{
				// don't just assign the settings to global
				// process it to make sure its formatted correctly
				foreach ( $fields as $key => $value )
				{
					if ( array_key_exists($key, $this->globals['form_fields'] ))
					{
			        	$opts = $this->globals['form_fields'][$key];
			        	
	        			$value = $this->decode_html ( $value, $this->html_ok ( $opts ) );
			        	
			        	$this->globals['form_fields'][$key]['value'] = $value;
		        	}//endif
				}//end foreach
			}//end if
		}//end set_data_values
		
		/**
		 * Process all entries in array for display
		 *
		 * @since 1.0.0
		 *
		 * @param none
		 *
		 * @return none
		 */
		public function decode_form_field_values ()
		{
			foreach ( $this->globals['form_fields'] as $key => $opts )
			{
    			$value = $this->decode_html ( $opts['value'], $this->html_ok ( $opts ) );
	        	
	        	$this->globals['form_fields'][$key]['value'] = $value;
			}//end foreach
		}//end decode_form_field_values

		
		/**
		 * Process all entries in array for database save
		 *
		 * @since 1.0.0
		 *
		 * @param none
		 *
		 * @return none
		 */
		public function encode_form_field_values ()
		{
			foreach ( $this->globals['form_fields'] as $key => $opts )
			{
    			$value = $this->encode_html ( $opts['value'], $this->html_ok ( $opts ) );
	        	
	        	$this->globals['form_fields'][$key]['value'] = $value;
			}//end foreach
		}//end encode_form_field_values

		/**
		 * Process string for viewing on screen
		 *
		 * @since 1.0.0
		 *
		 * @param string $str Required - String to process
		 *
		 * @return string $str Required - Processed string
		 */
		protected function decode_html ( $str )
		{
        	$str = stripslashes ( $str );
        	$str = preg_replace( '/"/', "'", $str );
        	$str = html_entity_decode( $str, ENT_QUOTES );

			return $str;
		}//end decode_html

		/**
		 * Process string for saving to database
		 *
		 * @since 1.0.0
		 *
		 * @param string $str Required - String to process
		 *
		 * @return string $str Required - Processed string
		 */
		protected function encode_html ( $str, $tags = false )
		{
        	if ( !$tags )
        	{
        		$str = strip_tags ( $str );
        	}//endif
        	
        	$str = preg_replace( '/"/', "'", $str );
        	$str = htmlentities( $str, ENT_QUOTES );

			return $str;
		}//end decode_html
		
		protected function html_ok ( $arr )
		{
        	$html = false;
        	
        	if ( array_key_exists('html', $arr ))
        	{
        		if ( $arr['html'] )
        		{
        			$html = true;
        		}//end if
        	}//end if
        	
        	return $html;
		}//end html_ok

	}//end TSP_Easy_Plugins_Globals
}//endif
?>