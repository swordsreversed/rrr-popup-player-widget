<?php
/*
Plugin Name: RRR Popup Player Widget
Plugin URI:
Description: 2RRR custom popup player widget
Version: 0.4
Author: D.Black
Author URI:
License: GPL2
*/

/**
 * Register the widget
 */
add_action('widgets_init', create_function('', 'return register_widget("RRR_Popup_Player_Widget");'));

/**
 * Class RRR_Popup_Player_Widget
 */
class RRR_Popup_Player_Widget extends WP_Widget {
  /** Basic Widget Settings */
  const WIDGET_NAME = "RRR Popup Player Widget";
  const WIDGET_DESCRIPTION = "2RRR custom popup player widget";

  var $textdomain;
  var $fields;

  /**
   * Construct the widget
   */
  function __construct() {
    //We're going to use $this->textdomain as both the translation domain and the widget class name and ID
    $this->textdomain = strtolower(get_class($this));

    //Figure out your textdomain for translations via this handy debug print
    //var_dump($this->textdomain);

    //Add fields
    $this->add_field('source', 'Stream source', 'http://110.142.218.7:88/broadwave.mp3', 'text');
    $this->add_field('player_height', 'Player page height', '330', 'text');
    $this->add_field('player_width', 'Player page width', '430', 'text');

    //Translations
    //load_plugin_textdomain($this->textdomain, false, basename(dirname(__FILE__)) . '/languages' );

    //Init the widget
    parent::__construct($this->textdomain, __(self::WIDGET_NAME, $this->textdomain), array( 'description' => __(self::WIDGET_DESCRIPTION, $this->textdomain), 'classname' => $this->textdomain));
  }

  /**
   * Widget frontend
   *
   * @param array $args
   * @param array $instance
   */
  public function widget($args, $instance) {
    $title = apply_filters('widget_title', $instance['title']);

    /* Before and after widget arguments are usually modified by themes */
    echo $args['before_widget'];

    if (!empty($title))
      echo $args['before_title'] . $title . $args['after_title'];

    /* Widget output here */
    $this->widget_output($args, $instance);

    /* After widget */
    echo $args['after_widget'];
  }

  /**
   * This function will execute the widget frontend logic.
   * Everything you want in the widget should be output here.
   *
   * @param array $args
   * @param array $instance
   */
  private function widget_output($args, $instance) {
    extract($instance);

    ?>
    <script>
      var windowObjectReference;
      var strWindowFeatures = "menubar=no,location=0,resizable=no,scrollbars=no,status=no,height=<?php echo $player_height; ?>,width=<?php echo $player_width; ?>";

      function openRequestedPopup() {
        windowObjectReference = window.open("/livestream", "2RRR Popup Player", strWindowFeatures);
      }
    </script>
    <?php
      echo do_shortcode('[audio src='.$source.']');
    ?>
    <div class="gutter-top">
      <a href="javascript: openRequestedPopup();">Listen Online Now - <small>Popup Player</small></a>
    </div>

    <?php
  }

  /**
   * Widget backend
   *
   * @param array $instance
   * @return string|void
   */
  public function form( $instance ) {
    /* Generate admin for fields */
    foreach($this->fields as $field_name => $field_data) {
      if($field_data['type'] === 'text'):
	?>
	<p>
	  <label for="<?php echo $this->get_field_id($field_name); ?>"><?php _e($field_data['description'], $this->textdomain ); ?></label>
	  <input class="widefat" id="<?php echo $this->get_field_id($field_name); ?>" name="<?php echo $this->get_field_name($field_name); ?>" type="text" value="<?php echo esc_attr(isset($instance[$field_name]) ? $instance[$field_name] : $field_data['default_value']); ?>" />
	</p>
      <?php
      //elseif($field_data['type'] == 'textarea'):
      //You can implement more field types like this.
      else:
	echo __('Error - Field type not supported', $this->textdomain) . ': ' . $field_data['type'];
      endif;
    }
  }

  /**
   * Adds a text field to the widget
   *
   * @param $field_name
   * @param string $field_description
   * @param string $field_default_value
   * @param string $field_type
   */
  private function add_field($field_name, $field_description = '', $field_default_value = '', $field_type = 'text') {
    if(!is_array($this->fields))
      $this->fields = array();

    $this->fields[$field_name] = array('name' => $field_name, 'description' => $field_description, 'default_value' => $field_default_value, 'type' => $field_type);
  }

  /**
   * Updating widget by replacing the old instance with new
   *
   * @param array $new_instance
   * @param array $old_instance
   * @return array
   */
  public function update($new_instance, $old_instance) {
    return $new_instance;
  }
}

