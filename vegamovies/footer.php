<!-- Footer -->
<footer id="footer" class="primary-footer">
<div class="container">
	<div class="col-sm-6 footer-brand">
		<p>&copy; <?php echo esc_html( date( 'Y' ) ); ?>. <?php echo esc_html( get_bloginfo( 'name' ) ); ?>.</p>
	</div>
	<div class="col-sm-6 text-right">
		<nav class="footer-navigation" aria-label="<?php esc_attr_e( 'Footer Menu', 'vegamovies' ); ?>">
			<?php
			wp_nav_menu( array(
				'theme_location' => 'footer',
				'menu_id'        => 'menu-footer-navigation',
				'menu_class'     => 'list-inline',
				'container'      => false,
				'fallback_cb'    => false,
			) );
			?>
		</nav>
	</div>
	<div class="clearfix"></div>
</div>
</footer><!-- #footer -->
<!-- /Footer -->

<?php wp_footer(); ?>
</body>
</html>
