<?php
/**
 * IBAPAM Theme — single.php
 *
 * @package ibapam
 */

get_header();
?>

<main id="main-content" class="container" style="padding-top:10px">
  <div class="ibapam-row">
    <div class="ibapam-main">
      <?php while ( have_posts() ) : the_post(); ?>
        <?php get_template_part( 'template-parts/content', 'single' ); ?>
      <?php endwhile; ?>
    </div>
    <?php get_sidebar(); ?>
  </div>
</main>

<?php get_footer(); ?>
