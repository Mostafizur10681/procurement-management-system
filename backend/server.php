<?php
// Simple PHP API server for development
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Simple routing
$request_uri = $_SERVER['REQUEST_URI'];
$request_method = $_SERVER['REQUEST_METHOD'];

// Remove query string
$request_uri = explode('?', $request_uri)[0];

// Database connection (SQLite for simplicity)
try {
    $pdo = new PDO('sqlite:procurement.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create tables if they don't exist
    createTables($pdo);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

function createTables($pdo) {
    // Users table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL,
            role TEXT DEFAULT 'user',
            department TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Departments table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS departments (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            code TEXT UNIQUE NOT NULL,
            description TEXT,
            head_of_department TEXT,
            budget_limit DECIMAL(15,2) DEFAULT 0,
            is_active BOOLEAN DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Categories table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS categories (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            code TEXT UNIQUE NOT NULL,
            description TEXT,
            is_active BOOLEAN DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Vendors table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS vendors (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            contact_person TEXT,
            email TEXT,
            phone TEXT,
            address TEXT,
            tax_id TEXT,
            credit_limit DECIMAL(15,2) DEFAULT 0,
            status TEXT DEFAULT 'active',
            notes TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Procurement requests table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS procurement_requests (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            request_number TEXT UNIQUE NOT NULL,
            user_id INTEGER NOT NULL,
            department_id INTEGER NOT NULL,
            category_id INTEGER NOT NULL,
            title TEXT NOT NULL,
            description TEXT NOT NULL,
            estimated_cost DECIMAL(15,2) NOT NULL,
            priority TEXT DEFAULT 'medium',
            status TEXT DEFAULT 'draft',
            required_date DATE NOT NULL,
            justification TEXT,
            approved_by INTEGER,
            approved_at DATETIME,
            approval_notes TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id),
            FOREIGN KEY (department_id) REFERENCES departments(id),
            FOREIGN KEY (category_id) REFERENCES categories(id),
            FOREIGN KEY (approved_by) REFERENCES users(id)
        )
    ");
    
    // Insert sample data if tables are empty
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $userCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    if ($userCount == 0) {
        // Insert sample data
        $pdo->exec("INSERT INTO users (name, email, password, role, department) VALUES 
            ('Admin User', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'IT'),
            ('Manager User', 'manager@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'manager', 'Finance'),
            ('Regular User', 'user@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'Operations')");
            
        $pdo->exec("INSERT INTO departments (name, code, description, head_of_department, budget_limit) VALUES 
            ('IT Department', 'IT', 'Information Technology', 'Admin User', 50000.00),
            ('Finance', 'FIN', 'Finance Department', 'Manager User', 75000.00),
            ('Operations', 'OPS', 'Operations Department', 'Regular User', 60000.00)");
            
        $pdo->exec("INSERT INTO categories (name, code, description) VALUES 
            ('Hardware', 'HW', 'Computer hardware and equipment'),
            ('Software', 'SW', 'Software licenses and subscriptions'),
            ('Office Supplies', 'OS', 'Office supplies and stationery'),
            ('Services', 'SV', 'Professional services and consulting')");
            
        $pdo->exec("INSERT INTO vendors (name, contact_person, email, phone, address, status) VALUES 
            ('Tech Solutions Inc', 'John Doe', 'john@techsolutions.com', '555-0101', '123 Tech St, City', 'active'),
            ('Office Depot', 'Jane Smith', 'jane@officedepot.com', '555-0102', '456 Office Ave, City', 'active'),
            ('Software Co', 'Bob Johnson', 'bob@softwareco.com', '555-0103', '789 Software Blvd, City', 'active')");
    }
}

// API Routes
switch ($request_uri) {
    case '/api/login':
        if ($request_method === 'POST') {
            handleLogin($pdo);
        }
        break;
        
    case '/api/register':
        if ($request_method === 'POST') {
            handleRegister($pdo);
        }
        break;
        
    case '/api/user':
        if ($request_method === 'GET') {
            handleGetUser($pdo);
        }
        break;
        
    case '/api/procurement-requests':
        if ($request_method === 'GET') {
            handleGetProcurementRequests($pdo);
        } elseif ($request_method === 'POST') {
            handleCreateProcurementRequest($pdo);
        }
        break;
        
    case '/api/vendors':
        if ($request_method === 'GET') {
            handleGetVendors($pdo);
        } elseif ($request_method === 'POST') {
            handleCreateVendor($pdo);
        }
        break;
        
    case '/api/categories':
        if ($request_method === 'GET') {
            handleGetCategories($pdo);
        }
        break;
        
    case '/api/departments':
        if ($request_method === 'GET') {
            handleGetDepartments($pdo);
        }
        break;
        
    case '/api/reports/procurement-summary':
        if ($request_method === 'GET') {
            handleProcurementSummary($pdo);
        }
        break;
        
    case '/api/workflow-rules':
        if ($request_method === 'GET') {
            handleWorkflowRules();
        }
        break;
        
    case '/api/workflow-rules/committee-types':
        if ($request_method === 'GET') {
            handleCommitteeTypes();
        }
        break;
        
    case '/api/workflow-rules/statuses':
        if ($request_method === 'GET') {
            handleWorkflowStatuses();
        }
        break;
        
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint not found']);
        break;
}

// Handler functions
function handleLogin($pdo) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['email']) || !isset($data['password'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Email and password required']);
        return;
    }
    
    // For demo, accept any password with correct email
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$data['email']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        // Generate simple token (in production, use proper JWT)
        $token = base64_encode($user['id'] . ':' . time());
        echo json_encode([
            'user' => [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role'],
                'department' => $user['department']
            ],
            'token' => $token,
            'token_type' => 'Bearer'
        ]);
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid credentials']);
    }
}

function handleRegister($pdo) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['name']) || !isset($data['email']) || !isset($data['password'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Name, email, and password required']);
        return;
    }
    
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, department) VALUES (?, ?, ?, ?, ?)");
    try {
        $stmt->execute([
            $data['name'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['role'] ?? 'user',
            $data['department'] ?? ''
        ]);
        
        // Get the newly created user
        $userId = $pdo->lastInsertId();
        $stmt = $pdo->prepare("SELECT id, name, email, role, department, created_at FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Generate a simple token (in production, use proper JWT)
        $token = base64_encode($user['id'] . ':' . time());
        
        echo json_encode([
            'message' => 'User registered successfully',
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer'
        ]);
    } catch (PDOException $e) {
        http_response_code(400);
        echo json_encode(['error' => 'Email already exists']);
    }
}

function handleGetUser($pdo) {
    // Simple token validation (in production, use proper JWT validation)
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? '';
    
    if (strpos($authHeader, 'Bearer ') === 0) {
        $token = substr($authHeader, 7);
        $tokenData = explode(':', base64_decode($token));
        $userId = $tokenData[0] ?? null;
        
        if ($userId) {
            $stmt = $pdo->prepare("SELECT id, name, email, role, department FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                echo json_encode(['user' => $user]);
                return;
            }
        }
    }
    
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
}

function handleGetProcurementRequests($pdo) {
    $stmt = $pdo->query("
        SELECT pr.*, u.name as user_name, d.name as department_name, c.name as category_name 
        FROM procurement_requests pr
        LEFT JOIN users u ON pr.user_id = u.id
        LEFT JOIN departments d ON pr.department_id = d.id
        LEFT JOIN categories c ON pr.category_id = c.id
        ORDER BY pr.created_at DESC
    ");
    
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['data' => $requests, 'total' => count($requests)]);
}

function handleCreateProcurementRequest($pdo) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Generate request number
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM procurement_requests");
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    $requestNumber = 'PR-' . date('Y') . '-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    
    $stmt = $pdo->prepare("
        INSERT INTO procurement_requests 
        (request_number, user_id, department_id, category_id, title, description, estimated_cost, priority, status, required_date, justification)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    try {
        $stmt->execute([
            $requestNumber,
            $data['user_id'] ?? 1,
            $data['department_id'],
            $data['category_id'],
            $data['title'],
            $data['description'],
            $data['estimated_cost'],
            $data['priority'] ?? 'medium',
            $data['status'] ?? 'draft',
            $data['required_date'],
            $data['justification'] ?? null
        ]);
        
        echo json_encode(['message' => 'Procurement request created', 'request_number' => $requestNumber]);
    } catch (PDOException $e) {
        http_response_code(400);
        echo json_encode(['error' => 'Failed to create request: ' . $e->getMessage()]);
    }
}

function handleGetVendors($pdo) {
    $stmt = $pdo->query("SELECT * FROM vendors ORDER BY name");
    $vendors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['data' => $vendors, 'total' => count($vendors)]);
}

function handleCreateVendor($pdo) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $stmt = $pdo->prepare("
        INSERT INTO vendors (name, contact_person, email, phone, address, status, notes)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    
    try {
        $stmt->execute([
            $data['name'],
            $data['contact_person'] ?? null,
            $data['email'] ?? null,
            $data['phone'] ?? null,
            $data['address'] ?? null,
            $data['status'] ?? 'active',
            $data['notes'] ?? null
        ]);
        
        echo json_encode(['message' => 'Vendor created successfully']);
    } catch (PDOException $e) {
        http_response_code(400);
        echo json_encode(['error' => 'Failed to create vendor: ' . $e->getMessage()]);
    }
}

function handleGetCategories($pdo) {
    $stmt = $pdo->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY name");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($categories);
}

function handleGetDepartments($pdo) {
    $stmt = $pdo->query("SELECT * FROM departments ORDER BY name");
    $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($departments);
}

function handleProcurementSummary($pdo) {
    $stmt = $pdo->query("
        SELECT 
            COUNT(*) as total_requests,
            SUM(CASE WHEN status IN ('submitted', 'under_review') THEN 1 ELSE 0 END) as pending_requests,
            SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved_requests,
            SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected_requests,
            SUM(CASE WHEN status = 'approved' THEN estimated_cost ELSE 0 END) as total_value
        FROM procurement_requests
    ");
    
    $summary = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Priority distribution
    $stmt = $pdo->query("
        SELECT priority, COUNT(*) as count 
        FROM procurement_requests 
        GROUP BY priority
    ");
    $summary['requests_by_priority'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Department distribution
    $stmt = $pdo->query("
        SELECT d.name, COUNT(*) as count 
        FROM procurement_requests pr
        JOIN departments d ON pr.department_id = d.id
        GROUP BY d.name
    ");
    $summary['requests_by_department'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($summary);
}

function handleWorkflowRules() {
    // Load workflow configuration from config file
    $configPath = __DIR__ . '/config/procurement.php';
    if (file_exists($configPath)) {
        $config = include $configPath;
        echo json_encode([
            'workflowconfig' => $config['workflowconfig'] ?? [],
            'workflowconfigs' => $config['workflowconfigs'] ?? []
        ]);
    } else {
        // Fallback configuration
        echo json_encode([
            'workflowconfig' => [
                'Purchase Requisition' => 5,
                'Price Estimate' => 20,
                'RFQ' => 21,
                'Tender Approval' => 23,
                'Vendor Approve' => 22,
                'PO/WO Approval' => 11,
                'Pre Bill' => 25,
                'Post Bill' => 26,
                'Committee Approve' => 28,
                'Audit' => 40
            ],
            'workflowconfigs' => [
                '5' => 'Purchase Requisition',
                '20' => 'Price Estimate',
                '21' => 'RFQ',
                '23' => 'Tender Approval',
                '22' => 'Vendor Approve',
                '11' => 'PO/WO Approval',
                '25' => 'Pre Bill',
                '26' => 'Post Bill',
                '28' => 'Committee Approve',
                '40' => 'Audit'
            ]
        ]);
    }
}

function handleCommitteeTypes() {
    $configPath = __DIR__ . '/config/procurement.php';
    if (file_exists($configPath)) {
        $config = include $configPath;
        echo json_encode([
            'committee_type' => $config['committee_type'] ?? [],
            'committee_type_bn' => $config['committee_type_bn'] ?? []
        ]);
    } else {
        echo json_encode([
            'committee_type' => [
                'Price Estimate Committee' => 1,
                'Tender opening committee' => 2,
                'Tender Evaluation Committee' => 3,
                'Goods Receiving Committee' => 4,
                'Bill Scrutiny Committee' => 5
            ],
            'committee_type_bn' => []
        ]);
    }
}

function handleWorkflowStatuses() {
    $configPath = __DIR__ . '/config/procurement.php';
    if (file_exists($configPath)) {
        $config = include $configPath;
        echo json_encode([
            'statuses' => $config['status'] ?? []
        ]);
    } else {
        echo json_encode([
            'statuses' => [
                'approved' => 1,
                'pending' => 2,
                'rejected' => 3,
                'forwarded' => 5
            ]
        ]);
    }
}
?>
