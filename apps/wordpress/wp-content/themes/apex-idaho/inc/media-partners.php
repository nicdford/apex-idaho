<?php
/**
 * Media partner photos and their focal point picker.
 *
 * The media_partner post type is registered outside the theme; the theme only
 * reads its featured image and stores a focal point used when the photo is
 * cropped into a card.
 *
 * @package apex-idaho
 */

defined( 'ABSPATH' ) || exit;

add_action( 'add_meta_boxes', 'apex_idaho_focal_meta_box' );
function apex_idaho_focal_meta_box() {
	add_meta_box(
		'apex_focal_point',
		'Photo Focal Point',
		'apex_idaho_render_focal_meta_box',
		'media_partner',
		'side',
		'default'
	);
}

function apex_idaho_render_focal_meta_box( $post ) {
	wp_nonce_field( 'apex_focal_save', 'apex_focal_nonce' );
	$thumb_id = get_post_thumbnail_id( $post->ID );
	if ( ! $thumb_id ) {
		echo '<p style="color:#999;margin:0;">Set a featured image first, then save and return here.</p>';
		return;
	}
	$img_url = wp_get_attachment_image_url( $thumb_id, 'medium_large' );
	list( $x, $y ) = apex_idaho_partner_focal_xy( $post->ID );
	?>
	<p style="margin:0 0 8px;color:#666;font-size:12px;">Click on the image to set the focal point — this is the spot kept centered when the photo is cropped.</p>
	<div class="apex-focal-wrap" style="position:relative;display:inline-block;max-width:100%;cursor:crosshair;user-select:none;line-height:0;">
		<img src="<?php echo esc_url( $img_url ); ?>" style="display:block;max-width:100%;height:auto;" id="apex-focal-img" alt="">
		<div id="apex-focal-marker" style="position:absolute;width:18px;height:18px;border:2px solid #fff;border-radius:50%;box-shadow:0 0 0 2px #BE1E2D, 0 0 6px rgba(0,0,0,.6);transform:translate(-50%,-50%);pointer-events:none;left:<?php echo esc_attr( $x ); ?>%;top:<?php echo esc_attr( $y ); ?>%;"></div>
	</div>
	<p style="margin:8px 0 0;font-size:12px;color:#666;">
		X: <span id="apex-focal-x-disp"><?php echo esc_html( $x ); ?></span>%
		&nbsp; Y: <span id="apex-focal-y-disp"><?php echo esc_html( $y ); ?></span>%
	</p>
	<input type="hidden" name="apex_focal_x" id="apex-focal-x" value="<?php echo esc_attr( $x ); ?>">
	<input type="hidden" name="apex_focal_y" id="apex-focal-y" value="<?php echo esc_attr( $y ); ?>">
	<script>
	(function () {
		var wrap = document.querySelector('.apex-focal-wrap');
		if (!wrap) return;
		var img = document.getElementById('apex-focal-img');
		var marker = document.getElementById('apex-focal-marker');
		var inX = document.getElementById('apex-focal-x');
		var inY = document.getElementById('apex-focal-y');
		var dispX = document.getElementById('apex-focal-x-disp');
		var dispY = document.getElementById('apex-focal-y-disp');
		wrap.addEventListener('click', function (e) {
			var r = img.getBoundingClientRect();
			var x = Math.round(Math.max(0, Math.min(100, ((e.clientX - r.left) / r.width) * 100)));
			var y = Math.round(Math.max(0, Math.min(100, ((e.clientY - r.top) / r.height) * 100)));
			marker.style.left = x + '%';
			marker.style.top = y + '%';
			inX.value = x; inY.value = y;
			dispX.textContent = x; dispY.textContent = y;
		});
	})();
	</script>
	<?php
}

add_action( 'save_post_media_partner', 'apex_idaho_save_focal_point' );
function apex_idaho_save_focal_point( $post_id ) {
	if ( ! isset( $_POST['apex_focal_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['apex_focal_nonce'] ) ), 'apex_focal_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( isset( $_POST['apex_focal_x'] ) ) {
		update_post_meta( $post_id, '_apex_focal_x', max( 0, min( 100, intval( $_POST['apex_focal_x'] ) ) ) );
	}
	if ( isset( $_POST['apex_focal_y'] ) ) {
		update_post_meta( $post_id, '_apex_focal_y', max( 0, min( 100, intval( $_POST['apex_focal_y'] ) ) ) );
	}
}

/**
 * Focal point of a media partner photo, defaulting to upper-centre (faces).
 *
 * @param int $post_id media_partner post ID.
 * @return int[] [x, y] percentages.
 */
function apex_idaho_partner_focal_xy( $post_id ) {
	$x = get_post_meta( $post_id, '_apex_focal_x', true );
	$y = get_post_meta( $post_id, '_apex_focal_y', true );
	return array(
		'' === $x ? 50 : (int) $x,
		'' === $y ? 25 : (int) $y,
	);
}

/**
 * Photo for a media partner, looked up by post slug.
 *
 * @param string $slug media_partner slug.
 * @return array{url:string,focal:string,link:string}|null
 */
function apex_idaho_partner_photo( $slug ) {
	if ( ! $slug || ! post_type_exists( 'media_partner' ) ) {
		return null;
	}

	$post = get_page_by_path( $slug, OBJECT, 'media_partner' );
	if ( ! $post ) {
		return null;
	}

	$url = get_the_post_thumbnail_url( $post->ID, 'medium_large' );
	if ( ! $url ) {
		return null;
	}

	list( $x, $y ) = apex_idaho_partner_focal_xy( $post->ID );

	return array(
		'url'   => $url,
		'focal' => $x . '% ' . $y . '%',
		'link'  => get_permalink( $post ),
	);
}
