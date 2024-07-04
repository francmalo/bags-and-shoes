<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove_item'])) {
    $item_key = $_POST['remove_item'];
    if (isset($_SESSION['cart'][$item_key])) {
        unset($_SESSION['cart'][$item_key]);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Item not found in cart']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}