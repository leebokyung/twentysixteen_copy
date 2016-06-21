<?php
if( ! defined( 'ABSPATH' ) ) exit;

if( class_exists( 'WP_Customize_Control' ) ):
	class WP_Customize_summeripha_button_set extends WP_Customize_Control {
		public $type = 'summeripha_button_set';

        public function enqueue() {

            wp_enqueue_style ( 
                'jquery-ui', 
                get_template_directory_uri() . '/core/admin/assets/css/jquery-ui-1.9.2.custom.css', 
                array(), 
                ''
            );

            wp_enqueue_script('jquery');
            wp_enqueue_script('jquery-ui-core');
            wp_enqueue_script('jquery-ui-button');

            wp_enqueue_script( 
                  'summeripha_field_button_set_js',
                  get_template_directory_uri().'/core/admin/inc/button_set.js',
                  array( 'jquery', 'jquery-ui-core' ),
                  '1.0.0', 
                  true
            );
        }

		public function render_content() {
            
            $value = $this->value();
            
            $options = !empty($this->choices) ? $this->choices : array();
            $is_multi = false;
            $class = '';

            echo '<div class="summeripha_field_th">'.esc_html($this->label);
            echo '<span class="description">'.esc_html($this->description).'</span></div>';

            echo '<div class="buttonset ui-buttonset">';
            $i = 0;

            foreach ( $options as $k => $v ) {
                if( empty($v) ) continue;

                $selected = '';
                if ( $is_multi ) {
                    $type         = "checkbox";
                    $multi_suffix = '[]';

                    if ( ! empty( $value ) && ! is_array( $value ) ) {
                        $value = array( $this->value );
                    }

                    if ( is_array( $value ) && in_array( $k, $value ) ) {
                        $selected = 'checked="checked"';
                    }

                } else {
                    $multi_suffix = "";
                    $type         = "radio";

                    if ( is_scalar( $value ) ) {
                        $selected = checked( $value, $k, false );
                    }
                }

                echo '<input data-id="' . $this->id . '" type="' . $type . '" id="' . $this->id . '-buttonset' . $k . '" name="' . $this->id . $multi_suffix . '" class="buttonset-item ' . $class . '" value="' . $k . '" ' . $selected . '/>';
                echo '<label for="' . $this->id . '-buttonset' . $k . '">' . $v . '</label>';
            }
            echo '</div>';

		}
	}
endif;

?>