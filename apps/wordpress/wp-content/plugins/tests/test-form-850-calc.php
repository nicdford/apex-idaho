<?php
/**
 * Plain-PHP tests for it_sbs_compute_form_850_totals() — no PHPUnit/WordPress
 * dependency required. Run with: php test-form-850-calc.php
 */

require_once __DIR__ . '/../sales-by-state-calc.php';

$failures = 0;
$passes = 0;

function it_sbs_assert_close($expected, $actual, $label)
{
  global $failures, $passes;
  if (abs($expected - $actual) < 0.005) {
    $passes++;
    echo "PASS: $label\n";
  } else {
    $failures++;
    echo "FAIL: $label (expected " . number_format($expected, 2) . ", got " . number_format($actual, 2) . ")\n";
  }
}

function order($state, $total, $tax, $shipping, $refunded = 0, $tax_refunded = 0, $shipping_refunded = 0)
{
  return [
    'state' => $state, 'total' => $total, 'tax' => $tax, 'shipping' => $shipping,
    'refunded' => $refunded, 'tax_refunded' => $tax_refunded, 'shipping_refunded' => $shipping_refunded,
  ];
}

/**
 * The pre-fix behavior: Line 1 was the grand total of Idaho orders ONLY
 * (out-of-state orders excluded from the query entirely), Line 2 was just
 * Idaho shipping + tax, and refunds were never netted out.
 */
function it_sbs_compute_form_850_totals_legacy($orders)
{
  $id_grand = 0.0; $id_tax = 0.0; $id_shipping = 0.0;
  foreach ($orders as $o) {
    if ($o['state'] !== 'ID') continue;
    $id_grand    += $o['total'];
    $id_tax      += $o['tax'];
    $id_shipping += $o['shipping'];
  }
  $line1 = $id_grand;
  $line2 = $id_shipping + $id_tax;
  $line3 = max(0, $line1 - $line2);
  return ['line1' => $line1, 'line2' => $line2, 'line3' => $line3];
}

// --- Test 1: no refunds, mix of ID and non-ID orders ---
// Tax burden (line 3, and everything downstream) must be identical before/after,
// since out-of-state sales now flow through line 1 and cancel out in line 2.
$orders = [
  order('ID', 1000.00, 60.00, 20.00),
  order('ID', 500.00, 30.00, 10.00),
  order('WA', 800.00, 0.00, 15.00),
  order('CA', 300.00, 0.00, 5.00),
];
$before = it_sbs_compute_form_850_totals_legacy($orders);
$after  = it_sbs_compute_form_850_totals($orders);
it_sbs_assert_close($before['line3'], $after['line3'], 'Test 1: net taxable sales unchanged when out-of-state orders are present (no refunds)');
it_sbs_assert_close(1500.00, $before['line1'], 'Test 1: legacy line1 sanity check (ID-only)');
it_sbs_assert_close(2600.00, $after['line1'], 'Test 1: new line1 includes all states (1000+500+800+300)');
it_sbs_assert_close(1220.00, $after['line2'], 'Test 1: new line2 = ID shipping+tax (30+90) + out-of-state total (1100)');

// --- Test 2: out-of-state-only orders must never affect net taxable sales ---
$orders2 = [
  order('ID', 1000.00, 60.00, 20.00),
  order('WA', 5000.00, 0.00, 100.00),
];
$after2 = it_sbs_compute_form_850_totals($orders2);
it_sbs_assert_close(920.00, $after2['line3'], 'Test 2: line3 reflects only Idaho item value regardless of out-of-state volume (1000-80)');

// --- Test 3: a same-period partial refund on an Idaho order reduces the tax burden ---
$orders3 = [
  order('ID', 1000.00, 60.00, 20.00, /*refunded*/ 200.00, /*tax_refunded*/ 12.00, /*shipping_refunded*/ 0.00),
];
$after3 = it_sbs_compute_form_850_totals($orders3);
// net_grand = 800, net_tax = 48, net_shipping = 20 -> line1=800, line2=68, line3=732
it_sbs_assert_close(800.00, $after3['line1'], 'Test 3: line1 nets out the refunded amount');
it_sbs_assert_close(732.00, $after3['line3'], 'Test 3: line3 (net taxable) is reduced by the refund, lowering tax due');

// --- Test 4: a fully refunded Idaho order contributes nothing ---
$orders4 = [
  order('ID', 1000.00, 60.00, 20.00),
  order('ID', 500.00, 30.00, 10.00, /*refunded*/ 500.00, /*tax_refunded*/ 30.00, /*shipping_refunded*/ 10.00),
];
$after4 = it_sbs_compute_form_850_totals($orders4);
it_sbs_assert_close(920.00, $after4['line3'], 'Test 4: fully refunded order nets to zero, only the other order counts (1000-80)');

// --- Test 5: without any refunds and without any out-of-state orders, new == legacy exactly ---
$orders5 = [order('ID', 1000.00, 60.00, 20.00)];
$before5 = it_sbs_compute_form_850_totals_legacy($orders5);
$after5  = it_sbs_compute_form_850_totals($orders5);
it_sbs_assert_close($before5['line1'], $after5['line1'], 'Test 5: line1 identical to legacy in the ID-only, no-refund baseline case');
it_sbs_assert_close($before5['line2'], $after5['line2'], 'Test 5: line2 identical to legacy in the ID-only, no-refund baseline case');
it_sbs_assert_close($before5['line3'], $after5['line3'], 'Test 5: line3 identical to legacy in the ID-only, no-refund baseline case');

echo "\n$passes passed, $failures failed\n";
exit($failures > 0 ? 1 : 0);
