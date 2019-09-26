<?php

// Products table
add_shortcode('products_table', function ($atts) {
  ob_start();

    $query = new WP_Query(array(
    'post_type' 		    => 'product',
    'posts_per_page' 	  => -1,
    'order' 			      => 'ASC',
    'orderby' 			    => 'name',
  ));

  if ($query->have_posts()) { ?>

    <div style="overflow-x: auto;">
    <table width="100%" class="table display">
    <thead>
    <tr>
  	<th style="text-align: left;">SKU</th>
  	<th style="text-align: left;">Name</th>
  	<th style="text-align: left;">Price (R)</th>
    	<th style="text-align: left;">Stock</th>
  	<th style="text-align: left;">Saftey Stock</th>
    </tr>
    </thead>
    <tbody>
  <?php while ($query->have_posts()) : $query->the_post(); ?>

    <tr>
  	<td><?php echo get_post_meta(get_the_ID(), '_sku', true); ?></td>
  	<td><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></td>
  	<td><?php echo get_post_meta(get_the_ID(), '_price', true); ?></td>
    <td><?php echo get_post_meta(get_the_ID(), '_stock', true); ?></td>
  	<td><?php echo get_post_meta(get_the_ID(), '_low_stock_amount', true); ?></td>
    </tr>

  <?php endwhile;
  wp_reset_postdata(); ?>
     </tbody>
    </table>
    </div>
  <?php
  return ob_get_clean();
  }
  }
);

// Raw Material tables
add_shortcode('rm_table', function ($atts) {
  ob_start();

    $query = new WP_Query(array(
    'post_type' 		    => 'raw_material',
    'posts_per_page' 	  => -1,
    'order' 			      => 'ASC',
    'orderby' 			    => 'name',
  ));

  if ($query->have_posts()) { ?>

    <div style="overflow-x: auto;">
    <table width="100%" class="table display">
    <thead>
    <tr>
  	<th style="text-align: left;">ID</th>
  	<th style="text-align: left;">Name</th>
  	<th style="text-align: left;">Actual Quantity</th>
    	<th style="text-align: left;">Projected Quantity</th>
  	<th style="text-align: left;">Measurement unit</th>
    </tr>
    </thead>
    <tbody>
  <?php while ($query->have_posts()) : $query->the_post(); ?>

    <tr>
  	<td><?php echo get_the_ID(); ?></td>
  	<td><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></td>
  	<td><?php the_field('actual_quantity'); ?></td>
    	<td><?php the_field('projected_quantity'); ?></td>
  	<td><?php the_field('measurement'); ?></td>
    </tr>

  <?php endwhile;
  wp_reset_postdata(); ?>
     </tbody>
    </table>
    </div>
  <?php
  return ob_get_clean();
  }
  }
);

// Manufacturing orders table
add_shortcode('manufacturing_table', function ($atts) {
  ob_start();

    $query = new WP_Query(array(
    'post_type' 		    => 'manufacturing_order',
    'posts_per_page' 	  => -1,
    'order' 			      => 'ASC',
    'orderby' 			    => 'name',
  ));

  if ($query->have_posts()) { ?>

    <div style="overflow-x: auto;">
    <table width="100%" class="table display">
    <thead>
    <tr>
  	<th style="text-align: left;">Order ID</th>
  	<th style="text-align: left;">Title</th>
  	<th style="text-align: left;">Product</th>
    <th style="text-align: left;">Quantity</th>
    <th style="text-align: left;">Date created</th>
  	<th style="text-align: left;">Date required</th>
    <th style="text-align: left;">Status</th>
    </tr>
    </thead>
    <tbody>
  <?php while ($query->have_posts()) : $query->the_post(); ?>

    <tr>
  	<td><?php echo get_the_ID(); ?></td>
  	<td><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></td>
  	<td><?php
    $the_product = get_field('product', get_the_ID());
    echo $the_product->post_title; ?></td>
    <td><?php the_field('quantity'); ?></td>
    <td><?php echo get_the_date( 'd/m/Y' ); ?></td>
  	<td><?php the_field('date_required'); ?></td>
    <td><?php the_field('status'); ?></td>
    </tr>

  <?php endwhile;
  wp_reset_postdata(); ?>
     </tbody>
    </table>
    </div>
  <?php
  return ob_get_clean();
  }
  }
);

// New products table
add_shortcode('new_products_table', function ($atts) {
  ob_start();

    $query = new WP_Query(array(
    'post_type' 		    => 'product_transactions',
    'posts_per_page' 	  => -1,
    'order' 			      => 'ASC',
    'orderby' 			    => 'name',
  ));

  if ($query->have_posts()) { ?>

    <div style="overflow-x: auto;">
    <table width="100%" class="table display">
    <thead>
    <tr>
  	<th style="text-align: left;">Order ID</th>
  	<th style="text-align: left;">Title</th>
  	<th style="text-align: left;">Product</th>
    <th style="text-align: left;">Quantity</th>
    <th style="text-align: left;">Date Received</th>
    <th style="text-align: left;">Manufacturing Order</th>
    </tr>
    </thead>
    <tbody>
  <?php while ($query->have_posts()) : $query->the_post(); ?>

    <tr>
    <td><?php echo get_the_ID(); ?></td>
  	<td><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></td>
  	<td><?php
    $the_product = get_field('product', get_the_ID());
    echo $the_product->post_title; ?></td>
    <td><?php the_field('quantity'); ?></td>
    <td><?php the_field('date'); ?></td>
    <td><?php
    $the_order = get_field('manufacturing_order', get_the_ID());
    echo $the_order->post_title;; ?></td>
    </tr>

  <?php endwhile;
  wp_reset_postdata(); ?>
     </tbody>
    </table>
    </div>
  <?php
  return ob_get_clean();
  }
  }
);

// New raw materials table
add_shortcode('new_rm_table', function ($atts) {
  ob_start();

    $query = new WP_Query(array(
    'post_type' 		    => 'rm_transactions',
    'posts_per_page' 	  => -1,
    'order' 			      => 'ASC',
    'orderby' 			    => 'name',
  ));

  if ($query->have_posts()) { ?>

    <div style="overflow-x: auto;">
    <table width="100%" class="table display">
    <thead>
    <tr>
  	<th style="text-align: left;">Order ID</th>
  	<th style="text-align: left;">Title</th>
  	<th style="text-align: left;">Raw Material</th>
    <th style="text-align: left;">Quantity</th>
    <th style="text-align: left;">Date Received</th>
    <th style="text-align: left;">Supplier</th>
    </tr>
    </thead>
    <tbody>
  <?php while ($query->have_posts()) : $query->the_post(); ?>

    <tr>
    <td><?php echo get_the_ID(); ?></td>
  	<td><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></td>
  	<td><?php
    $the_product = get_field('raw_material', get_the_ID());
    echo $the_product->post_title; ?></td>
    <td><?php the_field('quantity'); ?></td>
    <td><?php the_field('date'); ?></td>
    <td><?php the_field('supplier'); ?></td>
    </tr>

  <?php endwhile;
  wp_reset_postdata(); ?>
     </tbody>
    </table>
    </div>
  <?php
  return ob_get_clean();
  }
  }
);

// Returned raw material
add_shortcode('rm_return_table', function ($atts) {
  ob_start();

    $query = new WP_Query(array(
    'post_type' 		    => 'return_raw_material',
    'posts_per_page' 	  => -1,
    'order' 			      => 'ASC',
    'orderby' 			    => 'name',
  ));

  if ($query->have_posts()) { ?>

    <div style="overflow-x: auto;">
    <table width="100%" class="table display">
    <thead>
    <tr>
  	<th style="text-align: left;">Return ID</th>
  	<th style="text-align: left;">Title</th>
  	<th style="text-align: left;">Raw Material</th>
    <th style="text-align: left;">Quantity</th>
    <th style="text-align: left;">Date returned</th>
    <th style="text-align: left;">Supplier</th>
    </tr>
    </thead>
    <tbody>
  <?php while ($query->have_posts()) : $query->the_post(); ?>

    <tr>
  	<td><?php echo get_the_ID(); ?></td>
  	<td><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></td>
  	<td><?php
    $the_product = get_field('raw_material', get_the_ID());
    echo $the_product->post_title; ?></td>
    <td><?php the_field('quantity'); ?></td>
  	<td><?php the_field('date'); ?></td>
    <td><?php the_field('supplier'); ?></td>
    </tr>

  <?php endwhile;
  wp_reset_postdata(); ?>
     </tbody>
    </table>
    </div>
  <?php
  return ob_get_clean();
  }
  }
);

// Returned products
add_shortcode('products_return_table', function ($atts) {
  ob_start();

    $query = new WP_Query(array(
    'post_type' 		    => 'return_product',
    'posts_per_page' 	  => -1,
    'order' 			      => 'ASC',
    'orderby' 			    => 'name',
  ));

  if ($query->have_posts()) { ?>

    <div style="overflow-x: auto;">
    <table width="100%" class="table display">
    <thead>
    <tr>
  	<th style="text-align: left;">Return ID</th>
  	<th style="text-align: left;">Title</th>
  	<th style="text-align: left;">Product</th>
    <th style="text-align: left;">Quantity</th>
    <th style="text-align: left;">Date returned</th>
    <th style="text-align: left;">Customer</th>
    </tr>
    </thead>
    <tbody>
  <?php while ($query->have_posts()) : $query->the_post(); ?>

    <tr>
  	<td><?php echo get_the_ID(); ?></td>
  	<td><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></td>
  	<td><?php
    $the_product = get_field('product', get_the_ID());
    echo $the_product->post_title; ?></td>
    <td><?php the_field('quantity'); ?></td>
  	<td><?php the_field('date'); ?></td>
    <td><?php the_field('customer'); ?></td>
    </tr>

  <?php endwhile;
  wp_reset_postdata(); ?>
     </tbody>
    </table>
    </div>
  <?php
  return ob_get_clean();
  }
  }
);

// Raw Material losses table
add_shortcode('rm_losses_table', function ($atts) {

    $query = new WP_Query(array(
    'post_type' 		    => 'raw_material',
    'posts_per_page' 	  => -1,
    'order' 			      => 'ASC',
    'orderby' 			    => 'name',
  ));

  if ($query->have_posts()) {
    $raw_materials = [];

  $counter = 0;
  while ($query->have_posts()) : $query->the_post();

    $raw_materials[get_the_ID()] = [];
    $raw_material_losses = [];
    $stocktakes = get_stocktakes();

    foreach ($stocktakes as $key => $stocktake) {
      $rawMaterialStockTakeRows = get_field('raw_material_stock_take', $stocktake->ID);
      if ($rawMaterialStockTakeRows) {
          foreach ($rawMaterialStockTakeRows as $raw_material) {
              if (get_the_ID() == $raw_material['raw_material_stock']){
                $raw_material_losses[$stocktake->ID] = $raw_material['shrinkage_loss'];
              }
          }
      }
    }
    $raw_materials[get_the_ID()] = $raw_material_losses;
    $raw_material_losses = null;
    $counter++;
    endwhile;
  wp_reset_postdata();
  ?>
  <table class="display nowrap" style="width:100%">
  <thead>
    <tr>
      <th style="text-align: left;">Product</th>
      <?php foreach ($stocktakes as $key => $stocktake) {
        echo '<th style="text-align: left;">'.$stocktake->post_title.'</th>';
      } ?>
      <th style="text-align: left;">TOTAL</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($raw_materials as $product_key => $raw_material) {
      ?>
      <tr>
        <td><?php echo get_the_title($product_key); ?></td>
        <?php
        foreach ($raw_material as $stocktake_key => $stocktake) {
          ?><td class="product"><?php echo $stocktake ?></td><?php

        }
        ?>
        <td class="total-product"></td>
      </tr>
      <?php
    } ?>
   </tbody>
  </table>
  <?php
  return ob_get_clean();
  }
  }
);


function get_stocktakes(){
  $query = new WP_Query(array(
  'post_type' 		    => 'stock_take',
  'posts_per_page' 	  => -1,
  'order' 			      => 'ASC',
  'orderby' 			    => 'name',
));

return $query->posts;
}


// JQuery data tables
add_shortcode('datatables', function ($atts) {

    wp_register_script("wm-datatables", "https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js", array('jquery'), "1.10.19", true);
    wp_register_style("wm-datatables", "https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css");

    ob_start();

    ?>
    <script>
    jQuery(document).ready(function() {

    jQuery('table.display').DataTable( {
        "scrollX": true
    } );

    });

    </script>

    <?php

    wp_enqueue_script("wm-datatables");
    wp_enqueue_style("wm-datatables");

    return ob_get_clean();

});
