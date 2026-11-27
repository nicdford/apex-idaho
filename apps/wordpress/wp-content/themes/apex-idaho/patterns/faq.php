<?php
/**
 * Title: FAQ accordion
 * Slug: apex-idaho/faq
 * Categories: apex-sections
 * Keywords: faq, questions, accordion, details
 * Viewport Width: 1440
 * Description: Heading plus expandable question rows. Duplicate a row to add a question.
 *
 * @package apex-idaho
 */

$apex_faq = array(
	'Do I need racing experience?'  => 'No. APEX Idaho events are open to every skill level, from first-timers to seasoned competitors.',
	'What does my car need?'         => 'Every car goes through tech inspection before it touches the track. Check the official Tech & Rules page for the full list.',
	'Can I bring friends to watch?'  => 'Yes. Spectator tickets are sold per event on the event page. Some driver entries include a pass for a plus-one.',
	'Is camping allowed?'            => 'At multi-day events, yes — camping details are listed on each event page.',
);
?>
<!-- wp:group {"tagName":"section","align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80"},"blockGap":"var:preset|spacing|40"}},"backgroundColor":"carbon","layout":{"type":"constrained","contentSize":"960px"}} -->
<section class="wp-block-group alignfull has-carbon-background-color has-background" style="padding-top:var(--wp--preset--spacing--80);padding-bottom:var(--wp--preset--spacing--80)"><!-- wp:paragraph {"className":"is-style-apex-eyebrow"} -->
<p class="is-style-apex-eyebrow">Before you roll in</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Pit lane <strong>FAQ</strong></h2>
<!-- /wp:heading -->
<?php foreach ( $apex_faq as $apex_q => $apex_a ) : ?>

<!-- wp:details -->
<details class="wp-block-details"><summary><?php echo esc_html( $apex_q ); ?></summary><!-- wp:paragraph {"textColor":"smoke"} -->
<p class="has-smoke-color has-text-color"><?php echo esc_html( $apex_a ); ?></p>
<!-- /wp:paragraph --></details>
<!-- /wp:details -->
<?php endforeach; ?>
</section>
<!-- /wp:group -->
