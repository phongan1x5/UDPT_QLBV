<?php
class BaseController
{
    protected $db;

    public function __construct()
    {
        // $database = new Database();
        // $this->db = $database->getConnection();
    }

    protected function render($view, $data = [])
    {
        // Extract data to variables
        extract($data);

        // Include the view file
        $viewPath = __DIR__ . '/../views/' . $view . '.php';
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo "View not found: " . $view;
        }
    }

    protected function redirect($url)
    {
        // Get base path
        $basePath = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
        $fullUrl = rtrim($basePath, '/') . '/' . ltrim($url, '/');
        header("Location: " . $fullUrl);
        exit();
    }

    protected function isLoggedIn()
    {
        return isset($_SESSION['user']) && isset($_SESSION['user']['token']);
    }

    protected function requireLogin()
    {
        if (!$this->isLoggedIn()) {
            $this->redirect('login');
        }
    }

    protected function isAdmin()
    {
        return isset($_SESSION['user']['user_role']) && $_SESSION['user']['user_role'] === 'admin';
    }
}
