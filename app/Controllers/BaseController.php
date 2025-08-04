<?php

namespace App\Controllers;

abstract class BaseController
{
  // Method for loading the view
  protected function render(string $viewName, array $data = []): void
  {
    // Include header.php
    include_once '../app/Views/partials/header.php';

    // Verify if the requested view exists
    $viewFile = "../app/Views/{$viewName}.php";
    if (file_exists($viewFile)) {
      if (!empty($data)) extract($data);
      include_once $viewFile;
    } else {
      http_response_code(404);
      echo "Not found: {$viewName}.php";
    }

    // Include footer.php
    include_once '../app/Views/partials/footer.php';
  }

  // Verify if chat database exists
  protected function databaseExists(): bool
  {
    return file_exists(DB_PATH);
  }

  // Main method for the class
  abstract public function index(): void;
}
