<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- Header -->
<header id="site-header" class="primary-header">
<nav id="main-navigation" class="navbar navbar-default">
<div class="container">

	<!-- Search icon toggle (mobile) -->
	<div class="pull-right">
		<button type="button" id="search-toggle" class="navbar-toggle" aria-label="<?php esc_attr_e( 'Toggle search', 'vegamovies' ); ?>" aria-expanded="false">
			<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="6"></circle><path d="M21 21l-4.35-4.35"></path></svg>
		</button>
		<div class="clearfix"></div>
	</div>

	<!-- Logo + hamburger -->
	<div class="navbar-header">
		<button type="button" id="menu-toggle" class="navbar-toggle" aria-label="<?php esc_attr_e( 'Toggle menu', 'vegamovies' ); ?>" aria-expanded="false">
			<span class="sr-only"><?php esc_html_e( 'Toggle navigation', 'vegamovies' ); ?></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</button>
		<a class="navbar-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php bloginfo( 'name' ); ?>">
			<?php
			if ( has_custom_logo() ) {
				the_custom_logo();
			} else {
				echo '<span class="site-title">' . esc_html( get_bloginfo( 'name' ) ) . '</span>';
			}
			?>
		</a>
	</div>

	<!-- Primary navigation menu -->
	<div class="navbar-nav primary-menu" id="primary-menu" style="display:none;">
		<?php
		wp_nav_menu( array(
			'theme_location' => 'primary',
			'menu_id'        => 'menu-main-navigation',
			'menu_class'     => 'list-inline',
			'container'      => false,
			'fallback_cb'    => 'vegamovies_primary_menu_fallback',
		) );
		?>
	</div>

	<!-- Search bar -->
	<div class="navbar-search navbar-right col-md-3 col-sm-4" style="display:none;">
		<?php get_search_form(); ?>
	</div>

</div><!-- .container -->
</nav>
<div class="clearfix"></div>

<!-- Social / quick-links bar -->
<div id="social-share" class="share-now">
	<?php
	if ( has_nav_menu( 'social' ) ) {
		wp_nav_menu( array(
			'theme_location' => 'social',
			'container'      => false,
			'menu_class'     => '',
			'fallback_cb'    => false,
		) );
	}
	?>
</div>

<!-- Category list -->
<div class="home-categories">
	<nav id="category-list" aria-label="<?php esc_attr_e( 'Categories', 'vegamovies' ); ?>">
		<ul class="list-inline">
			<?php
			$cats = get_categories( array( 'hide_empty' => true, 'exclude' => array( 1 ), 'orderby' => 'name', 'order' => 'ASC' ) );
			foreach ( $cats as $cat ) {
				echo '<li><a href="' . esc_url( get_category_link( $cat->term_id ) ) . '" title="' . esc_attr( $cat->name ) . '">' . esc_html( $cat->name ) . '</a></li>';
			}
			?>
		</ul>
	</nav>
</div>

</header><!-- #site-header -->
<!-- /Header -->
<?php
/**
 * Fallback for primary menu when no menu is assigned.
 */
function vegamovies_primary_menu_fallback() {
	echo '<ul id="menu-main-navigation" class="list-inline">';
	echo '<li class="menu-item"><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'vegamovies' ) . '</a></li>';
	echo '</ul>';
}
