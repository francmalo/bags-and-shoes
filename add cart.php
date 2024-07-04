<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include('config.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    $size = $_POST['size'];
    $color = $_POST['color'];

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }

    $item_key = $product_id . '_' . $size . '_' . $color;
    $item_exists = false;

    foreach ($_SESSION['cart'] as &$item) {
        if (isset($item['item_key']) && $item['item_key'] === $item_key) {
            $item['quantity'] += $quantity;
            $item_exists = true;
            break;
        }
    }

    if ($item_exists) {
        $message = "Product already in cart. Quantity increased.";
    } else {
        $_SESSION['cart'][] = array(
            'item_key' => $item_key,
            'product_id' => $product_id,
            'quantity' => $quantity,
            'size' => $size,
            'color' => $color
        );
        $message = "Product added to cart successfully!";
    }

    $cart_count = array_sum(array_column($_SESSION['cart'], 'quantity'));

    echo json_encode(['success' => true, 'message' => $message, 'cart_count' => $cart_count]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}