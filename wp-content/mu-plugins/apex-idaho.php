<?php 
/**
 * Bypass Force Login to allow for exceptions.
 *
 * @param bool $bypass Whether to disable Force Login. Default false.
 * @param string $visited_url The visited URL.
 * @return bool
 */
function my_forcelogin_bypass( $bypass, $visited_url ) {

  // Allow 'My Page' to be publicly accessible
  if ( is_page('home') ) {
    $bypass = true;
  }

  return $bypass;
}
add_filter( 'v_forcelogin_bypass', 'my_forcelogin_bypass', 10, 2 );