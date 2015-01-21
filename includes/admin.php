<?php
class PluginThemeListAdmin extends PluginThemeListInit {

	public function __construct() {
		parent::__construct();

		add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
		add_action( 'admin_init', array( &$this, 'add_general_custom_fields' ) );
		add_filter( 'admin_init', array( &$this, 'save_register_setting' ) );
		add_action( 'wp_ajax_view_plugins_themes', array( &$this, 'view_plugins_themes' ) );
		add_action( 'admin_enqueue_scripts', array( &$this, 'admin_enqueue_scripts' ) );
	}

	public function admin_menu() {
		add_options_page( $this->name, $this->name, 'edit_pages', $this->basename, array( &$this, 'add_admin_edit_page' ) );
		add_submenu_page( 'edit.php?post_type=plugins-list', __( 'Search plugins list', $this->domain ), __( 'Search plugins list', $this->domain ), 'edit_pages', 'plugins-list', array( &$this, 'add_admin_edit_page' ) );
		add_submenu_page( 'edit.php?post_type=themes-list', __( 'Search themes list', $this->domain ), __( 'Search themes list', $this->domain ), 'edit_pages', 'themes-list', array( &$this, 'add_admin_edit_page' ) );
	}

	public function add_admin_edit_page() {
		global $plugin_page;
		$admin_url = admin_url( 'admin-ajax.php', is_ssl() ? 'https' : 'http' );
		echo '<div class="wrap" id="custom-next-page-options">' . "\n";
		screen_icon();
		echo '<h2>' . esc_html( get_admin_page_title() ) . '</h2>' . "\n";
		if ( 'plugins-list' === $plugin_page || 'themes-list' === $plugin_page ) {
			echo '<form id="search-plugins-themes" method="post" action="' . esc_js( esc_url_raw($admin_url) ) . '" data-ajaxurl="' . $admin_url . '">' . "\n";
			echo '<input type="hidden" name="plugin-page" value="' . $plugin_page . '" />' . "\n";
			echo '<input type="search" id="search-text" name="author" value="" />' . "\n";
			echo '<button id="search-submit">' . __( 'Search' ) . '</button>' . "\n";
			echo '</form>' . "\n";
		}
		echo '<form method="post" action="options.php">' . "\n";
		settings_fields( $this->basename );
		do_settings_sections( $this->basename );
		if ( 'plugins-list' === $plugin_page || 'themes-list' === $plugin_page ) {
			echo '<input type="hidden" name="plugin-page" value="' . $plugin_page . '" />' . "\n";
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
			echo '<th scope="col" id="author" class="manage-column column-author">' . "\n";
			echo __( 'Author' ) . "\n";
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
			echo '<th scope="col" class="manage-column column-author">' . "\n";
			echo __( 'Author' ) . "\n";
			echo '</th>' . "\n";
			echo '</tr>' . "\n";
			echo '</tfoot>' . "\n";
			echo '<tbody id="the-list">' . "\n";
			echo '</tbody>' . "\n";
			echo '</table>' . "\n";
		}
		submit_button();
		echo '</form>' . "\n";
		echo '</div>' . "\n";
	}
	public function add_general_custom_fields() {
		global $plugin_page;

		if ( 'plugin-theme-list' === $plugin_page ) {
			add_settings_section(
				'general',
				__( 'General', $this->domain ),
				'',
				$this->basename
			);

			add_settings_section(
				'plugins',
				__( 'Plugins', $this->domain ),
				'',
				$this->basename
			);
			add_settings_field(
				'plugins-author',
				__( 'In the plugin author name search', $this->domain ),
				array( &$this, 'text_field' ),
				$this->basename,
				'plugins',
				array(
					'name'  => 'plugins-author'
				)
			);

			add_settings_section(
				'themes',
				__( 'Themes', $this->domain ),
				'',
				$this->basename
			);
			add_settings_field(
				'themes-author',
				__( 'In the theme author name search', $this->domain ),
				array( &$this, 'text_field' ),
				$this->basename,
				'themes',
				array(
					'name'  => 'themes-author'
				)
			);
		}
	}

	public function text_field( $args ) {
		extract( $args );

		$id     = ! empty( $id ) ? $id : $name;
		$value  = ! empty( $value ) ? $value : '';
		$desc   = ! empty( $desc ) ? $desc : '';
		$output = '<input type="text" name="' . $name .'" id="' . $id .'" class="regular-text" value="' . $value .'" />' . "\n";
		if ( $desc )
			$output .= '<p class="description">' . $desc . '</p>' . "\n";

		echo $output;
	}

	public function textarea_field( $args ) {
		extract( $args );

		$id      = ! empty( $id ) ? $id : $name;
		$desc    = ! empty( $desc ) ? $desc : '';
		$output  = '<textarea name="' . $name .'" rows="10" cols="50" id="' . $id .'" class="large-text code">' . $value . '</textarea>' . "\n";
		if ( $desc )
			$output .= '<p class="description">' . $desc . '</p>' . "\n";
		echo $output;
	}

	public function check_field( $args ) {
		extract( $args );

		$id      = ! empty( $id ) ? $id : $name;
		$desc    = ! empty( $desc ) ? $desc : '';
		$output  = '<label for="' . $name . '">' . "\n";
		$output  .= '<input name="' . $name . '" type="checkbox" id="' . $id . '" value="1"' . checked( $value, 1, false ) . '>' . "\n";
		if ( $desc )
			$output .= $desc . "\n";
		$output  .= '</label>' . "\n";

		echo $output;
	}

	public function select_field( $args ) {
		extract( $args );

		$id             = ! empty( $id ) ? $id : $name;
		$desc           = ! empty( $desc ) ? $desc : '';
		$multi          = ! empty( $multi ) ? ' multiple' : '';
		$multi_selected = ! empty( $multi ) ? true : false;
		$output = '<select name="' . $name . '" id="' . $id . '"' . $multi . '>' . "\n";
			foreach ( $option as $key => $val ) {
				$output .= '<option value="' . $key . '"' . selected( $value, $key, $multi_selected ) . '>' . $val . '</option>' . "\n";
			}
		$output .= '</select>' . "\n";
			if ( $desc )
			$output .= $desc . "\n";

		echo $output;
	}

	public function selected( $value = '', $val = '', $multi = false ) {
		$select = '';
		if ( $multi ) {

			$select = selected( true, in_array( $val, $value ), false );
		} else {
			$select = selected( $value, $val, false );
		}
		return $select;
	}

	public function view_plugins_themes(){
		$plugin_page = $_POST['plugin-page'];
		if ( 'plugins-list' === $plugin_page ) {
			$search = $_POST['search'];
			echo self::plugins_themes_list( $search );
		} elseif ( 'themes-list' === $plugin_page ) {
			$search = $_POST['search'];
			echo self::plugins_themes_list( $search, 'html', 'themes-list' );
		}
		die();
	}

	public function plugins_themes_list( $search = null, $view = 'html', $plugin_page = 'plugins-list'  ) {
		$output = '';
		if ( empty( $search ) )
			return;

		if ( 'plugins-list' === $plugin_page ) {
			require_once ABSPATH.'wp-admin/includes/plugin-install.php';
			$post_plugins = get_option( 'plugins-list', array() );
			$value        = array();
			$args         = array(
				'author'   => $search,
				'page'     => '1',
				'per_page' => '9999',
				'fields'   => array(
					'description'       => false,
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
				$name   = $plugin->name;
				$slug   = $plugin->slug;
				$icons  = $plugin->icons;
				$author = key( $plugin->contributors );

				foreach ( $icons as $key => $value ) {
					$icon = $value;
				}
				if ( 'html' == $view ) {
					$output .= '<tr>' . "\n";
					$output .= '<th scope="row" class="check-column">' . "\n";

					//if ( ! in_array( $slug, $post_plugins ) )
						$output .= '<input name="plugins-list[]" type="checkbox" value="' . $slug . '">' . "\n";

					$output .= '</th>' . "\n";
					$output .= '<td class="post-icon page-icon column-icon">' . "\n";
					$output .= '<img src="' . $icon . '">' . "\n";
					$output .= '</td>' . "\n";
					$output .= '<td class="post-title page-title column-title">' . "\n";
					$output .= '<strong>' . $name . '</strong>' . "\n";
					$output .= '</td>' . "\n";
					$output .= '<td class="post-author page-author column-author">' . "\n";
					$output .= '<strong>' . $author . '</strong>' . "\n";
					$output .= '</td>' . "\n";
					$output .= '</tr>' . "\n";
				} else {
					$output[] = $slug;
				}
			}
		} elseif ( 'themes-list' === $plugin_page ) {
			require_once ABSPATH.'wp-admin/includes/theme-install.php';
			$post_themes = get_option( 'plugins-list', array() );
			$value       = array();
			$args        = array(
				'author'   => $search,
				'page'     => '1',
				'per_page' => '9999',
			);
			$query   = themes_api( 'query_themes', $args );

			if ( empty( $query ) )
				return;

			$themes = $query->themes;
			foreach ( $themes as $theme ) {
				$name   = $theme->name;
				$slug   = $theme->slug;
				$icon   = $theme->screenshot_url;
				$author = strip_tags( $theme->author );
				if ( 'html' == $view ) {
					$output .= '<tr>' . "\n";
					$output .= '<th scope="row" class="check-column">' . "\n";

					//if ( ! in_array( $slug, $post_themes ) )
						$output .= '<input name="themes-list[]" type="checkbox" value="' . $slug . '">' . "\n";

					$output .= '</th>' . "\n";
					$output .= '<td class="post-icon page-icon column-icon">' . "\n";
					$output .= '<img src="' . $icon . '">' . "\n";
					$output .= '</td>' . "\n";
					$output .= '<td class="post-title page-title column-title">' . "\n";
					$output .= '<strong>' . $name . '</strong>' . "\n";
					$output .= '</td>' . "\n";
					$output .= '<td class="post-author page-author column-author">' . "\n";
					$output .= '<strong>' . $author . '</strong>' . "\n";
					$output .= '</td>' . "\n";
					$output .= '</tr>' . "\n";
				} else {
					$output[] = $slug;
				}
			}
		}
		return $output;
	}

	public function save_register_setting() {
		global $plugin_page;
		register_setting( $this->basename, 'plugins-author', array( &$this, 'register_plugins_author' ) );
		register_setting( $this->basename, 'themes-author', array( &$this, 'register_themes_author' ) );
		register_setting( $this->basename, 'plugins-list', array( &$this, 'register_pulgins' ) );
		register_setting( $this->basename, 'themes-list', array( &$this, 'register_pulgins' ) );
	}

	public function register_plugins_author( $values ) {

		if ( $values ) {
			$authors = explode( ', ', $values );
			$values  = array();
			foreach ( $authors as $author ) {
				$list = self::plugins_themes_list( $author, 'array', 'plugins-list' );
				array_splice( $values, count( $values ), 0, $list );
			}
			self::register_pulgins( $values, 'plugins-list' );
		}
		return $values;
	}

	public function register_themes_author( $values ) {
		if ( $values ) {
			$authors = explode( ', ', $values );
			$values  = array();
			foreach ( $authors as $author ) {
				$list = self::plugins_themes_list( $author, 'array', 'themes-list' );
				array_splice( $values, count( $values ), 0, $list );
			}
			self::register_pulgins( $values, 'themes-list' );
		}
		return $values;
	}

	public function register_pulgins( $values, $plugin_page = null ) {
		if ( $values ) {
			$plugin_page = ( empty( $plugin_page ) && isset( $_POST['plugin-page'] ) && $_POST['plugin-page'] ) ? $_POST['plugin-page'] : $plugin_page;
			if ( 'plugins-list' === $plugin_page ) {
				require_once ABSPATH.'wp-admin/includes/plugin-install.php';
				$api  = 'plugins_api';
				$info = 'plugin_information';
				$url  = 'https://wordpress.org/plugins/';
			} elseif ( 'themes-list' === $plugin_page ) {
				require_once ABSPATH.'wp-admin/includes/theme-install.php';
				$api  = 'themes_api';
				$info = 'theme_information';
				$url  = 'https://wordpress.org/themes/';
			}
			foreach ( $values as $value ) {
				$args  = array(
					'slug' => $value,
					'fields' => array(
						'sections'       => false,
						'icons'             => true,
						'banners'           => true,
					),
				);
				$query         = $api( $info, $args );
				$slug          = $query->slug;
				if ( 'plugins-list' === $plugin_page ) {
					$author = key( $query->contributors );
				} elseif ( 'themes-list' === $plugin_page ) {
					$author = strip_tags( $theme->author );
				}
				$downloaded    = $query->downloaded;
				$download_link = $query->download_link;
				$directory_url = $url . $slug;
				$post = get_page_by_path( $slug, 'OBJECT', $plugin_page );
				if ( empty( $post ) ) {
					$title               = $query->name;
					$post                = array();
					$post['post_title']  = $query->name;
					$post['post_name']   = $query->slug;
					$post['post_status'] = 'publish';
					$post['post_author'] = 1;
					$post['post_type']   = $plugin_page;
					$post_id             = wp_insert_post( $post );
				} else {
					$post_id             = $post->ID;
				}
				update_post_meta( $post_id, 'update_time', date_i18n( 'Y-m-d' ) );
				update_post_meta( $post_id, 'author', $author );
				update_post_meta( $post_id, 'downloaded', $downloaded );
				update_post_meta( $post_id, 'download_link', $download_link );
				update_post_meta( $post_id, 'directory_url', $directory_url );
			}
		}
		return $values;
	}

	public function admin_enqueue_scripts( $hook ) {
		global $plugin_page;
		if ( 'plugins-list' === $plugin_page || 'themes-list' === $plugin_page ) {
			wp_enqueue_style( 'admin-plugin-theme-list', $this->url . '/css/admin-plugin-theme-list.css', array(), $this->version );
			wp_enqueue_script( 'admin-plugin-theme-list', $this->url . '/js/admin-plugin-theme-list.js', array(), $this->version, true );
		}
	}

}
