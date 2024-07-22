<?php
/*
Plugin Name: Sales by State
Description: Displays sales by state for a specific year.
Version: 1.5.0
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
  $selected_period = isset($_GET['period']) ? sanitize_text_field($_GET['period']) : 'year';

  echo "<h3>Sales by State</h3>";

  echo '<form method="GET">';
  echo '<input type="hidden" name="page" value="bb_sales_by_state" />';
  echo 'Select Period:
        <select name="period">
          <option value="q1" ' . selected($selected_period, 'q1', false) . '>Q1</option>
          <option value="q2" ' . selected($selected_period, 'q2', false) . '>Q2</option>
          <option value="q3" ' . selected($selected_period, 'q3', false) . '>Q3</option>
          <option value="q4" ' . selected($selected_period, 'q4', false) . '>Q4</option>
          <option value="year" ' . selected($selected_period, 'year', false) . '>Year</option>
        </select>';
  echo '<input type="submit" value="Filter" />';
  echo '</form>';

  // Define start and end dates based on selected period
  switch ($selected_period) {
    case 'q1':
      $start_date = '2024-01-01';
      $end_date = '2024-03-31';
      break;
    case 'q2':
      $start_date = '2024-04-01';
      $end_date = '2024-06-30';
      break;
    case 'q3':
      $start_date = '2024-07-01';
      $end_date = '2024-09-30';
      break;
    case 'q4':
      $start_date = '2024-10-01';
      $end_date = '2024-12-31';
      break;
    case 'year':
    default:
      $start_date = '2024-01-01';
      $end_date = '2024-12-31';
      break;
  }

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
