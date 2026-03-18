<?php
/**
 * IBAPAM Theme — page.php
 *
 * @package ibapam
 */

get_header();
?>

<main id="main-content" class="container" style="padding-top:20px">
  <div class="ibapam-row">
    <div class="ibapam-main">
      <?php while ( have_posts() ) : the_post(); ?>
        <?php ibapam_breadcrumb(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
          <h1 class="post-title-h1"><?php the_title(); ?></h1>
          <?php if ( has_post_thumbnail() ) : ?>
            <div style="margin-bottom:20px;border-radius:8px;overflow:hidden">
              <?php the_post_thumbnail( 'full', [ 'style' => 'max-height:400px;width:100%;object-fit:cover' ] ); ?>
            </div>
          <?php endif; ?>
          <div class="post-content-body">
            <?php the_content(); ?>
            <?php wp_link_pages(); ?>
          </div>
        </article>
      <?php endwhile; ?>
    </div>
    <?php get_sidebar(); ?>
  </div>
</main>

<?php get_footer(); ?>
