<?php
/**
 * Demo API for Grocerific App (works without database)
 * This version uses mock data to demonstrate functionality
 */

// Handle CORS for development
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Mock data storage in session
session_start();

// Initialize mock data if not exists
if (!isset($_SESSION['grocery_items'])) {
    $_SESSION['grocery_items'] = [
        [
            'id' => 1,
            'name' => 'Apples',
            'category' => 'Fruits',
            'quantity' => 10,
            'price' => '2.99',
            'description' => 'Fresh red apples',
            'created_at' => '2024-01-01 10:00:00',
            'updated_at' => '2024-01-01 10:00:00'
        ],
        [
            'id' => 2,
            'name' => 'Milk',
            'category' => 'Dairy',
            'quantity' => 5,
            'price' => '3.49',
            'description' => 'Whole milk - 1 gallon',
            'created_at' => '2024-01-01 10:05:00',
            'updated_at' => '2024-01-01 10:05:00'
        ],
        [
            'id' => 3,
            'name' => 'Bread',
            'category' => 'Bakery',
            'quantity' => 8,
            'price' => '2.49',
            'description' => 'Whole wheat bread',
            'created_at' => '2024-01-01 10:10:00',
            'updated_at' => '2024-01-01 10:10:00'
        ],
        [
            'id' => 4,
            'name' => 'Bananas',
            'category' => 'Fruits',
            'quantity' => 15,
            'price' => '1.99',
            'description' => 'Yellow bananas',
            'created_at' => '2024-01-01 10:15:00',
            'updated_at' => '2024-01-01 10:15:00'
        ],
        [
            'id' => 5,
            'name' => 'Chicken Breast',
            'category' => 'Meat',
            'quantity' => 3,
            'price' => '8.99',
            'description' => 'Boneless chicken breast',
            'created_at' => '2024-01-01 10:20:00',
            'updated_at' => '2024-01-01 10:20:00'
        ]
    ];
    $_SESSION['next_id'] = 6;
}

// Get request method and action
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {
    switch ($method) {
        case 'GET':
            handleGet($action);
            break;
        case 'POST':
            handlePost($action);
            break;
        case 'PUT':
            handlePut($action);
            break;
        case 'DELETE':
            handleDelete($action);
            break;
        default:
            sendJsonResponse(['error' => 'Method not allowed'], 405);
    }
} catch (Exception $e) {
    error_log("API Error: " . $e->getMessage());
    sendJsonResponse(['error' => 'Internal server error'], 500);
}

/**
 * Send JSON response
 */
function sendJsonResponse($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * Handle GET requests
 */
function handleGet($action) {
    switch ($action) {
        case 'items':
            getAllItems();
            break;
        case 'item':
            getItem($_GET['id'] ?? null);
            break;
        default:
            sendJsonResponse(['error' => 'Invalid action'], 400);
    }
}

/**
 * Handle POST requests
 */
function handlePost($action) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    switch ($action) {
        case 'add':
            addItem($input);
            break;
        default:
            sendJsonResponse(['error' => 'Invalid action'], 400);
    }
}

/**
 * Handle PUT requests
 */
function handlePut($action) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    switch ($action) {
        case 'update':
            updateItem($input);
            break;
        default:
            sendJsonResponse(['error' => 'Invalid action'], 400);
    }
}

/**
 * Handle DELETE requests
 */
function handleDelete($action) {
    switch ($action) {
        case 'delete':
            deleteItem($_GET['id'] ?? null);
            break;
        default:
            sendJsonResponse(['error' => 'Invalid action'], 400);
    }
}

/**
 * Get all grocery items
 */
function getAllItems() {
    $items = $_SESSION['grocery_items'];
    // Sort by name
    usort($items, function($a, $b) {
        return strcmp($a['name'], $b['name']);
    });
    sendJsonResponse(['success' => true, 'data' => $items]);
}

/**
 * Get single grocery item
 */
function getItem($id) {
    if (!$id) {
        sendJsonResponse(['error' => 'Item ID is required'], 400);
    }
    
    $items = $_SESSION['grocery_items'];
    $item = null;
    foreach ($items as $i) {
        if ($i['id'] == $id) {
            $item = $i;
            break;
        }
    }
    
    if (!$item) {
        sendJsonResponse(['error' => 'Item not found'], 404);
    }
    
    sendJsonResponse(['success' => true, 'data' => $item]);
}

/**
 * Add new grocery item
 */
function addItem($data) {
    // Validate required fields
    $required = ['name', 'category', 'quantity', 'price'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            sendJsonResponse(['error' => "Field '$field' is required"], 400);
        }
    }
    
    $newItem = [
        'id' => $_SESSION['next_id']++,
        'name' => $data['name'],
        'category' => $data['category'],
        'quantity' => (int)$data['quantity'],
        'price' => number_format((float)$data['price'], 2, '.', ''),
        'description' => $data['description'] ?? '',
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    $_SESSION['grocery_items'][] = $newItem;
    
    sendJsonResponse([
        'success' => true, 
        'message' => 'Item added successfully', 
        'id' => $newItem['id']
    ]);
}

/**
 * Update grocery item
 */
function updateItem($data) {
    if (empty($data['id'])) {
        sendJsonResponse(['error' => 'Item ID is required'], 400);
    }
    
    // Find item
    $items = &$_SESSION['grocery_items'];
    $itemIndex = -1;
    for ($i = 0; $i < count($items); $i++) {
        if ($items[$i]['id'] == $data['id']) {
            $itemIndex = $i;
            break;
        }
    }
    
    if ($itemIndex === -1) {
        sendJsonResponse(['error' => 'Item not found'], 404);
    }
    
    // Validate required fields
    $required = ['name', 'category', 'quantity', 'price'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            sendJsonResponse(['error' => "Field '$field' is required"], 400);
        }
    }
    
    // Update item
    $items[$itemIndex]['name'] = $data['name'];
    $items[$itemIndex]['category'] = $data['category'];
    $items[$itemIndex]['quantity'] = (int)$data['quantity'];
    $items[$itemIndex]['price'] = number_format((float)$data['price'], 2, '.', '');
    $items[$itemIndex]['description'] = $data['description'] ?? '';
    $items[$itemIndex]['updated_at'] = date('Y-m-d H:i:s');
    
    sendJsonResponse(['success' => true, 'message' => 'Item updated successfully']);
}

/**
 * Delete grocery item
 */
function deleteItem($id) {
    if (!$id) {
        sendJsonResponse(['error' => 'Item ID is required'], 400);
    }
    
    // Find and remove item
    $items = &$_SESSION['grocery_items'];
    $itemIndex = -1;
    for ($i = 0; $i < count($items); $i++) {
        if ($items[$i]['id'] == $id) {
            $itemIndex = $i;
            break;
        }
    }
    
    if ($itemIndex === -1) {
        sendJsonResponse(['error' => 'Item not found'], 404);
    }
    
    array_splice($items, $itemIndex, 1);
    
    sendJsonResponse(['success' => true, 'message' => 'Item deleted successfully']);
}
?>