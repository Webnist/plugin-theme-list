<?php
class PluginThemeListAdmin extends PluginThemeListInit {

	public function __construct() {
		parent::__construct();

		add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
		add_filter( 'admin_init', array( &$this, 'save_register_setting' ) );
		add_action( 'wp_ajax_view_plugins_themes', array( &$this, 'view_plugins_themes' ) );
		add_action( 'admin_enqueue_scripts', array( &$this, 'admin_enqueue_scripts' ) );
	}

	public function admin_menu() {
		add_submenu_page( 'edit.php?post_type=plugins-list', __( 'Search plugins list', $this->domain ), __( 'Search plugins list', $this->domain ), 'edit_pages', 'plugins-list', array( &$this, 'add_admin_edit_page' ) );
		add_submenu_page( 'edit.php?post_type=themes-list', __( 'Search themes list', $this->domain ), __( 'Search themes list', $this->domain ), 'edit_pages', 'themes-list', array( &$this, 'add_admin_edit_page' ) );
	}

	public function add_admin_edit_page() {
		global $plugin_page;
		var_dump( $plugin_page );
		$admin_url = admin_url( 'admin-ajax.php', is_ssl() ? 'https' : 'http' );
		echo '<div class="wrap" id="custom-next-page-options">' . "\n";
		screen_icon();
		echo '<h2>' . esc_html( get_admin_page_title() ) . '</h2>' . "\n";
		echo '<form id="my_great_action_form" method="post" action="' . esc_js( esc_url_raw($admin_url) ) . '" data-ajaxurl="' . $admin_url . '">' . "\n";
		echo '<input type="hidden" name="plugin-page" value="' . $plugin_page . '" />' . "\n";
		echo '<input type="search" id="search-text" name="author" value="" />' . "\n";
		echo '<button id="search-submit">View Sitename</button>' . "\n";
		echo '</form>' . "\n";
		echo '<form method="post" action="options.php">' . "\n";
		settings_fields( $this->basename );
		do_settings_sections( $this->basename  );
		echo '<table id="plugins-themes-table" class="wp-list-table widefat fixed striped posts">' . "\n";
		echo '<thead>' . "\n";
		echo '<tr>' . "\n";
		echo '<th scope="col" class="check-column">' . "\n";
		echo '<label class="screen-reader-text" for="cb-select-all-1">' . __( 'Select All' ) . '</label>' . "\n";
		echo '<input id="cb-select-all-1" type="checkbox">' . "\n";
		echo '</th>' . "\n";
		echo '<th scope="col" id="icon" class="manage-column column-icon"></th>' . "\n";
		echo '<th scope="col" id="title" class="manage-column column-title">' . "\n";
		echo __( 'Title' ) . "\n";
		echo '</th>' . "\n";
		echo '</tr>' . "\n";
		echo '</thead>' . "\n";
		echo '<tfoot>' . "\n";
		echo '<tr>' . "\n";
		echo '<th scope="col" class="check-column">' . "\n";
		echo '<label class="screen-reader-text" for="cb-select-all-2">' . __( 'Select All' ) . '</label>' . "\n";
		echo '<input id="cb-select-all-2" type="checkbox">' . "\n";
		echo '</th>' . "\n";
		echo '<th scope="col" id="icon" class="manage-column column-icon"></th>' . "\n";
		echo '<th scope="col" class="manage-column column-title">' . "\n";
		echo __( 'Title' ) . "\n";
		echo '</th>' . "\n";
		echo '</tr>' . "\n";
		echo '</tfoot>' . "\n";
		echo '<tbody id="the-list">' . "\n";
		echo '</tbody>' . "\n";
		echo '</table>' . "\n";
		submit_button();
		echo '</form>' . "\n";
		echo '</div>' . "\n";
	}

	public function view_plugins_themes(){
		$plugin_page = $_POST['plugin-page'];
		if ( 'plugins-list' === $plugin_page ) {
			$search = $_POST['search'];
			echo self::plugins_themes_list( $search );
		} elseif ( 'themes-list' === $plugin_page ) {
			$search = $_POST['search'];
			echo self::plugins_themes_list( $search, 'themes-list' );
		}
		die();
	}

	public function plugins_themes_list( $search = null, $plugin_page = 'plugins-list' ) {
		$output = '';
		if ( empty( $search ) )
			return;

		if ( 'plugins-list' === $plugin_page ) {
			require_once ABSPATH.'wp-admin/includes/plugin-install.php';
			$value = array();
			$args  = array(
				'author'   => $search,
				'page'     => '1',
				'per_page' => '1',
				'fields'   => array(
					'description'       => false,
					'contributors'      => false,
					'requires'          => false,
					'tested'            => false,
					'compatibility'     => false,
					'rating'            => false,
					'num_ratings'       => false,
					'ratings'           => false,
					'homepage'          => false,
					'short_description' => false,
					'downloaded'        => true,
					'icons'             => true,
					'banners'           => true,
				),
			);
			$query   = plugins_api( 'query_plugins', $args );

			if ( empty( $query ) )
				return;

			$plugins = $query->plugins;
			foreach ( $plugins as $plugin ) {
				$name    = $plugin->name;
				$slug    = $plugin->slug;
				$icons   = $plugin->icons;
				foreach ( $icons as $key => $value ) {
					$icon = $value;
				}
				$output .= '<tr>' . "\n";
				$output .= '<th scope="row" class="check-column">' . "\n";
				$output .= '<input name="plugins[]" type="checkbox" value="' . $slug . '">' . "\n";
				$output .= '</th>' . "\n";
				$output .= '<td class="post-icon page-icon column-icon">' . "\n";
				$output .= '<img src="' . $icon . '">' . "\n";
				$output .= '</td>' . "\n";
				$output .= '<td class="post-title page-title column-title">' . "\n";
				$output .= '<strong>' . $name . '</strong>' . "\n";
				$output .= '</td>' . "\n";
				$output .= '</tr>' . "\n";
			}
		} elseif ( 'themes-list' === $plugin_page ) {
			require_once ABSPATH.'wp-admin/includes/theme-install.php';
			$value = array();
			$args  = array(
				'author'   => $search,
				'page'     => '1',
				'per_page' => '9999',
			);
			$query   = themes_api( 'query_themes', $args );

			if ( empty( $query ) )
				return;

			$themes = $query->themes;
			foreach ( $themes as $theme ) {
				$name    = $theme->name;
				$slug    = $theme->slug;
				$icon    = $theme->screenshot_url;
				$output .= '<tr>' . "\n";
				$output .= '<th scope="row" class="check-column">' . "\n";
				$output .= '<input name="themes[]" type="checkbox" value="' . $slug . '">' . "\n";
				$output .= '</th>' . "\n";
				$output .= '<td class="post-icon page-icon column-icon">' . "\n";
				$output .= '<img src="' . $icon . '">' . "\n";
				$output .= '</td>' . "\n";
				$output .= '<td class="post-title page-title column-title">' . "\n";
				$output .= '<strong>' . $name . '</strong>' . "\n";
				$output .= '</td>' . "\n";
				$output .= '</tr>' . "\n";
			}
		}
		return $output;
	}

	public function save_register_setting() {
		global $plugin_page;
		register_setting( $this->basename, 'themes', array( &$this, 'register_themes' ) );
		register_setting( $this->basename, 'plugins', array( &$this, 'register_pulgins' ) );
	}
	public function register_themes( $values ) {
		require_once ABSPATH.'wp-admin/includes/theme-install.php';
		if ( $values ) {
			foreach ( $values as $value ) {
				$args  = array(
					'slug' => $value,
					'fields' => array(
						'sections'       => false,
					),
				);
				$query         = themes_api( 'theme_information', $args );
				$slug          = $query->slug;
				$downloaded    = $query->downloaded;
				$download_link = $query->download_link;
				$post          = get_page_by_path( $slug, 'OBJECT', 'themes-list' );
				if ( empty( $post ) ) {
					$title               = $query->name;
					$post                = array();
					$post['post_title']  = $query->name;
					$post['post_name']   = $query->slug;
					$post['post_status'] = 'publish';
					$post['post_author'] = 1;
					$post['post_type']   = 'themes-list';
					$post_id             = wp_insert_post( $post );
				} else {
					$post_id             = $post->ID;
				}
				add_post_meta( $post_id, 'theme_downloaded', $downloaded, true );
				add_post_meta( $post_id, 'theme_download_link', $download_link, true );
			}
		}
		return;
	}

	public function register_pulgins( $values ) {
		require_once ABSPATH.'wp-admin/includes/plugin-install.php';
		if ( $values ) {
			foreach ( $values as $value ) {
				$args  = array(
					'slug' => $value,
					'fields' => array(
						'sections'       => false,
					),
				);
				$query         = plugins_api( 'plugin_information', $args );
				$slug          = $query->slug;
				$downloaded    = $query->downloaded;
				$download_link = $query->download_link;
				$post          = get_page_by_path( $slug, 'OBJECT', 'plugins-list' );
				if ( empty( $post ) ) {
					$title               = $query->name;
					$post                = array();
					$post['post_title']  = $query->name;
					$post['post_name']   = $query->slug;
					$post['post_status'] = 'publish';
					$post['post_author'] = 1;
					$post['post_type']   = 'plugins-list';
					$post_id             = wp_insert_post( $post );
				} else {
					$post_id             = $post->ID;
				}
				add_post_meta( $post_id, 'plugin_downloaded', $downloaded, true );
				add_post_meta( $post_id, 'plugin_download_link', $download_link, true );
			}
		}
		return;
	}

	public function admin_enqueue_scripts( $hook ) {
		global $plugin_page;
		if ( 'plugins-list' === $plugin_page || 'themes-list' === $plugin_page ) {
			wp_enqueue_style( 'admin-plugin-theme-list', $this->url . '/css/admin-plugin-theme-list.css', array(), $this->version );
			wp_enqueue_script( 'admin-plugin-theme-list', $this->url . '/js/admin-plugin-theme-list.js', array(), $this->version, true );
		}
	}

}
