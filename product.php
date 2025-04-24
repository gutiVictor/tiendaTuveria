<?php
require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';

$db = new Database();
$conn = $db->connect();

// Get product ID from URL
$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch product details
try {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        header('Location: products.php');
        exit();
    }
} catch(PDOException $e) {
    header('Location: products.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="index.php"><?php echo SITE_NAME; ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="products.php">Productos</a></li>
                    <li class="nav-item"><a class="nav-link" href="cart.php">Carrito</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container my-4">
        <div class="row">
            <div class="col-md-6">
                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="img-fluid product-image">
            </div>
            <div class="col-md-6">
                <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                <p class="lead"><?php echo formatPrice($product['price']); ?></p>
                <p class="mb-4"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                
                <?php if ($product['stock'] > 0): ?>
                <form action="cart.php" method="post" class="mb-3">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <div class="input-group mb-3">
                        <input type="number" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>" class="form-control" style="max-width: 100px;">
                        <button type="submit" class="btn btn-primary">Agregar al Carrito</button>
                    </div>
                </form>
                <p class="text-success">Stock disponible: <?php echo $product['stock']; ?> unidades</p>
                <?php else: ?>
                <p class="text-danger">Producto agotado</p>
                <?php endif; ?>
                
                <div class="mt-4">
                    <h3>Detalles del Producto</h3>
                    <ul class="list-unstyled">
                        <li><strong>Categoría:</strong> <?php echo htmlspecialchars($product['category']); ?></li>
                    </ul>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-light py-3 mt-5">
        <div class="container text-center">
            <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?></p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>