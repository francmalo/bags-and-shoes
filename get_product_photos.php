<?php
include('config.php');

function getProductPhotos($conn, $productId) {
    $sql = "SELECT photo_url FROM Product_Photos WHERE product_id = ? AND is_primary = 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

if(isset($_GET['product_id'])) {
    $productId = $_GET['product_id'];
    $photos = getProductPhotos($conn, $productId);
    echo json_encode($photos);
}
?>