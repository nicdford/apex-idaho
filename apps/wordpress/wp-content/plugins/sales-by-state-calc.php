<?php
/**
 * Pure arithmetic for the Idaho Form 850 worksheet — no WordPress/WooCommerce
 * dependency, so it can be exercised directly in tests.
 *
 * Each entry in $orders is expected to have:
 *   state, total, tax, shipping, refunded, tax_refunded, shipping_refunded
 * (all money values as floats; refunded amounts are non-negative and already
 * netted against their own line, matching WC_Order::get_total_refunded() etc.)
 */
function it_sbs_compute_form_850_totals($orders)
{
  $id_grand = 0.0; $id_tax = 0.0; $id_shipping = 0.0; $other_grand = 0.0;

  foreach ($orders as $o) {
    $net_grand    = (float) $o['total']    - (float) $o['refunded'];
    $net_tax      = (float) $o['tax']      - (float) $o['tax_refunded'];
    $net_shipping = (float) $o['shipping'] - (float) $o['shipping_refunded'];

    if ($o['state'] === 'ID') {
      $id_grand    += $net_grand;
      $id_tax      += $net_tax;
      $id_shipping += $net_shipping;
    } else {
      $other_grand += $net_grand;
    }
  }

  $line1 = $id_grand + $other_grand;
  $line2 = $id_shipping + $id_tax + $other_grand;
  $line3 = max(0, $line1 - $line2);

  return [
    'line1' => $line1,
    'line2' => $line2,
    'line3' => $line3,
  ];
}
