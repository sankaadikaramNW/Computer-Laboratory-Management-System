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
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            return explode('/', $url);
        }
        return [];
    }
}
