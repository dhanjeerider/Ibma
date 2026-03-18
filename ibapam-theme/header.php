<?php
/**
 * IBAPAM Theme — header.php
 *
 * @package ibapam
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<link rel="profile" href="https://gmpg.org/xfn/11"/>
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- ══════════════════════════════════════════
     SITE HEADER
══════════════════════════════════════════ -->
<header id="site-header" role="banner">
  <div class="header-inner">

    <!-- Logo -->
    <div class="site-logo">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
        <?php if ( has_custom_logo() ) : ?>
          <?php the_custom_logo(); ?>
        <?php else : ?>
          <span><?php ibapam_split_logo_text(); ?></span>
        <?php endif; ?>
      </a>
    </div>

    <!-- Header Search -->
    <div class="header-search">
      <form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
        <input type="search"
               name="s"
               class="search-field"
               placeholder="<?php echo esc_attr__( 'Search movies, series...', 'ibapam' ); ?>"
               value="<?php echo esc_attr( get_search_query() ); ?>"
               autocomplete="off"/>
        <button type="submit" class="search-submit" aria-label="<?php esc_attr_e( 'Search', 'ibapam' ); ?>">
          <i class="fas fa-search"></i>
        </button>
      </form>
    </div>

    <!-- Header Actions -->
    <div class="header-actions">
      <?php
        $req_text = get_theme_mod( 'ibapam_request_btn_text', '🎬 Request' );
        $req_url  = get_theme_mod( 'ibapam_request_btn_url',  '/request' );
      ?>
      <a class="header-request-btn" href="<?php echo esc_url( $req_url ); ?>">
        <?php echo esc_html( $req_text ); ?>
      </a>

      <button class="menu-toggle-btn" id="menu-toggle" aria-expanded="false" aria-controls="mobile-nav" aria-label="<?php esc_attr_e( 'Open menu', 'ibapam' ); ?>">
        <i class="fas fa-bars"></i>
      </button>
    </div>

  </div><!-- /.header-inner -->
</header>

<!-- ══════════════════════════════════════════
     NAVBAR
══════════════════════════════════════════ -->
<nav id="site-navbar" role="navigation" aria-label="<?php esc_attr_e( 'Primary Navigation', 'ibapam' ); ?>">
  <div class="navbar-inner">
    <?php
      wp_nav_menu( [
          'theme_location'  => 'primary',
          'menu_id'         => 'primary-menu',
          'container'       => false,
          'fallback_cb'     => 'ibapam_fallback_menu',
          'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
          'link_before'     => '',
          'link_after'      => '',
          'walker'          => class_exists( 'IBAPAM_Nav_Walker' ) ? new IBAPAM_Nav_Walker() : null,
      ] );
    ?>
  </div>
</nav>

<!-- ══════════════════════════════════════════
     TICKER
══════════════════════════════════════════ -->
<?php
  $ticker_label = get_theme_mod( 'ibapam_ticker_label', 'LATEST' );
  $ticker_posts = ibapam_ticker_posts( 15 );
  if ( $ticker_posts->have_posts() ) :
?>
<div class="news-ticker">
  <div class="news-ticker-inner">
    <span class="ticker-label"><?php echo esc_html( $ticker_label ); ?></span>
    <div class="ticker-track">
      <div class="ticker-content" id="ticker-content">
        <?php while ( $ticker_posts->have_posts() ) : $ticker_posts->the_post(); ?>
          <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        <?php endwhile; wp_reset_postdata(); ?>
        <!-- duplicate for seamless loop -->
        <?php
          $ticker_posts->rewind_posts();
          while ( $ticker_posts->have_posts() ) : $ticker_posts->the_post();
        ?>
          <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        <?php endwhile; wp_reset_postdata(); ?>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- ══════════════════════════════════════════
     MOBILE NAV OVERLAY
══════════════════════════════════════════ -->
<div id="mobile-nav-overlay"></div>

<aside id="mobile-nav" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Mobile Navigation', 'ibapam' ); ?>">
  <div class="mobile-nav-head">
    <div class="mobile-nav-logo"><?php ibapam_split_logo_text(); ?></div>
    <button id="mobile-nav-close" aria-label="<?php esc_attr_e( 'Close menu', 'ibapam' ); ?>">
      <i class="fas fa-times"></i>
    </button>
  </div>

  <?php
    wp_nav_menu( [
        'theme_location' => 'mobile',
        'menu_id'        => 'mobile-menu',
        'container'      => false,
        'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
        'fallback_cb'    => 'ibapam_fallback_menu',
    ] );
  ?>
</aside>

<!-- ══════════════════════════════════════════
     MAIN WRAPPER
══════════════════════════════════════════ -->
<div id="page-wrap">
<?php
/**
 * Fallback menu when no menu is assigned.
 */
function ibapam_fallback_menu() {
	echo '<ul id="primary-menu">';
	echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'ibapam' ) . '</a></li>';
	$cats = get_categories( [ 'orderby' => 'count', 'order' => 'DESC', 'number' => 8 ] );
	foreach ( $cats as $cat ) {
		echo '<li><a href="' . esc_url( get_category_link( $cat->term_id ) ) . '">' . esc_html( $cat->name ) . '</a></li>';
	}
	echo '</ul>';
}
