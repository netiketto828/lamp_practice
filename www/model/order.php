<?php 
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'db.php';

function order_insert($db, $user_id, $order_datetime){
    $sql = "
        INSERT INTO
            `order`(
                user_id,
                order_datetime
            )
        VALUES(?, ?)
    ";

    return execute_query($db, $sql, array($user_id, $order_datetime));
}

function order_select($db,$user_id){
    $sql = '
        SELECT 
            `order`.order_id, order_datetime, SUM(detail.price * detail.amount)
        FROM 
            `order`
        JOIN
            detail
        ON
            `order`.order_id = detail.order_id
        WHERE 
            `order`.user_id = ?
        GROUP BY
            order_id
    ';

    return fetch_all_query($db, $sql, array($user_id));
}

function detail_insert($db, $item_id, $order_id, $amount, $price){
    $sql = '
        INSERT INTO
            detail(
                item_id,
                order_id,
                amount,
                price
            )
            VALUES(?, ?, ?, ?)
    ';

    return execute_query($db, $sql, array($item_id, $order_id, $amount, $price));
}

function detail_select($db, $order_id){
    $sql = '
    SELECT
        name, detail.price, amount, (detail.price * amount)
    FROM
        detail
    JOIN
        items
    ON 
        detail.item_id = items.item_id
    WHERE
        detail.order_id = ?
    ';

    return fetch_all_query($db, $sql, array($order_id));
}