<?php
$md5pw=""; // set MD5 hash for password
    if (isset($_POST['password']) && md5($_POST['password']) == $md5pw) {
		?>
		<!DOCTYPE html>
<html>
<head>
    <title>Password protected</title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		<script src="https://cdn.rawgit.com/eligrey/Blob.js/0cef2746414269b16834878a8abc52eb9d53e6bd/Blob.js" /></script>
  <script src="https://cdn.rawgit.com/eligrey/FileSaver.js/e9d941381475b5df8b7d7691013401e171014e89/FileSaver.min.js" /></script>

  
  
  
</head>
<body>
<?php
		
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
<table><th>Order ID</th><th>Name Client</th><th>Datum</th><th>Client e-mail</th><th>Order status</th>
';
$billingmail='';
foreach($results as $r){
	
	if($billingmail != $r->billing_email){
	echo "<tr><td>".$r->order_id."</td><td>".$r->_billing_first_name." ".$r->_billing_last_name."</td><td>".$r->date."</td><td>".$r->billing_email."</td><td>";
	
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

echo '<div class="countbox">Total results<br /><hr>'.$count.'<hr><button id="ref">download refund</button><hr>
  <button id="can">download cancelled</button>';

echo '</div>';
    
	?>
	<script>
  function totxtref() {
  var blob = new Blob(["<?php foreach($results as $r){ if($r->post_status == 'wc-refunded'){ echo $r->billing_email.','; }} ?>"], {
    type: "text/plain;charset=utf-8"
  });
  saveAs(blob, "ref.txt");
}

$("#ref").on("click", function(e) {
  e.preventDefault();
  totxtref();
});

function totxtcan() {
  var blob = new Blob(["<?php foreach($results as $r){ if($r->post_status == 'wc-cancelled'){ echo $r->billing_email.','; }} ?>"], {
    type: "text/plain;charset=utf-8"
  });
  saveAs(blob, "can.txt");
}

$("#can").on("click", function(a) {
  a.preventDefault();
  totxtcan();
});

</script><?php
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


	<?php }
	?>
	
	</body>
</html>
