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
                        <!-- Add other sidebar widgets here -->
                    </div>
                </div>
                <div class="order-1 order-lg-2 col-lg-9 col-xl-9">
                    <div class="shop-content">
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
                                        <h5 class="pro-title">
                                            <a
                                                href="product.php?id=<?php echo $product['product_id']; ?>"><?php echo htmlspecialchars($product['name']); ?></a>
                                        </h5>
                                        <p><?php echo htmlspecialchars($product['gender_name']); ?> /
                                            <span>$<?php echo number_format($product['price'], 2); ?></span>
                                        </p>
                                    </div>
                                    <div class="icon-wrapper">
                                        <div class="pro-icon">
                                            <ul>
                                                <li><a href="#"><i class="fas fa-heart"></i></a></li>
                                                <li><a href="#"><i class="fas fa-sync"></i></a></li>
                                                <li><a href="#" class="trigger"
                                                        data-product-id="<?php echo $product['product_id']; ?>"><i
                                                            class="fas fa-eye"></i></a></li>
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
    </section>

    <!-- Quickview Modal -->
    <div class="quickview-wrapper">
        <div class="quickview">
            <div class="row">
                <div class="col-12">
                    <span class="close-qv">
                        <i class="fas fa-times"></i>
                    </span>
                </div>
                <div class="col-md-6">
                    <div class="quickview-slider">
                        <div class="slider-for">
                            <div class="">
                                <img src="" alt="Thumb" id="qv-main-image">
                            </div>
                        </div>
                        <div class="slider-nav" id="qv-image-thumbnails">
                            <!-- Thumbnails will be dynamically added here -->
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="product-details">
                        <h5 class="pro-title"><a href="#" id="qv-product-name"></a></h5>
                        <span class="price">Price : $<span id="qv-product-price"></span></span>
                        <div class="size-variation">
                            <span>size :</span>
                            <select name="size-value" id="qv-product-size">
                                <!-- Sizes will be dynamically added here -->
                            </select>
                        </div>
                        <div class="color-variation">
                            <span>color :</span>
                            <ul id="qv-product-colors">
                                <!-- Colors will be dynamically added here -->
                            </ul>
                        </div>
                        <div class="add-tocart-wrap">
                            <div class="cart-plus-minus-button">
                                <input type="text" value="1" name="qtybutton" class="cart-plus-minus">
                            </div>
                            <a href="#" class="add-to-cart"><i class="fas fa-shopping-cart"></i> Add to Cart</a>
                        </div>
                        <p id="qv-product-description"></p>
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
            </div>
        </div>
    </div>


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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        $('.trigger').on('click', function(e) {
            e.preventDefault();
            var productId = $(this).data('product-id');

            $.ajax({
                url: 'get_product_details.php',
                method: 'GET',
                data: {
                    id: productId
                },
                dataType: 'json',
                success: function(product) {
                    console.log('Product data received:', product);

                    $('#qv-main-image').attr('src', product.photo_url);
                    $('#qv-product-name').text(product.name);
                    $('#qv-product-price').text(product.price);
                    $('#qv-product-description').text(product.description ||
                        'No description available');

                    // Clear existing thumbnails, sizes, and colors
                    $('#qv-image-thumbnails, #qv-product-size, #qv-product-colors').empty();

                    // Add thumbnails (adjust based on your data structure)
                    $('#qv-image-thumbnails').append('<div><img src="' + product.photo_url +
                        '" alt="thumb"></div>');

                    // Add sizes (adjust based on your data structure)
                    $('#qv-product-size').append('<option value="' + product.size + '">' +
                        product.size + '</option>');

                    // Add colors (adjust based on your data structure)
                    $('#qv-product-colors').append(
                        '<li><i class="fas fa-circle" style="color: ' + product.color +
                        ';"></i></li>');

                    $('.quickview-wrapper').fadeIn();
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    alert('Error fetching product details: ' + error);
                }
            });
        });

        $('.close-qv, .quickview-wrapper').on('click', function(e) {
            if (e.target === this) {
                $('.quickview-wrapper').fadeOut();
            }
        });
    });
    </script>
</body>

</html>

<?php
$conn->close();
?>