<?php
/**
 * Base Controller Class
 * Core File
 */

require_once __DIR__ . '/Session.php';
require_once __DIR__ . '/Auth.php';

abstract class Controller {
    /**
     * Render HTML view page with optional data
     */
    protected function view($path, $data = [], $layout = 'main') {
        // Extract variables to be accessible in views
        extract($data);
        
        // Define base views directory
        $viewsDir = dirname(__DIR__) . '/app/views/';
        
        // Start output buffering
        ob_start();
        $viewFile = $viewsDir . $path . '.php';
        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            die("View file not found: {$path}");
        }
        $content = ob_get_clean();
        
        // Load layout if specified
        if ($layout) {
            $layoutFile = $viewsDir . 'layouts/' . $layout . '.php';
            if (file_exists($layoutFile)) {
                require $layoutFile;
            } else {
                echo $content; // fallback if layout not found
            }
        } else {
            echo $content;
        }
    }

    /**
     * Send secure JSON response (for AJAX queries)
     */
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }

    /**
     * Redirect helper
     */
    protected function redirect($url) {
        // Convert host-absolute routing back to relative when hosted inside subfolders
        if (strpos($url, '/index.php') === 0) {
            $url = substr($url, 1);
        }
        header("Location: " . $url);
        exit();
    }

    /**
     * Validate POST request security
     */
    protected function validatePostRequest() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die("Method Not Allowed");
        }

        // Validate CSRF
        $token = $_POST['csrf_token'] ?? '';
        if (!Session::validateCsrfToken($token)) {
            http_response_code(403);
            die("Akses ditolak: CSRF token tidak valid. Silakan muat ulang halaman.");
        }
    }

    /**
     * Helper to get request input value safely
     */
    protected function input($key, $default = '', $sanitize = true) {
        $val = $_POST[$key] ?? $_GET[$key] ?? $default;
        if ($sanitize && is_string($val)) {
            return htmlspecialchars(trim($val), ENT_QUOTES, 'UTF-8');
        }
        return $val;
    }
}
