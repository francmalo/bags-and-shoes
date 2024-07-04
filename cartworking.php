<?php
session_start();
include('config.php');

function getProductDetails($conn, $productId) {
    $sql = "SELECT name, price FROM Products WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

$cartItems = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <!-- Add your CSS links here -->
</head>

<body>
    <h1>Your Shopping Cart</h1>
    <?php if (empty($cartItems)): ?>
    <p>Your cart is empty.</p>
    <?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Size</th>
                <th>Color</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                $total = 0;
                foreach ($cartItems as $item): 
                    $product = getProductDetails($conn, $item['product_id']);
                    $itemTotal = $product['price'] * $item['quantity'];
                    $total += $itemTotal;
                ?>
            <tr>
                <td><?php echo htmlspecialchars($product['name']); ?></td>
                <td><?php echo htmlspecialchars($item['size']); ?></td>
                <td><?php echo htmlspecialchars($item['color']); ?></td>
                <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                <td>$<?php echo number_format($product['price'], 2); ?></td>
                <td>$<?php echo number_format($itemTotal, 2); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5">Total:</td>
                <td>$<?php echo number_format($total, 2); ?></td>
            </tr>
        </tfoot>
    </table>
    <?php endif; ?>
</body>

</html>