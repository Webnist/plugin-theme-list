<?php
class PluginThemeListTemplateTag extends PluginThemeListInit {

	public function __construct() {
		parent::__construct();
		add_shortcode( 'total_download', array( &$this, 'shortcode_total_download' ) );
		add_shortcode( 'total_list', array( &$this, 'shortcode_total_list' ) );
		add_shortcode( 'plugin_theme_list', array( &$this, 'shortcode_plugin_theme_list' ) );
	}

	public function shortcode_total_download( $atts ) {
		extract( shortcode_atts( array(
			'type' => 'plugins-list',
		), $atts ) );
		$total       = 0;
		$now_date    = date_i18n( 'Y-m-d' );
		$numberposts = self::total_list( $type );
		$args        = array(
			'posts_per_page' => -1,
			'post_type'      => $type,
		);
		$query = new WP_Query( $args );
		if ( false === $query->have_posts() )
			return;

		foreach ( $query->posts as $post ) {
			$post_id = $post->ID;
			$slug    = $post->post_name;
			$update_time = get_post_meta( $post_id, 'update_time', true );
			if ( strtotime( $update_time ) < strtotime( $now_date ) || empty( $update_time ) ) {
				update_post_meta( $post_id, 'update_time', $now_date );
				PluginThemeListAdmin::register_pulgins( array( $slug ), $type );
			}
			$downloaded  = get_post_meta( $post_id, 'downloaded', true );
			$total       = $total + $downloaded;
		}
		return number_format_i18n( $total );
	}

	public function shortcode_total_list( $atts ) {
		extract( shortcode_atts( array(
			'type' => 'plugins-list',
		), $atts ) );
		return self::total_list( $type );
	}

	public function shortcode_plugin_theme_list( $atts ) {
		extract( shortcode_atts( array(
			'type'    => 'plugins-list',
			'class'   => 'plugin-theme-list',
			'orderby' => 'title',
			'order'   => 'ASC',
		), $atts ) );
		$output      = '';
		$total       = 0;
		$now_date    = date_i18n( 'Y-m-d' );
		$update_time = get_option( $type . '_total_update_time' );
		$numberposts = self::total_list( $type );
		$args  = array(
			'posts_per_page' => $numberposts,
			'orderby'        => $orderby,
			'order'          => $order,
			'post_type'      => $type,
		);
		$query = new WP_Query( $args );
		if ( false === $query->have_posts() )
			return;

		if ( 'plugins-list' === $type ) {
			$directory  = 'https://wordpress.org/plugins/';
		} elseif ( 'themes-list' === $plugin_page ) {
			$directory  = 'https://wordpress.org/themes/';
		}

		$output = get_transient( $type . '_transient' );
		if ( $output === false ) {
			$output .= '<ul class="' . $class . '">' . "\n";
			foreach ( $query->posts as $post ) {
				$post_id = $post->ID;
				$sulg    = $post->post_name;
				$title   = apply_filters( 'the_title', get_the_title( $post_id ) );
				$link    = esc_url( $directory . $sulg );
				$output .= '<li><a href="' . $link . '" target="_blank">' . $title . '</a></li>' . "\n";
			}
			$output .= '</ul>' . "\n";
			set_transient( $type . '_transient', $output, 60*60*12 );
		}
		return $output;
	}

	public function total_list( $type = 'plugins-list' ) {
		$now_date    = date_i18n( 'Y-m-d' );
		$update_time = get_option( $type . '_total_update_time' );
		$total       = get_option( $type . '_total' );
		if ( strtotime( $update_time ) < strtotime( $now_date ) || empty( $update_time ) ) {
			$total = wp_count_posts( $type, 'readable' );
			$total = $total->publish;
			update_option( $type . '_total_update_time', $now_date );
			update_option( $type . '_total', $total );
		}
		return $total;
	}

}
