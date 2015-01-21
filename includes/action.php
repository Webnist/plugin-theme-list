<?php
class PluginThemeListAction extends PluginThemeListInit {

	public function __construct() {
		parent::__construct();
		add_action( 'update_option', array( &$this, 'delete_transient' ) );
		add_action( 'deleted_post', array( &$this, 'delete_transient' ) );
		add_action( 'save_post_plugins-list', array( &$this, 'save_post_plugins' ), 10, 3 );
		add_action( 'save_post_themes-list', array( &$this, 'save_post_themes' ), 10, 3 );
	}

	public function delete_transient( $option ) {
		if ( stristr( $option, 'plugins-list' ) ) {
			delete_transient( 'plugins-list_transient' );
		} elseif ( stristr( $option, 'themes-list' ) ) {
			delete_transient( 'themes-list_transient' );
		}
	}

	public function save_post_plugins( $post_ID, $post, $update ) {
		delete_transient( 'plugins-list_transient' );
		delete_option( 'plugins-list_total_update_time' );
	}

	public function save_post_themes( $post_ID, $post, $update ) {
		delete_transient( 'themes-list_transient' );
		delete_option( 'themes-list_total_update_time' );
	}

}
