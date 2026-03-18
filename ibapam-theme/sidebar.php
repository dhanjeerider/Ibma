<?php
/**
 * IBAPAM Theme — sidebar.php
 *
 * @package ibapam
 */
?>
<aside class="ibapam-sidebar" id="secondary" role="complementary">

  <!-- Search Widget -->
  <div class="widget search-widget">
    <div class="widget-title-bar"><?php esc_html_e( 'Search', 'ibapam' ); ?></div>
    <form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
      <input type="search"
             name="s"
             class="search-field"
             placeholder="<?php esc_attr_e( 'Search movies...', 'ibapam' ); ?>"
             value="<?php echo esc_attr( get_search_query() ); ?>"/>
      <button type="submit" class="search-submit" aria-label="<?php esc_attr_e( 'Search', 'ibapam' ); ?>">
        <i class="fas fa-search"></i>
      </button>
    </form>
  </div>

  <!-- Ad Slot -->
  <div class="ad-slot">
    <div class="ad-label"><?php esc_html_e( 'Advertisement', 'ibapam' ); ?></div>
    <!-- Replace with AdSense code -->
    <!-- <ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-XXXXXXXXXX" data-ad-slot="XXXXXXXXXX" data-ad-format="auto"></ins> -->
  </div>

  <!-- Registered sidebars -->
  <?php if ( is_active_sidebar( 'sidebar-main' ) ) : ?>
    <?php dynamic_sidebar( 'sidebar-main' ); ?>
  <?php else : ?>

    <!-- Categories -->
    <div class="widget">
      <div class="widget-title-bar"><?php esc_html_e( 'Categories', 'ibapam' ); ?></div>
      <ul class="cat-list">
        <?php
          $cats = get_categories( [ 'orderby' => 'count', 'order' => 'DESC' ] );
          foreach ( $cats as $cat ) {
              printf(
                  '<li><a href="%s">%s <span class="cat-count">%d</span></a></li>',
                  esc_url( get_category_link( $cat->term_id ) ),
                  esc_html( $cat->name ),
                  (int) $cat->count
              );
          }
        ?>
      </ul>
    </div>

    <!-- Popular Posts -->
    <div class="widget">
      <div class="widget-title-bar"><?php esc_html_e( 'Popular Posts', 'ibapam' ); ?></div>
      <div class="widget-body">
        <?php
          $popular = new WP_Query( [
              'posts_per_page'      => 6,
              'orderby'             => 'comment_count',
              'order'               => 'DESC',
              'no_found_rows'       => true,
              'ignore_sticky_posts' => true,
          ] );
          while ( $popular->have_posts() ) : $popular->the_post();
        ?>
          <div class="post-card-h">
            <a class="card-thumb-h" href="<?php the_permalink(); ?>">
              <?php if ( has_post_thumbnail() ) : ?>
                <?php the_post_thumbnail( 'ibapam-card-sm', [ 'alt' => get_the_title() ] ); ?>
              <?php else : ?>
                <div style="width:100%;height:100%;background:#111;display:flex;align-items:center;justify-content:center;color:#555"><i class="fas fa-film"></i></div>
              <?php endif; ?>
            </a>
            <div class="card-body-h">
              <div class="card-title-h"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></div>
              <div class="card-meta-h">
                <?php echo ibapam_quality_badge(); ?>
                <?php
                  $yr = get_post_meta( get_the_ID(), 'movie_year', true );
                  if ( $yr ) echo '<span> · ' . esc_html( $yr ) . '</span>';
                ?>
              </div>
            </div>
          </div>
        <?php endwhile; wp_reset_postdata(); ?>
      </div>
    </div>

    <!-- Tags -->
    <div class="widget">
      <div class="widget-title-bar"><?php esc_html_e( 'Tags', 'ibapam' ); ?></div>
      <div class="tag-cloud-widget">
        <?php
          $tags = get_tags( [ 'orderby' => 'count', 'order' => 'DESC', 'number' => 25 ] );
          foreach ( $tags as $tag ) {
              printf(
                  '<a class="tag-cloud-link" href="%s">%s</a>',
                  esc_url( get_tag_link( $tag->term_id ) ),
                  esc_html( $tag->name )
              );
          }
        ?>
      </div>
    </div>

  <?php endif; ?>

</aside>
