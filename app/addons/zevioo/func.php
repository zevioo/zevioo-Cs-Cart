<?php
/*   */

use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

function fn_zevioo_place_order($order_id, $action, $order_status, $cart, $auth) { 
	if (!class_exists('ZeviooAPI\ZeviooAPI')) {
		require_once Registry::get('config.dir.root') . '/app/addons/zevioo/classes/ZeviooAPI/ZeviooAPI.php';
	}	
	$zevioo = new ZeviooAPI\ZeviooAPI('https://api.zevioo.com/main.svc'); //https://api.zevioo.com/main.svc/custpurchase

	if (empty($cart['order_id'])) {
		$order_info = fn_get_order_info($order_id);
		
		if (!empty($order_info['order_id'])) {
			
			$sale = new \ZeviooAPI\ZeviooSale(null, $zevioo);
			
			$shipping  = !empty($order_info['shipping']) ? array_shift($order_info['shipping']) : false;
			if (!empty($shipping['delivery_time'])) {
				$delivery_time = str_replace('-', ' ', $shipping['delivery_time']);
				$elems = explode(' ', $delivery_time);
				$numbers = array();
				foreach($elems as $elem) {
					if (is_numeric($elem)) {
						$numbers[] = $elem;
					}
				}
				if (!empty($numbers)) {
					$days = max($numbers);
				}
			}
			
			if (empty($days)) {
				$days = 3;
			}
			
			$sale->USR = Registry::get('addons.zevioo.username');
			$sale->PSW =  Registry::get('addons.zevioo.password');
		
			$sale->OID = $order_info['order_id'];
			$sale->EML = $order_info['email'];
			$sale->PDT = date('Y-m-d H:i:s', $order_info['timestamp']);
			$sale->DDT = date('Y-m-d H:i:s', strtotime("+$days days", $order_info['timestamp']));
			$sale->FN = $order_info['firstname'];
			$sale->LN = $order_info['lastname'];
			$sale->PC = $order_info['s_zipcode'];
			
			
			if (!empty($order_info['products'])) {
				
				$products = array();
				foreach($order_info['products'] as $p_k => $pr) {
					
					$image = '';
					foreach($order_info['product_groups'] as $group) {
						if (!empty($group['products'])) {
							foreach($group['products'] as $g_pk => $product) {
								if ($p_k == $g_pk) {
									if (!empty($product['main_pair']['detailed_id'])) {
										$image = $product['main_pair']['detailed']['https_image_path'];
									} elseif (!empty($product['main_pair']['image_id'])) {
										$image = $product['main_pair']['icon']['https_image_path'];
									}
								}
							}	
						}
					}
					
					
					$products[] = array('CD' => $pr['product_code'],
					                        'EAN' =>  !empty($pr['extra']['ean']) ? $pr['extra']['ean'] : 'ean',
											'IMG' => empty($image) ? 'no_image' : $image,
											'NM' => $pr['product'],
											
											'PRC' => $pr['price'],
											'QTY' => $pr['amount']
									);			
				}
				
				$sale->ITEMS = $products;
				
				list($code, $body) = $sale->save();
				
				if ($code == 200 && $body->RES == 0) {
					//Ok
					//echo 'Ok';
				} else {
					// not Ok
					//echo $code . ' ' . $body->MSG;
				}
				
			

			}
		}
	} 
	
}

function fn_zevioo_change_order_status($status_to, $status_from, $order_info, $force_notification, $order_statuses, $place_order) {
	
	if (!class_exists('ZeviooAPI\ZeviooAPI')) {
		require_once Registry::get('config.dir.root') . '/app/addons/zevioo/classes/ZeviooAPI/ZeviooAPI.php';
	}	
	$zevioo = new ZeviooAPI\ZeviooAPI('https://api.zevioo.com/main.svc'); //https://api.zevioo.com/main.svc/cnlpurchase
	
	if ($order_statuses[$status_to]['params']['inventory'] == 'I' || $status_from != $status_to) {
		
			$salecancel = new \ZeviooAPI\ZeviooSaleCancel(null, $zevioo);
			
			$salecancel->USR = Registry::get('addons.zevioo.username');
			$salecancel->PSW =  Registry::get('addons.zevioo.password');
			
			$salecancel->OID =  $order_info['order_id'];
			$salecancel->CDT =  date('Y-m-d H:i:s', TIME); ;
			
		
			
			list($code, $body) = $salecancel->save();
				
			if ($code == 200 && $body->RES == 0) {
				//Ok
				//echo 'Ok';
			} else {
				// not Ok
				//echo $code . ' ' . $body->RES . '  ' . $body->MSG;
			}
			
			//exit;
	}	
}
	
function fn_zevioo_add_product_to_cart_get_price($product_data, $cart, $auth, $update, $_id, &$data, $product_id, $amount, $price, $zero_price_action, $allow_add) {
	
	$ean = db_get_field("SELECT ean FROM ?:products WHERE product_id = ?i", $product_id);
	
	if (!empty($ean)) {
		if (empty($data['extra'])) {	
			$data['extra'] = array();
		}	
		$data['extra']['ean'] = $ean;
	}
	
}