<?php
require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';

$db = new Database();
$conn = $db->connect();

// Get filters from URL parameters
$category = isset($_GET['category']) ? sanitizeInput($_GET['category']) : '';
$sort = isset($_GET['sort']) ? sanitizeInput($_GET['sort']) : 'name_asc';

// Prepare the base query
$query = "SELECT * FROM products WHERE 1=1";
$params = [];

if ($category) {
    $query .= " AND category = ?";
    $params[] = $category;
}

// Add sorting
switch ($sort) {
    case 'price_asc':
        $query .= " ORDER BY price ASC";
        break;
    case 'price_desc':
        $query .= " ORDER BY price DESC";
        break;
    case 'name_desc':
        $query .= " ORDER BY name DESC";
        break;
    default:
        $query .= " ORDER BY name ASC";
}

try {
    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get unique categories for filter
    $categoryStmt = $conn->query("SELECT DISTINCT category FROM products ORDER BY category");
    $categories = $categoryStmt->fetchAll(PDO::FETCH_COLUMN);
} catch(PDOException $e) {
    $products = [];
    $categories = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos - <?php echo SITE_NAME; ?></title>
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
                    <li class="nav-item"><a class="nav-link active" href="products.php">Productos</a></li>
                    <li class="nav-item"><a class="nav-link" href="cart.php">Carrito</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container mt-4">
        <div class="row">
            <div class="col-lg-3 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h2 class="h5 mb-3">Filtros</h2>
                        <form action="products.php" method="get">
                            <div class="mb-3">
                                <label for="category" class="form-label">Categoría:</label>
                                <select name="category" id="category" class="form-select">
                                    <option value="">Todas las categorías</option>
                                    <?php foreach($categories as $cat): ?>
                                    <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo $category === $cat ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="sort" class="form-label">Ordenar por:</label>
                                <select name="sort" id="sort" class="form-select">
                                    <option value="name_asc" <?php echo $sort === 'name_asc' ? 'selected' : ''; ?>>Nombre (A-Z)</option>
                                    <option value="name_desc" <?php echo $sort === 'name_desc' ? 'selected' : ''; ?>>Nombre (Z-A)</option>
                                    <option value="price_asc" <?php echo $sort === 'price_asc' ? 'selected' : ''; ?>>Precio (Menor a Mayor)</option>
                                    <option value="price_desc" <?php echo $sort === 'price_desc' ? 'selected' : ''; ?>>Precio (Mayor a Menor)</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Aplicar Filtros</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-9">
                <?php if (empty($products)): ?>
                <div class="alert alert-info">No se encontraron productos.</div>
                <?php else: ?>
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    <?php foreach($products as $product): ?>
                    <div class="col">
                        <div class="card h-100 shadow-sm">
                            <?php if ($product['image_url']): ?>
                            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <?php endif; ?>
                            <div class="card-body">
                                <h3 class="card-title h5"><?php echo htmlspecialchars($product['name']); ?></h3>
                                <p class="card-text text-muted small"><?php echo htmlspecialchars($product['description']); ?></p>
                                <p class="card-text fw-bold text-primary mb-2"><?php echo formatPrice($product['price']); ?></p>
                                <p class="card-text small mb-3">Stock: <?php echo $product['stock']; ?> unidades</p>
                                <?php if ($product['stock'] > 0): ?>
                                <form action="cart.php" method="post">
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <div class="d-flex gap-2 align-items-center">
                                        <input type="number" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>" class="form-control form-control-sm" style="width: 70px">
                                        <button type="submit" class="btn btn-primary btn-sm flex-grow-1">Agregar al Carrito</button>
                                    </div>
                                </form>
                                <?php else: ?>
                                <p class="text-danger mb-0">Agotado</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer class="container-fluid bg-light mt-5 py-3">
        <div class="container text-center">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?></p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>