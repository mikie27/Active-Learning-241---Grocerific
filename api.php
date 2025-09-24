<?php
/**
 * API endpoints for Grocerific App
 * Handles CRUD operations for grocery items
 */

require_once 'config.php';

// Handle CORS for development
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Get request method and action
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {
    $pdo = getConnection();
    if (!$pdo) {
        sendJsonResponse(['error' => 'Database connection failed'], 500);
    }

    switch ($method) {
        case 'GET':
            handleGet($pdo, $action);
            break;
        case 'POST':
            handlePost($pdo, $action);
            break;
        case 'PUT':
            handlePut($pdo, $action);
            break;
        case 'DELETE':
            handleDelete($pdo, $action);
            break;
        default:
            sendJsonResponse(['error' => 'Method not allowed'], 405);
    }
} catch (Exception $e) {
    error_log("API Error: " . $e->getMessage());
    sendJsonResponse(['error' => 'Internal server error'], 500);
}

/**
 * Handle GET requests
 */
function handleGet($pdo, $action) {
    switch ($action) {
        case 'items':
            getAllItems($pdo);
            break;
        case 'item':
            getItem($pdo, $_GET['id'] ?? null);
            break;
        default:
            sendJsonResponse(['error' => 'Invalid action'], 400);
    }
}

/**
 * Handle POST requests
 */
function handlePost($pdo, $action) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    switch ($action) {
        case 'add':
            addItem($pdo, $input);
            break;
        default:
            sendJsonResponse(['error' => 'Invalid action'], 400);
    }
}

/**
 * Handle PUT requests
 */
function handlePut($pdo, $action) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    switch ($action) {
        case 'update':
            updateItem($pdo, $input);
            break;
        default:
            sendJsonResponse(['error' => 'Invalid action'], 400);
    }
}

/**
 * Handle DELETE requests
 */
function handleDelete($pdo, $action) {
    switch ($action) {
        case 'delete':
            deleteItem($pdo, $_GET['id'] ?? null);
            break;
        default:
            sendJsonResponse(['error' => 'Invalid action'], 400);
    }
}

/**
 * Get all grocery items
 */
function getAllItems($pdo) {
    $stmt = $pdo->prepare("SELECT * FROM grocery_items ORDER BY name ASC");
    $stmt->execute();
    $items = $stmt->fetchAll();
    sendJsonResponse(['success' => true, 'data' => $items]);
}

/**
 * Get single grocery item
 */
function getItem($pdo, $id) {
    if (!$id) {
        sendJsonResponse(['error' => 'Item ID is required'], 400);
    }
    
    $stmt = $pdo->prepare("SELECT * FROM grocery_items WHERE id = ?");
    $stmt->execute([$id]);
    $item = $stmt->fetch();
    
    if (!$item) {
        sendJsonResponse(['error' => 'Item not found'], 404);
    }
    
    sendJsonResponse(['success' => true, 'data' => $item]);
}

/**
 * Add new grocery item
 */
function addItem($pdo, $data) {
    // Validate required fields
    $required = ['name', 'category', 'quantity', 'price'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            sendJsonResponse(['error' => "Field '$field' is required"], 400);
        }
    }
    
    $stmt = $pdo->prepare("
        INSERT INTO grocery_items (name, category, quantity, price, description) 
        VALUES (?, ?, ?, ?, ?)
    ");
    
    $result = $stmt->execute([
        $data['name'],
        $data['category'],
        $data['quantity'],
        $data['price'],
        $data['description'] ?? ''
    ]);
    
    if ($result) {
        $id = $pdo->lastInsertId();
        sendJsonResponse(['success' => true, 'message' => 'Item added successfully', 'id' => $id]);
    } else {
        sendJsonResponse(['error' => 'Failed to add item'], 500);
    }
}

/**
 * Update grocery item
 */
function updateItem($pdo, $data) {
    if (empty($data['id'])) {
        sendJsonResponse(['error' => 'Item ID is required'], 400);
    }
    
    // Check if item exists
    $stmt = $pdo->prepare("SELECT id FROM grocery_items WHERE id = ?");
    $stmt->execute([$data['id']]);
    if (!$stmt->fetch()) {
        sendJsonResponse(['error' => 'Item not found'], 404);
    }
    
    // Validate required fields
    $required = ['name', 'category', 'quantity', 'price'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            sendJsonResponse(['error' => "Field '$field' is required"], 400);
        }
    }
    
    $stmt = $pdo->prepare("
        UPDATE grocery_items 
        SET name = ?, category = ?, quantity = ?, price = ?, description = ?
        WHERE id = ?
    ");
    
    $result = $stmt->execute([
        $data['name'],
        $data['category'],
        $data['quantity'],
        $data['price'],
        $data['description'] ?? '',
        $data['id']
    ]);
    
    if ($result) {
        sendJsonResponse(['success' => true, 'message' => 'Item updated successfully']);
    } else {
        sendJsonResponse(['error' => 'Failed to update item'], 500);
    }
}

/**
 * Delete grocery item
 */
function deleteItem($pdo, $id) {
    if (!$id) {
        sendJsonResponse(['error' => 'Item ID is required'], 400);
    }
    
    // Check if item exists
    $stmt = $pdo->prepare("SELECT id FROM grocery_items WHERE id = ?");
    $stmt->execute([$id]);
    if (!$stmt->fetch()) {
        sendJsonResponse(['error' => 'Item not found'], 404);
    }
    
    $stmt = $pdo->prepare("DELETE FROM grocery_items WHERE id = ?");
    $result = $stmt->execute([$id]);
    
    if ($result) {
        sendJsonResponse(['success' => true, 'message' => 'Item deleted successfully']);
    } else {
        sendJsonResponse(['error' => 'Failed to delete item'], 500);
    }
}
?>