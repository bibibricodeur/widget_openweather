<?php
/*
Plugin Name: Widget OpenWeather (météo)
Plugin URI: https://github.com/bibibricodeur
Description:
Version: 00.1
Author: bibibricodeur
Author URI: https://thiweb.fr
License: WTFPL
*/

// https://codex.wordpress.org/Widgets_API

class Widget_OpenWeather extends WP_Widget {

	/**
	 * Configure le nom des widgets, etc.
	 */
	public function __construct() {
		$widget_ops = array( 
			'classname' => 'widget_openweather',
			'description' => "Afficher la météo d'une ville à travers l'api d'OpenWeather",
		);
		parent::__construct( 'widget_openweather', 'Widget OpenWeather (météo)', $widget_ops );
	}

	/**
	 * Affiche le contenu du widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		// affiche le contenu du widget
		echo $args['before_widget'];
		//print_r( $instance );
		if ( ! empty( $instance['idville'] ) && ! empty( $instance['cleapi'] )) {
		    $requete = ( 'https://api.openweathermap.org/data/2.5/weather?id=' . $instance['idville'] . '&appid=' . $instance['cleapi'] . '&lang=fr&units=metric' );
            $reponse    = file_get_contents($requete);
            $objetsjson = json_decode($reponse);
            //var_dump($objetsjson);
            $patelin    = $objetsjson -> name;
            //echo $patelin;
			echo $args['before_title'] . 'Météo ' . apply_filters( 'widget_title', $patelin ) . $args['after_title'];
		//}
		//echo esc_html__( 'Hello, Widget_Openweather!', 'text_domain' );
            $description= $objetsjson -> weather[0] -> description;
            $humidite   = $objetsjson -> main -> humidity;
            $icone      = $objetsjson -> weather[0] -> icon . '.svg';
            $pression   = $objetsjson -> main -> pressure;
            $temp       = $objetsjson -> main -> temp;
            $temp_max   = $objetsjson -> main -> temp_max;
            $temp_min   = $objetsjson -> main -> temp_min;
            $vent       = $objetsjson -> wind -> speed;


            //echo ($icone); // OK
            echo '<img class="icone" src="' . plugin_dir_url(__FILE__) . 'node_modules/open-weather-icons/src/svg/' . $icone . '" />';            
            echo '<div class="temperature_widget">';
                echo '<ul class="div_temp" style="list-style: none; padding-left: 1.2em;">';
                    echo '<li class="temp_min">' . round($temp_min) . ' °C</li>';
                    echo '<li class="temp"><b>' . round($temp) . ' °C</b></li>';
                    echo '<li class="temp_max">' . round($temp_max) . ' °C</li>';
                echo '</ul>';
                echo '<ul style="list-style: none; padding-left: 1.2em;">';
                    echo '<li>Description: <b>' . $description . '</b></li>';
                    echo '<li>Humidité: <b>' . $humidite . ' %</b></li>';                
                    echo '<li>Pression: <b>' . $pression . ' hPa</b></li>';
                    echo '<li>Vent: <b>' . $vent . ' m/s</b></li>';
                echo '</ul>';
            echo '</div>';
		}
		echo $args['after_widget'];
	}

	/**
	 * Affiche le formulaire d'options sur admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		// affiche le formulaire d'options sur admin
		$ville = ! empty( $instance['idville'] ) ? $instance['idville'] : esc_html__( 'ID de la ville', 'text_domain' );
		$cleapi = ! empty( $instance['cleapi'] ) ? $instance['cleapi'] : esc_html__( 'Clé de l\'api', 'text_domain' );		
		?>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'idville' ) ); ?>"><?php esc_attr_e( 'ID de la ville:', 'text_domain' ); ?></label> 
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'idville' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'idville' ) ); ?>" type="text" value="<?php echo esc_attr( $ville ); ?>">
		</p>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'cleapi' ) ); ?>"><?php esc_attr_e( 'Clé api:', 'text_domain' ); ?></label> 
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'cleapi' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'cleapi' ) ); ?>" type="text" value="<?php echo esc_attr( $cleapi ); ?>">
		</p>
		<?php 
	}

	/**
	 * Traitement des options du widget lors de l'enregistrement
	 *
	 * @param array $new_instance Les nouvelles options
	 * @param array $old_instance Les options précédentes
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		// traite les options du widget à enregistrer
		$instance = array();
		$instance['idville'] = ( ! empty( $new_instance['idville'] ) ) ? sanitize_text_field( $new_instance['idville'] ) : '';
		$instance['cleapi'] = ( ! empty( $new_instance['cleapi'] ) ) ? sanitize_text_field( $new_instance['cleapi'] ) : '';
		return $instance;
	}
}

// register widget_openweather
function register_widget_openweather() {
    register_widget( 'Widget_OpenWeather' );
    wp_enqueue_style( 'widget_openweather', plugin_dir_url(__FILE__) . 'widget_openweather.css' );
}
add_action( 'widgets_init', 'register_widget_openweather' );

/// Fin
