<?php
/**
 * Archive template — categories, tags, date archives.
 *
 * @package vegamovies
 */

get_header();
?>

<!-- Main Content -->
<main id="main-content" class="container content-wrapper">

<section id="latest-posts" class="home-wrapper thumbnail-wrapper">

	<h2 class="category-name">
		<?php
		if ( is_category() ) {
			single_cat_title();
		} elseif ( is_tag() ) {
			single_tag_title();
		} elseif ( is_author() ) {
			printf( esc_html__( 'Posts by: %s', 'vegamovies' ), get_the_author() );
		} elseif ( is_year() ) {
			printf( esc_html__( 'Year: %s', 'vegamovies' ), get_the_date( 'Y' ) );
		} elseif ( is_month() ) {
			printf( esc_html__( 'Month: %s', 'vegamovies' ), get_the_date( 'F Y' ) );
		} else {
			the_archive_title();
		}
		?>
	</h2>

	<?php if ( have_posts() ) : ?>

		<?php while ( have_posts() ) : the_post(); ?>

			<div class="thumb col-md-2 col-sm-4 col-xs-6">
				<article <?php post_class( 'post-item' ); ?>>
					<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
						<div class="figure">
							<div class="post-thumbnail lazy-wrapper">
								<div class="lazy-loader"></div>
								<?php if ( has_post_thumbnail() ) :
									$thumb_url = get_the_post_thumbnail_url( get_the_ID(), 'vegamovies-card' );
								?>
									<img
										class="img-fluid lazy"
										src="<?php echo esc_url( $thumb_url ); ?>"
										data-src="<?php echo esc_url( $thumb_url ); ?>"
										alt="<?php the_title_attribute(); ?>"
										width="300"
										height="450"
										decoding="async"
										loading="lazy"
									>
								<?php else : ?>
									<img
										src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/no-poster.jpg' ); ?>"
										alt="<?php the_title_attribute(); ?>"
										width="300"
										height="450"
										decoding="async"
										loading="lazy"
									>
								<?php endif; ?>
							</div><!-- .post-thumbnail -->
							<div class="post-title"><h3><?php the_title(); ?></h3></div>
						</div><!-- .figure -->
					</a>
				</article>
			</div><!-- .thumb -->

		<?php endwhile; ?>

		<div class="clearfix"></div>

		<!-- Pagination -->
		<div class="col-md-12 text-center pagination-wrapper">
			<div class="pagination-wrap">
				<?php
				echo paginate_links( array(
					'type'      => 'list',
					'prev_text' => '&lt; <span>' . esc_html__( 'Prev', 'vegamovies' ) . '</span>',
					'next_text' => '<span>' . esc_html__( 'Next', 'vegamovies' ) . '</span> &gt;',
				) );
				?>
			</div>
		</div>
		<div class="clearfix"></div>

	<?php else : ?>
		<p class="no-posts"><?php esc_html_e( 'No posts found.', 'vegamovies' ); ?></p>
	<?php endif; ?>

</section>

</main><!-- #main-content -->
<!-- /Main Content -->

<?php get_footer(); ?>
