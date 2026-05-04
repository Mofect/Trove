<?php
/**
 * Static subsection title (no saved value; uses a no-op theme_mod).
 *
 * @package Trove
 */

namespace Trove\Customizer;

use WP_Customize_Control;
use WP_Customize_Manager;

/**
 * Renders a label row inside a section (e.g. “Global” above color options).
 *
 * Optional `trove_color_schemes`: ellipsis menu that applies preset theme_mod colors.
 */
final class Heading_Control extends WP_Customize_Control {

	/**
	 * @var string
	 */
	public $type = 'trove_heading';

	/**
	 * Preset color schemes: each item `label` + `colors` (theme_mod key => hex/rgb string).
	 *
	 * @var array<int, array{label:string, colors:array<string, string>}>
	 */
	public $trove_color_schemes = array();

	/**
	 * @param WP_Customize_Manager $manager Manager.
	 * @param string                 $id      Control id (matches dummy setting id).
	 * @param array<string, mixed>   $args    Arguments.
	 */
	public function __construct( WP_Customize_Manager $manager, $id, array $args = array() ) {
		$args['settings'] = array( 'default' => $id );
		parent::__construct( $manager, $id, $args );
	}

	/**
	 * @return list<array{label:string, colors:array<string, string>}>
	 */
	private function get_sanitized_schemes() {
		if ( empty( $this->trove_color_schemes ) || ! is_array( $this->trove_color_schemes ) ) {
			return array();
		}
		$allowed = array_flip( trove_global_color_scheme_setting_keys() );
		$out     = array();
		foreach ( $this->trove_color_schemes as $scheme ) {
			if ( ! is_array( $scheme ) || empty( $scheme['label'] ) || empty( $scheme['colors'] ) || ! is_array( $scheme['colors'] ) ) {
				continue;
			}
			$colors = array();
			foreach ( $scheme['colors'] as $key => $val ) {
				$key = (string) $key;
				if ( ! isset( $allowed[ $key ] ) ) {
					continue;
				}
				$san = trove_sanitize_css_color( $val );
				if ( '' !== $san ) {
					$colors[ $key ] = $san;
				}
			}
			if ( empty( $colors ) ) {
				continue;
			}
			$out[] = array(
				'label'  => (string) $scheme['label'],
				'colors' => $colors,
			);
		}
		return $out;
	}

	/**
	 * @return void
	 */
	public function render_content() {
		if ( '' === $this->label ) {
			return;
		}

		$schemes = $this->get_sanitized_schemes();
		if ( empty( $schemes ) ) {
			?>
			<p class="trove-cz-inline-heading"><?php echo esc_html( $this->label ); ?></p>
			<?php
			return;
		}

		$json_id = 'trove-schemes-json-' . $this->id;
		?>
		<div class="trove-cz-heading-row">
			<span class="trove-cz-heading-row__accent" aria-hidden="true"></span>
			<p class="trove-cz-inline-heading"><?php echo esc_html( $this->label ); ?></p>
			<div class="trove-cz-heading-menu-wrap">
				<button
					type="button"
					class="trove-cz-heading-menu__toggle"
					aria-expanded="false"
					aria-haspopup="true"
					aria-controls="<?php echo esc_attr( 'trove-schemes-panel-' . $this->id ); ?>"
					aria-label="<?php esc_attr_e( 'Color schemes', 'trove' ); ?>"
				>
					<span class="trove-cz-heading-menu__dots" aria-hidden="true">
						<span class="trove-cz-heading-menu__dot"></span>
						<span class="trove-cz-heading-menu__dot"></span>
						<span class="trove-cz-heading-menu__dot"></span>
					</span>
				</button>
				<div
					class="trove-cz-heading-menu"
					id="<?php echo esc_attr( 'trove-schemes-panel-' . $this->id ); ?>"
					role="menu"
					hidden
				>
					<ul class="trove-cz-heading-menu__list" role="none">
						<?php
						$swatch_titles = get_color_scheme_swatch_titles();
						foreach ( $schemes as $index => $scheme ) {
							$keys_order    = trove_global_color_scheme_setting_keys();
							$grouped       = array();
							$emitted_norms = array();
							foreach ( $keys_order as $sk ) {
								if ( ! isset( $scheme['colors'][ $sk ] ) ) {
									continue;
								}
								$norm = trove_normalize_color_for_scheme_dedupe( $scheme['colors'][ $sk ] );
								if ( '' === $norm ) {
									continue;
								}
								if ( ! isset( $grouped[ $norm ] ) ) {
									$grouped[ $norm ] = array();
								}
								$grouped[ $norm ][] = $sk;
							}
							?>
							<li class="trove-cz-heading-menu__item" role="none">
								<button
									type="button"
									class="trove-cz-scheme-item"
									role="menuitem"
									data-trove-scheme-kind="preset"
									data-scheme-index="<?php echo esc_attr( (string) $index ); ?>"
								>
									<span class="trove-cz-scheme-item__label"><?php echo esc_html( $scheme['label'] ); ?></span>
									<span class="trove-cz-scheme-item__swatches" aria-hidden="true">
										<?php
										foreach ( $keys_order as $sk ) {
											if ( ! isset( $scheme['colors'][ $sk ] ) ) {
												continue;
											}
											$norm = trove_normalize_color_for_scheme_dedupe( $scheme['colors'][ $sk ] );
											if ( '' === $norm || isset( $emitted_norms[ $norm ] ) ) {
												continue;
											}
											$emitted_norms[ $norm ] = true;
											$hex                    = $scheme['colors'][ $sk ];
											$title_parts            = array();
											foreach ( $grouped[ $norm ] as $gk ) {
												$t = isset( $swatch_titles[ $gk ] ) ? trim( (string) $swatch_titles[ $gk ] ) : '';
												if ( '' !== $t && ! in_array( $t, $title_parts, true ) ) {
													$title_parts[] = $t;
												}
											}
											$title            = implode( ' · ', $title_parts );
											$swatch_transparent = trove_color_is_transparent_swatch( $hex );
											?>
											<span
												class="trove-cz-scheme-swatch<?php echo $swatch_transparent ? ' trove-cz-swatch-transparent' : ''; ?>"
												<?php
												if ( ! $swatch_transparent ) {
													echo ' style="background-color: ' . esc_attr( $hex ) . '"';
												}
												?>
												<?php echo '' !== $title ? ' title="' . esc_attr( $title ) . '"' : ''; ?>
											></span>
											<?php
										}
										?>
									</span>
								</button>
							</li>
							<?php
						}
						?>
					</ul>
					<div class="trove-cz-heading-menu__footer">
						<button type="button" class="trove-cz-scheme-add">
							<?php esc_html_e( 'Add new scheme', 'trove' ); ?>
						</button>
					</div>
				</div>
			</div>
			<script type="application/json" class="trove-cz-heading-row__schemes-json" id="<?php echo esc_attr( $json_id ); ?>">
				<?php echo wp_json_encode( $schemes, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?>
			</script>
		</div>
		<?php
	}
}
