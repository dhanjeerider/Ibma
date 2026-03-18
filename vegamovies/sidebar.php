<?php
/**
 * Sidebar template.
 *
 * @package vegamovies
 */
?>

<aside class="primary-sidebar col-md-4">

	<?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>
		<div class="widget-body">
			<?php dynamic_sidebar( 'sidebar-1' ); ?>
		</div>
	<?php else : ?>
		<!-- Default sidebar: recent posts -->
		<div class="widget widget-body">
			<h3 class="widget-title"><?php esc_html_e( 'Recent Posts', 'vegamovies' ); ?></h3>
			<div class="widget-body widget-recent thumbnail-wrapper">
				<?php
				$recent_posts = new WP_Query( array(
					'post_type'      => 'post',
					'posts_per_page' => 6,
					'post_status'    => 'publish',
					'post__not_in'   => array( get_the_ID() ),
					'no_found_rows'  => true,
				) );
				if ( $recent_posts->have_posts() ) :
					while ( $recent_posts->have_posts() ) : $recent_posts->the_post();
						$thumb_url = has_post_thumbnail()
							? get_the_post_thumbnail_url( get_the_ID(), 'vegamovies-card' )
							: '';
				?>
					<div class="thumb col-md-4 col-sm-4 col-xs-6">
						<article <?php post_class( 'post-item' ); ?>>
							<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
								<div class="figure">
									<div class="post-thumbnail lazy-wrapper">
										<div class="lazy-loader"></div>
										<?php if ( $thumb_url ) : ?>
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
										<?php endif; ?>
									</div>
									<div class="post-title"><h3><?php the_title(); ?></h3></div>
								</div>
							</a>
						</article>
					</div>
				<?php
					endwhile;
					wp_reset_postdata();
				endif;
				?>
				<div class="clearfix"></div>
			</div><!-- .widget-body -->
		</div>
	<?php endif; ?>

</aside><!-- .primary-sidebar -->
