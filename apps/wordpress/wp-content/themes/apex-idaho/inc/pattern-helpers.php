<?php
/**
 * Markup builders shared by several patterns. They return block markup, so
 * the patterns stay ordinary, fully editable core blocks once inserted.
 *
 * @package apex-idaho
 */

defined( 'ABSPATH' ) || exit;

/**
 * Page title band: eyebrow, H1, optional lede and buttons, angled bottom.
 *
 * @param string $eyebrow Kicker text.
 * @param string $title   Heading HTML (may contain <strong>/<em>/<br>).
 * @param string $lede    Intro sentence, plain text.
 * @param array  $buttons List of [label, url, style] where style is '' or 'outline'.
 * @param string $into    Colour of the section below, for the angled edge:
 *                        'asphalt', 'paper', 'white' or 'red' ('' = carbon).
 * @return string
 */
function apex_idaho_page_band_markup( $eyebrow, $title, $lede = '', $buttons = array(), $into = '' ) {
	$class = 'apex-page-hero apex-texture-night apex-cut-bottom' . ( $into ? ' apex-into-' . sanitize_html_class( $into ) : '' );
	$out   = '<!-- wp:group {"tagName":"section","align":"full","className":"' . $class . '","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"calc(var(--wp--preset--spacing--70) + 2.5rem)"},"blockGap":"var:preset|spacing|30"}},"backgroundColor":"carbon","layout":{"type":"constrained","contentSize":"1320px"}} -->' . "\n";
	$out  .= '<section class="wp-block-group alignfull ' . $class . ' has-carbon-background-color has-background" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:calc(var(--wp--preset--spacing--70) + 2.5rem)">';
	$out .= '<!-- wp:paragraph {"className":"is-style-apex-eyebrow"} -->' . "\n" . '<p class="is-style-apex-eyebrow">' . esc_html( $eyebrow ) . '</p>' . "\n" . '<!-- /wp:paragraph -->' . "\n\n";
	$out .= '<!-- wp:heading {"level":1} -->' . "\n" . '<h1 class="wp-block-heading">' . wp_kses( $title, array( 'strong' => array(), 'em' => array(), 'br' => array() ) ) . '</h1>' . "\n" . '<!-- /wp:heading -->';

	if ( $lede ) {
		$out .= "\n\n" . '<!-- wp:paragraph {"className":"is-style-apex-lede"} -->' . "\n" . '<p class="is-style-apex-lede">' . esc_html( $lede ) . '</p>' . "\n" . '<!-- /wp:paragraph -->';
	}

	if ( $buttons ) {
		$out .= "\n\n" . apex_idaho_buttons_markup( $buttons, array( 'margin_top' => true ) );
	}

	$out .= '</section>' . "\n" . '<!-- /wp:group -->';

	return $out;
}

/**
 * A Buttons block.
 *
 * @param array $buttons List of [label, url, style] — style '' (arrow) or 'outline'.
 * @param array $opts    center => bool, margin_top => bool.
 * @return string
 */
function apex_idaho_buttons_markup( $buttons, $opts = array() ) {
	$attrs = array();
	if ( ! empty( $opts['center'] ) ) {
		$attrs['layout'] = array(
			'type'           => 'flex',
			'justifyContent' => 'center',
		);
	}
	$style = '';
	if ( ! empty( $opts['margin_top'] ) ) {
		$attrs['style'] = array( 'spacing' => array( 'margin' => array( 'top' => 'var:preset|spacing|30' ) ) );
		$style          = ' style="margin-top:var(--wp--preset--spacing--30)"';
	}

	$out = '<!-- wp:buttons' . ( $attrs ? ' ' . wp_json_encode( $attrs, JSON_UNESCAPED_SLASHES ) : '' ) . ' -->' . "\n" . '<div class="wp-block-buttons"' . $style . '>';
	foreach ( $buttons as $button ) {
		$class = ( isset( $button[2] ) && 'outline' === $button[2] ) ? 'is-style-outline' : 'is-style-apex-arrow';
		$out  .= '<!-- wp:button {"className":"' . $class . '"} -->' . "\n";
		$out  .= '<div class="wp-block-button ' . $class . '"><a class="wp-block-button__link wp-element-button" href="' . esc_url( $button[1] ) . '">' . esc_html( $button[0] ) . '</a></div>' . "\n";
		$out  .= '<!-- /wp:button -->';
	}
	$out .= '</div>' . "\n" . '<!-- /wp:buttons -->';

	return $out;
}
