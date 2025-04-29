<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';

$db = new Database();
$conn = $db->connect();

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle cart actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    switch ($action) {
        case 'add':
            $product = getProductById($conn, $productId);
            if ($product && $product['stock'] >= $quantity) {
                if (isset($_SESSION['cart'][$productId])) {
                    $_SESSION['cart'][$productId]['quantity'] += $quantity;
                } else {
                    $_SESSION['cart'][$productId] = [
                        'name' => $product['name'],
                        'price' => $product['price'],
                        'quantity' => $quantity
                    ];
                }
            }
            break;

        case 'update':
            if (isset($_SESSION['cart'][$productId])) {
                $product = getProductById($conn, $productId);
                if ($product && $product['stock'] >= $quantity) {
                    $_SESSION['cart'][$productId]['quantity'] = $quantity;
                }
            }
            break;

        case 'remove':
            if (isset($_SESSION['cart'][$productId])) {
                unset($_SESSION['cart'][$productId]);
            }
            break;

        case 'clear':
            $_SESSION['cart'] = [];
            break;
    }

    // Redirect back to cart page to prevent form resubmission
    header('Location: cart.php');
    exit();
}

// Calculate cart total
$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/dark-mode.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">
                <h1><?php echo SITE_NAME; ?></h1>
            </div>
            <ul>
                <li><a href="index.php">Inicio</a></li>
                <li><a href="products.php">Productos</a></li>
                <li><a href="cart.php">Carrito</a></li>
            </ul>
        </nav>
    </header>

    <main class="cart-page">
        <h2>Carrito de Compras</h2>

        <?php if (empty($_SESSION['cart'])): ?>
        <p class="empty-cart">Tu carrito está vacío. <a href="products.php">Ver productos</a></p>
        <?php else: ?>
        <div class="cart-items">
            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($_SESSION['cart'] as $productId => $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td><?php echo formatPrice($item['price']); ?></td>
                        <td>
                            <form action="cart.php" method="post" class="update-form">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
                                <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" class="quantity-input">
                                <button type="submit" class="btn btn-small">Actualizar</button>
                            </form>
                        </td>
                        <td><?php echo formatPrice($item['price'] * $item['quantity']); ?></td>
                        <td>
                            <form action="cart.php" method="post" class="remove-form">
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
                                <button type="submit" class="btn btn-danger">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3"><strong>Total</strong></td>
                        <td colspan="2"><strong><?php echo formatPrice($total); ?></strong></td>
                    </tr>
                </tfoot>
            </table>

            <div class="cart-actions">
                <form action="cart.php" method="post" class="clear-form">
                    <input type="hidden" name="action" value="clear">
                    <button type="submit" class="btn btn-danger">Vaciar Carrito</button>
                </form>
                <a href="checkout.php" class="btn btn-primary">Proceder al Pago</a>
            </div>
        </div>
        <?php endif; ?>
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