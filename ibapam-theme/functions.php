<?php
/**
 * IBAPAM Movie Theme — functions.php
 *
 * @package ibapam
 */

defined( 'ABSPATH' ) || exit;

/* ═══════════════════════════════════════════════════════════
   CONSTANTS
═══════════════════════════════════════════════════════════ */
define( 'IBAPAM_VERSION', wp_get_theme()->get( 'Version' ) ?: '1.0.0' );
define( 'IBAPAM_DIR',     get_template_directory() );
define( 'IBAPAM_URI',     get_template_directory_uri() );

/* ═══════════════════════════════════════════════════════════
   THEME SETUP
═══════════════════════════════════════════════════════════ */
function ibapam_setup() {
	load_theme_textdomain( 'ibapam', IBAPAM_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', [ 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ] );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'wp-block-styles' );

	/* Custom logo */
	add_theme_support( 'custom-logo', [
		'height'      => 60,
		'width'       => 200,
		'flex-height' => true,
		'flex-width'  => true,
	] );

	/* Image sizes */
	add_image_size( 'ibapam-poster',    300, 450, true );   // movie poster (2:3)
	add_image_size( 'ibapam-card',      360, 540, true );   // card grid
	add_image_size( 'ibapam-hero',     1280, 480, true );   // hero slider
	add_image_size( 'ibapam-thumb',    780,  440, true );   // screenshot / thumb
	add_image_size( 'ibapam-card-sm',  200,  300, true );   // sidebar card

	/* Navigation menus */
	register_nav_menus( [
		'primary'    => __( 'Primary Navigation',  'ibapam' ),
		'mobile'     => __( 'Mobile Navigation',   'ibapam' ),
		'footer-cat' => __( 'Footer Categories',   'ibapam' ),
		'footer-pg'  => __( 'Footer Pages',        'ibapam' ),
	] );
}
add_action( 'after_setup_theme', 'ibapam_setup' );

/* ═══════════════════════════════════════════════════════════
   ENQUEUE SCRIPTS & STYLES
═══════════════════════════════════════════════════════════ */
function ibapam_scripts() {
	/* Google Fonts */
	wp_enqueue_style(
		'ibapam-fonts',
		'https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&family=Oswald:wght@600;700&display=swap',
		[],
		null
	);

	/* Font Awesome 6 */
	wp_enqueue_style(
		'ibapam-fa',
		'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css',
		[],
		'6.5.0'
	);

	/* Main stylesheet (enqueue style.css as placeholder, real CSS is main.css) */
	wp_enqueue_style( 'ibapam-style', get_stylesheet_uri(), [], IBAPAM_VERSION );

	/* Main CSS */
	wp_enqueue_style(
		'ibapam-main',
		IBAPAM_URI . '/assets/css/main.css',
		[ 'ibapam-style' ],
		IBAPAM_VERSION
	);

	/* Main JS */
	wp_enqueue_script(
		'ibapam-main',
		IBAPAM_URI . '/assets/js/main.js',
		[],
		IBAPAM_VERSION,
		true
	);
	/* Provide REST URL to main JS for any front-end needs */
	wp_localize_script( 'ibapam-main', 'ibapamData', [
		'restUrl'       => esc_url_raw( rest_url( 'wp/v2' ) ),
		'ibapamRestUrl' => esc_url_raw( rest_url( 'ibapam/v1' ) ),
	] );
}
add_action( 'wp_enqueue_scripts', 'ibapam_scripts' );

/* ═══════════════════════════════════════════════════════════
   WIDGETS / SIDEBARS
═══════════════════════════════════════════════════════════ */
function ibapam_widgets_init() {
	$sidebars = [
		[
			'id'   => 'sidebar-main',
			'name' => __( 'Main Sidebar', 'ibapam' ),
		],
		[
			'id'   => 'sidebar-footer-1',
			'name' => __( 'Footer Column 2', 'ibapam' ),
		],
		[
			'id'   => 'sidebar-footer-2',
			'name' => __( 'Footer Column 3', 'ibapam' ),
		],
		[
			'id'   => 'sidebar-footer-3',
			'name' => __( 'Footer Column 4', 'ibapam' ),
		],
	];

	foreach ( $sidebars as $sb ) {
		register_sidebar( [
			'id'            => $sb['id'],
			'name'          => $sb['name'],
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<div class="widget-title-bar">',
			'after_title'   => '</div><div class="widget-body">',
		] );
	}
}
add_action( 'widgets_init', 'ibapam_widgets_init' );

/* ═══════════════════════════════════════════════════════════
   CUSTOM POST META (Movie Details)
═══════════════════════════════════════════════════════════ */
function ibapam_register_meta() {
	$fields = [
		'tmdb_id'          => 'integer',
		'tmdb_type'        => 'string',
		'movie_rating'     => 'string',
		'movie_year'       => 'string',
		'movie_genres'     => 'string',
		'movie_cast'       => 'string',
		'movie_director'   => 'string',
		'movie_runtime'    => 'string',
		'movie_quality'    => 'string',
		'movie_audio'      => 'string',
		'download_links'   => 'string',  // JSON-encoded array
	];
	foreach ( $fields as $key => $type ) {
		register_post_meta( 'post', $key, [
			'show_in_rest'  => true,
			'single'        => true,
			'type'          => $type,
			'auth_callback' => function () { return current_user_can( 'edit_posts' ); },
		] );
	}
}
add_action( 'init', 'ibapam_register_meta' );

/* ═══════════════════════════════════════════════════════════
   MOVIE DETAILS META BOX
═══════════════════════════════════════════════════════════ */
function ibapam_add_meta_boxes() {
	add_meta_box(
		'ibapam-movie-details',
		__( 'Movie Details', 'ibapam' ),
		'ibapam_movie_meta_box_cb',
		'post',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'ibapam_add_meta_boxes' );

function ibapam_movie_meta_box_cb( $post ) {
	wp_nonce_field( 'ibapam_save_meta', 'ibapam_meta_nonce' );
	$fields = [
		'movie_rating'   => __( 'Rating (e.g. 7.8)',               'ibapam' ),
		'movie_year'     => __( 'Release Year',                    'ibapam' ),
		'movie_genres'   => __( 'Genres (comma separated)',        'ibapam' ),
		'movie_cast'     => __( 'Cast (comma separated)',          'ibapam' ),
		'movie_director' => __( 'Director / Creator',              'ibapam' ),
		'movie_runtime'  => __( 'Runtime (e.g. 142 min)',          'ibapam' ),
		'movie_quality'  => __( 'Quality (e.g. WEB-DL)',           'ibapam' ),
		'movie_audio'    => __( 'Audio (e.g. Hindi + English)',    'ibapam' ),
		'tmdb_id'        => __( 'TMDB ID',                         'ibapam' ),
		'tmdb_type'      => __( 'TMDB Type (movie / tv)',          'ibapam' ),
		'download_links' => __( 'Download Links (JSON array — use TMDB importer)', 'ibapam' ),
	];
	echo '<table class="form-table">';
	foreach ( $fields as $key => $label ) {
		$value = get_post_meta( $post->ID, $key, true );
		$tag   = ( $key === 'download_links' ) ? 'textarea' : 'input';
		echo '<tr><th><label for="ibapam_' . esc_attr( $key ) . '">' . esc_html( $label ) . '</label></th><td>';
		if ( $tag === 'textarea' ) {
			echo '<textarea id="ibapam_' . esc_attr( $key ) . '" name="ibapam_' . esc_attr( $key ) . '" rows="4" class="large-text">' . esc_textarea( $value ) . '</textarea>';
		} else {
			echo '<input type="text" id="ibapam_' . esc_attr( $key ) . '" name="ibapam_' . esc_attr( $key ) . '" value="' . esc_attr( $value ) . '" class="large-text"/>';
		}
		echo '</td></tr>';
	}
	echo '</table>';
}

function ibapam_save_meta( $post_id ) {
	if ( ! isset( $_POST['ibapam_meta_nonce'] ) ) return;
	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ibapam_meta_nonce'] ) ), 'ibapam_save_meta' ) ) return;
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) ) return;

	$keys = [ 'movie_rating','movie_year','movie_genres','movie_cast','movie_director','movie_runtime','movie_quality','movie_audio','tmdb_id','tmdb_type','download_links' ];
	foreach ( $keys as $key ) {
		if ( isset( $_POST[ 'ibapam_' . $key ] ) ) {
			$raw = wp_unslash( $_POST[ 'ibapam_' . $key ] );
			if ( $key === 'download_links' ) {
				/* Validate JSON structure before saving */
				$decoded = json_decode( $raw, true );
				if ( json_last_error() === JSON_ERROR_NONE && is_array( $decoded ) ) {
					update_post_meta( $post_id, $key, wp_slash( wp_json_encode( $decoded ) ) );
				}
			} elseif ( $key === 'tmdb_id' ) {
				update_post_meta( $post_id, $key, absint( $raw ) );
			} else {
				update_post_meta( $post_id, $key, sanitize_text_field( $raw ) );
			}
		}
	}
}
add_action( 'save_post', 'ibapam_save_meta' );

/* ═══════════════════════════════════════════════════════════
   REST API — Image Sideload Endpoint
═══════════════════════════════════════════════════════════ */
function ibapam_register_rest_routes() {
	register_rest_route( 'ibapam/v1', '/sideload-image', [
		'methods'             => 'POST',
		'callback'            => 'ibapam_rest_sideload_image',
		'permission_callback' => function () {
			return current_user_can( 'upload_files' );
		},
		'args' => [
			'post_id'   => [ 'required' => true,  'type' => 'integer', 'sanitize_callback' => 'absint' ],
			'image_url' => [ 'required' => true,  'type' => 'string',  'sanitize_callback' => 'esc_url_raw' ],
			'alt_text'  => [ 'required' => false, 'type' => 'string',  'sanitize_callback' => 'sanitize_text_field', 'default' => '' ],
		],
	] );
}
add_action( 'rest_api_init', 'ibapam_register_rest_routes' );

function ibapam_rest_sideload_image( WP_REST_Request $request ) {
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$post_id   = $request->get_param( 'post_id' );
	$image_url = $request->get_param( 'image_url' );
	$alt_text  = $request->get_param( 'alt_text' );

	/* Validate the URL is from TMDB CDN */
	$parsed = wp_parse_url( $image_url );
	$allowed_hosts = [ 'image.tmdb.org' ];
	if ( ! isset( $parsed['host'] ) || ! in_array( $parsed['host'], $allowed_hosts, true ) ) {
		return new WP_Error( 'invalid_host', __( 'Image host not allowed.', 'ibapam' ), [ 'status' => 400 ] );
	}

	/* Sideload */
	$attachment_id = media_sideload_image( $image_url, $post_id, $alt_text, 'id' );
	if ( is_wp_error( $attachment_id ) ) {
		return $attachment_id;
	}

	/* Update alt text */
	if ( $alt_text ) {
		update_post_meta( $attachment_id, '_wp_attachment_image_alt', sanitize_text_field( $alt_text ) );
	}

	/* Set as featured image */
	set_post_thumbnail( $post_id, $attachment_id );

	return rest_ensure_response( [ 'attachment_id' => $attachment_id ] );
}

/* ═══════════════════════════════════════════════════════════
   HELPER FUNCTIONS
═══════════════════════════════════════════════════════════ */

/**
 * Split site name for logo display (first half plain, second half in <span>).
 * Used in header and footer to avoid duplicating the same logic.
 *
 * @param bool $echo Whether to echo (true) or return (false).
 * @return string|void
 */
function ibapam_split_logo_text( $echo = true ) {
	$name = get_bloginfo( 'name' );
	$half = (int) ceil( mb_strlen( $name ) / 2 );
	$out  = esc_html( mb_substr( $name, 0, $half ) ) . '<span>' . esc_html( mb_substr( $name, $half ) ) . '</span>';
	if ( $echo ) {
		echo $out; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already escaped above
	} else {
		return $out;
	}
}

/**
 * Get movie poster URL (fallback to featured image).
 */
function ibapam_get_poster_url( $post_id = null, $size = 'ibapam-poster' ) {
	$post_id = $post_id ?: get_the_ID();
	if ( has_post_thumbnail( $post_id ) ) {
		return get_the_post_thumbnail_url( $post_id, $size );
	}
	return IBAPAM_URI . '/assets/img/no-poster.svg';
}

/**
 * Render quality badge HTML.
 */
function ibapam_quality_badge( $post_id = null ) {
	$post_id = $post_id ?: get_the_ID();
	$quality = get_post_meta( $post_id, 'movie_quality', true );
	if ( ! $quality ) return '';
	$map = [
		'HDRip'  => 'badge-hd',
		'WEB-DL' => 'badge-web',
		'BluRay' => 'badge-fhd',
		'4K'     => 'badge-4k',
		'CAMRip' => 'badge-cam',
		'DVDRip' => 'badge-dvd',
	];
	$class = isset( $map[ $quality ] ) ? $map[ $quality ] : 'badge-hd';
	return '<span class="badge ' . esc_attr( $class ) . '">' . esc_html( $quality ) . '</span>';
}

/**
 * Render rating HTML.
 */
function ibapam_rating_html( $post_id = null ) {
	$post_id = $post_id ?: get_the_ID();
	$rating  = get_post_meta( $post_id, 'movie_rating', true );
	if ( ! $rating ) return '';
	return '<span class="rating-star"><i class="fas fa-star"></i> ' . esc_html( $rating ) . '</span>';
}

/**
 * Breadcrumb output.
 */
function ibapam_breadcrumb() {
	echo '<nav class="breadcrumb" aria-label="' . esc_attr__( 'Breadcrumb', 'ibapam' ) . '">';
	echo '<a href="' . esc_url( home_url( '/' ) ) . '"><i class="fas fa-home"></i></a>';
	echo '<span class="sep">/</span>';

	if ( is_category() || is_tag() || is_author() || is_archive() ) {
		echo '<span class="crumb-current">' . get_the_archive_title() . '</span>';
	} elseif ( is_search() ) {
		echo '<span class="crumb-current">' . esc_html__( 'Search Results', 'ibapam' ) . '</span>';
	} elseif ( is_singular() ) {
		$cats = get_the_category();
		if ( $cats ) {
			echo '<a href="' . esc_url( get_category_link( $cats[0]->term_id ) ) . '">' . esc_html( $cats[0]->name ) . '</a>';
			echo '<span class="sep">/</span>';
		}
		echo '<span class="crumb-current">' . esc_html( get_the_title() ) . '</span>';
	} elseif ( is_page() ) {
		echo '<span class="crumb-current">' . esc_html( get_the_title() ) . '</span>';
	}

	echo '</nav>';
}

/**
 * Related posts query by categories.
 */
function ibapam_related_posts( $post_id = null, $count = 6 ) {
	$post_id = $post_id ?: get_the_ID();
	$cats    = wp_get_post_categories( $post_id );
	if ( ! $cats ) return new WP_Query( [] );

	return new WP_Query( [
		'post__not_in'        => [ $post_id ],
		'posts_per_page'      => $count,
		'category__in'        => $cats,
		'ignore_sticky_posts' => true,
		'orderby'             => 'rand',
		'no_found_rows'       => true,
	] );
}

/**
 * Ticker / latest posts.
 */
function ibapam_ticker_posts( $count = 10 ) {
	return new WP_Query( [
		'posts_per_page' => $count,
		'no_found_rows'  => true,
		'orderby'        => 'date',
		'order'          => 'DESC',
	] );
}

/**
 * Return share URL for a given network.
 *
 * @param string $network telegram|whatsapp|facebook|twitter
 * @param string $url
 * @param string $title
 * @return string
 */
function ibapam_share_url( $network, $url, $title ) {
	$title = rawurlencode( $title );
	$url   = rawurlencode( $url );
	switch ( $network ) {
		case 'telegram':  return 'https://t.me/share/url?url=' . $url . '&text=' . $title;
		case 'whatsapp':  return 'https://wa.me/?text=' . $title . '%20' . $url;
		case 'facebook':  return 'https://www.facebook.com/sharer/sharer.php?u=' . $url;
		case 'twitter':   return 'https://twitter.com/intent/tweet?text=' . $title . '&url=' . $url;
		default:          return '#';
	}
}

/* ═══════════════════════════════════════════════════════════
   CUSTOMIZER
═══════════════════════════════════════════════════════════ */
function ibapam_customize_register( WP_Customize_Manager $wp_customize ) {

	/* ── Site Identity ── */
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';

	/* ── IBAPAM Panel ── */
	$wp_customize->add_panel( 'ibapam_panel', [
		'title'    => __( 'IBAPAM Theme Options', 'ibapam' ),
		'priority' => 30,
	] );

	/* Section: Header */
	$wp_customize->add_section( 'ibapam_header', [
		'panel' => 'ibapam_panel',
		'title' => __( 'Header', 'ibapam' ),
	] );
	/* Ticker text */
	$wp_customize->add_setting( 'ibapam_ticker_label', [ 'default' => 'LATEST', 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'refresh' ] );
	$wp_customize->add_control( 'ibapam_ticker_label', [ 'section' => 'ibapam_header', 'label' => __( 'Ticker Label', 'ibapam' ), 'type' => 'text' ] );
	/* Request button text */
	$wp_customize->add_setting( 'ibapam_request_btn_text', [ 'default' => '🎬 Request', 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'refresh' ] );
	$wp_customize->add_control( 'ibapam_request_btn_text', [ 'section' => 'ibapam_header', 'label' => __( 'Request Button Text', 'ibapam' ), 'type' => 'text' ] );
	/* Request button URL */
	$wp_customize->add_setting( 'ibapam_request_btn_url', [ 'default' => '/request', 'sanitize_callback' => 'esc_url_raw', 'transport' => 'refresh' ] );
	$wp_customize->add_control( 'ibapam_request_btn_url', [ 'section' => 'ibapam_header', 'label' => __( 'Request Button URL', 'ibapam' ), 'type' => 'url' ] );

	/* Section: Footer */
	$wp_customize->add_section( 'ibapam_footer', [
		'panel' => 'ibapam_panel',
		'title' => __( 'Footer', 'ibapam' ),
	] );
	$wp_customize->add_setting( 'ibapam_footer_about', [ 'default' => 'IBAPAM is your #1 destination for latest Bollywood, Hollywood, and South Hindi Dubbed movies & web series.', 'sanitize_callback' => 'sanitize_textarea_field', 'transport' => 'refresh' ] );
	$wp_customize->add_control( 'ibapam_footer_about', [ 'section' => 'ibapam_footer', 'label' => __( 'Footer About Text', 'ibapam' ), 'type' => 'textarea' ] );
	$wp_customize->add_setting( 'ibapam_footer_disclaimer', [ 'default' => '⚠️ Disclaimer: This website does not host any files. All content linked is provided by third parties. For DMCA requests contact us.', 'sanitize_callback' => 'sanitize_textarea_field', 'transport' => 'refresh' ] );
	$wp_customize->add_control( 'ibapam_footer_disclaimer', [ 'section' => 'ibapam_footer', 'label' => __( 'Footer Disclaimer Text', 'ibapam' ), 'type' => 'textarea' ] );
	$wp_customize->add_setting( 'ibapam_copyright', [ 'default' => '© 2025 IBAPAM. All Rights Reserved.', 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'refresh' ] );
	$wp_customize->add_control( 'ibapam_copyright', [ 'section' => 'ibapam_footer', 'label' => __( 'Copyright Text', 'ibapam' ), 'type' => 'text' ] );

	/* Social links */
	$socials = [
		'telegram'  => __( 'Telegram URL',  'ibapam' ),
		'youtube'   => __( 'YouTube URL',   'ibapam' ),
		'instagram' => __( 'Instagram URL', 'ibapam' ),
		'facebook'  => __( 'Facebook URL',  'ibapam' ),
		'twitter'   => __( 'Twitter URL',   'ibapam' ),
	];
	foreach ( $socials as $key => $label ) {
		$wp_customize->add_setting( 'ibapam_social_' . $key, [ 'default' => '', 'sanitize_callback' => 'esc_url_raw', 'transport' => 'refresh' ] );
		$wp_customize->add_control( 'ibapam_social_' . $key, [ 'section' => 'ibapam_footer', 'label' => $label, 'type' => 'url' ] );
	}

	/* Section: TMDB Importer defaults */
	$wp_customize->add_section( 'ibapam_tmdb', [
		'panel' => 'ibapam_panel',
		'title' => __( 'TMDB Importer Defaults', 'ibapam' ),
	] );
	$tmdb_settings = [
		'ibapam_default_quality'    => [ __( 'Default Quality',     'ibapam' ), 'WEB-DL' ],
		'ibapam_default_audio'      => [ __( 'Default Audio',       'ibapam' ), 'Hindi' ],
		'ibapam_default_dl_link'    => [ __( 'Default Download URL','ibapam' ), '#' ],
		'ibapam_default_sizes'      => [ __( 'Default Sizes (CSV)', 'ibapam' ), '400MB, 700MB, 1.4GB, 2.8GB' ],
		'ibapam_telegram_channel'   => [ __( 'Telegram Channel',    'ibapam' ), '@ibapam' ],
	];
	foreach ( $tmdb_settings as $key => $data ) {
		$wp_customize->add_setting( $key, [ 'default' => $data[1], 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'refresh' ] );
		$wp_customize->add_control( $key, [ 'section' => 'ibapam_tmdb', 'label' => $data[0], 'type' => 'text' ] );
	}
}
add_action( 'customize_register', 'ibapam_customize_register' );

/* ═══════════════════════════════════════════════════════════
   EXCERPT LENGTH
═══════════════════════════════════════════════════════════ */
add_filter( 'excerpt_length', function () { return 20; }, 999 );
add_filter( 'excerpt_more',   function () { return '...'; } );

/* ═══════════════════════════════════════════════════════════
   DOCUMENT TITLE
═══════════════════════════════════════════════════════════ */
function ibapam_document_title_parts( $parts ) {
	if ( is_singular() ) {
		$year    = get_post_meta( get_the_ID(), 'movie_year',    true );
		$quality = get_post_meta( get_the_ID(), 'movie_quality', true );
		$audio   = get_post_meta( get_the_ID(), 'movie_audio',   true );
		if ( $year || $quality ) {
			$extra = implode( ' ', array_filter( [ $year, $quality, $audio ] ) );
			$parts['title'] = get_the_title() . ( $extra ? ' [' . $extra . ']' : '' ) . ' Download';
		}
	}
	return $parts;
}
add_filter( 'document_title_parts', 'ibapam_document_title_parts' );

/* ═══════════════════════════════════════════════════════════
   JSON-LD STRUCTURED DATA (Movie schema)
═══════════════════════════════════════════════════════════ */
function ibapam_json_ld() {
	if ( ! is_singular( 'post' ) ) return;
	$post_id = get_the_ID();
	$rating  = get_post_meta( $post_id, 'movie_rating',   true );
	$year    = get_post_meta( $post_id, 'movie_year',     true );
	$genres  = get_post_meta( $post_id, 'movie_genres',   true );
	$director= get_post_meta( $post_id, 'movie_director', true );
	$schema  = [
		'@context'    => 'https://schema.org',
		'@type'       => 'Movie',
		'name'        => get_the_title(),
		'url'         => get_permalink(),
		'description' => wp_strip_all_tags( get_the_excerpt() ),
		'datePublished' => $year ?: get_the_date( 'Y' ),
		'image'       => ibapam_get_poster_url( $post_id, 'ibapam-poster' ),
	];
	if ( $genres ) {
		$schema['genre'] = array_map( 'trim', explode( ',', $genres ) );
	}
	if ( $director ) {
		$schema['director'] = [ '@type' => 'Person', 'name' => $director ];
	}
	if ( $rating ) {
		$schema['aggregateRating'] = [
			'@type'       => 'AggregateRating',
			'ratingValue' => $rating,
			'bestRating'  => '10',
			'ratingCount' => '100',
		];
	}
	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}
add_action( 'wp_head', 'ibapam_json_ld' );
