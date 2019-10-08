
<?php
// Do not allow directly accessing this file.
if (! defined('ABSPATH')) {
    exit('Direct script access denied.');
}

function theme_enqueue_styles()
{
    wp_enqueue_style('child-style', get_stylesheet_directory_uri() . '/style.css', array( 'avada-stylesheet' ));
}
add_action('wp_enqueue_scripts', 'theme_enqueue_styles');

function avada_lang_setup()
{
    $lang = get_stylesheet_directory() . '/languages';
    load_child_theme_textdomain('Avada', $lang);
}
add_action('after_setup_theme', 'avada_lang_setup');

//****************************************************************
  // Title: Inventory Management skripsie events code
  // Author: Theunis Duminy
  // SU Number: 18969399
  // 2019 - B.Eng (Industrial) - Final Year
//****************************************************************


// 1 & 2)
// Display raw material and product information
// Product Information
include 'functions-tables.php';

// 3)
// Insert stock take result of raw material and Products
add_action('save_post', 'stockTakeResult');
function stockTakeResult($post_id)
{
    // Safety stock
    $lowStockParameters = array(
    'milk'            =>'1000',
    'sugar'           =>'1000',
    'butter'          =>'1000',
    'cocoa-beans'     =>'1000',
    'coffee-beans'    =>'1000',
    'chocolate-bar'   =>'20',
    'chocolate-muffin'=>'20',
    'coffee'          =>'10',
  );

    if (get_post_type($post_id) == 'stock_take') {
        $rawMaterialStockTakeRows = get_field('raw_material_stock_take');
        $productStockTakeRows = get_field('product_stock_take');

        if ($rawMaterialStockTakeRows) {
            $counter = 0;
            foreach ($rawMaterialStockTakeRows as $row) {
                $rawMaterialType = $row['raw_material_stock'];
                $RMquantity = $row['quantity'];
                $rawMaterialProjectedQuantity = get_field('projected_quantity', $rawMaterialType);
                $rmShrinkageLoss = $rawMaterialProjectedQuantity - $RMquantity;

                $counter++;

                update_sub_field(array('raw_material_stock_take', $counter, 'shrinkage_loss'), $rmShrinkageLoss);
                update_post_meta($rawMaterialType, 'projected_quantity', $RMquantity);

                $slug = $lowStockParameters[get_post_field('post_name', $rawMaterialType)];
                if ($RMquantity < $slug) {
                    $to = 'tduminy6@gmail.com';
                    $subject = 'LOW STOCK: ' . get_the_title($rawMaterialType);
                    $body =
          'THIS IS AN AUTOMATED LOW STOCK NOTIFICATION. PLEASE ENSURE TO PLACE AN ORDER FOR THE FOLLOWING RAW MATERIAL: ' .
          get_the_title($rawMaterialType) . '<br/>' . 'CURRENT STOCK AVAILABLE: ' . $RMquantity . '<br/>' . 'Thank you!';
                    $headers = array('Content-Type: text/html; charset=UTF-8');
                    wp_mail($to, $subject, $body, $headers);
                }
            }
        }

        if ($productStockTakeRows) {
            $otherCounter = 0;
            foreach ($productStockTakeRows as $row) {
                $productType = $row['product_stock'];
                $productQuantity = $row['quantity'];
                $productProjectedQuantity = get_field('_stock', $productType);
                $productShrinkageLoss =  $productProjectedQuantity - $productQuantity;

                $otherCounter++;

                update_sub_field(array('product_stock_take', $otherCounter, 'shrinkage_loss'), $productShrinkageLoss);
                update_post_meta($productType->ID, '_stock', $productQuantity);

                $slug = $lowStockParameters[get_post_field('post_name', $productType)];
                if ($productQuantity < $slug) {
                    $to = 'tduminy6@gmail.com';
                    $subject = 'LOW STOCK: ' . get_the_title($productType);
                    $body =
          'THIS IS AN AUTOMATED LOW STOCK NOTIFICATION. PLEASE ENSURE TO PLACE A MANUFACTURING ORDER FOR THE FOLLOWING PRODUCT: ' .
          get_the_title($productType) . '<br/>' . 'CURRENT STOCK AVAILABLE: ' . $productQuantity . '<br/>' . 'Thank you!';
                    $headers = array('Content-Type: text/html; charset=UTF-8');
                    wp_mail($to, $subject, $body, $headers);
                } // end slug if
            }
        }
    } //end if
} // end function


// 4) Customer orders
// Done in front-end


// 5)
// Update Products when receiving a completed manufacturing order
add_action('wp_insert_post', 'updateProductwithTransaction');
function updateProductwithTransaction($post_id)
{
    if (get_post_type($post_id) == 'product_transactions') {
        $product_type = get_field('product', $post_id);
        $quantityCreated = get_field('quantity', $post_id);
        $old_stock_value = get_field('_stock', $product_type->ID);
        $manufactOrderStatus = get_field('manufacturing_order', $post_id);
        $stock_value =  $old_stock_value + $quantityCreated;

        update_field('status', 'Completed', $manufactOrderStatus->ID);
        update_post_meta($product_type->ID, '_stock', $stock_value);
    }

    $productRawMaterialNeeded = get_field('raw_material_requirements', $product_type->ID);

    if ($productRawMaterialNeeded) {
        foreach ($productRawMaterialNeeded as $row) {
            $rawMaterialType = $row['raw_material'];
            $quantity = $row['quantity'] * $quantityCreated;
            $old_stock_value = get_field('projected_quantity', $rawMaterialType);

            $newRawMaterialStock = $old_stock_value - $quantity;
            update_field('projected_quantity', $newRawMaterialStock, $rawMaterialType->ID);
        }
    }
}


// 6)
// Submit return product
add_action('save_post', 'returnProduct');
function returnProduct($post_id)
{
    if (get_post_type($post_id) == 'return_product') {
        $product_type = get_field('product', $post_id);
        $quantityReturned = get_field('quantity', $post_id);
        $old_stock_value = get_field('loss', $product_type->ID);

        $stock_value = $old_stock_value + $quantityReturned;
        update_field('loss', $stock_value, $product_type->ID);
    }
}


// 7)
// Update raw material when received
add_action('save_post', 'updateRawMaterialwithTransaction');
function updateRawMaterialwithTransaction($post_id)
{
    if (get_post_type($post_id) == 'rm_transactions') {

    // get related raw material post object
        $raw_material = get_field('raw_material', $post_id);

        $new_stock_value = get_field('quantity', $post_id);

        // Old Raw Material projected quantity
        $old_stock_value = get_field('projected_quantity', $raw_material->ID);

        // Add old and new for the actual stock value
        $stock_value = $old_stock_value + $new_stock_value;

        // Update the Raw Material field with the new value
        update_field('projected_quantity', $stock_value, $raw_material->ID);
    }
}


// 8)
// Raw material returned
add_action('save_post', 'returnRawMaterial');
function returnRawMaterial($post_id)
{
    if (get_post_type($post_id) == 'return_raw_material') {
        $raw_material = get_field('raw_material', $post_id);
        $quantityReturned = get_field('quantity', $post_id);
        $old_stock_value = get_field('loss', $raw_material->ID);

        $stock_value = $old_stock_value + $quantityReturned;
        update_field('loss', $stock_value, $raw_material->ID);
    }
}


// 9
// Request a manufacturing order upon receiving low stock notification
add_action('save_post', 'mailManufacturingOrder');
function mailManufacturingOrder($post_id)
{
    if (get_post_type($post_id) == 'manufacturing_order') {
        $product = get_field('product', $post_id);
        $quantity = get_field('quantity', $post_id);
        $dateRequired = get_field('date_required', $post_id);

        $to = 'tduminy6@gmail.com';
        $subject = 'NEW ORDER: ' . get_the_title($post_id);
        $body = 'Hi, we need a new batch of the following product: ' . $product->post_title . '<br/>' . 'Quantity requested: ' . $quantity . '<br/>' . 'Required date to be completed: ' . $dateRequired . '<br/>' . 'Thank you!';
        $headers = array('Content-Type: text/html; charset=UTF-8');
        wp_mail($to, $subject, $body, $headers);
    }
}


// webeng_notify_slack($message, 'testing');


// JS shit
function my_admin_enqueue_scripts()
{
    wp_enqueue_script('disableJS', get_theme_file_uri('/js/disable.js'), array('jquery'), '1.0', true);
}
add_action('wp_enqueue_scripts', 'my_admin_enqueue_scripts');

// Change Login Title
// Change title of login box
// CSS for login screen
add_action('login_enqueue_scripts', 'ourLoginCSS');
function ourLoginCSS() {
	wp_enqueue_style('university_main_styles', get_stylesheet_uri(), NULL, microtime());
}

// Change title of login box
add_filter( 'login_headertitle', 'ourLoginTitle' );
function ourLoginTitle() {
    return 'Inventory Management';
}

// Redirect user
add_filter('woocommerce_login_redirect', 'wc_custom_user_redirect', 10, 2);
function wc_custom_user_redirect($redirect, $user)
{
    // Get the first of all the roles assigned to the user
    $role = $user->roles[0];
    $dashboard = admin_url();
    $myaccount = get_permalink(wc_get_page_id('myaccount'));
    if ($role == 'administrator') {
        //Redirect administrators to the dashboard
        $redirect = $dashboard;
    } elseif ($role == 'employee') {
        //Redirect shop managers to the dashboard
        $redirect = $dashboard;
    } elseif ($role == 'editor') {
        //Redirect editors to the dashboard
        $redirect = $dashboard;
    } elseif ($role == 'author') {
        //Redirect authors to the dashboard
        $redirect = $dashboard;
    } elseif ($role == 'customer' || $role == 'subscriber') {
        //Redirect customers and subscribers to the "My Account" page
        $redirect = $myaccount;
    } else {
        //Redirect any other role to the previous visited page or, if not available, to the home
        $redirect = wp_get_referer() ? wp_get_referer() : home_url();
    }
    return $redirect;
}
