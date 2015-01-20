<?php
class PluginThemeListPostType extends PluginThemeListInit {

	public function __construct() {
		parent::__construct();

		add_action( 'init', array( &$this, 'create_initial_post_types' ) );
	}

	public function create_initial_post_types() {

		register_post_type( 'plugins-list', array(
			'labels' => self::get_post_type_labels( 'Plugins List' ),
			'public'  => true,
			'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'custom-fields', 'comments', 'revisions', 'post-formats' ),
		) );

		register_post_type( 'themes-list', array(
			'labels' => self::get_post_type_labels( 'Themes List' ),
			'public'  => true,
			'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'custom-fields', 'comments', 'revisions', 'post-formats' ),
		) );

	}

	public function get_post_type_labels( $name ) {

		$labels = array(
			'name'               => sprintf( _x( '%s', 'post type general name', $this->domain ), $name ),
			'singular_name'      => sprintf( _x( '%s', 'post type singular name', $this->domain ), $name ),
			'add_new'            => sprintf( _x( 'Add New', '%s', $this->domain ), $name ),
			'add_new_item'       => sprintf( __( 'Add New %s', $this->domain ), $name ),
			'edit_item'          => sprintf( __( 'Edit %s', $this->domain ), $name ),
			'new_item'           => sprintf( __( 'New %s', $this->domain ), $name ),
			'view_item'          => sprintf( __( 'View %s', $this->domain ), $name ),
			'search_items'       => sprintf( __( 'Search %s', $this->domain ), $name ),
			'not_found'          => sprintf( __( 'No %s found.', $this->domain ), $name ),
			'not_found_in_trash' => sprintf( __( 'No %s found in Trash.', $this->domain ), $name ),
			'parent_item_colon'  => sprintf( __( 'Parent %s:', $this->domain ), $name ),
			'all_items'          => sprintf( __( 'All %s', $this->domain ), $name ),
			'name_admin_bar'     => sprintf( _x( '%s', 'add new on admin bar', $this->domain ), $name ),
		);
		return $labels;
	}

}
