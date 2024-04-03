<?php
/**
 * Compatibility class.
 *
 * @since 1.6
 */
final class FLThemeCompat {

	/**
	 * Filters and actions to fix plugin compatibility.
	 */
	public static function init() {

		// Filter fl_archive_show_full to fix CRED form preview.
		add_filter( 'fl_archive_show_full', 'FLThemeCompat::fix_cred_preview' );
		add_action( 'template_redirect', 'FLThemeCompat::fix_ld_focus' );
		add_action( 'customize_controls_enqueue_scripts', array( __CLASS__, 'tribe_select2' ) );
		add_action( 'admin_enqueue_scripts', 'FLThemeCompat::addify_tax_exempt', 11 );
		add_action( 'template_redirect', 'FLThemeCompat::fix_reviews' );
	}

	/**
	 * Fix woo reviews if no stars and YITH (311 266)
	 */
	public static function fix_reviews() {
		if ( function_exists( 'is_product' ) && is_product() ) {
			add_filter( 'comment_form_defaults', function( $args ) {
				$args['id_form'] = 'commentform';
			}, 11 );
		}
	}

	/**
	 * If we are showing a CRED form preview we need to show full post always
	 * so the shortcodes will render.
	 * @since 1.6
	 */
	public static function fix_cred_preview( $show_full ) {

		if ( isset( $_REQUEST['cred_form_preview'] ) ) {
			return true;
		}
		return $show_full;
	}

	public static function fix_ld_focus() {
		if ( class_exists( 'LearnDash_Settings_Section' ) ) {
			$focus_mode = LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Theme_LD30', 'focus_mode_enabled' );
			$post_types = array(
				'sfwd-lessons',
				'sfwd-topic',
				'sfwd-quiz',
				'sfwd-assignment',
			);
			if ( 'yes' === $focus_mode && in_array( get_post_type(), $post_types, true ) ) {
				add_action( 'wp_head', 'FLTheme::fonts' );
				remove_action( 'fl_head_open', 'FLTheme::fonts' );
			}
		}
	}

	/**
	 * Deregister tribe select2, we load our own for font selection.
	 * @since 1.7.4
	 */
	public static function tribe_select2() {
		wp_deregister_script( 'tribe-select2' );
		wp_deregister_style( 'tribe-select2-css' );
	}

	/**
	 * Deregister jquery-ui when using Addify's WooCommerce Tax Exempt Plugin
	 * @since 1.7.8
	 */
	public static function addify_tax_exempt() {
		if ( class_exists( 'Addify_Tax_Exempt' ) && ( is_customize_preview() ) ) {
			wp_dequeue_script( 'jquery-ui' );
		}
	}
}
