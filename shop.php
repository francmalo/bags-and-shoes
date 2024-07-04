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

// Function to fetch products with additional details
function getProducts($conn) {
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
            GROUP BY p.product_id, p.name, p.description, p.price, p.brand_id, p.category_id, p.type_id, p.gender_id,
                     b.name, c.name, t.name, g.name";
    return $conn->query($sql);
}

// Function to fetch additional photos for a product
function getProductPhotos($conn, $productId) {
    $sql = "SELECT photo_url FROM Product_Photos WHERE product_id = ? AND is_primary = 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}


$sql="SELECT * FROM Product_Photos WHERE product_id = 1 AND is_primary = 0";

// Fetch categories and products
try {
    $categories = getCategories($conn);
    $products = getProducts($conn);
} catch (mysqli_sql_exception $e) {
    echo "MySQL Error: " . $e->getMessage();
    exit;
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





<body id="home-version-1" class="home-version-1" data-style="default">

    <div class="site-content">







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
                            <!-- Other sidebar widgets... -->
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
                                                    <p><?php echo htmlspecialchars($product['gender_name']); ?> /
                                                        <span>Ksh<?php echo number_format($product['price'], 2); ?></span>
                                                    </p>
                                                </div>
                                                <div class="icon-wrapper">
                                                    <div class="pro-icon">
                                                        <ul>
                                                            <li><a href="#"><i
                                                                        class="flaticon-valentines-heart"></i></a></li>
                                                            <li><a href="#"><i class="flaticon-compare"></i></a></li>
                                                            <li><a
                                                                    href="product.php?id=<?php echo $product['product_id']; ?>"><i
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
        <!-- /.shop-area -->



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
    <!-- Google map api -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBsBrMPsyNtpwKXPPpG54XwJXnyobfMAIc"></script>

    <!-- Site Scripts -->
    <script src="assets/js/app.js"></script>



</body>

</html>