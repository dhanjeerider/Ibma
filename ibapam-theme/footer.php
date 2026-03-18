<?php
/**
 * IBAPAM Theme — footer.php
 *
 * @package ibapam
 */
?>
</div><!-- /#page-wrap -->

<!-- ══════════════════════════════════════════
     FOOTER
══════════════════════════════════════════ -->
<footer id="site-footer" role="contentinfo">

  <!-- Footer Top -->
  <div class="footer-top">
    <div class="container">
      <div class="footer-grid">

        <!-- Col 1: About -->
        <div class="footer-col">
          <?php if ( has_custom_logo() ) : ?>
            <div class="footer-logo"><?php the_custom_logo(); ?></div>
          <?php else : ?>
          <div class="footer-logo"><?php ibapam_split_logo_text(); ?></div>
          <?php endif; ?>

          <p class="footer-about-text">
            <?php echo esc_html( get_theme_mod( 'ibapam_footer_about', 'IBAPAM is your #1 destination for latest Bollywood, Hollywood, and South Hindi Dubbed movies & web series.' ) ); ?>
          </p>

          <!-- Social Links -->
          <div class="footer-social">
            <?php
              $socials = [
                  'telegram'  => [ 'fab fa-telegram',  '#0088cc' ],
                  'youtube'   => [ 'fab fa-youtube',   '#ff0000' ],
                  'instagram' => [ 'fab fa-instagram', '' ],
                  'facebook'  => [ 'fab fa-facebook',  '#1877f2' ],
                  'twitter'   => [ 'fab fa-twitter',   '#1da1f2' ],
              ];
              foreach ( $socials as $key => $data ) {
                  $url = get_theme_mod( 'ibapam_social_' . $key, '' );
                  if ( $url ) {
                      printf(
                          '<a class="social-btn %s" href="%s" target="_blank" rel="noopener noreferrer" aria-label="%s"><i class="%s"></i></a>',
                          esc_attr( $key ),
                          esc_url( $url ),
                          esc_attr( ucfirst( $key ) ),
                          esc_attr( $data[0] )
                      );
                  }
              }
            ?>
          </div>

          <?php
            $disclaimer = get_theme_mod( 'ibapam_footer_disclaimer', '⚠️ This website does not host any files. All content is linked from third-party servers. For DMCA requests contact us.' );
            if ( $disclaimer ) :
          ?>
            <div class="footer-disclaimer"><?php echo esc_html( $disclaimer ); ?></div>
          <?php endif; ?>
        </div>

        <!-- Col 2: Categories (widget area or fallback) -->
        <div class="footer-col">
          <?php if ( is_active_sidebar( 'sidebar-footer-1' ) ) : ?>
            <?php dynamic_sidebar( 'sidebar-footer-1' ); ?>
          <?php else : ?>
            <div class="footer-col-title"><?php esc_html_e( 'Categories', 'ibapam' ); ?></div>
            <ul class="footer-links">
              <?php
                $cats = get_categories( [ 'orderby' => 'count', 'order' => 'DESC', 'number' => 8 ] );
                foreach ( $cats as $cat ) {
                    printf(
                        '<li><a href="%s">%s</a></li>',
                        esc_url( get_category_link( $cat->term_id ) ),
                        esc_html( $cat->name )
                    );
                }
              ?>
            </ul>
          <?php endif; ?>
        </div>

        <!-- Col 3: Pages (widget area or fallback) -->
        <div class="footer-col">
          <?php if ( is_active_sidebar( 'sidebar-footer-2' ) ) : ?>
            <?php dynamic_sidebar( 'sidebar-footer-2' ); ?>
          <?php else : ?>
            <div class="footer-col-title"><?php esc_html_e( 'Quick Links', 'ibapam' ); ?></div>
            <?php
              wp_nav_menu( [
                  'theme_location' => 'footer-pg',
                  'container'      => false,
                  'menu_class'     => 'footer-links',
                  'depth'          => 1,
                  'fallback_cb'    => function () {
                      echo '<ul class="footer-links">';
                      $pages = get_pages( [ 'sort_column' => 'menu_order', 'number' => 8 ] );
                      foreach ( $pages as $pg ) {
                          printf( '<li><a href="%s">%s</a></li>', esc_url( get_permalink( $pg->ID ) ), esc_html( $pg->post_title ) );
                      }
                      echo '</ul>';
                  },
              ] );
            ?>
          <?php endif; ?>
        </div>

        <!-- Col 4: Latest posts / widget -->
        <div class="footer-col">
          <?php if ( is_active_sidebar( 'sidebar-footer-3' ) ) : ?>
            <?php dynamic_sidebar( 'sidebar-footer-3' ); ?>
          <?php else : ?>
            <div class="footer-col-title"><?php esc_html_e( 'Latest', 'ibapam' ); ?></div>
            <?php
              $latest = new WP_Query( [ 'posts_per_page' => 5, 'no_found_rows' => true ] );
              if ( $latest->have_posts() ) :
            ?>
            <ul class="footer-links">
              <?php while ( $latest->have_posts() ) : $latest->the_post(); ?>
                <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
              <?php endwhile; wp_reset_postdata(); ?>
            </ul>
            <?php endif; ?>
          <?php endif; ?>
        </div>

      </div><!-- /.footer-grid -->
    </div><!-- /.container -->
  </div><!-- /.footer-top -->

  <!-- Footer Bottom -->
  <div class="footer-bottom">
    <div class="container">
      <div class="footer-bottom-inner">
        <span><?php echo esc_html( get_theme_mod( 'ibapam_copyright', '© ' . gmdate( 'Y' ) . ' ' . get_bloginfo( 'name' ) . '. All Rights Reserved.' ) ); ?></span>
        <div class="footer-bottom-links">
          <?php
            $pages_ids = get_pages( [ 'sort_column' => 'menu_order', 'number' => 6 ] );
            foreach ( $pages_ids as $pg ) {
                printf( '<a href="%s">%s</a>', esc_url( get_permalink( $pg->ID ) ), esc_html( $pg->post_title ) );
            }
          ?>
        </div>
      </div>
    </div>
  </div>

</footer>

<!-- Back to Top -->
<button id="back-to-top" aria-label="<?php esc_attr_e( 'Back to top', 'ibapam' ); ?>">
  <i class="fas fa-chevron-up"></i>
</button>

<?php wp_footer(); ?>
</body>
</html>
