<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('config.php');

// Start the session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Initialize the cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

// Handle adding to cart
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $size = $_POST['size'];
    $color = $_POST['color'];

    // Add to cart (you might want to add more checks here)
    $_SESSION['cart'][] = array(
        'product_id' => $product_id,
        'quantity' => $quantity,
        'size' => $size,
        'color' => $color
    );

    // Redirect to prevent form resubmission
    header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $product_id);
    exit;
}

// Check if product ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid product ID');
}

$productId = $_GET['id'];

// Function to fetch product details
function getProductDetails($conn, $productId) {
    $sql = "SELECT p.*, b.name AS brand_name, c.name AS category_name, t.name AS type_name, g.name AS gender_name, 
                   MAX(pp.photo_url) AS photo_url, GROUP_CONCAT(DISTINCT s.size_value) AS sizes, 
                   GROUP_CONCAT(DISTINCT cl.color_name) AS colors,
                   SUM(pa.stock_quantity) AS total_stock
            FROM Products p
            LEFT JOIN Brands b ON p.brand_id = b.brand_id
            LEFT JOIN Categories c ON p.category_id = c.category_id
            LEFT JOIN Types t ON p.type_id = t.type_id
            LEFT JOIN Genders g ON p.gender_id = g.gender_id
            LEFT JOIN Product_Photos pp ON p.product_id = pp.product_id AND pp.is_primary = 1
            LEFT JOIN Product_Attributes pa ON p.product_id = pa.product_id
            LEFT JOIN Sizes s ON pa.size_id = s.size_id
            LEFT JOIN Colors cl ON pa.color_id = cl.color_id
            WHERE p.product_id = ?
            GROUP BY p.product_id";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Function to fetch product photos
function getProductPhotos($conn, $productId) {
    $sql = "SELECT photo_url FROM Product_Photos WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Fetch product details
$product = getProductDetails($conn, $productId);
$photos = getProductPhotos($conn, $productId);

if (!$product) {
    die('Product not found');
}
?>

<!doctype html>
<html>

<head>
    <!-- Meta Data -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Comercio - Fashion Shop Ecommerce HTML Template</title>

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




</head>



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

                        <p><i class="far fa-envelope"></i><a href="mailto:comercio@info.com">comercio@info.com</a></p>
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
                                            <h5 class="pro-title"><a href="product.html">High-Low Dresses</a></h5>
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
                                            <h5 class="pro-title"><a href="product.html">Bodycon Dresses</a></h5>
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
                                            <h5 class="pro-title"><a href="product.html">Laptop carry bag</a></h5>
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
                        <li class="top-cart"><a href="javascript:void(0)"><i class="flaticon-bag"></i><span>2</span></a>

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
<!--=        Mobile Header     =-->
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
<!--=        Shop area          =-->
<!--=========================-->





<body id="home-version-1" class="home-version-1" data-style="default">

    <div class="site-content">


        <section class="shop-area style-two">
            <div class="container">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="row">
                            <div class="col-lg-6 col-xl-6">
                                <!-- Product View Slider -->
                                <div class="quickview-slider">
                                    <div class="slider-for">
                                        <?php foreach ($photos as $photo): ?>
                                        <div class="">
                                            <img src="<?php echo htmlspecialchars($photo['photo_url']); ?>"
                                                alt="Product Image">
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="slider-nav">
                                        <?php foreach ($photos as $photo): ?>
                                        <div class="">
                                            <img src="<?php echo htmlspecialchars($photo['photo_url']); ?>"
                                                alt="Product Thumbnail">
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-xl-6">
                                <div class="product-details">
                                    <h5 class="pro-title"><a
                                            href="#"><?php echo htmlspecialchars($product['name']); ?></a></h5>
                                    <span class="price">Price :
                                        $<?php echo number_format($product['price'], 2); ?></span>
                                    <div class="size-variation">
                                        <span>size :</span>
                                        <select name="size-value">
                                            <?php
                                            $sizes = explode(',', $product['sizes']);
                                            foreach ($sizes as $size) {
                                                echo "<option value=\"" . htmlspecialchars(trim($size)) . "\">" . htmlspecialchars(trim($size)) . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="color-variation">
                                        <span>color :</span>
                                        <select name="color">
                                            <?php
                                                $colors = explode(',', $product['colors']);
                                                foreach ($colors as $color) {
                                                    echo "<option value=\"" . htmlspecialchars(trim($color)) . "\">" . htmlspecialchars(trim($color)) . "</option>";
                                                }
                                                ?>
                                        </select>
                                    </div>
                                    <div class="add-tocart-wrap">
                                        <!--PRODUCT INCREASE BUTTON START-->
                                        <div class="cart-plus-minus-button">
                                            <input type="text" value="0" name="qtybutton" class="cart-plus-minus">
                                        </div>
                                        <a href="#" class="add-to-cart"><i class="flaticon-shopping-purse-icon"></i>Add
                                            to Cart</a>
                                        <!-- <a href="#"><i class="flaticon-valentines-heart"></i></a> -->
                                    </div>
                                    <!-- Add to cart and other product details -->
                                    <p><?php echo htmlspecialchars($product['description']); ?></p>
                                    <!-- Additional product information -->

                                    <div class="product-social">
                                        <span>Share :</span>
                                        <ul>
                                            <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                                            <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                                            <li><a href="#"><i class="fab fa-instagram"></i></a></li>
                                            <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
                                        </ul>
                                    </div>

                                </div>
                            </div>



                            <div class="col-xl-12">
                                <div class="product-des-tab">
                                    <ul class="nav nav-tabs " role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home"
                                                role="tab" aria-controls="home" aria-selected="true">DESCRIPTION</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile"
                                                role="tab" aria-controls="profile" aria-selected="false">ADDITIONAL
                                                INFORMATION</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact"
                                                role="tab" aria-controls="contact" aria-selected="false">REVIEWS (1)</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content" id="myTabContent">
                                        <div class="tab-pane fade show active" id="home" role="tabpanel"
                                            aria-labelledby="home-tab">
                                            <div class="prod-bottom-tab-sin description">
                                                <h5>Description</h5>
                                                <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                                                <!-- You can add more dynamic content here based on your product data -->
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="profile" role="tabpanel"
                                            aria-labelledby="profile-tab">
                                            <div class="prod-bottom-tab-sin">
                                                <h5>Additional information</h5>
                                                <div class="info-wrap">
                                                    <div class="sin-aditional-info">
                                                        <div class="first">Brand</div>
                                                        <div class="secound">
                                                            <?php echo htmlspecialchars($product['brand_name']); ?>
                                                        </div>
                                                    </div>
                                                    <div class="sin-aditional-info">
                                                        <div class="first">Category</div>
                                                        <div class="secound">
                                                            <?php echo htmlspecialchars($product['category_name']); ?>
                                                        </div>
                                                    </div>
                                                    <div class="sin-aditional-info">
                                                        <div class="first">Colors</div>
                                                        <div class="secound">
                                                            <?php echo htmlspecialchars($product['colors']); ?></div>
                                                    </div>
                                                    <div class="sin-aditional-info">
                                                        <div class="first">Gender</div>
                                                        <div class="secound">
                                                            <?php echo htmlspecialchars($product['gender_name']); ?>
                                                        </div>
                                                    </div>
                                                    <!-- Add more additional information as needed -->
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="contact" role="tabpanel"
                                            aria-labelledby="contact-tab">
                                            <div class="prod-bottom-tab-sin">
                                                <h5>Review (1)</h5>
                                                <div class="product-review">
                                                    <!-- You can add dynamic reviews here if you have a reviews system -->
                                                    <div class="reviwer">
                                                        <img src="media/images/reviewer.png" alt="">
                                                        <div class="review-details">
                                                            <span>Posted by Tonoy - Published on March 22, 2024</span>
                                                            <div class="rating">
                                                                <ul>
                                                                    <li><a href="#"><i class="far fa-star"></i></a></li>
                                                                    <li><a href="#"><i class="far fa-star"></i></a></li>
                                                                    <li><a href="#"><i class="far fa-star"></i></a></li>
                                                                    <li><a href="#"><i class="far fa-star"></i></a></li>
                                                                    <li><a href="#"><i class="far fa-star"></i></a></li>
                                                                </ul>
                                                            </div>
                                                            <p>But I must explain to you how all this mistaken idea of
                                                                denouncipleasure and praisi pain was born and I will
                                                                give you a complete.</p>
                                                        </div>
                                                    </div>
                                                    <div class="add-your-review">
                                                        <h6>ADD A REVIEW</h6>
                                                        <p>YOUR RATING* </p>
                                                        <div class="rating">
                                                            <ul>
                                                                <li><a href="#"><i class="far fa-star"></i></a></li>
                                                                <li><a href="#"><i class="far fa-star"></i></a></li>
                                                                <li><a href="#"><i class="far fa-star"></i></a></li>
                                                                <li><a href="#"><i class="far fa-star"></i></a></li>
                                                                <li><a href="#"><i class="far fa-star"></i></a></li>
                                                            </ul>
                                                        </div>
                                                        <div class="raing-form">
                                                            <form action="#">
                                                                <input type="text" placeholder="Name">
                                                                <input type="email" placeholder="Email">
                                                                <textarea name="rating-form"
                                                                    placeholder="Your review"></textarea>
                                                                <input type="submit" value="Submit">
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!--=========================-->
        <!--=   Footer area      =-->
        <!--=========================-->

        <footer class="footer-widget-area style-two">
            <div class="container-fluid custom-container">
                <div class="row">
                    <div class="col-12 col-sm-12 col-md-6 col-lg-3">
                        <div class="footer-widget style-two">
                            <div class="logo">
                                <a href="#">
                                    <img src="media/images/logo.png" alt="">
                                </a>
                            </div>
                            <p>Autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat vel
                                illum dolore eu olestie.</p>
                            <div class="time-table">
                                <p>Opening time</p>
                                <span>Monday - Friday ( 8.00 to 18.00 )</span>
                                <span>Saturday ( 8.00 to 18.00 )</span>
                            </div>
                        </div>
                    </div>
                    <!-- /.col-xl-3 -->
                    <div class="col-12 col-sm-6 col-md-6 col-lg-2">
                        <div class="footer-widget style-two">
                            <h3>Quick shop</h3>
                            <div class="footer-menu">
                                <ul>
                                    <li><a href="#">Home</a></li>
                                    <li><a href="#">Clothing</a></li>
                                    <li><a href="#">Jewellery</a></li>
                                    <li><a href="#">Shoes</a></li>
                                    <li><a href="#">Accessories</a></li>
                                    <li><a href="#">Collections</a></li>
                                    <li><a href="#">Sale</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!-- /.col-xl-3 -->
                    <div class="col-12 col-sm-6 col-md-6 col-lg-2">
                        <div class="footer-widget style-two">
                            <h3>CUSTOMER SERVICES</h3>
                            <div class="footer-menu">
                                <ul>
                                    <li><a href="#">FAQ's</a></li>
                                    <li><a href="#">Contact Us</a></li>
                                    <li><a href="#">Customer Service</a></li>
                                    <li><a href="#">Orders and Returns</a></li>
                                    <li><a href="#">Consultant</a></li>
                                    <li><a href="#">Collections</a></li>
                                    <li><a href="#">Support Center</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!-- /.col-xl-3 -->
                    <div class="col-12 col-sm-6 col-md-6 col-lg-2">
                        <div class="footer-widget style-two">
                            <h3>EXPERIENCE</h3>
                            <div class="footer-menu">
                                <ul>
                                    <li><a href="#">Help</a></li>
                                    <li><a href="#">Order Status</a></li>
                                    <li><a href="#">Returns & Exchanges</a></li>
                                    <li><a href="#">International</a></li>
                                    <li><a href="#">Gift Cards</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!-- /.col-xl-3 -->
                    <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                        <div class="footer-widget style-two">
                            <h3>instagram</h3>
                            <div class="footer-instagram">
                                <ul>
                                    <li><a href="#"><img src="media/images/instagram/6.jpg" alt=""></a></li>
                                    <li><a href="#"><img src="media/images/instagram/7.jpg" alt=""></a></li>
                                    <li><a href="#"><img src="media/images/instagram/8.jpg" alt=""></a></li>
                                    <li><a href="#"><img src="media/images/instagram/9.jpg" alt=""></a></li>
                                    <li><a href="#"><img src="media/images/instagram/10.jpg" alt=""></a></li>
                                    <li><a href="#"><img src="media/images/instagram/11.jpg" alt=""></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!-- /.col-xl-3 -->
                </div>
                <div class="footer-bottom">
                    <div class="row">
                        <div class="col-md-12 col-lg-6 col-xl-6 order-2 order-lg-1">
                            <p>Copyright © <span>2024</span> ThemeIM Solution • Designed by <a href="#">ThemeIM</a></p>
                        </div>
                        <!-- /.col-xl-6 -->
                        <div class="col-md-12 col-lg-6 col-xl-6 order-1 order-lg-2">
                            <div class="footer-payment-icon">
                                <ul>
                                    <li><a href="#"><img src="media/images/p1.png" alt=""></a></li>
                                    <li><a href="#"><img src="media/images/p2.png" alt=""></a></li>
                                    <li><a href="#"><img src="media/images/p3.png" alt=""></a></li>
                                    <li><a href="#"><img src="media/images/p4.png" alt=""></a></li>
                                </ul>
                            </div>
                        </div>
                        <!-- /.col-xl-6 -->
                    </div>
                    <!-- /.row -->
                </div>
            </div>
            <!-- container-fluid -->
        </footer>
        <!-- footer-widget-area -->

        <!-- Back to top
	============================================= -->

        <div class="backtotop">
            <i class="fa fa-angle-up backtotop_btn"></i>
        </div>








    </div>
    <!-- /#site -->

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
    <!--Google map api -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBsBrMPsyNtpwKXPPpG54XwJXnyobfMAIc"></script>


    <!-- Site Scripts -->
    <script src="assets/js/app.js"></script>

</body>

</html>