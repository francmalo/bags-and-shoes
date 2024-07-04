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
    <title><?php echo htmlspecialchars($product['name']); ?> - Comercio</title>

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
    #notification-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1000;
    }

    .notification {
        background-color: #ffffff;
        border-left: 4px solid #4CAF50;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        padding: 16px;
        margin-bottom: 10px;
        border-radius: 4px;
        font-family: Arial, sans-serif;
        display: flex;
        align-items: center;
        animation: slideIn 0.5s ease-out;
    }

    .notification.error {
        border-left-color: #f44336;
    }

    .notification-icon {
        margin-right: 12px;
        font-size: 24px;
    }

    .notification-message {
        flex-grow: 1;
    }

    .notification-close {
        cursor: pointer;
        padding: 0 6px;
        font-weight: bold;
    }

    @keyframes slideIn {
        from {
            transform: translateX(100%);
        }

        to {
            transform: translateX(0);
        }
    }
    </style>


</head>











<body id="home-version-1" class="home-version-1" data-style="default">

    <div id="notification-container"></div>

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

                                    <form id="addToCartForm" action="" method="post">
                                        <input type="hidden" name="product_id"
                                            value="<?php echo $product['product_id']; ?>">
                                        <div class="size-variation">
                                            <span>size :</span>
                                            <select name="size" id="size">
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
                                            <div class="cart-plus-minus-button">
                                                <input type="number" value="1" name="quantity" class="cart-plus-minus"
                                                    min="1">
                                            </div>
                                            <a href="#" class="add-to-cart" id="addToCartLink">
                                                <i class="flaticon-shopping-purse-icon"></i>Add to Cart
                                            </a>
                                            <!-- <a href="#"><i class="flaticon-valentines-heart"></i></a> -->
                                        </div>

                                    </form>
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>




    <!-- <script>
    document.addEventListener('DOMContentLoaded', function() {
        var addToCartLink = document.getElementById('addToCartLink');
        var addToCartForm = document.getElementById('addToCartForm');

        addToCartLink.addEventListener('click', function(e) {
            e.preventDefault();

            var formData = new FormData(addToCartForm);
            formData.append('add_to_cart', '1');

            fetch('add_to_cart.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Product added to cart successfully!');
                        // You can update the cart count here if needed
                    } else {
                        alert('Failed to add product to cart. Please try again.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
        });
    });
    </script> -->




    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var addToCartLink = document.getElementById('addToCartLink');
        var addToCartForm = document.getElementById('addToCartForm');

        function showNotification(message, isError = false) {
            const icon = isError ? '❌' : '✅';
            const notificationHtml = `
            <div class="notification ${isError ? 'error' : ''}">
                <span class="notification-icon">${icon}</span>
                <span class="notification-message">${message}</span>
                <span class="notification-close">&times;</span>
            </div>
        `;
            document.getElementById('notification-container').insertAdjacentHTML('beforeend', notificationHtml);

            setTimeout(() => {
                document.querySelector('.notification').remove();
            }, 5000);
        }

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('notification-close')) {
                e.target.closest('.notification').remove();
            }
        });

        function updateCartCount(count) {
            const cartCountElements = document.querySelectorAll('.cart-count');
            cartCountElements.forEach(el => el.textContent = count);
        }

        addToCartLink.addEventListener('click', function(e) {
            e.preventDefault();
            var formData = new FormData(addToCartForm);
            formData.append('add_to_cart', '1');

            fetch('add_to_cart.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message);
                        updateCartCount(data.cart_count);
                    } else {
                        showNotification(data.message, true);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('An error occurred. Please try again.', true);
                });
        });

        // Fetch initial cart count
        fetch('get_cart_count.php')
            .then(response => response.json())
            .then(data => {
                updateCartCount(data.count);
            })
            .catch(error => {
                console.error('Error fetching cart count:', error);
            });
    });
    </script>


</body>

</html>