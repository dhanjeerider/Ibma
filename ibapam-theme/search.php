<?php
/**
 * IBAPAM Theme — search.php
 *
 * @package ibapam
 */

get_header();
?>

<main id="main-content" class="container" style="padding-top:20px">
  <div class="ibapam-row">
    <div class="ibapam-main">

      <div class="search-header">
        <h1>
          <?php printf( esc_html__( 'Search Results for: %s', 'ibapam' ), '<span>' . esc_html( get_search_query() ) . '</span>' ); ?>
        </h1>
        <?php if ( have_posts() ) : ?>
          <p style="color:var(--text-muted);font-size:.88rem;margin-top:6px">
            <?php printf( esc_html__( '%d results found', 'ibapam' ), (int) $wp_query->found_posts ); ?>
          </p>
        <?php endif; ?>
      </div>

      <?php if ( have_posts() ) : ?>
        <div class="posts-grid grid-4">
          <?php while ( have_posts() ) : the_post(); ?>
            <?php get_template_part( 'template-parts/content', 'card' ); ?>
          <?php endwhile; ?>
        </div>

        <div class="pagination-wrap">
          <?php echo paginate_links( [ 'prev_text' => '<i class="fas fa-chevron-left"></i>', 'next_text' => '<i class="fas fa-chevron-right"></i>' ] ); ?>
        </div>

      <?php else : ?>
        <div class="no-results">
          <i class="fas fa-search"></i>
          <h2><?php esc_html_e( 'No Results Found', 'ibapam' ); ?></h2>
          <p><?php printf( esc_html__( 'Nothing found for "%s". Try different keywords.', 'ibapam' ), esc_html( get_search_query() ) ); ?></p>
          <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" style="margin-top:16px;display:flex;gap:10px;justify-content:center">
            <input type="search" name="s" placeholder="<?php esc_attr_e( 'Search again...', 'ibapam' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" style="padding:10px 16px;background:#1a1a1a;border:1px solid #2a2a2a;border-radius:8px;color:#e0e0e0;font-size:.9rem;min-width:260px;outline:none"/>
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> <?php esc_html_e( 'Search', 'ibapam' ); ?></button>
          </form>
        </div>
      <?php endif; ?>

    </div>
    <?php get_sidebar(); ?>
  </div>
</main>

<?php get_footer(); ?>
