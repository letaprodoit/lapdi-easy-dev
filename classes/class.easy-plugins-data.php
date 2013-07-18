<?php
if ( !class_exists( 'TSP_Easy_Plugins_Data' ) )
{
	/**
	 * TSP_Easy_Plugins_Data - Class to manipulate easy plugin global settings
	 * @package TSP_Easy_Plugins
	 * @author sharrondenice, thesoftwarepeople
	 * @author Sharron Denice, The Software People
	 * @copyright 2013 The Software People
	 * @license APACHE v2.0 (http://www.apache.org/licenses/LICENSE-2.0)
	 * @version $Id: [FILE] [] [DATE] [TIME] [USER] $
	 */
	
	final class TSP_Easy_Plugins_Data
	{
		private $fields = array();
		
		/**
		 * PHP4 constructor
		 */
		public function TSP_Easy_Plugins_Data( $fields ) 
		{
			TSP_Easy_Plugins_Data::__construct( $fields );
		}//end TSP_Plugin_Settings
	
		/**
		 * PHP5 constructor
		 */
		public function __construct( $fields ) 
		{
			$this->set( $fields );

		}//end __construct

		/**
		 * Replace settings with values in instance
		 *
		 * @since 1.0.0
		 *
		 * @param array $settings Required - Settings to store in globals
		 *
		 * @return none
		 */
		public function set ( $fields )
		{
			$this->fields = $fields;
		}//end set
		
		/**
		 * Set and process field values
		 *
		 * @since 1.0.0
		 *
		 * @param array $settings Required - Settings to store in globals
		 *
		 * @return array $fields array of field values
		 */
		public function set_values ( $fields )
		{
			if (!empty ( $fields ))
			{
				// don't just assign the settings to global
				// process it to make sure its formatted correctly
				foreach ( $this->fields as $key => $opts )
				{
		        	if ( array_key_exists( $key, $fields ))
		        	{
	        			$value = $this->encode_html( $fields[$key], $this->html_ok ( $opts ) );
			        	
			        	$this->fields[$key]['value'] = $value;
		        	}//end if
				}//end foreach
			}//end if
		}//end set_values

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
		public function set_value_by_key ( $key, $value = null )
		{
			if ( array_key_exists($key, $this->fields ))
        	{
	        	$opts = $this->fields[$key];
	        	
        		$value = $this->encode_html ( $value, $this->html_ok ( $opts ) );
	        	
	        	$this->fields[$key]['value'] = $value;	
        	}//endif
		}//end set_by_key

		/**
		 * Get the current field values
		 *
		 * @since 1.0.0
		 *
		 * @param none
		 *
		 * @return array The assigned global values
		 */
		public function get ()
		{
			return $this->fields;
		}//end get

		
		/**
		 * Get the field values
		 *
		 * @since 1.0.0
		 *
		 * @param bool $values_only Optional - Return array that is in $key/value or all field attributes
		 *
		 * @return array $fields array of field values
		 */
		public function get_values ( $include_options = false )
		{
			$fields = array();
			
			// if we want all field attributes 
			if ( $include_options )
			{
				$fields = $this->fields;
				
			}//endif
			
			foreach ( $this->fields as $key => $opts )
			{
        		$value = $this->decode_html ( $opts['value'] );
	        		        		
	        	// in addition to updating the value we also
	        	// need to add an id and a name for form fields
	        	if ( $include_options )
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
		}//end get_values

		/**
		 * Get the global settings given a key
		 *
		 * @since 1.0.0
		 *
		 * @param string $key Required - The setting key to be used to get the entire array
		 *
		 * @return object The assigned global settings with the specified key
		 */
		public function get_options_by_key ( $key )
		{
			$options = array();

			if ( array_key_exists($key, $this->fields ))
			{
				$options = $this->fields[$key];
			}//end if
			
			return $options;
		}//end get_by_key

		/**
		 * Get the global settings value given a key
		 *
		 * @since 1.0.0
		 *
		 * @param string $key Required - The setting key to be used to get value
		 *
		 * @return string|int The assigned global settings value with the specified key
		 */
		public function get_value_by_key ( $key )
		{
        	$value = "";
			
			if ( array_key_exists($key, $this->fields ))
			{
	        	$opts = $this->fields[$key];
	        	$value = $this->decode_html ( $opts['value'] );
        	}//end if
        	
			return $value;
		}//end get_value_by_key
		
		/**
		 * Process all entries in array for display
		 *
		 * @since 1.0.0
		 *
		 * @param none
		 *
		 * @return none
		 */
		public function decode_values ()
		{
			foreach ( $this->fields as $key => $opts )
			{
    			$value = $this->decode_html ( $opts['value'] );
	        	
	        	$this->fields[$key]['value'] = $value;
			}//end foreach
		}//end decode_values

		
		/**
		 * Process all entries in array for database save
		 *
		 * @since 1.0.0
		 *
		 * @param none
		 *
		 * @return none
		 */
		public function encode_values ()
		{
			foreach ( $this->fields as $key => $opts )
			{
    			$value = $this->encode_html ( $opts['value'], $this->html_ok ( $opts ) );
	        	
	        	$this->fields[$key]['value'] = $value;
			}//end foreach
		}//end encode_values

		/**
		 * Process string for viewing on screen
		 *
		 * @since 1.0.0
		 *
		 * @param string $str Required - String to process
		 *
		 * @return string $str Required - Processed string
		 */
		private function decode_html ( $str )
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
		private function encode_html ( $str, $tags = false )
		{
        	if ( !$tags )
        	{
        		$str = strip_tags ( $str );
        	}//endif
        	
        	$str = preg_replace( '/"/', "'", $str );
        	$str = htmlentities( $str, ENT_QUOTES );

			return $str;
		}//end decode_html
		
		/**
		 * Determine if its ok to store html in the field
		 *
		 * @since 1.0.0
		 *
		 * @param string $str Required - String to process
		 *
		 * @return boolean $html_ok Required - html status
		 */
		private function html_ok ( $arr )
		{
        	$html_ok = false;
        	
        	if ( array_key_exists('html', $arr ))
        	{
        		$html_ok = $arr['html'];
        	}//end if
        	
        	return $html_ok;
		}//end html_ok

	}//end TSP_Easy_Plugins_Data
}//endif
?>