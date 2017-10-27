<?php
$md5pw=""; // set md5 password hash
    if (isset($_POST['password']) && md5($_POST['password']) == $md5pw) {
		
		$count=0;
		
      include_once("wp-config.php");
include_once("wp-includes/wp-db.php");

$sql = "select
    p.ID as order_id,
    p.post_date as date,
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
    p.ID
	order by billing_email";
	
$results = $wpdb->get_results($sql);

// print_r($results);

echo '<style>
table {
   
    border-collapse: collapse;
    width: 80%;
}

table th {
    padding-top: 12px;
    padding-bottom: 12px;
    text-align: left;
    background-color: #4CAF50;
    color: white;
}

table td {
    border: 1px solid #ddd;
    padding: 8px;
}

table tr:nth-child(even){background-color: #f2f2f2;}

table tr:hover {background-color: #ddd;}

.countbox{
	position:fixed;
	right:20px;
	top:20px;
	padding:20px;
	display:block;
	background:#333;
	color:#fff;
	width:15%;
}
</style>
<table><th>Order ID</th><th>Datum</th><th>Client e-mail</th><th>Order status</th>
';
$billingmail='';
foreach($results as $r){
	
	if($billingmail != $r->billing_email){
	echo "<tr><td>".$r->order_id."</td><td>".$r->date."</td><td>".$r->billing_email."</td><td>";
	
	if($r->post_status == 'wc-refunded'){
	echo '<span style="color:#333;font-style:italic;">'.$r->post_status.'</span></td></tr>';
	}else{
	echo '<span style="color:red;">'.$r->post_status.'</span></td></tr>';
	}
		$count++;
	}
	$billingmail=$r->billing_email;


}
echo '<table>';

echo '<div class="countbox">Total results<br /><hr>'.$count.'</div>';
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
