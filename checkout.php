<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit();
}

$db = new Database();
$conn = $db->connect();

$errors = [];
$success = false;
$orderId = null;

// Calculate cart total
$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate form data
    $name = sanitizeInput($_POST['name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $phone = sanitizeInput($_POST['phone'] ?? '');
    $address = sanitizeInput($_POST['address'] ?? '');

    if (empty($name)) $errors[] = 'El nombre es requerido';
    if (empty($email)) $errors[] = 'El email es requerido';
    if (empty($phone)) $errors[] = 'El teléfono es requerido';
    if (empty($address)) $errors[] = 'La dirección es requerida';

    if (empty($errors)) {
        try {
            $conn->beginTransaction();

            // Create or get user
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                $stmt = $conn->prepare("INSERT INTO users (email, name, phone, address) VALUES (?, ?, ?, ?)");
                $stmt->execute([$email, $name, $phone, $address]);
                $userId = $conn->lastInsertId();
            } else {
                $userId = $user['id'];
                $stmt = $conn->prepare("UPDATE users SET name = ?, phone = ?, address = ? WHERE id = ?");
                $stmt->execute([$name, $phone, $address, $userId]);
            }

            // Prepare order items
            $items = [];
            foreach ($_SESSION['cart'] as $productId => $item) {
                $items[] = [
                    'product_id' => $productId,
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ];
            }

            // Create order
            $orderId = createOrder($conn, $userId, $items, $total, $address);

            if ($orderId) {
                $conn->commit();
                $success = true;
                // Clear cart after successful order
                $_SESSION['cart'] = [];
            } else {
                throw new Exception('Error al crear el pedido');
            }
        } catch (Exception $e) {
            $conn->rollBack();
            $errors[] = 'Error al procesar el pedido: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/checkout.css">
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

    <main class="checkout-page">
        <?php if ($success): ?>
        <div class="success-message">
            <h2>¡Pedido Realizado con Éxito!</h2>
            <p>Tu número de pedido es: <strong><?php echo htmlspecialchars($orderId); ?></strong></p>
            <p>Recibirás un correo electrónico con los detalles de tu pedido.</p>
            <a href="products.php" class="btn">Continuar Comprando</a>
        </div>
        <?php else: ?>
        <h2>Finalizar Compra</h2>

        <?php if (!empty($errors)): ?>
        <div class="error-messages">
            <?php foreach($errors as $error): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="checkout-container">
            <div class="order-summary">
                <h3>Resumen del Pedido</h3>
                <div class="order-items">
                    <?php foreach($_SESSION['cart'] as $productId => $item): ?>
                    <div class="order-item">
                        <div>
                            <span><?php echo htmlspecialchars($item['name']); ?></span>
                            <small>Cantidad: <?php echo $item['quantity']; ?></small>
                        </div>
                        <div><?php echo formatPrice($item['price'] * $item['quantity']); ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="order-total">
                    <span>Total</span>
                    <span><?php echo formatPrice($total); ?></span>
                </div>
            </div>

            <form action="checkout.php" method="post" class="checkout-form">
                <h3>Información de Envío</h3>
                <div class="form-group">
                    <label for="name">Nombre Completo:</label>
                    <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($name ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($email ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="phone">Teléfono:</label>
                    <input type="tel" id="phone" name="phone" required value="<?php echo htmlspecialchars($phone ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="address">Dirección de Envío:</label>
                    <textarea id="address" name="address" required><?php echo htmlspecialchars($address ?? ''); ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Confirmar Pedido</button>
            </form>
        </div>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?></p>
    </footer>
</body>
</html>