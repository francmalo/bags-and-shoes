<?php
session_start();
include('config.php');

// Function to get product details
function getProductDetails($conn, $product_id) {
    $sql = "SELECT p.*, b.name AS brand_name, pp.photo_url 
            FROM Products p
            LEFT JOIN Brands b ON p.brand_id = b.brand_id
            LEFT JOIN Product_Photos pp ON p.product_id = pp.product_id AND pp.is_primary = 1
            WHERE p.product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}


// Handle removing items from cart
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove_item'])) {
    $item_key = $_POST['remove_item'];
    if (isset($_SESSION['cart'][$item_key])) {
        unset($_SESSION['cart'][$item_key]);
    }
}

// Handle updating quantities
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_quantity'])) {
    $item_key = $_POST['item_key'];
    $new_quantity = (int)$_POST['quantity'];
    
    if (isset($_SESSION['cart'][$item_key])) {
        if ($new_quantity > 0) {
            $_SESSION['cart'][$item_key]['quantity'] = $new_quantity;
        } else {
            unset($_SESSION['cart'][$item_key]);
        }
    }
}


// Calculate total
$total = 0;
?>
<!doctype html>
<html>

<head>
    <!-- Meta Data -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shopping Cart - Comercio</title>

    <!-- Fav Icon -->
    <link rel="apple-touch-icon" sizes="180x180" href="assets/img/fav-icons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/img/fav-icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/img/fav-icons/favicon-16x16.png">

    <!-- Dependency Styles -->
    <link rel="stylesheet" href="dependencies/bootstrap/css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="dependencies/fontawesome/css/fontawesome-all.min.css" type="text/css">
    <link rel="stylesheet" href="dependencies/owl.carousel/css/owl.carousel.min.css" type="text/css">
    <link rel="stylesheet" href="dependencies/owl.carousel/css/owl.theme.default.min.css" type="text/css">
    <link rel="stylesheet" href="dependencies/flaticon/css/flaticon.css" type="text/css">
    <link rel="stylesheet" href="dependencies/wow/css/animate.css" type="text/css">
    <link rel="stylesheet" href="dependencies/jquery-ui/css/jquery-ui.css" type="text/css">
    <link rel="stylesheet" href="dependencies/venobox/css/venobox.css" type="text/css">
    <link rel="stylesheet" href="dependencies/slick-carousel/css/slick.css" type="text/css">

    <!-- Site Stylesheet -->
    <link rel="stylesheet" href="assets/css/app.css" type="text/css">

    <style>
    /* .cart-product-image {
        width: 80px;
        height: 80px;
        overflow: hidden;
    }

    .cart-product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    } */

    .cart-product-image {
        width: 100px;
        padding-top: 100%;
        /* Creates a 1:1 aspect ratio */
        position: relative;
        overflow: hidden;
    }

    .cart-product-image img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .remove-item {
        color: crimson;
        font-weight: bold;
        font-size: 1.2em;
        border: none;
        background: none;
        cursor: pointer;
        transition: color 0.3s, text-shadow 0.3s;
    }

    .remove-item:hover {
        color: #cc0000;
        /* Darker red on hover */
        text-shadow: 0px 0px 10px rgba(255, 0, 0, 0.7);
        /* Glowing effect */
    }


    .quantity-input {
        width: 60px;
        text-align: center;
        border: 1px solid #ccc;
        border-radius: 4px;
        padding: 5px;
    }
    </style>
</head>

<body id="home-version-1" class="home-version-1" data-style="default">

    <div class="site-content">


        <!--=========================-->
        <!--=        Header         =-->
        <!--=========================-->



        <!-- Top Bar area start
	============================================= -->


        <header id="header" class="header-area">
            <div class="top-bar">
                <div class="container-fluid custom-container">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="top-bar-left">
                                <p><i class="far fa-flag"></i><a href="contact.html">Our Location</a></p>

                                <p><i class="far fa-envelope"></i><a
                                        href="mailto:comercio@info.com">comercio@info.com</a></p>
                            </div>
                        </div>
                        <!-- Col -->
                        <div class="col-lg-6">
                            <div class="top-bar-right">
                                <div class="social">
                                    <ul>
                                        <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                                        <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                                        <li><a href="#"><i class="fab fa-pinterest-p"></i></a></li>
                                        <li><a href="#"><i class="fab fa-instagram"></i></a></li>
                                        <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
                                        <li><a href="#"><i class="fab fa-dribbble"></i></a></li>
                                    </ul>
                                </div>
                                <a href="login.html" class="my-account">My Account</a>
                            </div>
                            <!--top-bar-right end-->
                        </div>
                        <!-- Col end-->
                    </div>
                    <!--Row end-->
                </div>
                <!--container end-->
            </div>
            <!--top-bar end-->

            <!-- Main Menu
		============================================= -->


            <div class="container-fluid custom-container menu-rel-container">
                <div class="row">
                    <!-- Logo
				============================================= -->
                    <div class="col-lg-6 col-xl-3">
                        <div class="logo">
                            <a href="index.html">
                                <img src="media/images/logo.png" alt="">
                            </a>
                        </div>
                    </div>
                    <!--Col end-->

                    <!-- Main menu
				============================================= -->

                    <div class="col-lg-12 col-xl-7 order-lg-3 order-xl-2 menu-container">
                        <div class="mainmenu style-two">
                            <ul id="navigation">
                                <li class="has-child"><a href="index.html">home</a>
                                    <ul class="sub-menu">
                                        <li><a href="index.html">Home one</a></li>
                                        <li><a href="index-2.html">Home two</a></li>
                                        <li><a href="index-3.html">Home three</a></li>
                                    </ul>
                                </li>
                                <li><a href="shop.html">Collections</a></li>
                                <li class="has-child"><a href="index.html">Men</a>
                                    <div class="mega-menu">
                                        <div class="mega-catagory per-20">
                                            <h4><a class="font-red" href="shop.html">Woman Dresses</a></h4>
                                            <ul class="mega-button">
                                                <li><a href="shop.html">Woman Dresses</a></li>
                                                <li><a href="shop.html">Women & Flowers</a></li>
                                                <li><a href="shop.html">Girl Hat in Sunlights</a></li>
                                                <li><a href="shop.html">Men Watches</a></li>
                                                <li><a href="shop.html">Clothes Fashion</a></li>
                                            </ul>
                                        </div>
                                        <div class="mega-catagory per-20">
                                            <h4><a class="font-red" href="shop.html">Clothes Fashion</a></h4>
                                            <ul class="mega-button">
                                                <li><a href="shop.html">Woman Dresses</a></li>
                                                <li><a href="shop.html">Girl Hat in Sunlights</a></li>
                                                <li><a href="shop.html">Men Watches</a></li>
                                                <li><a href="shop.html">Clothes Fashion</a></li>
                                                <li><a href="shop.html">Woman Dresses</a></li>
                                            </ul>
                                        </div>
                                        <div class="mega-catagory mega-img per-30">
                                            <a href="#"><img src="media/images/banner/mmb1.jpg" alt=""></a>
                                        </div>
                                        <div class="mega-catagory mega-img per-30">
                                            <a href="#"><img src="media/images/banner/mmb2.jpg" alt=""></a>
                                        </div>
                                    </div>
                                </li>
                                <li class="has-child"><a href="index.html">Women</a>
                                    <div class="mega-menu five-col">
                                        <div class="mega-product">
                                            <h4><a class="font-red" href="shop.html">Product Category</a></h4>
                                            <ul class="mega-button">
                                                <li><a href="shop.html">Woman Dresses</a></li>
                                                <li><a href="shop.html">Women & Flowers</a></li>
                                                <li><a href="shop.html">Girl Hat in Sunlights</a></li>
                                                <li><a href="shop.html">Men Watches</a></li>
                                                <li><a href="shop.html">Clothes Fashion</a></li>
                                            </ul>
                                        </div>
                                        <div class="mega-product">
                                            <div class="sin-product">
                                                <div class="pro-img">
                                                    <img src="media/images/product/10.jpg" alt="">
                                                </div>
                                                <div class="mid-wrapper">
                                                    <h5 class="pro-title"><a href="product.html">High-Low Dresses</a>
                                                    </h5>
                                                    <span>$60.00</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mega-product">
                                            <div class="sin-product">
                                                <div class="pro-img">
                                                    <img src="media/images/product/11.jpg" alt="">
                                                </div>
                                                <div class="mid-wrapper">
                                                    <h5 class="pro-title"><a href="product.html">Empire Dresses</a></h5>
                                                    <span>$10.00</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mega-product">
                                            <div class="sin-product">
                                                <div class="pro-img">
                                                    <img src="media/images/product/12.jpg" alt="">
                                                </div>
                                                <div class="mid-wrapper">
                                                    <h5 class="pro-title"><a href="product.html">Bodycon Dresses</a>
                                                    </h5>
                                                    <span>$40.00</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mega-product">
                                            <div class="sin-product">
                                                <div class="pro-img">
                                                    <img src="media/images/product/13.jpg" alt="">
                                                </div>
                                                <div class="mid-wrapper">
                                                    <h5 class="pro-title"><a href="product.html">Laptop carry bag</a>
                                                    </h5>
                                                    <span>$70.00</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="has-child"><a href="shop.html">Shop</a>
                                    <ul class="sub-menu">
                                        <li><a href="shop.html">shop with sidebar</a></li>
                                        <li><a href="shop-four-grid.html">shop four grid</a></li>
                                        <li><a href="shop-without-sidebar.html">shop without sidebar</a></li>
                                        <li><a href="product.html">Product details</a></li>
                                        <li><a href="product-fullwidth.html">Product Fullwidth</a></li>
                                        <li><a href="cart.html">Cart</a></li>
                                        <li><a href="account.html">Order</a></li>
                                    </ul>
                                </li>
                                <li class="has-child"><a href="blog.html">Blog</a>
                                    <ul class="sub-menu">
                                        <li><a href="single.html">Single post</a></li>
                                        <li><a href="blog.html">Blog three grid</a></li>
                                        <li><a href="blog-two-grid.html">Blog two grid</a></li>
                                    </ul>
                                </li>
                                <li><a href="contact.html">CONTACT</a></li>
                            </ul>
                        </div>
                    </div>
                    <!--Menu container end-->


                    <div class="col-lg-6 col-xl-2 order-lg-2 order-xl-3">
                        <div class="header-right-menu">
                            <ul>
                                <li class="top-search style-two"><a href="javascript:void(0)"><i
                                            class="flaticon-magnifying-glass"></i></a>
                                    <input type="text" class="search-input" placeholder="Search">
                                </li>
                                <li><a href="#"><i class="flaticon-like"></i></a></li>
                                <li class="top-cart"><a href="javascript:void(0)"><i
                                            class="flaticon-bag"></i><span>2</span></a>

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
                                                    src="<?php echo htmlspecialchars($product['photo_url']); ?>"
                                                    width="50" height="50">
                                                <!-- Adjusted size here -->
                                            </div>
                                            <div class="cart-title">
                                                <p><a
                                                        href="product.php?id=<?php echo $product['product_id']; ?>"><?php echo htmlspecialchars($product['name']); ?></a>
                                                </p>
                                            </div>
                                            <div class="cart-price">
                                                <p><?php echo $item['quantity']; ?> x
                                                    $<?php echo number_format($product['price'], 2); ?></p>
                                            </div>
                                            <a href="#" class="remove-item" data-item-key="<?php echo $item_key; ?>"><i
                                                    class="fa fa-times"></i></a>
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
                                                <p>Sub-Total <span>$<?php echo number_format($subtotal, 2); ?></span>
                                                </p>
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

                                </li>
                            </ul>
                        </div>
                    </div>
                    <!--Col end-->
                </div>
                <!--Row end-->
            </div>
            <!--container end-->
        </header>
        <!--Header end-->



        <!--=========================-->
        <!--=        Mobile Header         =-->
        <!--=========================-->



        <header class="mobile-header">
            <div class="container-fluid custom-container">
                <div class="row">

                    <!-- Mobile menu Opener
					============================================= -->
                    <div class="col-4">
                        <div class="accordion-wrapper">
                            <a href="#" class="mobile-open"><i class="flaticon-menu-1"></i></a>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="logo">
                            <a href="index.html">
                                <img src="media/images/logo.png" alt="">
                            </a>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="top-cart">
                            <a href="javascript:void(0)"><i class="fa fa-shopping-cart" aria-hidden="true"></i> (2)</a>
                            <div class="cart-drop">
                                <div class="single-cart">
                                    <div class="cart-img">
                                        <img alt="" src="media/images/product/car1.jpg">
                                    </div>
                                    <div class="cart-title">
                                        <p><a href="">Aliquam Consequat</a></p>
                                    </div>
                                    <div class="cart-price">
                                        <p>1 x $500</p>
                                    </div>
                                    <a href="#"><i class="fa fa-times"></i></a>
                                </div>
                                <div class="single-cart">
                                    <div class="cart-img">
                                        <img alt="" src="media/images/product/car2.jpg">
                                    </div>
                                    <div class="cart-title">
                                        <p><a href="">Quisque In Arcuc</a></p>
                                    </div>
                                    <div class="cart-price">
                                        <p>1 x $200</p>
                                    </div>
                                    <a href="#"><i class="fa fa-times"></i></a>
                                </div>
                                <div class="cart-bottom">
                                    <div class="cart-sub-total">
                                        <p>Sub-Total <span>$700</span></p>
                                    </div>
                                    <div class="cart-sub-total">
                                        <p>Eco Tax (-2.00)<span>$7.00</span></p>
                                    </div>
                                    <div class="cart-sub-total">
                                        <p>VAT (20%) <span>$40.00</span></p>
                                    </div>
                                    <div class="cart-sub-total">
                                        <p>Total <span>$244.00</span></p>
                                    </div>
                                    <div class="cart-checkout">
                                        <a href="cart.html"><i class="fa fa-shopping-cart"></i>View Cart</a>
                                    </div>
                                    <div class="cart-share">
                                        <a href="#"><i class="fa fa-share"></i>Checkout</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.row end -->
            </div>
            <!-- /.container end -->
        </header>

        <div class="accordion-wrapper">

            <!-- Mobile Menu Navigation
				============================================= -->
            <div id="mobilemenu" class="accordion">
                <ul>
                    <li class="mob-logo"><a href="index.html">
                            <img src="media/images/logo.png" alt="">
                        </a></li>
                    <li><a href="#" class="closeme"><i class="flaticon-close"></i></a></li>
                    <li>
                        <a href="#" class="link">Home<i class="fa fa-chevron-down"></i></a>
                        <ul class="submenu">
                            <li><a href="index.html">Home one</a></li>
                            <li><a href="index-2.html">Home two</a></li>
                            <li><a href="index-3.html">Home three</a></li>

                        </ul>
                    </li>
                    <li class="out-link"><a href="shop.html">Collections</a></li>

                    <li>
                        <a href="shop.html" class="link">Men<i class="fa fa-chevron-down"></i></a>
                        <ul class="submenu">
                            <li><a href="shop.html">Shop with sidebar</a></li>
                            <li><a href="shop-four-grid.html">Shop four grid</a></li>
                            <li><a href="shop-without-sidebar.html">Shop without sidebar</a></li>
                            <li><a href="product.html">Product details</a></li>
                            <li><a href="product-fullwidth.html">Product Fullwidth</a></li>
                            <li><a href="cart.html">Cart</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="#" class="link">Women<i class="fa fa-chevron-down"></i></a>
                        <ul class="submenu">
                            <li><a href="shop.html">Woman Dresses</a></li>
                            <li><a href="shop.html">Women & Flowers</a></li>
                            <li><a href="shop.html">Girl Hat in Sunlights</a></li>
                            <li><a href="shop.html">Men Watches</a></li>
                            <li><a href="shop.html">Clothes Fashion</a></li>
                            <li><a href="shop.html">Woman Dresses</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="#" class="link">Shop<i class="fa fa-chevron-down"></i></a>
                        <ul class="submenu">
                            <li><a href="shop.html">Shop with sidebar</a></li>
                            <li><a href="shop-four-grid.html">Shop four grid</a></li>
                            <li><a href="shop-without-sidebar.html">Shop without sidebar</a></li>
                            <li><a href="product.html">Product details</a></li>
                            <li><a href="product-fullwidth.html">Product Fullwidth</a></li>
                            <li><a href="cart.html">Cart</a></li>
                            <li><a href="account.html">Order</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="#" class="link">Blog<i class="fa fa-chevron-down"></i></a>
                        <ul class="submenu">
                            <li><a href="single.html">Single post</a></li>
                            <li><a href="blog.html">Blog three grid</a></li>
                            <li><a href="blog-two-grid.html">Blog two grid</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="#" class="link">Pages<i class="fa fa-chevron-down"></i></a>
                        <ul class="submenu">
                            <li><a href="login.html">Account</a></li>
                            <li><a href="create-account.html">Signup</a></li>
                            <li><a href="account.html">Login</a></li>
                        </ul>
                    </li>


                </ul>
                <div class="mobile-login">
                    <a href="login.html">Log in</a> |
                    <a href="create-account.html">Create Account</a>
                </div>
                <form action="#" id="moble-search">
                    <input placeholder="Search...." type="text">
                    <button type="submit"><i class="fa fa-search"></i></button>
                </form>
            </div>
        </div>

        <!--=========================-->
        <!--=        Breadcrumb         =-->
        <!--=========================-->

        <section class="breadcrumb-area">
            <div class="container-fluid custom-container">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="bc-inner">
                            <p><a href="#">Home |</a> Shop</p>
                        </div>
                    </div>
                    <!-- /.col-xl-12 -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container -->
        </section>

        <!--=========================-->
        <!--=        Breadcrumb         =-->
        <!--=========================-->



        <section class="cart-area">
            <div class="container-fluid custom-container">
                <div class="row">
                    <div class="col-xl-9">
                        <div class="cart-table">
                            <?php if (!empty($_SESSION['cart'])): ?>
                            <table class="tables">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Image</th>
                                        <th>Product Name</th>
                                        <th>Size</th>
                                        <th>Color</th>
                                        <th>Quantity</th>
                                        <th>Unit Price</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($_SESSION['cart'] as $item_key => $item):
                                $product = getProductDetails($conn, $item['product_id']);
                                $itemTotal = $product['price'] * $item['quantity'];
                                $total += $itemTotal;
                            ?>
                                    <tr>

                                        <td>
                                            <button type="button" class="btn btn-link remove-item"
                                                data-item-key="<?php echo $item_key; ?>">X</button>
                                        </td>


                                        <td>
                                            <a href="#">
                                                <div class="product-image cart-product-image">
                                                    <img alt="<?php echo htmlspecialchars($product['name']); ?>"
                                                        src="<?php echo htmlspecialchars($product['photo_url']); ?>">
                                                </div>
                                            </a>
                                        </td>
                                        <td>
                                            <div class="product-title">
                                                <a href="#"><?php echo htmlspecialchars($product['name']); ?></a>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($item['size']); ?></td>
                                        <td><?php echo htmlspecialchars($item['color']); ?></td>
                                        <td>
                                            <div class="quantity">
                                                <input type="number" name="quantity"
                                                    value="<?php echo $item['quantity']; ?>" min="1"
                                                    class="quantity-input" data-item-key="<?php echo $item_key; ?>">
                                            </div>
                                        </td>

                                        <td>
                                            <div class="price-box">
                                                <span
                                                    class="price">$<?php echo number_format($product['price'], 2); ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="total-price-box">
                                                <span class="price">$<?php echo number_format($itemTotal, 2); ?></span>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <?php else: ?>
                            <p>Your cart is empty.</p>
                            <?php endif; ?>
                        </div>
                        <!-- /.cart-table -->
                        <div class="row cart-btn-section">
                            <div class="col-12 col-sm-8 col-lg-6">
                                <div class="cart-btn-left">
                                    <a class="coupon-code" href="#">Coupon Code</a>
                                    <a href="#">Apply Coupon</a>
                                </div>
                            </div>
                            <div class="col-12 col-sm-4 col-lg-6">
                                <div class="cart-btn-right">
                                    <a href="#" id="update-cart">Update Cart</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3">
                        <div class="cart-subtotal">
                            <p>SUBTOTAL</p>
                            <ul>
                                <li><span>Sub-Total:</span>$<?php echo number_format($total, 2); ?></li>
                                <li><span>Tax (16%):</span>$<?php echo number_format($total * 0.16, 2); ?></li>
                                <li><span>Shipping Cost:</span>$0.00</li>
                                <li><span>TOTAL:</span>$<?php echo number_format($total * 1.16, 2); ?></li>
                            </ul>
                            <div class="note">
                                <span>Order Note :</span>
                                <textarea></textarea>
                            </div>
                            <a href="#">Proceed To Checkout</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="backtotop">
            <i class="fa fa-angle-up backtotop_btn"></i>
        </div>
    </div>
    <!-- /#site -->
    <!-- 
    <script>
    $(document).ready(function() {
        // Confirm before removing item
        $('.remove-item-button').on('click', function() {
            var form = $(this).closest('form');
            if (confirm("Are you sure you want to delete this item from the cart?")) {
                form.submit();
            }
        });

        // Update quantity when input changes
        $('.quantity-input').on('change', function() {
            $(this).closest('form').submit();
        });

        // Update cart button click
        $('#update-cart').on('click', function(e) {
            e.preventDefault();
            $('.update-quantity-form').submit();
        });

        // Function to update cart count
        function updateCartCount() {
            $.get('get_cart_count.php', function(data) {
                $('.cart-count').text(data.count);
            });
        }

        // Call updateCartCount when page loads
        updateCartCount();
    });
    </script> -->


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    $(document).ready(function() {
        // Remove item with confirmation
        $('.remove-item').on('click', function() {
            var itemKey = $(this).data('item-key');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#069b49',
                cancelButtonColor: 'crimson',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('cart.php', {
                        remove_item: itemKey
                    }, function() {
                        location.reload();
                    });
                }
            });
        });

        // Update quantity with debounce
        let updateTimeout;
        $('.quantity-input').on('input', function() {
            clearTimeout(updateTimeout);
            var input = $(this);
            var itemKey = input.data('item-key');
            var newQuantity = input.val();

            updateTimeout = setTimeout(function() {
                $.post('cart.php', {
                    update_quantity: 1,
                    item_key: itemKey,
                    quantity: newQuantity
                }, function() {
                    location.reload();
                });
            }, 500);
        });

        // Update cart button click
        $('#update-cart').on('click', function(e) {
            e.preventDefault();
            location.reload();
        });

        // Function to update cart count
        function updateCartCount() {
            $.get('get_cart_count.php', function(data) {
                $('.cart-count').text(data.count);
            });
        }

        // Call updateCartCount when page loads
        updateCartCount();
    });

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



    <!-- Dependency Scripts -->
    <script src="dependencies/jquery/jquery.min.js"></script>
    <script src="dependencies/popper.js/popper.min.js"></script>
    <script src="dependencies/bootstrap/js/bootstrap.min.js"></script>
    <script src="dependencies/owl.carousel/js/owl.carousel.min.js"></script>
    <script src="dependencies/wow/js/wow.min.js"></script>
    <script src="dependencies/isotope-layout/js/isotope.pkgd.min.js"></script>
    <script src="dependencies/imagesloaded/js/imagesloaded.pkgd.min.js"></script>
    <script src="dependencies/jquery.countdown/js/jquery.countdown.min.js"></script>
    <script src="dependencies/gmap3/js/gmap3.min.js"></script>
    <script src="dependencies/venobox/js/venobox.min.js"></script>
    <script src="dependencies/slick-carousel/js/slick.js"></script>
    <script src="dependencies/headroom/js/headroom.js"></script>
    <script src="dependencies/jquery-ui/js/jquery-ui.min.js"></script>
    <!-- Site Scripts -->
    <script src="assets/js/app.js"></script>

</body>

</html>