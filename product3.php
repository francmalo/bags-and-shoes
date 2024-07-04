<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('config.php');

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
    <!-- Your head content here -->
</head>

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
                                        <ul>
                                            <?php
                                            $colors = explode(',', $product['colors']);
                                            foreach ($colors as $color) {
                                                echo "<li><i class=\"fas fa-circle\" style=\"color: " . htmlspecialchars(trim($color)) . "\"></i></li>";
                                            }
                                            ?>
                                        </ul>
                                    </div>
                                    <!-- Add to cart and other product details -->
                                    <p><?php echo htmlspecialchars($product['description']); ?></p>
                                    <!-- Additional product information -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <!-- Your script includes here -->
</body>

</html>