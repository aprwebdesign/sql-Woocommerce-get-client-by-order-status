<?php
$md5pw=""; // set md5 password hash
    if (isset($_POST['password']) && md5($_POST['password']) == $md5pw) {
		
		$count=0;
		
      include_once("wp-config.php");
include_once("wp-includes/wp-db.php");

$sql = "select
    p.ID as order_id,
    p.post_date,
	p.post_status as post_status,
    max( CASE WHEN pm.meta_key = '_billing_email' and p.ID = pm.post_id THEN pm.meta_value END ) as billing_email,
    max( CASE WHEN pm.meta_key = '_billing_first_name' and p.ID = pm.post_id THEN pm.meta_value END ) as _billing_first_name,
    max( CASE WHEN pm.meta_key = '_billing_last_name' and p.ID = pm.post_id THEN pm.meta_value END ) as _billing_last_name,
    ( select group_concat( order_item_name separator '|' ) from wp_woocommerce_order_items where order_id = p.ID ) as order_items
from
    wp_posts p 
    join wp_postmeta pm on p.ID = pm.post_id
    join wp_woocommerce_order_items oi on p.ID = oi.order_id
where
    post_type = 'shop_order' and
       post_status = 'wc-refunded' OR post_status = 'wc-cancelled'
group by
    p.ID";
	
$results = $wpdb->get_results($sql);

// print_r($results);

foreach($results as $r){
	echo $r->order_id." - ".$r->billing_email." - ". $r->post_status;
echo "<br />";	
$count++;
}

echo $count;
    }
	else{
?>
<!DOCTYPE html>
<html>
<head>
    <title>Password protected</title>
</head>
<body>
    <div style="text-align:center;margin-top:50px;">
        You must enter the password to view this content.
        <form method="POST">
            <input type="text" name="password">
			<input type="submit" value="login"/>
        </form>
    </div>
</body>
</html>

	<?php }
