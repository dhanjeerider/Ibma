<?php
/**
 * The main template file — homepage & fallback.
 *
 * @package vegamovies
 */

get_header();
?>

<!-- Main Content -->
<main id="main-content" class="container content-wrapper">

<section id="latest-posts" class="home-wrapper thumbnail-wrapper">

	<?php if ( is_home() && ! is_paged() ) : ?>
		<h2 class="category-name"><?php printf( esc_html__( 'Trending Items on %s', 'vegamovies' ), esc_html( get_bloginfo( 'name' ) ) ); ?></h2>
	<?php elseif ( is_category() ) : ?>
		<h2 class="category-name"><?php single_cat_title(); ?></h2>
	<?php elseif ( is_tag() ) : ?>
		<h2 class="category-name"><?php single_tag_title(); ?></h2>
	<?php elseif ( is_search() ) : ?>
		<h2 class="search-results-header">
			<?php printf( esc_html__( 'Search Results for: %s', 'vegamovies' ), '<span>' . esc_html( get_search_query() ) . '</span>' ); ?>
		</h2>
	<?php else : ?>
		<h2 class="category-name"><?php the_archive_title(); ?></h2>
	<?php endif; ?>

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
