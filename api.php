<?php
header('Content-Type: application/json');
require 'config.php';

// =========================
// CREATE PRODUCT
// =========================
function addProduct($pdo, $name, $price) {

    $sql = "INSERT INTO products_demo (name, price) VALUES (?, ?)";
    $stmt = $pdo->prepare($sql);

    if ($stmt->execute([$name, $price])) {
        return [
            'status' => 'success',
            'message' => 'Product added successfully',
            'id' => $pdo->lastInsertId()
        ];
    }

    return [
        'status' => 'error',
        'message' => 'Failed to add product'
    ];
}


// =========================
// UPDATE PRODUCT
// =========================
function updateProduct($pdo, $id, $name, $price) {

    $sql = "UPDATE products_demo SET name = ?, price = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);

    if ($stmt->execute([$name, $price, $id])) {
        return [
            'status' => 'success',
            'message' => 'Product updated successfully'
        ];
    }

    return [
        'status' => 'error',
        'message' => 'Failed to update product'
    ];
}


// =========================
// DELETE PRODUCT
// =========================
function deleteProduct($pdo, $id) {

    $sql = "DELETE FROM products_demo WHERE id = ?";
    $stmt = $pdo->prepare($sql);

    if ($stmt->execute([$id])) {
        return [
            'status' => 'success',
            'message' => 'Product deleted successfully'
        ];
    }

    return [
        'status' => 'error',
        'message' => 'Failed to delete product'
    ];
}


// =========================
// GET ALL PRODUCTS
// =========================
function getAllProducts($pdo) {

    $sql = "SELECT * FROM products_demo ORDER BY id DESC";
    $stmt = $pdo->query($sql);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($products) {
        return [
            'status' => 'success',
            'products' => $products
        ];
    }

    return [
        'status' => 'success',
        'products' => []
    ];
}


// =========================
// REQUEST HANDLER
// =========================
function handleRequest($pdo) {

    $method = $_SERVER['REQUEST_METHOD'];

    switch ($method) {

        // =====================
        // READ
        // =====================
        case 'GET':
            echo json_encode(getAllProducts($pdo));
            break;


        // =====================
        // CREATE
        // =====================
        case 'POST':

            $action = $_POST['action'] ?? '';

            if ($action === 'add') {

                $name = $_POST['name'] ?? '';
                $price = $_POST['price'] ?? 0;

                echo json_encode(
                    addProduct($pdo, $name, $price)
                );
            }
            break;


        // =====================
        // UPDATE
        // =====================
        case 'PUT':

            parse_str(file_get_contents("php://input"), $_PUT);

            $id = $_PUT['id'] ?? null;
            $name = $_PUT['name'] ?? '';
            $price = $_PUT['price'] ?? 0;

            echo json_encode(
                updateProduct($pdo, $id, $name, $price)
            );
            break;


        // =====================
        // DELETE
        // =====================
        case 'DELETE':

            parse_str(file_get_contents("php://input"), $_DELETE);

            $id = $_DELETE['id'] ?? null;

            echo json_encode(
                deleteProduct($pdo, $id)
            );
            break;


        default:
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid request method'
            ]);
            break;
    }
}


// =========================
// RUN
// =========================
handleRequest($pdo);
?>
