<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$db = new $dbname ;
$conn = $db->connect();

$error = '';
$success = '';
$product = [
    'id' => '',
    'name' => '',
    'description' => '',
    'price' => '',
    'stock' => '',
    'category' => '',
    'image_url' => ''
];

// Edit mode
if (isset($_GET['id'])) {
    try {
        $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([(int)$_GET['id']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $product = $result;
        } else {
            $error = 'Producto no encontrado';
        }
    } catch(PDOException $e) {
        $error = 'Error al obtener el producto: ' . $e->getMessage();
    }
}

// Form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product = [
        'id' => $_POST['id'] ?? '',
        'name' => sanitizeInput($_POST['name']),
        'description' => sanitizeInput($_POST['description']),
        'price' => (float)$_POST['price'],
        'stock' => (int)$_POST['stock'],
        'category' => sanitizeInput($_POST['category']),
        'image_url' => sanitizeInput($_POST['image_url'])
    ];

    // Validation
    if (empty($product['name'])) {
        $error = 'El nombre es obligatorio';
    } elseif ($product['price'] <= 0) {
        $error = 'El precio debe ser mayor que 0';
    } elseif ($product['stock'] < 0) {
        $error = 'El stock no puede ser negativo';
    } else {
        try {
            if ($product['id']) { // Update
                $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ?, stock = ?, category = ?, image_url = ? WHERE id = ?");
                $stmt->execute([
                    $product['name'],
                    $product['description'],
                    $product['price'],
                    $product['stock'],
                    $product['category'],
                    $product['image_url'],
                    $product['id']
                ]);
                $success = 'Producto actualizado correctamente';
            } else { // Insert
                $stmt = $conn->prepare("INSERT INTO products (name, description, price, stock, category, image_url) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $product['name'],
                    $product['description'],
                    $product['price'],
                    $product['stock'],
                    $product['category'],
                    $product['image_url']
                ]);
                $success = 'Producto creado correctamente';
                // Clear form after successful insertion
                $product = [
                    'id' => '',
                    'name' => '',
                    'description' => '',
                    'price' => '',
                    'stock' => '',
                    'category' => '',
                    'image_url' => ''
                ];
            }
        } catch(PDOException $e) {
            $error = 'Error al guardar el producto: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product['id'] ? 'Editar' : 'Nuevo'; ?> Producto - PVC Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">Admin Panel</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="products.php">Productos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="orders.php">Pedidos</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h2 class="mb-0"><?php echo $product['id'] ? 'Editar' : 'Nuevo'; ?> Producto</h2>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <?php if ($success): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>

                        <form method="POST" class="needs-validation" novalidate>
                            <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">Nombre *</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Descripción</label>
                                <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($product['description']); ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="price" class="form-label">Precio *</label>
                                    <input type="number" class="form-control" id="price" name="price" step="0.01" value="<?php echo $product['price']; ?>" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="stock" class="form-label">Stock *</label>
                                    <input type="number" class="form-control" id="stock" name="stock" value="<?php echo $product['stock']; ?>" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="category" class="form-label">Categoría</label>
                                <input type="text" class="form-control" id="category" name="category" value="<?php echo htmlspecialchars($product['category']); ?>">
                            </div>

                            <div class="mb-3">
                                <label for="image_url" class="form-label">URL de la imagen</label>
                                <input type="text" class="form-control" id="image_url" name="image_url" value="<?php echo htmlspecialchars($product['image_url']); ?>">
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="products.php" class="btn btn-secondary">Cancelar</a>
                                <button type="submit" class="btn btn-primary">Guardar Producto</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>