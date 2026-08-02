<?php
/*
Plugin Name: Sales by State
Description: Displays sales by state, drills down into per-state orders, and outputs an Idaho Form 850 (Sales & Use Tax) worksheet.
Version: 1.7.0
Author: Nic D. Ford
Author URI: https://nicdford.com
 */

if (!defined('ABSPATH')) {
  exit;
}

require_once __DIR__ . '/sales-by-state-calc.php';

add_action('admin_menu', 'it_wp_dashboard_woocommerce_subpage', 9999);
function it_wp_dashboard_woocommerce_subpage()
{
  add_submenu_page('woocommerce', 'Sales by State', 'Sales by State', 'manage_woocommerce', 'bb_sales_by_state', 'it_yearly_sales_by_state', 9999);
}

function it_sbs_get_period_range($period, $year)
{
  switch ($period) {
    case 'q1': return [$year . '-01-01', $year . '-03-31'];
    case 'q2': return [$year . '-04-01', $year . '-06-30'];
    case 'q3': return [$year . '-07-01', $year . '-09-30'];
    case 'q4': return [$year . '-10-01', $year . '-12-31'];
    case 'year':
    default:   return [$year . '-01-01', $year . '-12-31'];
  }
}

function it_yearly_sales_by_state()
{
  $selected_period  = isset($_GET['period'])  ? sanitize_text_field($_GET['period'])  : 'year';
  $selected_product = isset($_GET['product']) ? intval($_GET['product'])              : 0;
  $selected_state   = isset($_GET['state'])   ? sanitize_text_field($_GET['state'])   : '';
  $selected_year    = isset($_GET['year'])    ? intval($_GET['year'])                 : intval(date('Y'));

  list($start_date, $end_date) = it_sbs_get_period_range($selected_period, $selected_year);

  echo '<div class="wrap">';
  echo '<h1>Sales by State</h1>';

  // --- Filter form ---
  echo '<form method="GET" style="margin:12px 0;">';
  echo '<input type="hidden" name="page" value="bb_sales_by_state" />';

  echo '<label>Year: <select name="year">';
  $this_year = intval(date('Y'));
  for ($y = $this_year; $y >= $this_year - 6; $y--) {
    echo '<option value="' . $y . '" ' . selected($selected_year, $y, false) . '>' . $y . '</option>';
  }
  echo '</select></label> ';

  echo '<label>Period: <select name="period">';
  foreach (['q1' => 'Q1', 'q2' => 'Q2', 'q3' => 'Q3', 'q4' => 'Q4', 'year' => 'Year'] as $k => $v) {
    echo '<option value="' . $k . '" ' . selected($selected_period, $k, false) . '>' . $v . '</option>';
  }
  echo '</select></label> ';

  echo '<label>Product: <select name="product"><option value="0">All Products</option>';
  $products = wc_get_products(['limit' => -1]);
  foreach ($products as $product) {
    echo '<option value="' . $product->get_id() . '" ' . selected($selected_product, $product->get_id(), false) . '>' . esc_html($product->get_name()) . '</option>';
  }
  echo '</select></label> ';

  // Build state list from WooCommerce (US states) plus "All"
  echo '<label>State: <select name="state">';
  echo '<option value="">— Summary (all states) —</option>';
  $countries = new WC_Countries();
  $us_states = $countries->get_states('US');
  if (is_array($us_states)) {
    foreach ($us_states as $code => $name) {
      echo '<option value="' . esc_attr($code) . '" ' . selected($selected_state, $code, false) . '>' . esc_html($name) . ' (' . esc_html($code) . ')</option>';
    }
  }
  echo '</select></label> ';

  echo '<input type="submit" class="button button-primary" value="Filter" />';
  echo '</form>';

  echo '<p><em>Date Range: ' . esc_html($start_date) . ' to ' . esc_html($end_date) . '</em></p>';

  // --- Fetch orders once ---
  $orders = wc_get_orders([
    'limit'        => -1,
    'date_created' => $start_date . '...' . $end_date,
    'status'       => ['wc-completed'],
  ]);

  if ($selected_state === '') {
    it_sbs_render_summary($orders, $selected_product);
    it_sbs_render_form_850($start_date, $end_date);
  } else {
    it_sbs_render_state_detail($orders, $selected_product, $selected_state, $start_date, $end_date);
  }

  echo '</div>';
}

function it_sbs_render_summary($orders, $selected_product)
{
  $sales_by_state = [];
  foreach ($orders as $order) {
    if (!($order instanceof WC_Order)) continue;
    $state = $order->get_billing_state();
    foreach ($order->get_items() as $item) {
      $product_id = $item->get_product_id();
      if ($selected_product > 0 && $product_id != $selected_product) continue;
      if (!isset($sales_by_state[$state])) {
        $sales_by_state[$state] = ['total_orders' => 0, 'total_amount' => 0.0, 'product_quantity' => 0];
      }
      $sales_by_state[$state]['total_orders']     += 1;
      $sales_by_state[$state]['total_amount']     += (float) $item->get_total();
      $sales_by_state[$state]['product_quantity'] += (int) $item->get_quantity();
    }
  }

  if (empty($sales_by_state)) {
    echo '<p>No sales data available for the selected period and product.</p>';
    return;
  }

  $tot_o = 0; $tot_a = 0.0; $tot_q = 0;
  foreach ($sales_by_state as $d) { $tot_o += $d['total_orders']; $tot_a += $d['total_amount']; $tot_q += $d['product_quantity']; }

  echo '<table class="widefat striped" style="max-width:720px">';
  echo '<thead><tr><th>State</th><th>Line Items</th><th>Total Amount</th><th>Quantity</th></tr></thead>';
  echo '<tbody>';
  echo '<tr style="font-weight:bold;background:#f0f0f0"><td>Total</td><td>' . $tot_o . '</td><td>' . wc_price($tot_a) . '</td><td>' . $tot_q . '</td></tr>';
  foreach ($sales_by_state as $state => $d) {
    $label = $state ? esc_html($state) : 'Unknown';
    $link  = esc_url(add_query_arg('state', $state));
    echo '<tr><td><a href="' . $link . '">' . $label . '</a></td><td>' . $d['total_orders'] . '</td><td>' . wc_price($d['total_amount']) . '</td><td>' . $d['product_quantity'] . '</td></tr>';
  }
  echo '</tbody></table>';
  echo '<p style="margin-top:10px"><em>Tip: click a state to see all orders for that state. The Idaho Form 850 worksheet below always covers all states for the selected period.</em></p>';
}

function it_sbs_render_state_detail($orders, $selected_product, $state, $start_date, $end_date)
{
  $rows = [];
  $totals = ['orders' => 0, 'subtotal' => 0.0, 'shipping' => 0.0, 'tax' => 0.0, 'grand' => 0.0];
  $seen_orders = [];

  foreach ($orders as $order) {
    if (!($order instanceof WC_Order)) continue;
    if ($order->get_billing_state() !== $state) continue;

    // Sum the matching items in this order
    $order_item_total = 0.0;
    $order_item_qty   = 0;
    $matched_items    = [];
    foreach ($order->get_items() as $item) {
      $product_id = $item->get_product_id();
      if ($selected_product > 0 && $product_id != $selected_product) continue;
      $order_item_total += (float) $item->get_total();
      $order_item_qty   += (int)   $item->get_quantity();
      $matched_items[]  = $item->get_name() . ' × ' . $item->get_quantity();
    }
    if ($selected_product > 0 && $order_item_qty === 0) continue;

    $rows[] = [
      'id'       => $order->get_id(),
      'number'   => $order->get_order_number(),
      'date'     => $order->get_date_created() ? $order->get_date_created()->date('Y-m-d') : '',
      'name'     => trim($order->get_billing_first_name() . ' ' . $order->get_billing_last_name()),
      'city'     => $order->get_billing_city(),
      'items'    => $matched_items,
      'qty'      => $order_item_qty,
      'subtotal' => $order_item_total,
      'shipping' => (float) $order->get_shipping_total(),
      'tax'      => (float) $order->get_total_tax(),
      'grand'    => (float) $order->get_total(),
    ];

    if (!isset($seen_orders[$order->get_id()])) {
      $seen_orders[$order->get_id()] = true;
      $totals['orders']   += 1;
      $totals['shipping'] += (float) $order->get_shipping_total();
      $totals['tax']      += (float) $order->get_total_tax();
      $totals['grand']    += (float) $order->get_total();
    }
    $totals['subtotal'] += $order_item_total;
  }

  $countries = new WC_Countries();
  $state_name = $countries->get_states('US')[$state] ?? $state;

  echo '<h2>Orders in ' . esc_html($state_name) . ' (' . esc_html($state) . ')</h2>';
  echo '<p><a class="button" href="' . esc_url(remove_query_arg('state')) . '">← Back to state summary</a> ';
  echo '<button class="button" onclick="window.print()">Print / Save PDF</button></p>';

  if (empty($rows)) {
    echo '<p>No orders found for this state in the selected period.</p>';
    return;
  }

  echo '<table class="widefat striped">';
  echo '<thead><tr><th>Order #</th><th>Date</th><th>Customer</th><th>City</th><th>Items</th><th>Qty</th><th>Item Subtotal</th><th>Shipping</th><th>Tax</th><th>Order Total</th></tr></thead>';
  echo '<tbody>';
  foreach ($rows as $r) {
    $edit = admin_url('post.php?post=' . $r['id'] . '&action=edit');
    echo '<tr>';
    echo '<td><a href="' . esc_url($edit) . '">#' . esc_html($r['number']) . '</a></td>';
    echo '<td>' . esc_html($r['date']) . '</td>';
    echo '<td>' . esc_html($r['name']) . '</td>';
    echo '<td>' . esc_html($r['city']) . '</td>';
    echo '<td>' . esc_html(implode('; ', $r['items'])) . '</td>';
    echo '<td>' . $r['qty'] . '</td>';
    echo '<td>' . wc_price($r['subtotal']) . '</td>';
    echo '<td>' . wc_price($r['shipping']) . '</td>';
    echo '<td>' . wc_price($r['tax']) . '</td>';
    echo '<td>' . wc_price($r['grand']) . '</td>';
    echo '</tr>';
  }
  echo '<tr style="font-weight:bold;background:#f0f0f0">';
  echo '<td colspan="6">Total (' . $totals['orders'] . ' orders)</td>';
  echo '<td>' . wc_price($totals['subtotal']) . '</td>';
  echo '<td>' . wc_price($totals['shipping']) . '</td>';
  echo '<td>' . wc_price($totals['tax']) . '</td>';
  echo '<td>' . wc_price($totals['grand']) . '</td>';
  echo '</tr>';
  echo '</tbody></table>';
}

function it_sbs_render_form_850($start_date, $end_date)
{
  // Form 850 needs gross sales across ALL states (not just Idaho), net of refunds,
  // so it pulls its own dataset rather than reusing the Idaho-filtered order list.
  $period_orders = wc_get_orders([
    'limit'        => -1,
    'date_created' => $start_date . '...' . $end_date,
    'status'       => ['wc-completed', 'wc-refunded'],
  ]);

  // Net out refunds (partial or full) so a refunded sale never counts as taxable.
  $orders_data = [];
  foreach ($period_orders as $order) {
    if (!($order instanceof WC_Order)) continue;
    $orders_data[] = [
      'state'             => $order->get_billing_state(),
      'total'             => (float) $order->get_total(),
      'tax'               => (float) $order->get_total_tax(),
      'shipping'          => (float) $order->get_shipping_total(),
      'refunded'          => (float) $order->get_total_refunded(),
      'tax_refunded'      => (float) $order->get_total_tax_refunded(),
      'shipping_refunded' => (float) $order->get_total_shipping_refunded(),
    ];
  }

  // Line 1 = gross sales everywhere, net of refunds.
  // Line 2 = nontaxable-to-Idaho portion: out-of-state sales plus shipping/tax
  // already collected on Idaho orders. Line 3 (net taxable) is unaffected by
  // including out-of-state sales, since they cancel out between lines 1 and 2 —
  // this just makes the worksheet's own figures accurate on their own terms.
  $totals = it_sbs_compute_form_850_totals($orders_data);
  $line1_default = number_format($totals['line1'], 2, '.', '');
  $line2_default = number_format($totals['line2'], 2, '.', '');

  ?>
  <div id="sbs-form-850" style="margin-top:32px;max-width:820px;border:1px solid #ccd0d4;background:#fff;padding:24px 28px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    <h2 style="margin:0 0 4px;border-bottom:2px solid #111;padding-bottom:8px;">Idaho Form 850 — Sales &amp; Use Tax Calculator</h2>
    <p style="color:#555;margin:8px 0 20px;">Period: <?php echo esc_html($start_date . ' to ' . $end_date); ?>. Tax rate: 6%. Editable fields update automatically.</p>

    <?php
    $rows = [
      ['1',  'Total Sales',                        'line1', 'input', $line1_default, false],
      ['2',  'Less nontaxable sales',              'line2', 'input', $line2_default, false],
      ['3',  'Net taxable sales (line 1 − line 2)','line3', 'calc',  '',            true ],
      ['4',  'Items subject to use tax',           'line4', 'input', '',            false],
      ['5',  'Total taxable (lines 3 + 4)',        'line5', 'calc',  '',            true ],
      ['6',  'Tax (6% of line 5)',                 'line6', 'calc',  '',            true ],
      ['7',  'Adjustments (attach explanation)',   'line7', 'input', '',            false],
      ['8',  'Tax due (lines 6 + 7)',              'line8', 'calc',  '',            true ],
      ['9',  'Penalty (after due date)',           'line9', 'input', '',            false],
      ['10', 'Interest (after due date)',          'line10','input', '',            false],
      ['11', 'Total due',                          'line11','calc',  '',            true ],
    ];
    foreach ($rows as $r) {
      list($n, $label, $id, $kind, $default, $bold) = $r;
      $style_row = 'display:flex;align-items:center;justify-content:space-between;padding:12px 0;border-bottom:1px solid #eee;';
      if ($n === '11') $style_row .= 'font-weight:bold;';
      echo '<div style="' . $style_row . '">';
      echo '<div style="flex:1;">' . esc_html($n . '. ' . $label) . '</div>';
      if ($kind === 'input') {
        echo '<input type="text" inputmode="decimal" id="' . $id . '" data-money="1" value="' . esc_attr($default) . '" style="width:180px;padding:8px 10px;text-align:right;border:1px solid #bbb;border-radius:6px;" />';
      } else {
        $bg = ($n === '11') ? '#fff8c5' : '#f4f4f4';
        echo '<div id="' . $id . '" style="width:180px;padding:8px 10px;text-align:right;background:' . $bg . ';border:1px solid #ddd;border-radius:6px;">$0.00</div>';
      }
      echo '</div>';
    }
    ?>

    <div style="margin-top:16px;display:flex;gap:8px;">
      <button type="button" class="button" onclick="sbsResetForm850()">Reset</button>
      <button type="button" class="button" onclick="window.print()">Print / Save PDF</button>
    </div>
  </div>

  <style>
    @media print {
      #adminmenumain, #wpadminbar, #wpfooter, .notice, form, table.widefat, h1.wp-heading-inline, .wrap > h1, .wrap > h2, .wrap > p:not(#sbs-form-850 p) { display: none !important; }
      #wpcontent, #wpbody, #wpbody-content { margin:0 !important; padding:0 !important; }
      #sbs-form-850 { border: none; }
    }
  </style>

  <script>
    (function () {
      const ids = ['line1','line2','line3','line4','line5','line6','line7','line8','line9','line10','line11'];
      const fmtMoney = n => '$' + (isFinite(n) ? n : 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
      const fmtInput = n => (isFinite(n) && n !== 0 ? n : 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
      const num = v => {
        if (v === null || v === undefined) return 0;
        const n = parseFloat(String(v).replace(/[$,\s]/g, ''));
        return isFinite(n) ? n : 0;
      };
      const get = id => {
        const el = document.getElementById(id);
        if (!el) return 0;
        return el.tagName === 'INPUT' ? num(el.value) : num(el.textContent);
      };
      const set = (id, val) => {
        const el = document.getElementById(id);
        if (el && el.tagName !== 'INPUT') el.textContent = fmtMoney(val);
      };
      function recalc() {
        const l1 = get('line1'), l2 = get('line2'), l4 = get('line4'), l7 = get('line7'), l9 = get('line9'), l10 = get('line10');
        const l3 = Math.max(0, l1 - l2);
        const l5 = l3 + l4;
        const l6 = l5 * 0.06;
        const l8 = l6 + l7;
        const l11 = l8 + l9 + l10;
        set('line3', l3); set('line5', l5); set('line6', l6); set('line8', l8); set('line11', l11);
      }
      window.sbsResetForm850 = function () {
        ['line4','line7','line9','line10'].forEach(id => { const el = document.getElementById(id); if (el) el.value = ''; });
        recalc();
      };
      document.querySelectorAll('#sbs-form-850 input[data-money]').forEach(el => {
        // Format any pre-filled default value on load
        if (el.value !== '') el.value = fmtInput(num(el.value));
        el.addEventListener('input', recalc);
        el.addEventListener('focus', () => {
          const n = num(el.value);
          el.value = n === 0 ? '' : String(n);
          el.select();
        });
        el.addEventListener('blur', () => {
          const raw = el.value.trim();
          el.value = raw === '' ? '' : fmtInput(num(raw));
          recalc();
        });
      });
      recalc();
    })();
  </script>
  <?php
}
