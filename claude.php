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


    <style>
    .quickview-slider .main-image {
        text-align: center;
        margin-bottom: 5px;
        width: 100%;
        /* Increased from any previous value */
    }

    .quickview-slider .main-image img {
        max-width: 100%;
        max-height: 500px;
        width: auto;
        height: auto;
        object-fit: contain;
        /* Ensures the entire image is visible */
    }

    .additional-photos {
        margin-top: -10px;
        width: 100%;
        /* Ensure it takes full width of its container */
    }

    .additional-photos .slick-slide {
        margin: 0 3px;
    }

    .additional-photos img {
        max-width: 100%;
        height: 110px;
        /* Reduced from 100px */
        width: auto;
        object-fit: cover;
        /* This ensures the image covers the area without stretching */
    }

    /* Add these new styles */
    .additional-photos .slick-list {
        margin: 0 -5px;
    }

    .additional-photos .slick-slide>div {
        height: 110px;
        /* Match the img height */
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Add these new styles for size selection */
    .size-variation {
        margin-bottom: 15px;
    }

    .size-options {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 5px;
    }

    .size-variation {
        margin-bottom: 15px;
    }

    .size-container {
        display: flex;
        align-items: center;
    }

    .size-container label {
        margin-right: 10px;
        white-space: nowrap;
    }


    #qv-product-sizes {
        width: 130px;
        padding: 5px;
        margin-top: 5px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }
    </style>

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
                                                            <li><a href="#" class="trigger"
                                                                    data-product-id="<?php echo $product['product_id']; ?>"
                                                                    data-product-name="<?php echo htmlspecialchars($product['name']); ?>"
                                                                    data-product-price="<?php echo number_format($product['price'], 2); ?>"
                                                                    data-product-description="<?php echo htmlspecialchars($product['description']); ?>"
                                                                    data-product-image="<?php echo htmlspecialchars($product['photo_url']); ?>"
                                                                    data-product-brand="<?php echo htmlspecialchars($product['brand_name']); ?>"
                                                                    data-product-sizes="<?php echo htmlspecialchars($product['sizes']); ?>"
                                                                    data-product-colors="<?php echo htmlspecialchars($product['colors']); ?>"
                                                                    data-product-stock="<?php echo $product['total_stock']; ?>">
                                                                    <i class="flaticon-eye"></i>
                                                                </a></li>
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

        <!-- Quickview Modal -->
        <div class="modal quickview-wrapper">
            <div class="quickview">
                <div class="row">
                    <div class="col-12">
                        <span class="close-qv">
                            <i class="flaticon-close"></i>
                        </span>
                    </div>
                    <div class="col-md-6">
                        <div class="quickview-slider">
                            <div class="slider-for">
                                <div class="main-image">
                                    <img id="qv-product-image" src="" alt="Product Image">
                                </div>
                            </div>
                            <div id="qv-additional-photos" class="additional-photos"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="product-details">
                            <h5 class="pro-title"><a href="#" id="qv-product-name"></a></h5>
                            <span class="price">Price : Ksh<span id="qv-product-price"></span></span>
                            <p>Brand: <span id="qv-product-brand"></span></p>
                            <div class="size-variation">
                                <div class="size-container">
                                    <label for="qv-product-sizes">Size:</label>
                                    <select id="qv-product-sizes" class="form-control">
                                        <option value="">Select a size</option>
                                    </select>
                                </div>
                            </div>
                            <div class="color-variation">
                                <span>Colors available:</span>
                                <span id="qv-product-colors"></span>
                            </div>
                            <p>Stock Quantity: <span id="qv-product-stock"></span></p>
                            <div class="add-tocart-wrap">
                                <div class="cart-plus-minus-button">
                                    <input type="text" value="1" name="qtybutton" class="cart-plus-minus">
                                </div>
                                <a href="#" class="add-to-cart"><i class="flaticon-shopping-purse-icon"></i>Add to
                                    Cart</a>
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

    <!-- Quickview Script -->
    <script>
    $(document).ready(function() {
        // Trigger for opening the quickview
        $('.trigger').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            var productId = $(this).data('product-id');
            var productName = $(this).data('product-name');
            var productPrice = $(this).data('product-price');
            var productDescription = $(this).data('product-description');
            var productImage = $(this).data('product-image');
            var productBrand = $(this).data('product-brand');
            var productSizes = $(this).data('product-sizes').split(',');
            var productColors = $(this).data('product-colors');
            var productStock = $(this).data('product-stock');

            // Populate the quickview with product details
            $('#qv-product-name').text(productName);
            $('#qv-product-price').text(productPrice);
            $('#qv-product-description').text(productDescription);
            $('#qv-product-image').attr('src', productImage);
            $('#qv-product-brand').text(productBrand);
            $('#qv-product-colors').text(productColors);
            $('#qv-product-stock').text(productStock);

            // Populate the sizes dropdown
            var sizeSelect = $('#qv-product-sizes');
            sizeSelect.find('option:not(:first)')
                .remove(); // Clear existing options except the first one
            productSizes.forEach(function(size) {
                sizeSelect.append($('<option>', {
                    value: size.trim(),
                    text: size.trim()
                }));
            });

            // Fetch additional photos
            $.ajax({
                url: 'get_product_photos.php',
                method: 'GET',
                data: {
                    product_id: productId
                },
                success: function(response) {
                    console.log('Raw response:', response);
                    try {
                        var photos = JSON.parse(response);
                        console.log('Parsed photos:', photos);
                        var photoHtml = '';
                        if (photos.length > 0) {
                            photos.forEach(function(photo) {
                                photoHtml += '<div><img src="' + photo.photo_url +
                                    '" alt="Product Photo"></div>';
                            });
                            $('#qv-additional-photos').html(photoHtml);
                            console.log('Photo HTML:', photoHtml);

                            // Initialize or reinitialize slick slider for additional photos
                            if ($('#qv-additional-photos').hasClass('slick-initialized')) {
                                $('#qv-additional-photos').slick('unslick');
                            }
                            $('#qv-additional-photos').slick({
                                dots: true,
                                infinite: true,
                                speed: 300,
                                slidesToShow: 3,
                                slidesToScroll: 1,
                                responsive: [{
                                        breakpoint: 1024,
                                        settings: {
                                            slidesToShow: 2,
                                            slidesToScroll: 1,
                                            infinite: true,
                                            dots: true
                                        }
                                    },
                                    {
                                        breakpoint: 600,
                                        settings: {
                                            slidesToShow: 1,
                                            slidesToScroll: 1
                                        }
                                    }
                                ]
                            });
                        } else {
                            console.log('No additional photos found');
                            $('#qv-additional-photos').html(
                                '<p>No additional photos available</p>');
                        }
                    } catch (e) {
                        console.error('Error parsing JSON:', e);
                        $('#qv-additional-photos').html(
                            '<p>Error loading additional photos</p>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', status, error);
                    $('#qv-additional-photos').html(
                        '<p>Error loading additional photos</p>');
                }
            });

            // Show the quickview modal
            $('.quickview-wrapper').fadeIn().css('display', 'flex');
        });

        // Close the quickview when clicking the close button
        $('.close-qv').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $('.quickview-wrapper').fadeOut();
        });

        // Close the quickview when clicking outside of it
        $(document).on('click', function(event) {
            if (!$(event.target).closest('.quickview').length && !$(event.target).closest('.trigger')
                .length) {
                $('.quickview-wrapper').fadeOut();
            }
        });

        // Prevent closing when clicking inside the quickview
        $('.quickview').on('click', function(event) {
            event.stopPropagation();
        });

        // Handle size selection
        $('#qv-product-sizes').on('change', function() {
            var selectedSize = $(this).val();
            console.log('Selected size:', selectedSize);
            // You can perform additional actions here based on the selected size
        });

        // Handle quantity changes
        $('.cart-plus-minus').on('change', function() {
            var quantity = $(this).val();
            console.log('Quantity changed to:', quantity);
            // You can perform additional actions here based on the quantity change
        });

        // Handle add to cart
        $('.add-to-cart').on('click', function(e) {
            e.preventDefault();
            var selectedSize = $('#qv-product-sizes').val();
            var quantity = $('.cart-plus-minus').val();
            var productId = $('.trigger').data(
                'product-id'); // Assuming you store the product ID on the trigger element

            if (!selectedSize) {
                alert('Please select a size before adding to cart.');
                return;
            }

            console.log('Adding to cart:', {
                productId: productId,
                size: selectedSize,
                quantity: quantity
            });
            // Here you would typically send an AJAX request to add the item to the cart
            // For now, we'll just log it to the console
        });
    });
    </script>

</body>

</html>