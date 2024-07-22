<?php
/*
Plugin Name: Sales by State
Description: Displays sales by state for a specific year.
Version: 1.2.1
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

  echo "<h3>Sales by State for Year 2024</h3>";

  $start_date = '2024-01-01';
  $end_date = '2024-12-31';

  $args = array(
    'limit' => -1,
    'date_created' => $start_date . '...' . $end_date,
    'status' => array('wc-completed'),
  );
  $orders = wc_get_orders($args);

  echo "<h4>Total Number of Orders: " . count($orders) . "</h4>";

  // Iterate through each order to collect state-wise sales data
  foreach ($orders as $order) {
    $state = $order->get_billing_state();
    $total = $order->get_total();

    if (!isset($sales_by_state[$state])) {
      $sales_by_state[$state] = array(
        'total_orders' => 0,
        'total_amount' => 0,
      );
    }

    $sales_by_state[$state]['total_orders'] += 1;
    $sales_by_state[$state]['total_amount'] += $total;
  }

  // Display results
  if (!empty($sales_by_state)) {
    echo '<table border="1">';
    echo '<tr><th>State</th><th>Total Orders</th><th>Total Amount</th></tr>';
    foreach ($sales_by_state as $state => $data) {
      echo '<tr>';
      echo '<td>' . ($state ? $state : 'Unknown') . '</td>';
      echo '<td>' . $data['total_orders'] . '</td>';
      echo '<td>' . wc_price($data['total_amount']) . '</td>';
      echo '</tr>';
    }
    echo '</table>';
  } else {
    echo "<p>No sales data available for the selected period.</p>";
  }
}
