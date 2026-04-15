<?php
/**
 * Comments template.
 *
 * @package Trove
 */

if ( post_password_required() ) {
	return;
}
?>
<div id="comments" class="comments-area">
	<?php if ( have_comments() ) : ?>
		<h2 class="comments-title">
			<?php
			$count = get_comments_number();
			if ( '1' === $count ) {
				esc_html_e( 'One comment', 'trove' );
			} else {
				printf(
					/* translators: %s: comment count */
					esc_html( _n( '%s comment', '%s comments', $count, 'trove' ) ),
					number_format_i18n( $count )
				);
			}
			?>
		</h2>
		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'style'      => 'ol',
					'short_ping' => true,
					'avatar_size'=> 40,
				)
			);
			?>
		</ol>
		<?php
		the_comments_navigation(
			array(
				'prev_text' => __( 'Older comments', 'trove' ),
				'next_text' => __( 'Newer comments', 'trove' ),
			)
		);
	endif;
	if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
		?>
		<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'trove' ); ?></p>
		<?php
	endif;
	comment_form();
	?>
</div>
