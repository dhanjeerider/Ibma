<?php
/**
 * Vegamovies Theme Functions
 *
 * @package vegamovies
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* =====================================================
   THEME SETUP
   ===================================================== */
function vegamovies_setup() {
	// Translations
	load_theme_textdomain( 'vegamovies', get_template_directory() . '/languages' );

	// Automatic <title> tag
	add_theme_support( 'title-tag' );

	// Featured images
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 300, 450, true );
	add_image_size( 'vegamovies-card', 300, 450, true );
	add_image_size( 'vegamovies-poster', 150, 250, false );

	// Custom logo
	add_theme_support( 'custom-logo', array(
		'height'      => 40,
		'width'       => 240,
		'flex-height' => true,
		'flex-width'  => true,
	) );

	// HTML5 markup
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script',
	) );

	// Gutenberg wide/full alignment
	add_theme_support( 'align-wide' );

	// Selective refresh for customizer widgets
	add_theme_support( 'customize-selective-refresh-widgets' );

	// Register navigation menus
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'vegamovies' ),
		'footer'  => __( 'Footer Menu', 'vegamovies' ),
		'social'  => __( 'Social / Share Bar', 'vegamovies' ),
	) );
}
add_action( 'after_setup_theme', 'vegamovies_setup' );

/* =====================================================
   ENQUEUE STYLES & SCRIPTS
   ===================================================== */
function vegamovies_scripts() {
	// Bootstrap 3
	wp_enqueue_style(
		'bootstrap',
		'https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css',
		array(),
		'3.4.1'
	);

	// Main theme stylesheet
	wp_enqueue_style(
		'vegamovies-style',
		get_stylesheet_uri(),
		array( 'bootstrap' ),
		wp_get_theme()->get( 'Version' )
	);

	// Theme JS
	wp_enqueue_script(
		'vegamovies-theme',
		get_template_directory_uri() . '/assets/js/theme.js',
		array(),
		wp_get_theme()->get( 'Version' ),
		true  // Load in footer
	);

	// Pass data to JS
	wp_localize_script( 'vegamovies-theme', 'vegamoviesData', array(
		'homeUrl' => esc_url( home_url( '/' ) ),
	) );
}
add_action( 'wp_enqueue_scripts', 'vegamovies_scripts' );

/* =====================================================
   WIDGET AREAS / SIDEBARS
   ===================================================== */
function vegamovies_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Right Sidebar', 'vegamovies' ),
		'id'            => 'sidebar-1',
		'description'   => __( 'Widgets shown on single posts and pages.', 'vegamovies' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => __( 'Footer Widgets', 'vegamovies' ),
		'id'            => 'footer-widgets',
		'description'   => __( 'Widgets shown in the footer area.', 'vegamovies' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s col-sm-3">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
}
add_action( 'widgets_init', 'vegamovies_widgets_init' );

/* =====================================================
   CUSTOM META BOXES — MOVIE DETAILS
   ===================================================== */
function vegamovies_add_meta_boxes() {
	add_meta_box(
		'vegamovies_movie_details',
		__( 'Movie Details', 'vegamovies' ),
		'vegamovies_movie_details_callback',
		'post',
		'normal',
		'high'
	);

	add_meta_box(
		'vegamovies_download_links',
		__( 'Download Links', 'vegamovies' ),
		'vegamovies_download_links_callback',
		'post',
		'normal',
		'default'
	);
}
add_action( 'add_meta_boxes', 'vegamovies_add_meta_boxes' );

/**
 * Movie Details meta box markup.
 */
function vegamovies_movie_details_callback( $post ) {
	wp_nonce_field( 'vegamovies_save_meta', 'vegamovies_meta_nonce' );

	$fields = array(
		'movie_title'  => __( 'Title', 'vegamovies' ),
		'genres'       => __( 'Genres', 'vegamovies' ),
		'language'     => __( 'Language', 'vegamovies' ),
		'release_date' => __( 'Release Date', 'vegamovies' ),
		'poster_url'   => __( 'Poster URL', 'vegamovies' ),
	);

	echo '<table class="form-table" style="width:100%;">';
	foreach ( $fields as $key => $label ) {
		$value = get_post_meta( $post->ID, $key, true );
		echo '<tr>';
		echo '<th><label for="' . esc_attr( $key ) . '">' . esc_html( $label ) . '</label></th>';
		echo '<td><input type="text" id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" value="' . esc_attr( $value ) . '" class="widefat"></td>';
		echo '</tr>';
	}
	// Synopsis — textarea
	$synopsis = get_post_meta( $post->ID, 'synopsis', true );
	echo '<tr>';
	echo '<th><label for="synopsis">' . esc_html__( 'Synopsis', 'vegamovies' ) . '</label></th>';
	echo '<td><textarea id="synopsis" name="synopsis" class="widefat" rows="3">' . esc_textarea( $synopsis ) . '</textarea></td>';
	echo '</tr>';
	// Snapshots — textarea (one URL per line)
	$snapshots = get_post_meta( $post->ID, 'snapshots', true );
	echo '<tr>';
	echo '<th><label for="snapshots">' . esc_html__( 'Snapshot URLs (one per line)', 'vegamovies' ) . '</label></th>';
	echo '<td><textarea id="snapshots" name="snapshots" class="widefat" rows="4">' . esc_textarea( $snapshots ) . '</textarea></td>';
	echo '</tr>';
	echo '</table>';
}

/**
 * Download Links meta box markup.
 */
function vegamovies_download_links_callback( $post ) {
	$links = array(
		'dllink1'       => __( 'Link 1 URL', 'vegamovies' ),
		'dllink1_label' => __( 'Link 1 Label (e.g. Download 720p)', 'vegamovies' ),
		'dllink2'       => __( 'Link 2 URL', 'vegamovies' ),
		'dllink2_label' => __( 'Link 2 Label', 'vegamovies' ),
		'dllink3'       => __( 'Link 3 URL', 'vegamovies' ),
		'dllink3_label' => __( 'Link 3 Label', 'vegamovies' ),
	);

	echo '<table class="form-table" style="width:100%;">';
	foreach ( $links as $key => $label ) {
		$value = get_post_meta( $post->ID, $key, true );
		echo '<tr>';
		echo '<th><label for="' . esc_attr( $key ) . '">' . esc_html( $label ) . '</label></th>';
		echo '<td><input type="text" id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" value="' . esc_attr( $value ) . '" class="widefat"></td>';
		echo '</tr>';
	}
	echo '</table>';
}

/**
 * Save all custom meta fields.
 */
function vegamovies_save_meta( $post_id ) {
	// Nonce check
	if ( ! isset( $_POST['vegamovies_meta_nonce'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( $_POST['vegamovies_meta_nonce'], 'vegamovies_save_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$text_fields = array(
		'movie_title', 'genres', 'language', 'release_date', 'poster_url',
		'dllink1', 'dllink1_label', 'dllink2', 'dllink2_label', 'dllink3', 'dllink3_label',
	);
	foreach ( $text_fields as $field ) {
		if ( isset( $_POST[ $field ] ) ) {
			update_post_meta( $post_id, $field, sanitize_text_field( $_POST[ $field ] ) );
		}
	}

	// Textarea fields — preserve newlines
	$textarea_fields = array( 'synopsis', 'snapshots' );
	foreach ( $textarea_fields as $field ) {
		if ( isset( $_POST[ $field ] ) ) {
			update_post_meta( $post_id, $field, sanitize_textarea_field( $_POST[ $field ] ) );
		}
	}
}
add_action( 'save_post', 'vegamovies_save_meta' );

/* =====================================================
   SEARCH FORM
   ===================================================== */
function vegamovies_search_form( $form ) {
	$form = '<form method="get" id="searchForm" class="search-form" action="' . esc_url( home_url( '/' ) ) . '">
		<div class="input-group">
			<input type="search" name="s" class="search-input search-field" placeholder="' . esc_attr__( 'Search...', 'vegamovies' ) . '" autocomplete="off" aria-label="' . esc_attr__( 'Search', 'vegamovies' ) . '" value="' . esc_attr( get_search_query() ) . '">
			<button class="search-submit" id="searchSubmit" aria-label="' . esc_attr__( 'Submit search', 'vegamovies' ) . '">' . esc_html__( 'Search', 'vegamovies' ) . '</button>
		</div>
	</form>';
	return $form;
}
add_filter( 'get_search_form', 'vegamovies_search_form' );

/* =====================================================
   EXCERPT LENGTH
   ===================================================== */
function vegamovies_excerpt_length( $length ) {
	return 20;
}
add_filter( 'excerpt_length', 'vegamovies_excerpt_length' );

/* =====================================================
   CLEAN UP <head>
   ===================================================== */
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
