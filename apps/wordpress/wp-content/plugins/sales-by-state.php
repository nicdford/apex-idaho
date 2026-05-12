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
  $selected_product = isset($_GET['product']) ? intval($_GET['product']) : 0;
  
  // Get current year
  $current_year = date('Y');

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

  // Add product dropdown
  echo ' Select Product:
        <select name="product">
          <option value="0">All Products</option>';
  
  $products = wc_get_products(array('limit' => -1));
  foreach ($products as $product) {
    echo '<option value="' . $product->get_id() . '" ' . selected($selected_product, $product->get_id(), false) . '>' . 
         esc_html($product->get_name()) . '</option>';
  }
  echo '</select>';
  
  echo '<input type="submit" value="Filter" />';
  echo '</form>';

  // Define start and end dates based on selected period
  switch ($selected_period) {
    case 'q1':
      $start_date = $current_year . '-01-01';
      $end_date = $current_year . '-03-31';
      break;
    case 'q2':
      $start_date = $current_year . '-04-01';
      $end_date = $current_year . '-06-30';
      break;
    case 'q3':
      $start_date = $current_year . '-07-01';
      $end_date = $current_year . '-09-30';
      break;
    case 'q4':
      $start_date = $current_year . '-10-01';
      $end_date = $current_year . '-12-31';
      break;
    case 'year':
    default:
      $start_date = $current_year . '-01-01';
      $end_date = $current_year . '-12-31';
      break;
  }

  // Debug information
  echo "<div style='background: #f0f0f0; padding: 10px; margin: 10px 0;'>";
  echo "<strong>Debug Information:</strong><br>";
  echo "Date Range: " . $start_date . " to " . $end_date . "<br>";
  echo "Selected Product ID: " . $selected_product . "<br>";
  
  $args = array(
    'limit' => -1,
    'date_created' => $start_date . '...' . $end_date,
    'status' => array('wc-completed'),
  );
  
  $orders = wc_get_orders($args);
  echo "Total Orders Found: " . count($orders) . "<br>";
  
  if (count($orders) > 0) {
    echo "First Order ID: " . $orders[0]->get_id() . "<br>";
    echo "First Order Date: " . $orders[0]->get_date_created() . "<br>";
  }
  echo "</div>";

  // Iterate through each order to collect state-wise sales data
  foreach ($orders as $order) {
    // Checking if the order is not an instance of OrderRefund
    if ($order instanceof WC_Order) {
      $state = $order->get_billing_state();
      
      // Check each item in the order
      foreach ($order->get_items() as $item) {
        $product_id = $item->get_product_id();
        
        // Debug product information
        if ($selected_product > 0 && $product_id == $selected_product) {
          echo "<div style='background: #e0e0e0; padding: 5px; margin: 5px 0;'>";
          echo "Found matching product in order #" . $order->get_id() . "<br>";
          echo "Product ID: " . $product_id . "<br>";
          echo "Quantity: " . $item->get_quantity() . "<br>";
          echo "State: " . $state . "<br>";
          echo "</div>";
        }
        
        // If a specific product is selected, only process that product
        if ($selected_product > 0 && $product_id != $selected_product) {
          continue;
        }

        if (!isset($sales_by_state[$state])) {
          $sales_by_state[$state] = array(
            'total_orders' => 0,
            'total_amount' => 0,
            'product_quantity' => 0
          );
        }

        $sales_by_state[$state]['total_orders'] += 1;
        $sales_by_state[$state]['total_amount'] += $item->get_total();
        $sales_by_state[$state]['product_quantity'] += $item->get_quantity();
      }
    }
  }

  // Display results
  if (!empty($sales_by_state)) {
    // Calculate totals
    $total_orders = 0;
    $total_amount = 0;
    $total_quantity = 0;
    
    foreach ($sales_by_state as $data) {
      $total_orders += $data['total_orders'];
      $total_amount += $data['total_amount'];
      $total_quantity += $data['product_quantity'];
    }

    echo '<table border="1">';
    echo '<tr><th>State</th><th>Total Orders</th><th>Total Amount</th><th>Total Quantity</th></tr>';
    
    // Add total row at the top
    echo '<tr style="background-color: #f0f0f0; font-weight: bold;">';
    echo '<td>Total</td>';
    echo '<td>' . $total_orders . '</td>';
    echo '<td>' . wc_price($total_amount) . '</td>';
    echo '<td>' . $total_quantity . '</td>';
    echo '</tr>';
    
    // Add a separator row
    echo '<tr><td colspan="4" style="padding: 0;"><hr></td></tr>';
    
    // Display state data
    foreach ($sales_by_state as $state => $data) {
      echo '<tr>';
      echo '<td>' . ($state ? $state : 'Unknown') . '</td>';
      echo '<td>' . $data['total_orders'] . '</td>';
      echo '<td>' . wc_price($data['total_amount']) . '</td>';
      echo '<td>' . $data['product_quantity'] . '</td>';
      echo '</tr>';
    }
    echo '</table>';
  } else {
    echo "<p>No sales data available for the selected period and product.</p>";
  }
}
