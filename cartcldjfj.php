<div class="cart-drop">
    <?php
    $subtotal = 0;
    if (!empty($_SESSION['cart'])):
        foreach ($_SESSION['cart'] as $item_key => $item):
            $product = getProductDetails($conn, $item['product_id']);
            $item_total = $product['price'] * $item['quantity'];
            $subtotal += $item_total;
    ?>
    <div class="single-cart">
        <div class="cart-img">
            <img alt="<?php echo htmlspecialchars($product['name']); ?>"
                src="<?php echo htmlspecialchars($product['photo_url']); ?>" width="50" height="50">
            <!-- Adjusted size here -->
        </div>
        <div class="cart-title">
            <p><a
                    href="product.php?id=<?php echo $product['product_id']; ?>"><?php echo htmlspecialchars($product['name']); ?></a>
            </p>
        </div>
        <div class="cart-price">
            <p><?php echo $item['quantity']; ?> x $<?php echo number_format($product['price'], 2); ?></p>
        </div>
        <a href="#" class="remove-item" data-item-key="<?php echo $item_key; ?>"><i class="fa fa-times"></i></a>
    </div>
    <?php
        endforeach;
    else:
    ?>
    <p>Your cart is empty.</p>
    <?php
    endif;
    ?>

    <div class="cart-bottom">
        <div class="cart-sub-total">
            <p>Sub-Total <span>$<?php echo number_format($subtotal, 2); ?></span></p>
        </div>
        <?php
        $eco_tax = 7.00;
        $vat = $subtotal * 0.20;
        $total = $subtotal + $eco_tax + $vat;
        ?>
        <div class="cart-sub-total">
            <p>Eco Tax <span>$<?php echo number_format($eco_tax, 2); ?></span></p>
        </div>
        <div class="cart-sub-total">
            <p>VAT (20%) <span>$<?php echo number_format($vat, 2); ?></span></p>
        </div>
        <div class="cart-sub-total">
            <p>Total <span>$<?php echo number_format($total, 2); ?></span></p>
        </div>
        <div class="cart-checkout">
            <a href="cart.php"><i class="fa fa-shopping-cart"></i>View Cart</a>
        </div>
        <div class="cart-share">
            <a href="checkout.php"><i class="fa fa-share"></i>Checkout</a>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.remove-item').on('click', function(e) {
        e.preventDefault();
        var itemKey = $(this).data('item-key');
        $.post('remove_from_cart.php', {
            remove_item: itemKey
        }, function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Error removing item from cart');
            }
        }, 'json');
    });
});
</script>