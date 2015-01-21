<?php
class PluginThemeListFilter extends PluginThemeListInit {

	public function __construct() {
		parent::__construct();
		add_filter( 'manage_posts_columns', array( &$this, 'manage_posts_columns' ), 10, 2 );
		add_filter( 'manage_edit-plugins-list_sortable_columns', array( &$this, 'manage_sortable_columns' ) );
		add_filter( 'manage_edit-themes-list_sortable_columns', array( &$this, 'manage_sortable_columns' ) );
		add_action( 'manage_posts_custom_column', array( &$this, 'manage_posts_custom_column' ), 10, 2 );
		add_filter( 'request',  array( &$this, 'column_orderby_request' ) );
	}

	public function manage_posts_columns( $columns, $post_type ) {
		if ( 'plugins-list' == $post_type ) {
			unset( $columns['comments'] );
			$columns['contribution_author'] = __( 'Plugin Author', $this->domain );
		} elseif ( 'themes-list' == $post_type ) {
			unset( $columns['comments'] );
			$columns['contribution_author'] = __( 'Theme Author', $this->domain );
		}
		return $columns;
	}

	public function manage_sortable_columns( $columns ) {
		$columns['contribution_author'] = 'contribution_author';
		return $columns;
	}

	public function manage_posts_custom_column( $column_name, $post_id ) {
		if ( 'contribution_author' == $column_name ) {
			$author = get_post_meta( $post_id, 'author', true );
		}
		if ( isset( $author ) && $author ) {
			echo $author;
		}
	}

	public function column_orderby_request( $vars ) {
		if ( isset( $vars['orderby'] ) && 'contribution_author' == $vars['orderby'] ) {
			$vars = array_merge( $vars, array(
				'meta_key' => 'author',
				'orderby'  => 'meta_value',
			));
		}
		return $vars;
	}

}
