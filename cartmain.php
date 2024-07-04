<?php
session_start();
include('config.php');

// Function to get product details
function getProductDetails($conn, $product_id) {
    $sql = "SELECT p.*, b.name AS brand_name 
            FROM Products p
            LEFT JOIN Brands b ON p.brand_id = b.brand_id
            WHERE p.product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Handle removing items from cart
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove_item'])) {
    $index = $_POST['remove_item'];
    if (isset($_SESSION['cart'][$index])) {
        unset($_SESSION['cart'][$index]);
        $_SESSION['cart'] = array_values($_SESSION['cart']); // Re-index the array
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

<body id="home-version-1" class="home-version-1" data-style="default">

    <div class="site-content">





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
                                        <th>Quantity</th>
                                        <th>unit price</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <a href="#">X</a>
                                        </td>
                                        <td>
                                            <a href="#">
                                                <div class="product-image">
                                                    <img alt="Stylexpo" src="media/images/product/cp1.jpg">
                                                </div>
                                            </a>
                                        </td>
                                        <td>
                                            <div class="product-title">
                                                <a href="#">Cross Colours Camo Print Tank half mengo</a>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="quantity">
                                                <input type="number" value="0">
                                            </div>
                                        </td>
                                        <td>
                                            <ul>
                                                <li>
                                                    <div class="price-box">
                                                        <span class="price">$387 x 2</span>
                                                    </div>
                                                </li>
                                            </ul>
                                        </td>
                                        <td>
                                            <div class="total-price-box">
                                                <span class="price">$774</span>
                                            </div>
                                        </td>

                                    </tr>
                                    <!-- /.single product  -->
                                    <tr>
                                        <td>
                                            <a href="#">X</a>
                                        </td>
                                        <td>
                                            <a href="#">
                                                <div class="product-image">
                                                    <img alt="Stylexpo" src="media/images/product/cp2.jpg">
                                                </div>
                                            </a>
                                        </td>
                                        <td>
                                            <div class="product-title">
                                                <a href="#">Cross Colours Camo Print Tank half mengo</a>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="quantity">
                                                <input type="number" value="0">
                                            </div>
                                        </td>
                                        <td>
                                            <ul>
                                                <li>
                                                    <div class="price-box">
                                                        <span class="price">$387 x 1</span>
                                                    </div>
                                                </li>
                                            </ul>
                                        </td>
                                        <td>
                                            <div class="total-price-box">
                                                <span class="price">$387</span>
                                            </div>
                                        </td>

                                    </tr>
                                    <!-- /.single product  -->
                                    <tr>
                                        <td>
                                            <a href="#">X</a>
                                        </td>
                                        <td>
                                            <a href="#">
                                                <div class="product-image">
                                                    <img alt="Stylexpo" src="media/images/product/cp2.jpg">
                                                </div>
                                            </a>
                                        </td>
                                        <td>
                                            <div class="product-title">
                                                <a href="#">Cross Colours Camo Print Tank half mengo</a>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="quantity">
                                                <input type="number" value="0">
                                            </div>
                                        </td>
                                        <td>
                                            <ul>
                                                <li>
                                                    <div class="price-box">
                                                        <span class="price">$387 x 1</span>
                                                    </div>
                                                </li>
                                            </ul>
                                        </td>
                                        <td>
                                            <div class="total-price-box">
                                                <span class="price">$387</span>
                                            </div>
                                        </td>

                                    </tr>
                                    <!-- /.single product  -->
                                </tbody>
                            </table>

                        </div>
                        <!-- /.cart-table -->
                        <div class="row cart-btn-section">
                            <div class="col-12 col-sm-8 col-lg-6">
                                <div class="cart-btn-left">
                                    <a class="coupon-code" href="#">Coupon Code</a>
                                    <a href="#">Apply Coupon</a>
                                </div>
                            </div>
                            <!-- /.col-xl-6 -->
                            <div class="col-12 col-sm-4 col-lg-6">
                                <div class="cart-btn-right">
                                    <a href="#">Update Cart</a>
                                </div>
                            </div>
                            <!-- /.col-xl-6 -->
                        </div>
                        <!-- /.row -->
                    </div>
                    <!-- /.col-xl-9 -->
                    <div class="col-xl-3">
                        <div class="cart-subtotal">
                            <p>SUBTOTAL</p>
                            <ul>
                                <li><span>Sub-Total:</span>$1161.00</li>
                                <li><span>Tax (-4.00):</span>$11.00</li>
                                <li><span>Shipping Cost:</span>$00.00</li>
                                <li><span>TOTAL:</span>$1172.00</li>
                            </ul>
                            <div class="note">
                                <span>Order Note :</span>
                                <textarea></textarea>
                            </div>
                            <a href="#">Proceed To Checkout</a>
                        </div>
                        <!-- /.cart-subtotal -->


                    </div>
                    <!-- /.col-xl-3 -->
                </div>
            </div>
        </section>
        <!-- /.cart-area -->

        <!--=========================-->
        <!--=   Subscribe area      =-->
        <!--=========================-->


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