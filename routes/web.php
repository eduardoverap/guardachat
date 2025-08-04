<?php
// Get the requested route
$route  = $_GET['route'] ?? 'home';  // Default route: 'home'

// Get controller name from route
$parts = explode('/', $route);
$controllerName = ucfirst(strtolower($parts[0])) . 'Controller';

// Load the matching controller
$controllerClass = "App\\Controllers\\$controllerName";

if (class_exists($controllerClass)) {
  $controller = new $controllerClass();

  // Execute 'index' method by default
  if (method_exists($controller, 'index')) {
    $controller->index();
  }
} else {
  // Method not found, so go to homepage
  http_response_code(404);
  goHome();
}
