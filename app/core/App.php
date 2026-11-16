<?php
/**
 * App Routing Engine (Router)
 * Parses URL query and routes execution to controller actions.
 * URL Format: /controller/action/param1/param2...
 */
class App {
    protected $currentController = 'AuthController';
    protected $currentMethod = 'index';
    protected $params = [];

    public function __construct() {
        $url = $this->getUrl();

        // Check if controller file exists
        if (isset($url[0])) {
            $controllerName = ucfirst($url[0]) . 'Controller';
            if (file_exists(APPROOT . '/controllers/' . $controllerName . '.php')) {
                $this->currentController = $controllerName;
                unset($url[0]);
            }
        }

        // Require the controller
        require_once APPROOT . '/controllers/' . $this->currentController . '.php';

        // Instantiate controller class
        $this->currentController = new $this->currentController;

        // Check for routing action (method)
        if (isset($url[1])) {
            if (method_exists($this->currentController, $url[1])) {
                $this->currentMethod = $url[1];
                unset($url[1]);
            }
        }

        // Capture remaining parts as parameters
        $this->params = $url ? array_values($url) : [];

        // Execute controller method with params
        call_user_func_array([$this->currentController, $this->currentMethod], $this->params);
    }

    /**
     * Get clean URL parameter array
     */
    public function getUrl() {
        if (isset($_GET['url']) && $_GET['url'] !== '') {
            $url = trim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            return explode('/', $url);
        }

        // Direct fallback from REQUEST_URI for web servers (Nginx / CloudPanel)
        if (!empty($_SERVER['REQUEST_URI'])) {
            $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            if (isset($_SERVER['SCRIPT_NAME'])) {
                $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
                if ($scriptDir !== '/' && $scriptDir !== '.' && !empty($scriptDir) && strpos($requestUri, $scriptDir) === 0) {
                    $requestUri = substr($requestUri, strlen($scriptDir));
                }
            }
            $requestUri = preg_replace('/^\/?index\.php/', '', $requestUri);
            $url = trim($requestUri, '/');
            if (!empty($url)) {
                $url = filter_var($url, FILTER_SANITIZE_URL);
                return explode('/', $url);
            }
        }

        return [];
    }
}
