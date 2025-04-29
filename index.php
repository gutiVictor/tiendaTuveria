<?php
require_once 'includes/config.php';
require_once 'includes/database.php';

// Initialize database connection
$db = new Database();
$conn = $db->connect();

// Fetch one featured product from each category
$sql = "SELECT DISTINCT p.* 
        FROM products p 
        INNER JOIN (
            SELECT MIN(id) as id 
            FROM products 
            GROUP BY category
        ) p2 ON p.id = p2.id 
        ORDER BY p.category";
$stmt = $conn->prepare($sql);
$stmt->execute();
$featured_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/dark-mode.css">
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

    <main class="container mt-4">
        <section class="hero text-center py-5 mb-5">
            <h2 class="display-4 mb-3">Materiales de PVC y Almacenamiento de Agua</h2>
            <p class="lead">Encuentra todo lo que necesitas para tus proyectos de plomería</p>
        </section>

        <section class="featured-products">
            <h3 class="mb-4">Productos Destacados</h3>
            <div class="row" id="product-grid">
                <?php foreach ($featured_products as $product): ?>
                    <div class="col-md-4 col-lg-3 mb-4">
                        <div class="card h-100">
                            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                                 class="card-img-top" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($product['description']); ?></p>
                                <p class="card-text"><strong>$<?php echo number_format($product['price'], 2); ?></strong></p>
                                <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-primary">Ver Detalles</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </main>

    <footer class="container-fluid py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4 footer-column">
                    <h5>Tienda de PVC</h5>
                    <p>Tu proveedor confiable de materiales de PVC y productos para almacenamiento de agua.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
                <div class="col-md-4 footer-column">
                    <h5>Enlaces Rápidos</h5>
                    <ul>
                        <li><a href="index.php">Inicio</a></li>
                        <li><a href="products.php">Productos</a></li>
                        <li><a href="cart.php">Carrito</a></li>
                    </ul>
                </div>
                <div class="col-md-4 footer-column">
                    <h5>Contacto</h5>
                    <ul>
                        <li><i class="fas fa-phone"></i> (123) 456-7890</li>
                        <li><i class="fas fa-envelope"></i> info@tiendapvc.com</li>
                        <li><i class="fas fa-map-marker-alt"></i> Calle Principal #123</li>
                    </ul>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12 text-center">
                    <p class="copyright">&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. Todos los derechos reservados.</p>
                </div>
            </div>
        </div>
    </footer>

    <button class="theme-toggle" onclick="toggleTheme()">
        <i class="fas fa-moon"></i>
        <span>Cambiar Tema</span>
    </button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/theme.js"></script>
</body>
</html>