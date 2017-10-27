select
    p.ID as order_id,
    p.post_date,
	p.post_status,
    max( CASE WHEN pm.meta_key = '_billing_email' and p.ID = pm.post_id THEN pm.meta_value END ) as billing_email,
    max( CASE WHEN pm.meta_key = '_billing_first_name' and p.ID = pm.post_id THEN pm.meta_value END ) as _billing_first_name,
    max( CASE WHEN pm.meta_key = '_billing_last_name' and p.ID = pm.post_id THEN pm.meta_value END ) as _billing_last_name,
    ( select group_concat( order_item_name separator '|' ) from bkwp_woocommerce_order_items where order_id = p.ID ) as order_items
from
    bkwp_posts p 
    join bkwp_postmeta pm on p.ID = pm.post_id
    join bkwp_woocommerce_order_items oi on p.ID = oi.order_id
where
    post_type = 'shop_order' and
       post_status = 'wc-refunded' OR post_status = 'wc-cancelled'
group by
    p.ID
