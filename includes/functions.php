<?php

function formatPrice($price) {
    return number_format($price, 2, '.', ',') . ' ' . CURRENCY;
}

function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function generateOrderId() {
    return strtoupper(uniqid('ORD'));
}

function getProductById($conn, $id) {
    try {
        $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        return false;
    }
}

function updateStock($conn, $productId, $quantity) {
    try {
        $stmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?");
        return $stmt->execute([$quantity, $productId, $quantity]);
    } catch(PDOException $e) {
        return false;
    }
}

function createOrder($conn, $userId, $items, $total, $shippingAddress) {
    try {
        $conn->beginTransaction();
        
        $orderId = generateOrderId();
        $stmt = $conn->prepare("INSERT INTO orders (order_id, user_id, total, shipping_address, status) VALUES (?, ?, ?, ?, 'pending')");
        $stmt->execute([$orderId, $userId, $total, $shippingAddress]);
        
        $orderItemsStmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        foreach($items as $item) {
            $orderItemsStmt->execute([$orderId, $item['product_id'], $item['quantity'], $item['price']]);
            updateStock($conn, $item['product_id'], $item['quantity']);
        }
        
        $conn->commit();
        return $orderId;
    } catch(PDOException $e) {
        $conn->rollBack();
        return false;
    }
}

function getOrderDetails($conn, $orderId) {
    try {
        $stmt = $conn->prepare(
            "SELECT o.*, oi.product_id, oi.quantity, oi.price, p.name as product_name 
            FROM orders o 
            JOIN order_items oi ON o.order_id = oi.order_id 
            JOIN products p ON oi.product_id = p.id 
            WHERE o.order_id = ?"
        );
        $stmt->execute([$orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        return false;
    }
}

function updateOrderStatus($conn, $orderId, $status) {
    try {
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
        return $stmt->execute([$status, $orderId]);
    } catch(PDOException $e) {
        return false;
    }
}
?>