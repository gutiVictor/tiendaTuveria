<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';
require_once 'vendor/autoload.php';

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit();
}

// Load Stripe API key from configuration
$stripeSecretKey = getenv('STRIPE_SECRET_KEY') ?: 'your_stripe_secret_key';
\Stripe\Stripe::setApiKey($stripeSecretKey);

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
    $card_number = sanitizeInput($_POST['card_number'] ?? '');
    $card_exp_month = sanitizeInput($_POST['card_exp_month'] ?? '');
    $card_exp_year = sanitizeInput($_POST['card_exp_year'] ?? '');
    $card_cvv = sanitizeInput($_POST['card_cvv'] ?? '');

    // Validate required fields
    if (empty($name)) $errors[] = 'El nombre es requerido';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email inválido';
    if (empty($phone)) $errors[] = 'El teléfono es requerido';
    if (empty($address)) $errors[] = 'La dirección es requerida';
    if (empty($card_number) || !preg_match('/^[0-9]{16}$/', str_replace(' ', '', $card_number))) $errors[] = 'Número de tarjeta inválido';
    if (empty($card_exp_month) || !preg_match('/^(0[1-9]|1[0-2])$/', $card_exp_month)) $errors[] = 'Mes de expiración inválido';
    if (empty($card_exp_year) || !preg_match('/^20[2-9][0-9]$/', $card_exp_year)) $errors[] = 'Año de expiración inválido';
    if (empty($card_cvv) || !preg_match('/^[0-9]{3,4}$/', $card_cvv)) $errors[] = 'CVV inválido';

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

            // Prepare order items and check stock
            $items = [];
            foreach ($_SESSION['cart'] as $productId => $item) {
                // Check stock availability
                $stmt = $conn->prepare("SELECT stock FROM products WHERE id = ?");
                $stmt->execute([$productId]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$product || $product['stock'] < $item['quantity']) {
                    throw new Exception('Producto no disponible o stock insuficiente');
                }

                $items[] = [
                    'product_id' => $productId,
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ];
            }

            // Process payment with Stripe
            try {
                $payment_intent = \Stripe\PaymentIntent::create([
                    'amount' => (int)($total * 100), // Convert to cents and ensure integer
                    'currency' => 'mxn',
                    'payment_method_data' => [
                        'type' => 'card',
                        'card' => [
                            'number' => str_replace(' ', '', $card_number),
                            'exp_month' => intval($card_exp_month),
                            'exp_year' => intval($card_exp_year),
                            'cvc' => $card_cvv,
                        ],
                    ],
                    'confirm' => true,
                    'description' => 'Order for ' . $email,
                ]);

                if ($payment_intent->status === 'succeeded') {
                    // Create order
                    $orderId = createOrder($conn, $userId, $items, $total, $address);
                    if ($orderId) {
                        // Update product stock
                        foreach ($items as $item) {
                            $stmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
                            $stmt->execute([$item['quantity'], $item['product_id']]);
                        }

                        $conn->commit();
                        $success = true;
                        $_SESSION['cart'] = [];
                    } else {
                        throw new Exception('Error al crear el pedido');
                    }
                } else {
                    throw new Exception('Error al procesar el pago');
                }
            } catch (\Stripe\Exception\CardException $e) {
                throw new Exception('Error en la tarjeta: ' . $e->getMessage());
            } catch (\Stripe\Exception\InvalidRequestException $e) {
                throw new Exception('Error en la solicitud de pago: ' . $e->getMessage());
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
    <script src="https://js.stripe.com/v3/"></script>
    <script src="assets/js/payment.js"></script>
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
            <p>El pago ha sido procesado correctamente.</p>
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
            <form action="checkout.php" method="post" class="checkout-form" id="payment-form">
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

                <h3>Información de Pago</h3>
                <div class="form-group">
                    <label for="card_number">Número de Tarjeta:</label>
                    <input type="text" id="card_number" name="card_number" required maxlength="19" placeholder="1234 5678 9012 3456">
                </div>

                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="card_exp_month">Mes de Expiración:</label>
                        <input type="text" id="card_exp_month" name="card_exp_month" required maxlength="2" placeholder="MM">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="card_exp_year">Año de Expiración:</label>
                        <input type="text" id="card_exp_year" name="card_exp_year" required maxlength="4" placeholder="YYYY">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="card_cvv">CVV:</label>
                        <input type="text" id="card_cvv" name="card_cvv" required maxlength="4" placeholder="123">
                    </div>
                </div>

                <button type="submit" class="btn btn-confirm">Confirmar Pedido y Pagar</button>
            </form>

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
        </div>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?></p>
    </footer>
</body>
</html>