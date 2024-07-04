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

    if (isset($_SESSION['cart'][$item_key])) {
        // Product already in cart, increase quantity
        $_SESSION['cart'][$item_key]['quantity'] += $quantity;
        $message = "Product already in cart. Quantity increased.";
    } else {
        // New product, add to cart
        $_SESSION['cart'][$item_key] = array(
            'product_id' => $product_id,
            'quantity' => $quantity,
            'size' => $size,
            'color' => $color
        );
        $message = "Product added to cart successfully!";
    }

    // Calculate total cart count
    $cart_count = 0;
    foreach ($_SESSION['cart'] as $item) {
        $cart_count += $item['quantity'];
    }

    echo json_encode([
        'success' => true, 
        'message' => $message, 
        'cart_count' => $cart_count
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}