<?php
if ( ! defined( 'ABSPATH' ) ) exit;

abstract class SIR_COMM_Widget extends WP_Widget {
	/**
	 * CSS class.
	 *
	 * @var string
	 */
	public $widget_cssclass;

	/**
	 * Widget description.
	 *
	 * @var string
	 */
	public $widget_description;

	/**
	 * Widget ID.
	 *
	 * @var string
	 */
	public $widget_id;

	/**
	 * Widget name.
	 *
	 * @var string
	 */
	public $widget_name;

	/**
	 * Settings.
	 *
	 * @var array
	 */
	public $settings;

	/**
	 * Constructor.
	 */
	public function __construct() {

        if( ! defined('GC_NAME') ){     //그누커머스 플러그인이 없다면
            return;
        }

		$widget_ops = array(
			'classname'   => $this->widget_cssclass,
			'description' => $this->widget_description
		);

		parent::__construct( $this->widget_id, $this->widget_name, $widget_ops );

		add_action( 'save_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );
	}

	/**
	 * Get cached widget.
	 *
	 * @param  array $args
	 * @return bool true if the widget is cached otherwise false
	 */
	public function get_cached_widget( $args ) {

		$cache = wp_cache_get( apply_filters( 'sir_comm_cached_widget_id', $this->widget_id ), 'widget' );

		if ( ! is_array( $cache ) ) {
			$cache = array();
		}

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo $cache[ $args['widget_id'] ];
			return true;
		}

		return false;
	}

	/**
	 * Cache the widget.
	 *
	 * @param  array $args
	 * @param  string $content
	 * @return string the content that was cached
	 */
	public function cache_widget( $args, $content ) {
		wp_cache_set( apply_filters( 'sir_comm_cached_widget_id', $this->widget_id ), array( $args['widget_id'] => $content ), 'widget' );

		return $content;
	}

	/**
	 * Flush the cache.
	 */
	public function flush_widget_cache() {
		wp_cache_delete( apply_filters( 'sir_comm_cached_widget_id', $this->widget_id ), 'widget' );
	}

	/**
	 * Output the html at the start of a widget.
	 *
	 * @param  array $args
	 * @return string
	 */
	public function widget_start( $args, $instance ) {
		echo $args['before_widget'];

		if ( $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
	}

	/**
	 * Output the html at the end of a widget.
	 *
	 * @param  array $args
	 * @return string
	 */
	public function widget_end( $args ) {
		echo $args['after_widget'];
	}

	/**
	 * Updates a particular instance of a widget.
	 *
	 * @see    WP_Widget->update
	 * @param  array $new_instance
	 * @param  array $old_instance
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		if ( empty( $this->settings ) ) {
			return $instance;
		}

		// Loop settings and get values to save.
		foreach ( $this->settings as $key => $setting ) {
			if ( ! isset( $setting['type'] ) ) {
				continue;
			}

			// Format the value based on settings type.
			switch ( $setting['type'] ) {
				case 'number' :
					$instance[ $key ] = absint( $new_instance[ $key ] );

					if ( isset( $setting['min'] ) && '' !== $setting['min'] ) {
						$instance[ $key ] = max( $instance[ $key ], $setting['min'] );
					}

					if ( isset( $setting['max'] ) && '' !== $setting['max'] ) {
						$instance[ $key ] = min( $instance[ $key ], $setting['max'] );
					}
				break;
				case 'textarea' :
					$instance[ $key ] = wp_kses( trim( wp_unslash( $new_instance[ $key ] ) ), wp_kses_allowed_html( 'post' ) );
				break;
				case 'checkbox' :
					$instance[ $key ] = is_null( $new_instance[ $key ] ) ? 0 : 1;
				break;
				default:
					$instance[ $key ] = sanitize_text_field( $new_instance[ $key ] );
				break;
			}

			/**
			 * Sanitize the value of a setting.
			 */
			$instance[ $key ] = apply_filters( 'sir_comm_widget_settings_sanitize_option', $instance[ $key ], $new_instance, $key, $setting );
		}

		$this->flush_widget_cache();

		return $instance;
	}

	/**
	 * Outputs the settings update form.
	 *
	 * @see   WP_Widget->form
	 * @param array $instance
	 */
	public function form( $instance ) {

		if ( empty( $this->settings ) ) {
			return;
		}

		foreach ( $this->settings as $key => $setting ) {

			$class = isset( $setting['class'] ) ? $setting['class'] : '';
			$value = isset( $instance[ $key ] ) ? $instance[ $key ] : $setting['std'];

			switch ( $setting['type'] ) {

				case 'text' :
					?>
					<p>
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
						<input class="widefat <?php echo esc_attr( $class ); ?>" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" type="text" value="<?php echo esc_attr( $value ); ?>" />
					</p>
					<?php
				break;

				case 'number' :
					?>
					<p>
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
						<input class="widefat <?php echo esc_attr( $class ); ?>" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" type="number" step="<?php echo esc_attr( $setting['step'] ); ?>" min="<?php echo esc_attr( $setting['min'] ); ?>" max="<?php echo esc_attr( $setting['max'] ); ?>" value="<?php echo esc_attr( $value ); ?>" />
					</p>
					<?php
				break;

				case 'select' :
					?>
					<p>
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
						<select class="widefat <?php echo esc_attr( $class ); ?>" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>">
							<?php foreach ( $setting['options'] as $option_key => $option_value ) : ?>
								<option value="<?php echo esc_attr( $option_key ); ?>" <?php selected( $option_key, $value ); ?>><?php echo esc_html( $option_value ); ?></option>
							<?php endforeach; ?>
						</select>
					</p>
					<?php
				break;

				case 'textarea' :
					?>
					<p>
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
						<textarea class="widefat <?php echo esc_attr( $class ); ?>" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" cols="20" rows="3"><?php echo esc_textarea( $value ); ?></textarea>
						<?php if ( isset( $setting['desc'] ) ) : ?>
							<small><?php echo esc_html( $setting['desc'] ); ?></small>
						<?php endif; ?>
					</p>
					<?php
				break;

				case 'checkbox' :
					?>
					<p>
						<input class="checkbox <?php echo esc_attr( $class ); ?>" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $key ) ); ?>" type="checkbox" value="1" <?php checked( $value, 1 ); ?> />
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
					</p>
					<?php
				break;

				// Default: run an action
				default :
					do_action( 'sir_comm_widget_field_' . $setting['type'], $key, $value, $setting, $instance );
				break;
			}
		}
	}
}

class sir_latest_board_widget extends SIR_COMM_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
        
        //그누커머스 플러그인이 없다면
        if( !defined('GC_BOARD_KEY') ) return;

        global $wpdb;

        $result_array = array();
        $board_array = array();

        /*
        $dirname = get_template_directory().'/gnucommerce/skin/latest/';
        $handle = opendir($dirname);
        while ($file = readdir($handle)) {
            if($file == '.'||$file == '..') continue;

            if (is_dir($dirname.$file)) $result_array[] = $file;
        }
        closedir($handle);
        */

        $result_array = array('basic', 'gallery', 'tip');

        $result_array = apply_filters('sir_comm_get_latest_skin_folder', array_combine($result_array, $result_array) );

        $gc_boards = get_option(GC_BOARD_KEY);

        if( isset($gc_boards['board_page']) ){
            $board_array = array_keys($gc_boards['board_page']);
        }

        $board_array = apply_filters('sir_comm_get_bo_table_list', array_combine($board_array, $board_array) );

		$this->widget_cssclass    = 'sir_comm widget_latest';
		$this->widget_description = __( '그누커머스 최신글 위젯입니다.', SIR_CMM_NAME );
		$this->widget_id          = 'sir_comm_latest';
		$this->widget_name        = __( '그누커머스 최신글 위젯', SIR_CMM_NAME );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => __( '최신글', SIR_CMM_NAME ),
				'label' => __( '제목', SIR_CMM_NAME )
			),
			'rows' => array(
				'type'  => 'number',
				'step'  => 1,
				'min'   => 1,
				'max'   => 20,
				'std'   => 5,
				'label' => __( '출력할 갯수', SIR_CMM_NAME ),
			),
			'bo_table' => array(
				'type'  => 'select',
				'std'   => '',
				'label' => __( '출력할 게시판', SIR_CMM_NAME ),
				'options' => $board_array,
			),
			'skin_dir' => array(
				'type'  => 'select',
				'std'   => '',
				'label' => __( '스킨폴더', SIR_CMM_NAME ),
				'options' => $result_array,
			),
			'row_mod' => array(
				'type'  => 'number',
				'step'  => 1,
				'min'   => 1,
				'max'   => 6,
				'std'   => 3,
				'label' => __( '이미지 스킨일 경우 한줄에 출력할 갯수 설정', SIR_CMM_NAME ),
			),
            'url'   => array(
                'type'  => 'text',
				'std'   => '',
                'label' => __( '주소( url )를 지정합니다.', SIR_CMM_NAME ),
            ),
			'subject_len' => array(
				'type'  => 'number',
				'step'  => 1,
				'min'   => 10,
				'max'   => 40,
				'std'   => 40,
				'label' => __( '출력할 제목 길이', SIR_CMM_NAME ),
			),
			'content_len' => array(
				'type'  => 'number',
				'step'  => 1,
				'min'   => 10,
				'max'   => 80,
				'std'   => 80,
				'label' => __( '출력할 내용 길이', SIR_CMM_NAME ),
			),
			'cache_time' => array(
				'type'  => 'number',
				'step'  => 1,
				'min'   => 0,
				'max'   => '',
				'std'   => 1,
				'label' => __( '캐쉬사용( 미사용시 0으로 설정 )', SIR_CMM_NAME ),
			),
		);

		parent::__construct();
	}

	/**
	 * Output widget.
	 *
	 * @see WP_Widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		if ( $this->get_cached_widget( $args ) ) {
			return;
		}

		ob_start();

        echo $args['before_widget'];

		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'sir_comm_latest_widget_title', $instance['title'] ). $args['after_title'];
		}

        $widget_attr = '';

        $attributes = wp_parse_args( $instance, array(
            'rows' => 5 ,
            'url'  => '',
            'row_mod'   => 3,
        ));

        foreach($attributes as $key => $value){


            $widget_attr .= $key.'="'.$value.'" ';
        }

        echo do_shortcode('[gnucommerce_board_latest '.$widget_attr.' ]');

        echo $args['after_widget'];

		echo $this->cache_widget( $args, ob_get_clean() );
	}
}
