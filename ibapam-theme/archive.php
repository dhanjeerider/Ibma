<?php
/**
 * IBAPAM Theme — archive.php / category.php
 *
 * @package ibapam
 */

get_header();
?>

<main id="main-content" class="container" style="padding-top:20px">
  <div class="ibapam-row">
    <div class="ibapam-main">

      <!-- Archive Header -->
      <div class="archive-header">
        <h1 class="archive-title"><?php the_archive_title(); ?></h1>
        <?php if ( is_category() ) : ?>
          <?php $desc = category_description(); if ( $desc ) echo '<p class="archive-desc">' . wp_kses_post( $desc ) . '</p>'; ?>
          <span class="archive-count">
            <?php printf( esc_html__( '%d posts', 'ibapam' ), (int) $wp_query->found_posts ); ?>
          </span>
        <?php endif; ?>
      </div>

      <?php if ( have_posts() ) : ?>
        <div class="posts-grid grid-4">
          <?php while ( have_posts() ) : the_post(); ?>
            <?php get_template_part( 'template-parts/content', 'card' ); ?>
          <?php endwhile; ?>
        </div>

        <div class="pagination-wrap">
          <?php
            echo paginate_links( [
                'prev_text' => '<i class="fas fa-chevron-left"></i>',
                'next_text' => '<i class="fas fa-chevron-right"></i>',
            ] );
          ?>
        </div>

      <?php else : ?>
        <div class="no-results">
          <i class="fas fa-film"></i>
          <h2><?php esc_html_e( 'No posts found', 'ibapam' ); ?></h2>
          <p><?php esc_html_e( 'Nothing in this category yet. Check back later!', 'ibapam' ); ?></p>
        </div>
      <?php endif; ?>

    </div>
    <?php get_sidebar(); ?>
  </div>
</main>

<?php get_footer(); ?>
