<?php
/**
 * IBAPAM Theme — template-parts/content-card.php
 *
 * Movie post card displayed in grids.
 *
 * @package ibapam
 */

$post_id = get_the_ID();
$year    = get_post_meta( $post_id, 'movie_year',    true );
$rating  = get_post_meta( $post_id, 'movie_rating',  true );
$quality = get_post_meta( $post_id, 'movie_quality', true );
$audio   = get_post_meta( $post_id, 'movie_audio',   true );
$genres  = get_post_meta( $post_id, 'movie_genres',  true );
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card' ); ?> aria-label="<?php the_title_attribute(); ?>">

  <!-- Poster / Thumbnail -->
  <a class="card-thumb" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
    <?php if ( has_post_thumbnail() ) : ?>
      <?php the_post_thumbnail( 'ibapam-card', [ 'alt' => get_the_title(), 'loading' => 'lazy' ] ); ?>
    <?php else : ?>
      <div style="width:100%;height:100%;background:#111;display:flex;align-items:center;justify-content:center;color:#555;aspect-ratio:2/3">
        <i class="fas fa-film" style="font-size:2rem"></i>
      </div>
    <?php endif; ?>

    <!-- Badges overlay -->
    <div class="card-badges">
      <?php if ( $quality ) : ?>
        <?php echo ibapam_quality_badge( $post_id ); ?>
      <?php endif; ?>
      <?php if ( $year ) : ?>
        <span class="badge badge-new"><?php echo esc_html( $year ); ?></span>
      <?php endif; ?>
    </div>

    <!-- Rating -->
    <?php if ( $rating ) : ?>
      <div class="card-imdb"><i class="fas fa-star"></i> <?php echo esc_html( $rating ); ?></div>
    <?php endif; ?>

    <!-- Play overlay -->
    <div class="card-overlay">
      <div class="card-play"><i class="fas fa-play"></i></div>
    </div>
  </a>

  <!-- Card Body -->
  <div class="card-body">
    <h3 class="card-title">
      <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
    </h3>
    <div class="card-meta">
      <?php if ( $year ) : ?>
        <span><i class="fas fa-calendar-alt"></i> <?php echo esc_html( $year ); ?></span>
      <?php endif; ?>
      <?php if ( $audio ) : ?>
        <span><i class="fas fa-volume-up"></i> <?php echo esc_html( $audio ); ?></span>
      <?php endif; ?>
      <?php if ( $genres ) : ?>
        <span><i class="fas fa-tag"></i> <?php echo esc_html( wp_trim_words( $genres, 2, '' ) ); ?></span>
      <?php endif; ?>
    </div>
  </div>

</article>
