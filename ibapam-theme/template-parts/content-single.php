<?php
/**
 * IBAPAM Theme — template-parts/content-single.php
 *
 * Full single post (movie) content.
 *
 * @package ibapam
 */

$post_id   = get_the_ID();
$year      = get_post_meta( $post_id, 'movie_year',     true );
$rating    = get_post_meta( $post_id, 'movie_rating',   true );
$quality   = get_post_meta( $post_id, 'movie_quality',  true );
$audio     = get_post_meta( $post_id, 'movie_audio',    true );
$genres    = get_post_meta( $post_id, 'movie_genres',   true );
$cast      = get_post_meta( $post_id, 'movie_cast',     true );
$director  = get_post_meta( $post_id, 'movie_director', true );
$runtime   = get_post_meta( $post_id, 'movie_runtime',  true );
$dl_raw    = get_post_meta( $post_id, 'download_links', true );
$tmdb_type = get_post_meta( $post_id, 'tmdb_type',      true ) ?: 'movie';
$permalink = get_permalink();
$post_title = get_the_title();
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'single-post-wrap' ); ?>>

  <!-- Breadcrumb -->
  <?php ibapam_breadcrumb(); ?>

  <!-- Post Title -->
  <h1 class="post-title-h1"><?php the_title(); ?></h1>

  <!-- Post Meta Row -->
  <div class="post-meta-row">
    <?php if ( $year ) : ?>
      <span><i class="fas fa-calendar-alt"></i> <?php echo esc_html( $year ); ?></span>
    <?php endif; ?>
    <?php if ( $rating ) : ?>
      <span class="rating-star"><i class="fas fa-star"></i> <?php echo esc_html( $rating ); ?>/10</span>
    <?php endif; ?>
    <?php if ( $runtime ) : ?>
      <span><i class="fas fa-clock"></i> <?php echo esc_html( $runtime ); ?></span>
    <?php endif; ?>
    <?php if ( $quality ) : ?>
      <span><?php echo ibapam_quality_badge( $post_id ); ?></span>
    <?php endif; ?>
    <?php if ( $audio ) : ?>
      <span><i class="fas fa-volume-up"></i> <?php echo esc_html( $audio ); ?></span>
    <?php endif; ?>
    <span><i class="fas fa-user"></i>
      <?php echo esc_html( get_the_author() ); ?>
    </span>
    <span><i class="fas fa-calendar"></i>
      <?php echo esc_html( get_the_date() ); ?>
    </span>
  </div>

  <!-- Movie Info Box -->
  <div class="movie-info-box">
    <?php if ( has_post_thumbnail() ) : ?>
      <div class="movie-poster">
        <?php the_post_thumbnail( 'ibapam-poster', [ 'alt' => $post_title, 'class' => 'movie-poster-img' ] ); ?>
      </div>
    <?php endif; ?>
    <div class="movie-details-wrap">
      <table class="movie-table">
        <?php if ( $year )    : ?><tr><td><?php esc_html_e( 'Release Year', 'ibapam' ); ?></td><td><?php echo esc_html( $year ); ?></td></tr><?php endif; ?>
        <?php if ( $genres )  : ?><tr><td><?php esc_html_e( 'Genres',       'ibapam' ); ?></td><td><?php echo esc_html( $genres ); ?></td></tr><?php endif; ?>
        <?php if ( $rating )  : ?><tr><td><?php esc_html_e( 'IMDB Rating',  'ibapam' ); ?></td><td><span class="rating-star"><i class="fas fa-star"></i> <?php echo esc_html( $rating ); ?>/10</span></td></tr><?php endif; ?>
        <?php if ( $runtime ) : ?><tr><td><?php esc_html_e( 'Runtime',      'ibapam' ); ?></td><td><?php echo esc_html( $runtime ); ?></td></tr><?php endif; ?>
        <?php if ( $director ): ?><tr><td><?php echo 'tv' === $tmdb_type ? esc_html__( 'Creator', 'ibapam' ) : esc_html__( 'Director', 'ibapam' ); ?></td><td><?php echo esc_html( $director ); ?></td></tr><?php endif; ?>
        <?php if ( $cast )    : ?><tr><td><?php esc_html_e( 'Cast',         'ibapam' ); ?></td><td><?php echo esc_html( $cast ); ?></td></tr><?php endif; ?>
        <?php if ( $quality ) : ?><tr><td><?php esc_html_e( 'Quality',      'ibapam' ); ?></td><td><?php echo ibapam_quality_badge( $post_id ); ?></td></tr><?php endif; ?>
        <?php if ( $audio )   : ?><tr><td><?php esc_html_e( 'Audio',        'ibapam' ); ?></td><td><?php echo esc_html( $audio ); ?></td></tr><?php endif; ?>
        <tr>
          <td><?php esc_html_e( 'Available On', 'ibapam' ); ?></td>
          <td><?php echo esc_html( get_bloginfo( 'name' ) ); ?></td>
        </tr>
      </table>
      <div class="movie-actions-row">
        <?php echo ibapam_quality_badge( $post_id ); ?>
        <?php if ( $year ) echo '<span class="badge badge-new">' . esc_html( $year ) . '</span>'; ?>
        <?php echo ibapam_rating_html( $post_id ); ?>
      </div>
    </div>
  </div>

  <!-- Post Content (synopsis, screenshots, trailer) -->
  <div class="post-content-body">
    <?php the_content(); ?>
  </div>

  <!-- Download Links Section (from post meta JSON) -->
  <?php if ( $dl_raw ) : ?>
    <?php $dl_links = json_decode( $dl_raw, true ); ?>
    <?php if ( is_array( $dl_links ) && count( $dl_links ) ) : ?>
      <div class="download-section">
        <div class="download-section-title">
          <i class="fas fa-download"></i>
          <?php printf( esc_html__( 'Download %s', 'ibapam' ), get_the_title() ); ?>
          <?php if ( $year ) echo '(' . esc_html( $year ) . ')'; ?>
        </div>
        <div class="download-links-grid">
          <?php foreach ( $dl_links as $link ) :
            if ( empty( $link['url'] ) ) continue;
            $link_url   = esc_url( $link['url'] );
            $link_label = isset( $link['label'] ) ? $link['label'] : $post_title;
            $link_size  = isset( $link['size'] )  ? $link['size']  : '';
            $link_qual  = isset( $link['quality'] )? $link['quality'] : $quality;
          ?>
          <a class="download-link-btn" href="<?php echo $link_url; ?>" rel="nofollow" target="_blank">
            <div class="download-link-btn-left">
              <i class="fas fa-download"></i>
              <span><?php echo esc_html( $link_label ); ?> <?php if ( $link_qual ) echo '[' . esc_html( $link_qual ) . ']'; ?></span>
            </div>
            <?php if ( $link_size ) : ?>
              <span class="download-size-label"><?php echo esc_html( $link_size ); ?></span>
            <?php endif; ?>
          </a>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- How to Download -->
      <div class="note-box">
        <div class="note-box-title"><?php esc_html_e( '📌 How to Download?', 'ibapam' ); ?></div>
        <?php printf(
          esc_html__( 'Click the download button → the link opens → choose your preferred size. If links are broken, join our %s Telegram channel for updated links.', 'ibapam' ),
          '<a href="' . esc_url( get_theme_mod( 'ibapam_social_telegram', '#' ) ) . '" rel="nofollow" target="_blank">@' . esc_html( ltrim( get_theme_mod( 'ibapam_telegram_channel', 'ibapam' ), '@' ) ) . '</a>'
        ); ?>
      </div>
    <?php endif; ?>
  <?php endif; ?>

  <!-- Share Buttons -->
  <div class="share-row">
    <div class="share-row-title"><i class="fas fa-share-alt"></i> <?php esc_html_e( 'Share this post', 'ibapam' ); ?></div>
    <div class="share-btns">
      <a class="share-btn telegram"  href="<?php echo esc_url( ibapam_share_url( 'telegram',  $permalink, $post_title ) ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Telegram"><i class="fab fa-telegram"></i></a>
      <a class="share-btn whatsapp"  href="<?php echo esc_url( ibapam_share_url( 'whatsapp',  $permalink, $post_title ) ); ?>" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
      <a class="share-btn facebook"  href="<?php echo esc_url( ibapam_share_url( 'facebook',  $permalink, $post_title ) ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
      <a class="share-btn twitter"   href="<?php echo esc_url( ibapam_share_url( 'twitter',   $permalink, $post_title ) ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
    </div>
  </div>

  <!-- Tags -->
  <?php $tags = get_the_tags(); if ( $tags ) : ?>
    <div class="post-tags-row">
      <span class="post-tags-label"><i class="fas fa-tags"></i> <?php esc_html_e( 'Tags:', 'ibapam' ); ?></span>
      <?php foreach ( $tags as $tag ) : ?>
        <a class="tag" href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>"><?php echo esc_html( $tag->name ); ?></a>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

</article>

<!-- Related Posts -->
<?php
  $related = ibapam_related_posts( $post_id, 6 );
  if ( $related->have_posts() ) :
?>
<section class="related-posts-section">
  <div class="section-title"><?php esc_html_e( 'Related Movies', 'ibapam' ); ?></div>
  <div class="posts-grid grid-3">
    <?php while ( $related->have_posts() ) : $related->the_post(); ?>
      <?php get_template_part( 'template-parts/content', 'card' ); ?>
    <?php endwhile; wp_reset_postdata(); ?>
  </div>
</section>
<?php endif; ?>
