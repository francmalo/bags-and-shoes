<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('config.php');

// Function to fetch categories
function getCategories($conn) {
    $sql = "SELECT c.name, COUNT(p.product_id) as product_count 
            FROM Categories c
            LEFT JOIN Products p ON c.category_id = p.category_id
            GROUP BY c.category_id";
    return $conn->query($sql);
}

// Function to fetch products
function getProducts($conn) {
    $sql = "SELECT p.*, b.name AS brand_name, c.name AS category_name, t.name AS type_name, g.name AS gender_name, pp.photo_url 
            FROM Products p
            LEFT JOIN Brands b ON p.brand_id = b.brand_id
            LEFT JOIN Categories c ON p.category_id = c.category_id
            LEFT JOIN Types t ON p.type_id = t.type_id
            LEFT JOIN Genders g ON p.gender_id = g.gender_id
            LEFT JOIN Product_Photos pp ON p.product_id = pp.product_id AND pp.is_primary = 1";
    return $conn->query($sql);
}

// Fetch categories and products
$categories = getCategories($conn);
$products = getProducts($conn);

try {
    $categories = getCategories($conn);
    $products = getProducts($conn);
} catch (mysqli_sql_exception $e) {
    echo "MySQL Error: " . $e->getMessage();
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

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

        <section class="shop-area">
            <div class="container-fluid custom-container">
                <div class="row">
                    <div class="order-2 order-lg-1 col-lg-3 col-xl-3">
                        <div class="shop-sidebar left-side">
                            <div class="sidebar-widget sidebar-search">
                                <input type="text" placeholder="Search Product....">
                                <button type="submit"><i class="fas fa-search"></i></button>
                            </div>
                            <div class="sidebar-widget category-widget">
                                <h6>PRODUCT CATEGORIES</h6>
                                <ul>
                                    <?php
                                if ($categories->num_rows > 0) {
                                    while($category = $categories->fetch_assoc()) {
                                        echo "<li><a href='#'>{$category['name']}</a> <span>({$category['product_count']})</span></li>";
                                    }
                                } else {
                                    echo "<li>No categories found</li>";
                                }
                                ?>
                                </ul>
                            </div>
                            <div class="sidebar-widget range-widget">
                                <h6>SEARCH BY PRICE</h6>
                                <div class="price-range">
                                    <div id="slider-range"></div>
                                    <span>Price :</span>
                                    <input type="text" id="amount">
                                </div>
                            </div>

                            <div class="sidebar-widget color-widget">
                                <h6>PRODUCT COLOR</h6>
                                <ul>
                                    <li><a href="#"></a></li>
                                    <li><a href="#"></a></li>
                                    <li><a href="#"></a></li>
                                    <li><a href="#"></a></li>
                                    <li><a href="#"></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="order-1 order-lg-2 col-lg-9 col-xl-9">
                        <div class="shop-sorting-area row">
                            <!-- Add sorting options here -->
                        </div>
                        <div class="shop-content">
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show active" id="home" role="tabpanel"
                                    aria-labelledby="home-tab">
                                    <div class="row">
                                        <?php
                                    if ($products->num_rows > 0) {
                                        while($product = $products->fetch_assoc()) {
                                            ?>
                                        <div class="col-sm-6 col-xl-4">
                                            <div class="sin-product style-two">
                                                <div class="pro-img">
                                                    <img src="<?php echo htmlspecialchars($product['photo_url']); ?>"
                                                        alt="<?php echo htmlspecialchars($product['name']); ?>">
                                                </div>
                                                <div class="mid-wrapper">
                                                    <h5 class="pro-title"><a
                                                            href="product.php?id=<?php echo $product['product_id']; ?>"><?php echo htmlspecialchars($product['name']); ?></a>
                                                    </h5>
                                                    <div class="color-variation">
                                                        <!-- Add color variations here if needed -->
                                                    </div>
                                                    <p><?php echo htmlspecialchars($product['gender_name']); ?> /
                                                        <span>$<?php echo number_format($product['price'], 2); ?></span>
                                                    </p>
                                                </div>
                                                <div class="icon-wrapper">
                                                    <div class="pro-icon">
                                                        <ul>
                                                            <li><a href="#"><i
                                                                        class="flaticon-valentines-heart"></i></a>
                                                            </li>
                                                            <li><a href="#"><i class="flaticon-compare"></i></a></li>
                                                            <li><a href="#" class="trigger"><i
                                                                        class="flaticon-eye"></i></a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div class="add-to-cart">
                                                        <a href="#">add to cart</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                        }
                                    } else {
                                        echo "<p>No products found</p>";
                                    }
                                    ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>





        <div class="backtotop">
            <i class="fa fa-angle-up backtotop_btn"></i>
        </div>



        <div class="backtotop">
            <i class="fa fa-angle-up backtotop_btn"></i>
        </div>




        <!--=========================-->
        <!--=   Product Quick view area      =-->
        <!--=========================-->

        <!-- Quick View -->
        <div class="modal quickview-wrapper">
            <div class="quickview">
                <div class="row">
                    <div class="col-12">
                        <span class="close-qv">
                            <i class="flaticon-close"></i>
                        </span>
                    </div>
                    <div class="col-md-6">
                        <!-- Product View Slider -->
                        <div class="quickview-slider">
                            <div class="slider-for">
                                <div class="">
                                    <img src="media/images/product/single/b1.jpg" alt="Thumb">
                                </div>
                                <div class="">
                                    <img src="media/images/product/single/b2.jpg" alt="thumb">
                                </div>
                                <div class="">
                                    <img src="media/images/product/single/b3.jpg" alt="thumb">
                                </div>
                                <div class="">
                                    <img src="media/images/product/single/b4.jpg" alt="thumb">
                                </div>
                                <div class="">
                                    <img src="media/images/product/single/b5.jpg" alt="Thumb">
                                </div>
                                <div class="">
                                    <img src="media/images/product/single/b1.jpg" alt="thumb">
                                </div>
                                <div class="">
                                    <img src="media/images/product/single/b2.jpg" alt="thumb">
                                </div>
                                <div class="">
                                    <img src="media/images/product/single/b3.jpg" alt="thumb">
                                </div>
                            </div>

                            <div class="slider-nav">

                                <div class="">
                                    <img src="media/images/product/single/b1.jpg" alt="thumb">
                                </div>
                                <div class="">
                                    <img src="media/images/product/single/b2.jpg" alt="thumb">
                                </div>
                                <div class="">
                                    <img src="media/images/product/single/b3.jpg" alt="thumb">
                                </div>
                                <div class="">
                                    <div class="">
                                        <img src="media/images/product/single/b4.jpg" alt="Thumb">
                                    </div>
                                </div>
                                <div class="">
                                    <img src="media/images/product/single/b5.jpg" alt="thumb">
                                </div>
                                <div class="">
                                    <img src="media/images/product/single/b1.jpg" alt="thumb">
                                </div>
                                <div class="">
                                    <img src="media/images/product/single/b2.jpg" alt="thumb">
                                </div>
                                <div class="">
                                    <img src="media/images/product/single/b3.jpg" alt="thumb">
                                </div>
                            </div>
                        </div>
                        <!-- /.quickview-slider -->
                    </div>
                    <!-- /.col-xl-6 -->

                    <div class="col-md-6">
                        <div class="product-details">
                            <h5 class="pro-title"><a href="#">Woman fashion dress</a></h5>
                            <span class="price">Price : $387</span>
                            <div class="size-variation">
                                <span>size :</span>
                                <select name="size-value">
                                    <option value="">1</option>
                                    <option value="">2</option>
                                    <option value="">3</option>
                                    <option value="">4</option>
                                    <option value="">5</option>
                                </select>
                            </div>
                            <div class="color-variation">
                                <span>color :</span>
                                <ul>
                                    <li><i class="fas fa-circle"></i></li>
                                    <li><i class="fas fa-circle"></i></li>
                                    <li><i class="fas fa-circle"></i></li>
                                    <li><i class="fas fa-circle"></i></li>
                                </ul>
                            </div>

                            <div class="add-tocart-wrap">
                                <!--PRODUCT INCREASE BUTTON START-->
                                <div class="cart-plus-minus-button">
                                    <input type="text" value="1" name="qtybutton" class="cart-plus-minus">
                                </div>
                                <a href="#" class="add-to-cart"><i class="flaticon-shopping-purse-icon"></i>Add to
                                    Cart</a>
                                <!-- <a href="#"><i class="flaticon-valentines-heart"></i></a> -->
                            </div>

                            <!-- <span>SKU:	N/A</span>
								<p>Tags <a href="#">boys,</a><a href="#"> dress,</a><a href="#">Rok-dress</a></p> -->

                            <p>But I must explain to you how all this mistaken idedenounc pleasure and praisi pain was
                                born and I will give you a complete accosystem, and expound the actu teachings of the
                                great explore tmaster-builder of human happiness. No one rejects, dislikes,
                                or avoids.</p>

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
                        <!-- /.product-details -->
                    </div>
                    <!-- /.col-xl-6 -->
                </div>
                <!-- /.row -->
            </div>
        </div>



    </div>
    <!-- /#site -->

    <!-- Add your JavaScript links here -->

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

<?php
$conn->close();
?>