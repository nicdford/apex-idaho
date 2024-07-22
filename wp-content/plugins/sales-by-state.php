<?php
/*
Plugin Name: Sales by State
Description: Displays sales by state for a specific year.
Version: 1.1.6
Author: Nic D. Ford
Author URI: https://nicdford.com
 */

// Prevent direct access to the file
if (!defined('ABSPATH')) {
  exit;
}

// 1. Create "Sales by State" submenu page
add_action('admin_menu', 'it_wp_dashboard_woocommerce_subpage', 9999);
function it_wp_dashboard_woocommerce_subpage()
{
  add_submenu_page('woocommerce', 'Sales by State', 'Sales by State', 'manage_woocommerce', 'bb_sales_by_state', 'it_yearly_sales_by_state', 9999);
}

// 2. Calculate sales for all states
function it_yearly_sales_by_state()
{
  $sales_by_state = array();
  $status_counts = array();

  echo "<h3>Sales by State For Year 2024</h3>";

  $start_date = '2024-01-01';
  $end_date = '2024-12-31';

  $args = array(
    'limit' => -1,
    'return' => 'ids',
    'date_created' => $start_date . '...' . $end_date,
  );
  $orders = wc_get_orders($args);

  foreach ($orders as $order_id) {
    $status = $order->get_status();

    // Update status counts
    if (isset($status_counts[$status])) {
      $status_counts[$status]++;
    } else {
      $status_counts[$status] = 1;
    }
  }

  // Filter to keep only orders with a status of completed
  $valid_orders = array_filter($orders, function ($order_id) {
    $order = wc_get_order($order_id);
    return $order->has_status('completed');
  });

  // Display the total number of sales
  $total_orders = count($valid_orders);
  echo "<h4>Total Number of Sales (Completed): {$total_orders} (308)</h4>";

  $total_sales_amount = 0;

  foreach ($valid_orders as $order_id) {
    $order = wc_get_order($order_id);
    $state = $order->get_billing_state();
    $total = $order->get_total();
    $status = $order->get_status();

    // Update status counts
    if (isset($status_counts[$status])) {
      $status_counts[$status]++;
    } else {
      $status_counts[$status] = 1;
    }

    // Optional: Keep or remove debug output
    // echo "<pre style='font-size: 16px'>";
    // print_r($order);
    // echo "</pre>";

    $total_sales_amount += $total;

    if (isset($sales_by_state[$state])) {
      $sales_by_state[$state] += $total;
    } else {
      $sales_by_state[$state] = $total;
    }
  }

  echo '<pre style="font-size: 16px">';
  echo 'Total Sales Amount: ' . $total_sales_amount . ' (51,891.15)';
  echo '</pre>';

  echo '<table style="font-size: 16px; border-collapse: collapse;">';
  echo '<tr><th style="border: 1px solid black; padding: 5px;">State</th><th style="border: 1px solid black; padding: 5px;">Sales Amount</th></tr>';
  ksort($sales_by_state);
  foreach ($sales_by_state as $state => $amount) {
    echo '<tr><td style="border: 1px solid black; padding: 5px;">' . $state . '</td><td style="border: 1px solid black; padding: 5px;">' . $amount . '</td></tr>';
  }
  echo '</table>';

  echo '<h3>Order Status Counts</h3>';
  echo '<pre style="font-size: 16px">';
  ksort($status_counts);
  foreach ($status_counts as $status => $count) {
    echo $status . ': ' . $count . "\n";
  }
  echo '</pre>';
}
