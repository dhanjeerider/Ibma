<?php
/**
 * Single post template — movie download post.
 *
 * @package vegamovies
 */

get_header();
?>

<!-- Main Content -->
<main id="main-content" class="container content-wrapper">
<div class="row">

<?php while ( have_posts() ) : the_post(); ?>

<!-- Left / Post Content -->
<article id="post-<?php the_ID(); ?>" <?php post_class( 'left-wrapper col-md-8' ); ?>>

	<h1 class="page-title"><?php the_title(); ?></h1>

	<div class="page-meta">
		<span><time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time></span>
		<?php the_category( ' &bull; ' ); ?>
		<button type="button" class="share-btn" aria-label="<?php esc_attr_e( 'Share this page', 'vegamovies' ); ?>" onclick="if(navigator.share){navigator.share({title:document.title,url:location.href});}">
			<svg fill="currentColor" height="14px" width="14px" viewBox="0 0 458.624 458.624" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"><g><path d="M339.588,314.529c-14.215,0-27.456,4.133-38.621,11.239l-112.682-78.67c1.809-6.315,2.798-12.976,2.798-19.871c0-6.896-0.989-13.557-2.798-19.871l109.64-76.547c11.764,8.356,26.133,13.286,41.662,13.286c39.79,0,72.047-32.257,72.047-72.047C411.634,32.258,379.378,0,339.588,0c-39.79,0-72.047,32.257-72.047,72.047c0,5.255,0.578,10.373,1.646,15.308l-112.424,78.491c-10.974-6.759-23.892-10.666-37.727-10.666c-39.79,0-72.047,32.257-72.047,72.047s32.256,72.047,72.047,72.047c13.834,0,26.753-3.907,37.727-10.666l113.292,79.097c-1.629,6.017-2.514,12.34-2.514,18.872c0,39.79,32.257,72.047,72.047,72.047c39.79,0,72.047-32.257,72.047-72.047C411.635,346.787,379.378,314.529,339.588,314.529z"/></g></svg>
		</button>
	</div><!-- .page-meta -->

	<div class="page-body">

		<?php
		/* ---- Movie Info Table (custom fields) ---- */
		$movie_title  = get_post_meta( get_the_ID(), 'movie_title', true );
		$genres       = get_post_meta( get_the_ID(), 'genres', true );
		$language     = get_post_meta( get_the_ID(), 'language', true );
		$release_date = get_post_meta( get_the_ID(), 'release_date', true );
		$synopsis     = get_post_meta( get_the_ID(), 'synopsis', true );
		$poster_url   = get_post_meta( get_the_ID(), 'poster_url', true );

		if ( $movie_title || $genres || $language || $release_date || $synopsis || $poster_url || has_post_thumbnail() ) :
		?>
		<table class="movie-table">
			<tbody>
				<tr>
					<td class="poster-cell">
						<figure>
							<?php if ( $poster_url ) : ?>
								<img src="<?php echo esc_url( $poster_url ); ?>" alt="<?php echo esc_attr( $movie_title ? $movie_title : get_the_title() ); ?>" width="150" height="250">
							<?php elseif ( has_post_thumbnail() ) : ?>
								<?php the_post_thumbnail( 'vegamovies-poster', array( 'width' => 150, 'height' => 250 ) ); ?>
							<?php endif; ?>
						</figure>
					</td>
					<td class="info-cell">
						<ul class="info-list">
							<?php if ( $movie_title )  : ?><li><strong><?php esc_html_e( 'Title:', 'vegamovies' ); ?></strong> <?php echo esc_html( $movie_title ); ?></li><?php endif; ?>
							<?php if ( $genres )        : ?><li><strong><?php esc_html_e( 'Genres:', 'vegamovies' ); ?></strong> <?php echo esc_html( $genres ); ?></li><?php endif; ?>
							<?php if ( $language )      : ?><li><strong><?php esc_html_e( 'Language:', 'vegamovies' ); ?></strong> <?php echo esc_html( $language ); ?></li><?php endif; ?>
							<?php if ( $release_date )  : ?><li><strong><?php esc_html_e( 'Release:', 'vegamovies' ); ?></strong> <?php echo esc_html( $release_date ); ?></li><?php endif; ?>
							<?php if ( $synopsis )      : ?><li><strong><?php esc_html_e( 'Synopsis:', 'vegamovies' ); ?></strong> <?php echo esc_html( $synopsis ); ?></li><?php endif; ?>
						</ul>
					</td>
				</tr>
			</tbody>
		</table>
		<?php endif; ?>

		<?php
		/* ---- Snapshots ---- */
		$snapshots = get_post_meta( get_the_ID(), 'snapshots', true );
		if ( $snapshots ) :
			$snap_urls = array_filter( array_map( 'trim', explode( "\n", $snapshots ) ) );
		?>
		<div class="hh2"><?php esc_html_e( 'Snapshots', 'vegamovies' ); ?></div>
		<?php foreach ( $snap_urls as $snap_url ) : ?>
			<figure>
				<img
					decoding="async"
					loading="lazy"
					src="<?php echo esc_url( $snap_url ); ?>"
					class="snapshot"
					alt="<?php echo esc_attr( get_the_title() ); ?> <?php esc_attr_e( 'Snapshots', 'vegamovies' ); ?>"
					width="350"
					height="650"
				>
			</figure>
		<?php endforeach; ?>
		<?php endif; ?>

		<?php
		/* ---- Download Links ---- */
		$dllink1       = get_post_meta( get_the_ID(), 'dllink1', true );
		$dllink1_label = get_post_meta( get_the_ID(), 'dllink1_label', true );
		$dllink2       = get_post_meta( get_the_ID(), 'dllink2', true );
		$dllink2_label = get_post_meta( get_the_ID(), 'dllink2_label', true );
		$dllink3       = get_post_meta( get_the_ID(), 'dllink3', true );
		$dllink3_label = get_post_meta( get_the_ID(), 'dllink3_label', true );

		if ( $dllink1 || $dllink2 || $dllink3 ) :
		?>
		<div class="hh2"><?php esc_html_e( 'Links', 'vegamovies' ); ?></div>
		<?php if ( $dllink1 ) : ?>
			<a class="dllink dllink1" href="<?php echo esc_url( $dllink1 ); ?>" target="_blank" rel="nofollow noopener">
				<?php echo esc_html( $dllink1_label ? $dllink1_label : __( 'Download Link 1', 'vegamovies' ) ); ?>
			</a>
		<?php endif; ?>
		<?php if ( $dllink2 ) : ?>
			<a class="dllink dllink2" href="<?php echo esc_url( $dllink2 ); ?>" target="_blank" rel="nofollow noopener">
				<?php echo esc_html( $dllink2_label ? $dllink2_label : __( 'Download Link 2', 'vegamovies' ) ); ?>
			</a>
		<?php endif; ?>
		<?php if ( $dllink3 ) : ?>
			<a class="dllink dllink3" href="<?php echo esc_url( $dllink3 ); ?>" target="_blank" rel="nofollow noopener">
				<?php echo esc_html( $dllink3_label ? $dllink3_label : __( 'Download Link 3', 'vegamovies' ) ); ?>
			</a>
		<?php endif; ?>
		<?php endif; ?>

		<!-- Post content with Read More -->
		<div class="read-more-wrapper">
			<div id="rmContent" class="read-more-content">
				<?php the_content(); ?>
			</div>
			<button id="rmBtn" class="read-more-btn">
				<?php esc_html_e( 'Read More', 'vegamovies' ); ?>
			</button>
		</div>

	</div><!-- .page-body -->

</article><!-- .left-wrapper -->

<?php endwhile; ?>

<?php get_sidebar(); ?>

</div><!-- .row -->

<!-- Disclaimer -->
<section id="disclaimer" class="home-wrapper">
	<div class="home-post">
		<p>
			<strong><?php esc_html_e( 'Disclaimer:', 'vegamovies' ); ?></strong>
			<?php esc_html_e( 'All content is hosted on third-party servers. We do not upload or control any media. Any legal issues should be addressed to the respective hosting providers. All image credit: IMDb.com.', 'vegamovies' ); ?>
		</p>
	</div>
</section>

</main><!-- #main-content -->
<!-- /Main Content -->

<?php get_footer(); ?>
