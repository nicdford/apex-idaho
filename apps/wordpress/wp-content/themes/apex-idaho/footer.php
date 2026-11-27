<?php
/**
 * Footer counterpart to header.php, for hand-built PHP templates only.
 * The footer part was already rendered in header.php (see the note there).
 *
 * @package apex-idaho
 */

defined( 'ABSPATH' ) || exit;

if ( ! isset( $GLOBALS['apex_idaho_classic_footer_html'] ) ) {
	ob_start();
	block_template_part( 'footer' );
	$GLOBALS['apex_idaho_classic_footer_html'] = ob_get_clean();
}
?>
</main>
<footer class="wp-block-template-part"><?php echo $GLOBALS['apex_idaho_classic_footer_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rendered block markup. ?></footer>
</div>
<?php wp_footer(); ?>
</body>
</html>
