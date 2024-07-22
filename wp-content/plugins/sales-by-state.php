<?php
/*
Plugin Name: Sales by State
Description: Displays sales by state for a specific year.
Version: 1.0.5
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
  $year = 2024; // Optional: make this dynamic if needed
  $sales_by_state = array();
  echo "<h3>Sales by State For Year {$year} ($)</h3>";

  $args = array(
    'billing_country' => 'US', // COUNTRY
    'limit' => -1,
    'return' => 'ids',
    'date_created' => array(
      'after' => date('Y-m-d', strtotime("first day of January $year")),
      'before' => date('Y-m-d', strtotime("last day of December $year")),
    ),
  );
  $orders = wc_get_orders($args);

  // Display the total number of sales
  $total_orders = count($orders);
  echo "<h4>Total Number of Sales: {$total_orders}</h4>";

  $all_orders_total = 0;

  foreach ($orders as $order_id) {
    $order = wc_get_order($order_id);
    $state = $order->get_billing_state();
    $total = $order->get_total();

    // Optional: Keep or remove debug output
    // echo "<pre style='font-size: 16px'>";
    // print_r($order);
    // echo "</pre>";

    $all_orders_total += $total;

    if (isset($sales_by_state[$state])) {
      $sales_by_state[$state] += $total;
    } else {
      $sales_by_state[$state] = $total;
    }
  }

  echo '<pre style="font-size: 16px">';
  echo 'Total Sales Amount: ' . $all_orders_total;
  echo '</pre>';

  echo '<pre style="font-size: 16px">';
  ksort($sales_by_state);
  print_r($sales_by_state);
  echo '</pre>';
}
