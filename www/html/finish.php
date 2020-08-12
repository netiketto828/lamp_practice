<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';
require_once MODEL_PATH . 'cart.php';
require_once MODEL_PATH . 'order.php';

session_start();

if(is_logined() === false){
  redirect_to(LOGIN_URL);
}

$db = get_db_connect();
$user = get_login_user($db);

$carts = get_user_carts($db, $user['user_id']);

$order_datetime = date('Y-m-d h:i:s');

$db->beginTransaction();

if(purchase_carts($db, $carts) === false){
  set_error('商品が購入できませんでした。');
  redirect_to(CART_URL);
}


if(order_insert($db, $user['user_id'], $order_datetime) === false){
  set_error('商品履歴を更新できませんでした。');
  redirect_to(CART_URL);
}

$order_id = $db->lastinsertid();

foreach($carts as $cart){
  if(detail_insert($db, $cart['item_id'], $order_id, $cart['amount'], $cart['price']) === false){
    set_error('商品詳細を更新できませんでした。');
    redirect_to(CART_URL);
  }
}
if(isset($_SESSION['__error']) === false || count($_SESSION['__error']) === 0){
  $db->commit();
}else{
  $db->rollback();
}

$total_price = sum_carts($carts);

include_once '../view/finish_view.php';