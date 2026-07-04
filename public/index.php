<?php
/**
 * Front Controller
 * Sistem Monitoring dan Pengelolaan Inventaris Barang MENWA
 */

// Load Configuration and Constants
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/constants.php';

// Autoload Core Classes
require_once dirname(__DIR__) . '/core/Database.php';
require_once dirname(__DIR__) . '/core/Session.php';
require_once dirname(__DIR__) . '/core/Auth.php';
require_once dirname(__DIR__) . '/core/Controller.php';
require_once dirname(__DIR__) . '/core/Model.php';
require_once dirname(__DIR__) . '/core/Validator.php';
require_once dirname(__DIR__) . '/core/AuditLog.php';
require_once dirname(__DIR__) . '/core/Notification.php';

// Initialize Session
Session::start();

// Parse Controller and Action from Request URI or Query String
$controllerName = isset($_GET['controller']) ? trim($_GET['controller']) : 'home';
$actionName = isset($_GET['action']) ? trim($_GET['action']) : 'index';

// Clean Controller Name (e.g. auth -> AuthController)
$controllerClass = ucfirst($controllerName) . 'Controller';
$controllerFile = dirname(__DIR__) . '/app/controllers/' . $controllerClass . '.php';

// 404 Route Check
if (!file_exists($controllerFile)) {
    http_response_code(404);
    // Load 404 View
    require_once dirname(__DIR__) . '/app/views/layouts/404.php';
    exit();
}

// Load the controller file
require_once $controllerFile;

// Check if Controller class exists
if (!class_exists($controllerClass)) {
    http_response_code(404);
    require_once dirname(__DIR__) . '/app/views/layouts/404.php';
    exit();
}

// Instantiate Controller
$controllerInstance = new $controllerClass();

// Check if action method exists
if (!method_exists($controllerInstance, $actionName)) {
    http_response_code(404);
    require_once dirname(__DIR__) . '/app/views/layouts/404.php';
    exit();
}

// Execute Action
$controllerInstance->$actionName();
