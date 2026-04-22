<?php
/**
 * Archive template.
 *
 * @package Trove
 */

get_header();
?>
<header class="archive-header<?php echo ( is_author() && get_queried_object() instanceof \WP_User ) ? ' archive-header--author' : ''; ?>">
	<?php
	if ( is_author() ) {
		$author_obj = get_queried_object();
		if ( $author_obj instanceof \WP_User ) {
			$bio_raw = get_the_author_meta( 'description', $author_obj->ID );
			$has_bio = '' !== trim( wp_strip_all_tags( (string) $bio_raw ) );

			$avatar_html = get_avatar(
				$author_obj->ID,
				192,
				'',
				esc_attr(
					sprintf(
						/* translators: %s: author display name */
						__( 'Photo of %s', 'trove' ),
						$author_obj->display_name
					)
				),
				array(
					'class'    => 'archive-header__avatar',
					'loading'  => 'lazy',
					'decoding' => 'async',
				)
			);
			?>
			<div class="archive-header__author">
				<?php if ( $avatar_html ) : ?>
					<?php echo wp_kses_post( $avatar_html ); ?>
				<?php endif; ?>
				<div class="archive-header__author-text">
					<h1 class="archive-title"><?php echo esc_html( $author_obj->display_name ); ?></h1>
					<?php
					$post_count = (int) count_user_posts( $author_obj->ID, 'post', true );
					$articles     = sprintf(
						/* translators: %d: number of posts published by this author */
						_n( '%d article', '%d articles', $post_count, 'trove' ),
						$post_count
					);

					$user_url_raw = trim( (string) get_the_author_meta( 'user_url', $author_obj->ID ) );
					$website_href = '';
					$website_label = '';
					if ( '' !== $user_url_raw ) {
						$website_href = esc_url( $user_url_raw, array( 'http', 'https' ) );
						if ( $website_href && '#' !== $website_href ) {
							$parsed = wp_parse_url( $website_href );
							if ( ! empty( $parsed['host'] ) ) {
								$website_label = preg_replace( '/^www\./i', '', $parsed['host'] );
								if ( ! empty( $parsed['path'] ) && '/' !== $parsed['path'] ) {
									$website_label .= untrailingslashit( $parsed['path'] );
								}
							} else {
								$website_label = $user_url_raw;
							}
						}
					}
					?>
					<p class="archive-header__meta">
						<span class="archive-header__post-count"><?php echo esc_html( $articles ); ?></span>
						<?php if ( '' !== $website_href && '' !== $website_label ) : ?>
							<span class="archive-header__meta-sep" aria-hidden="true"> / </span>
							<a class="archive-header__website" href="<?php echo esc_url( $website_href ); ?>">
								<svg class="archive-header__website-icon ct-icon" width="12" height="12" viewBox="0 0 20 20" aria-hidden="true" focusable="false">
									<path fill="currentColor" d="M10 0C4.5 0 0 4.5 0 10s4.5 10 10 10 10-4.5 10-10S15.5 0 10 0zm6.9 6H14c-.4-1.8-1.4-3.6-1.4-3.6s2.8.8 4.3 3.6zM10 2s1.2 1.7 1.9 4H8.1C8.8 3.6 10 2 10 2zM2.2 12s-.6-1.8 0-4h3.4c-.3 1.8 0 4 0 4H2.2zm.9 2H6c.6 2.3 1.4 3.6 1.4 3.6C4.3 16.5 3.1 14 3.1 14zM6 6H3.1c1.6-2.8 4.3-3.6 4.3-3.6S6.4 4.2 6 6zm4 12s-1.3-1.9-1.9-4h3.8c-.6 2.1-1.9 4-1.9 4zm2.3-6H7.7s-.3-2 0-4h4.7c.3 1.8-.1 4-.1 4zm.3 5.6s1-1.8 1.4-3.6h2.9c-1.6 2.7-4.3 3.6-4.3 3.6zm1.7-5.6s.3-2.1 0-4h3.4c.6 2.2 0 4 0 4h-3.4z"></path>
								</svg>
								<span class="archive-header__website-label"><?php echo esc_html( $website_label ); ?></span>
							</a>
						<?php endif; ?>
					</p>
					<?php if ( $has_bio ) : ?>
						<div class="archive-description"><?php echo wp_kses_post( wpautop( $bio_raw ) ); ?></div>
					<?php endif; ?>
				</div>
			</div>
			<?php
		} else {
			the_archive_title( '<h1 class="archive-title">', '</h1>' );
			the_archive_description( '<div class="archive-description">', '</div>' );
		}
	} else {
		the_archive_title( '<h1 class="archive-title">', '</h1>' );
		the_archive_description( '<div class="archive-description">', '</div>' );
	}
	?>
</header>
<main id="primary" class="site-main">
	<?php do_action( 'trove_blog_loop' ); ?>
</main>
<?php
get_footer();
