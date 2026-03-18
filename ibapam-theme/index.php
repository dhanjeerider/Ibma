<?php
/**
 * IBAPAM Theme — index.php
 * Homepage with hero slider, filter tabs, and posts grid.
 *
 * @package ibapam
 */

get_header();
?>

<main id="main-content" class="container" style="padding-top:20px">

  <!-- ══════════════════════════════════════════
       HERO SLIDER (latest 6 posts with thumbnails)
  ══════════════════════════════════════════ -->
  <?php
    $hero_posts = new WP_Query( [
        'posts_per_page'      => 6,
        'meta_key'            => '_thumbnail_id',
        'no_found_rows'       => true,
        'ignore_sticky_posts' => true,
        'orderby'             => 'date',
    ] );
  ?>
  <?php if ( $hero_posts->have_posts() ) : ?>
  <section class="hero-section">
    <div class="hero-slider" role="region" aria-label="<?php esc_attr_e( 'Featured Posts', 'ibapam' ); ?>">
      <?php $i = 0; while ( $hero_posts->have_posts() ) : $hero_posts->the_post(); ?>
        <?php
          $h_year   = get_post_meta( get_the_ID(), 'movie_year',    true );
          $h_rating = get_post_meta( get_the_ID(), 'movie_rating',  true );
          $h_qual   = get_post_meta( get_the_ID(), 'movie_quality', true );
        ?>
        <div class="hero-slide <?php echo $i === 0 ? 'active' : ''; ?>" role="group" aria-label="Slide <?php echo $i + 1; ?>">
          <?php the_post_thumbnail( 'ibapam-hero', [ 'alt' => get_the_title(), 'loading' => $i === 0 ? 'eager' : 'lazy' ] ); ?>
          <div class="hero-slide-overlay">
            <div class="hero-meta">
              <?php if ( $h_year )   echo '<span class="badge badge-new">' . esc_html( $h_year ) . '</span>'; ?>
              <?php if ( $h_qual )   echo ibapam_quality_badge( get_the_ID() ); ?>
              <?php if ( $h_rating ) echo '<span class="rating-star"><i class="fas fa-star"></i> ' . esc_html( $h_rating ) . '</span>'; ?>
            </div>
            <h2><a href="<?php the_permalink(); ?>" style="color:inherit"><?php the_title(); ?></a></h2>
            <p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 18, '...' ) ); ?></p>
            <a class="btn btn-primary" href="<?php the_permalink(); ?>"><i class="fas fa-download"></i> <?php esc_html_e( 'Download', 'ibapam' ); ?></a>
          </div>
        </div>
        <?php $i++; endwhile; wp_reset_postdata(); ?>

      <!-- Dots -->
      <div class="hero-dots" role="tablist">
        <?php for ( $d = 0; $d < $i; $d++ ) : ?>
          <button class="hero-dot <?php echo $d === 0 ? 'active' : ''; ?>" role="tab" aria-label="<?php printf( esc_attr__( 'Go to slide %d', 'ibapam' ), $d + 1 ); ?>"></button>
        <?php endfor; ?>
      </div>

      <!-- Prev/Next -->
      <button class="hero-btn" id="hero-prev" aria-label="<?php esc_attr_e( 'Previous', 'ibapam' ); ?>"><i class="fas fa-chevron-left"></i></button>
      <button class="hero-btn" id="hero-next" style="right:60px" aria-label="<?php esc_attr_e( 'Next', 'ibapam' ); ?>"><i class="fas fa-chevron-right"></i></button>
    </div>
  </section>
  <?php endif; ?>

  <!-- ══════════════════════════════════════════
       MAIN CONTENT ROW
  ══════════════════════════════════════════ -->
  <div class="ibapam-row">
    <div class="ibapam-main">

      <!-- Filter Tabs by Category -->
      <?php
        $filter_cats = get_categories( [ 'orderby' => 'count', 'order' => 'DESC', 'number' => 8 ] );
        $current_cat = get_query_var( 'cat' );
      ?>
      <?php if ( $filter_cats ) : ?>
      <div class="filter-tabs" role="tablist">
        <a class="filter-tab <?php echo ! $current_cat ? 'active' : ''; ?>"
           href="<?php echo esc_url( home_url( '/' ) ); ?>"
           role="tab"
           aria-selected="<?php echo ! $current_cat ? 'true' : 'false'; ?>">
          <?php esc_html_e( 'All', 'ibapam' ); ?>
        </a>
        <?php foreach ( $filter_cats as $fc ) : ?>
          <a class="filter-tab <?php echo ( (int) $current_cat === $fc->term_id ) ? 'active' : ''; ?>"
             href="<?php echo esc_url( get_category_link( $fc->term_id ) ); ?>"
             role="tab"
             aria-selected="<?php echo ( (int) $current_cat === $fc->term_id ) ? 'true' : 'false'; ?>">
            <?php echo esc_html( $fc->name ); ?>
          </a>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <!-- Posts Section Header -->
      <div class="section-header">
        <div class="section-title">
          <i class="fas fa-film" style="color:var(--primary);margin-right:8px"></i>
          <?php esc_html_e( 'Latest Movies &amp; Series', 'ibapam' ); ?>
        </div>
        <?php if ( $filter_cats ) : ?>
          <a class="view-all" href="<?php echo esc_url( get_category_link( $filter_cats[0]->term_id ) ); ?>">
            <?php esc_html_e( 'View All', 'ibapam' ); ?> <i class="fas fa-arrow-right"></i>
          </a>
        <?php endif; ?>
      </div>

      <!-- Posts Grid -->
      <?php if ( have_posts() ) : ?>
        <div class="posts-grid grid-4">
          <?php while ( have_posts() ) : the_post(); ?>
            <?php get_template_part( 'template-parts/content', 'card' ); ?>
          <?php endwhile; ?>
        </div>

        <!-- Pagination -->
        <div class="pagination-wrap">
          <?php
            echo paginate_links( [
                'prev_text' => '<i class="fas fa-chevron-left"></i>',
                'next_text' => '<i class="fas fa-chevron-right"></i>',
                'type'      => 'list',
            ] );
          ?>
        </div>

      <?php else : ?>
        <div class="no-results">
          <i class="fas fa-film"></i>
          <h2><?php esc_html_e( 'No posts found', 'ibapam' ); ?></h2>
          <p><?php esc_html_e( 'Try searching or check back later.', 'ibapam' ); ?></p>
          <a class="btn btn-primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to Home', 'ibapam' ); ?></a>
        </div>
      <?php endif; ?>

    </div><!-- /.ibapam-main -->

    <!-- Sidebar -->
    <?php get_sidebar(); ?>

  </div><!-- /.ibapam-row -->
</main>

<?php get_footer(); ?>
