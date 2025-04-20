<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/database.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$db = new Database();
$conn = $db->connect();

// Get summary statistics
try {
    // Count total products
    $stmt = $conn->query("SELECT COUNT(*) FROM products");
    $totalProducts = $stmt->fetchColumn();

    // Count total orders
    $stmt = $conn->query("SELECT COUNT(*) FROM orders");
    $totalOrders = $stmt->fetchColumn();

    // Count pending orders
    $stmt = $conn->prepare("SELECT COUNT(*) FROM orders WHERE status = ?");
    $stmt->execute(['pending']);
    $pendingOrders = $stmt->fetchColumn();

    // Get low stock products (less than 10 items)
    $stmt = $conn->prepare("SELECT COUNT(*) FROM products WHERE stock < ?");
    $stmt->execute([10]);
    $lowStockProducts = $stmt->fetchColumn();
} catch(PDOException $e) {
    $error = 'Error al obtener estadísticas: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - PVC Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-dark: #1a1a2e;
            --secondary-dark: #2d3047;
            --accent-blue: #4d7cff;
            --light-blue: #e8f0ff;
            --gray-100: #f8f9fa;
            --gray-200: #e9ecef;
            --gray-800: #343a40;
        }

        body {
            background-color: var(--gray-100);
        }

        .navbar {
            background-color: var(--primary-dark) !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            color: var(--light-blue) !important;
            font-weight: 600;
        }

        .nav-link {
            color: var(--gray-200) !important;
            transition: color 0.3s ease;
        }

        .nav-link:hover {
            color: var(--accent-blue) !important;
        }

        .container {
            max-width: 1400px;
            padding: 0 1.5rem;
        }

        h1 {
            color: var(--secondary-dark);
            font-weight: 600;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card.bg-primary {
            background: linear-gradient(135deg, #4d7cff, #2d3047) !important;
        }

        .card.bg-success {
            background: linear-gradient(135deg, #28a745, #1e7e34) !important;
        }

        .card.bg-warning {
            background: linear-gradient(135deg, #ffc107, #d39e00) !important;
        }

        .card.bg-danger {
            background: linear-gradient(135deg, #dc3545, #bd2130) !important;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 500;
        }

        .card-text {
            font-size: 2rem;
            font-weight: 700;
        }

        .btn-light {
            background-color: rgba(255,255,255,0.9);
            border: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-light:hover {
            background-color: #ffffff;
            transform: scale(1.05);
        }

        @media (max-width: 768px) {
            .container {
                padding: 0 1rem;
            }

            .card-text {
                font-size: 1.5rem;
            }

            .card {
                margin-bottom: 1rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">Admin Panel</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="products.php">Productos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="orders.php">Pedidos</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Cerrar Sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1 class="mb-4">Panel de Control</h1>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Productos</h5>
                        <h2 class="card-text"><?php echo $totalProducts ?? 0; ?></h2>
                        <a href="products.php" class="btn btn-light mt-2">Ver Productos</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Pedidos</h5>
                        <h2 class="card-text"><?php echo $totalOrders ?? 0; ?></h2>
                        <a href="orders.php" class="btn btn-light mt-2">Ver Pedidos</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card bg-warning text-dark">
                    <div class="card-body">
                        <h5 class="card-title">Pedidos Pendientes</h5>
                        <h2 class="card-text"><?php echo $pendingOrders ?? 0; ?></h2>
                        <a href="orders.php?status=pending" class="btn btn-light mt-2">Ver Pendientes</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <h5 class="card-title">Stock Bajo</h5>
                        <h2 class="card-text"><?php echo $lowStockProducts ?? 0; ?></h2>
                        <a href="products.php?filter=low_stock" class="btn btn-light mt-2">Ver Productos</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>