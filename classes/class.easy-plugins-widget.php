<?php
if ( !class_exists( 'TSP_Easy_Plugins_Widget' ) )
{
	/**
	 * TSP_Easy_Plugins_Widget - Class to extend WP_Widget to show widget fields, save and load settings
	 * @package TSP_Easy_Plugins
	 * @author sharrondenice, thesoftwarepeople
	 * @author Sharron Denice, The Software People
	 * @copyright 2013 The Software People
	 * @license APACHE v2.0 (http://www.apache.org/licenses/LICENSE-2.0)
	 * @version $Id: [FILE] [] [DATE] [TIME] [USER] $
	 */
	abstract class TSP_Easy_Plugins_Widget extends WP_Widget 
	{
		protected $plugin_globals = null;
		
		/**
		 * Constructor
		 */
		public function __construct( $globals ) 
		{
			$this->plugin_globals = $globals;
			
	        // Get widget options
	        $widget_options  = array(
	            'classname'  			=> $this->plugin_globals['name'],
	            'description'   		=> __( $this->plugin_globals['Description'], $this->plugin_globals['name'] )
	        );
	        
	        // Get control options
	        $control_options = array(
	            'width' 				=> $this->plugin_globals['widget_width'],
	            'height'				=> $this->plugin_globals['widget_height'],
	            'id_base' 				=> $this->plugin_globals['name'],
	        );

			$this->load_shortcodes();

	        // Create the widget
			parent::__construct( $this->plugin_globals['name'], __( $this->plugin_globals['Name'], $this->plugin_globals['name'] ) , $widget_options, $control_options);
		}//end __construct
	
		/**
		 * Implements update function
		 *
		 * @since 1.0.0
		 *
	 	 * @param array $new_instance Values just sent to be saved.
		 * @param array $old_instance Previously saved values from database.
		 *
		 * @return array Updated safe values to be saved.
		 */
		public function update( $new_instance, $old_instance ) 
		{
			$plugin_data = get_option( $this->plugin_globals['option_name'] );
			$defaults = new TSP_Easy_Plugins_Data ( $plugin_data['widget_fields'] );

			if ( !empty ( $new_instance ))
			{
				$defaults->set_values( $new_instance ); // overwrite defaults with new instance (user data)
			}//endif
			else
			{
				$defaults->encode_values();
			}//endelse
			
			$instance = $defaults->get_values();
	        
	        return $instance;
		}//end update
	
	
		/**
		 * widget function can be overriden by the plugin to display plugin widget info to screen
		 *
		 * @since 1.0.0
		 *
		 * @param none
		 *
		 * @return none
		 */
		public function widget( $args, $instance )
		{
			$plugin_data = get_option( $this->plugin_globals['option_name'] );
			$defaults = new TSP_Easy_Plugins_Data ( $plugin_data['widget_fields'] );
			
			if ( !empty ( $instance ))
			{
				$defaults->set_values( $instance );
			}//endif
			else
			{
				$defaults->decode_values();
			}//endelse
			
			$fields = $defaults->get_values();

	        if ( !empty( $args ) )
	        {
	        	extract($args);
	        }//end if
	        
	        // Display data before widget
	        if ( isset( $before_widget ) )
	        {	
	        	echo $before_widget;
	        }//end if
	        
	        $this->display_widget( $fields );
	        
	        // Display data after widget
	        if ( isset( $after_widget ) )
	        {	
	        	echo $after_widget;
	        }//end if
		}//end widget
		
		/**
		 * form function must be expanded by the plugin to display plugin widget info to screen
		 *
		 * @since 1.0.0
		 *
		 * @param array $instance Required - Data to be displayed on the form
		 *
		 * @return none
		 */
	 	public function form( $instance )
	 	{
			$plugin_data = get_option( $this->plugin_globals['option_name'] );
			$defaults = new TSP_Easy_Plugins_Data ( $plugin_data['widget_fields'] );

			if ( !empty ( $instance ) )
			{
				$defaults->set_values( $instance );
			}//endif
			else
			{
				$defaults->decode_values();
			}//endelse
						
			$fields = $defaults->get_values( true );

			$this->display_form ( $fields );
	 	}//end form
	
		/**
		 * Process all shorcodes associated with this widget
		 *
		 * @since 1.0.0
		 *
		 * @param none
		 *
		 * @return none
		 */
	 	public function load_shortcodes()
	 	{
			if ( !empty ($this->plugin_globals['shortcodes']) )
			{
				// add all the associated shortcodes associated with this widget
				foreach ( $this->plugin_globals['shortcodes'] as $code )
				{
					add_shortcode($code, array( $this, 'process_shortcode') );
				}//endforeach
			}//endif
	 	}//end form

	
		/**
		 * Process shortcodes passed to widget
		 *
		 * @since 1.0.0
		 *
		 * @param array $attributes Optional the arguments passed to the shortcode
		 *
		 * @return none
		 */
		public function process_shortcode( $attributes )
		{
			if ( is_feed() )
				return '[' . $this->plugin_globals['name'] . ']';
						
			$plugin_data = get_option( $this->plugin_globals['option_name'] );
			$defaults = new TSP_Easy_Plugins_Data ( $plugin_data['widget_fields'] );

			if (! empty ( $attributes) )
			{
				$fields = $defaults->get_values( true );

				// Update attributes to include old attribute names from short codes
				// Backwards compatibility
				foreach ( $fields as $key => $opts )
				{
					// continue if this label was renamed
					if ( !empty( $opts['old_labels'] ) )
					{
						$new_label = $key;
						// looop through all the old label names
						foreach ( $opts['old_labels'] as $old_label )
						{
							// if the new label isn't being used and the user is using the old label
							// set the new labels value to the old label's value
							if ( !array_key_exists( $new_label, $attributes ) && array_key_exists( $old_label, $attributes ) )
							{
								$attributes[$new_label] = $attributes[$old_label];
								unset($attributes[$old_label]);
							}//end fi
						}//end foreach
					}//end if
				}//end foreach
			}//end if
			
			if ( !empty ( $attributes ))
			{
				$defaults->set_values( $attributes );
			}//endif
			else
			{
				$defaults->decode_values();
			}//endelse
			
			$fields = $defaults->get_values();

			$output = $this->display_widget( $fields, false );
			
			return $output;
		}//end process_shortcode
		
		/**
		 * Required: Must be implemented by the plugin to display the HTML to the screen
		 *
		 * @since 1.0.0
		 *
		 * @param array $fields Data to display to the screen
		 *
		 * @return none
		 */
		abstract public function display_form( $fields );

		/**
		 * Required: Must be implemented by the plugin to display the HTML to the screen
		 *
		 * @since 1.0.0
		 *
		 * @param array $instance Required data to display to the screen
		 * @param boolean $echo Optional if true display data to screen
		 *
		 * @return none
		 */
		abstract public function display_widget( $fields, $echo = false );
		
	}//end TSP_Easy_Plugins_Widget
}//endif
?>