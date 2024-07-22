<?php
/*
Plugin Name: Sales by State
Description: Displays sales by state for a specific year.
Version: 1.4.0
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

  // Check if custom dates are set via GET request
  $start_date = isset($_GET['start_date']) ? sanitize_text_field($_GET['start_date']) : '2024-01-01';
  $end_date = isset($_GET['end_date']) ? sanitize_text_field($_GET['end_date']) : '2024-12-31';

  echo "<h3>Sales by State</h3>";

  // Form to get custom start and end dates
  echo '<form method="GET">';
  echo '<input type="hidden" name="page" value="bb_sales_by_state" />';
  echo 'Start Date: <input type="text" name="start_date" value="' . esc_attr($start_date) . '" />';
  echo 'End Date: <input type="text" name="end_date" value="' . esc_attr($end_date) . '" />';
  echo '<input type="submit" value="Filter" />';
  echo '</form>';

  $args = array(
    'limit' => -1,
    'date_created' => $start_date . '...' . $end_date,
    'status' => array('wc-completed'),
  );
  $orders = wc_get_orders($args);

  echo "<h4>Total Number of Orders: " . count($orders) . "</h4>";

  // Iterate through each order to collect state-wise sales data
  foreach ($orders as $order) {
    // Checking if the order is not an instance of OrderRefund
    if ($order instanceof WC_Order) {
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
