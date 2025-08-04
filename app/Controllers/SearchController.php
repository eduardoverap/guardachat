<?php

namespace App\Controllers;

use App\Models\FTS;

class SearchController extends BaseController
{
  public function index(): void
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['query'])) {
      $query = htmlspecialchars($_POST['query']);
      $model = new FTS();
      $searchResults = $model->search($query);
      if (is_null($searchResults)) {
        echo 'Nothing to show. Try again.';
      } else {
        echo $searchResults;
      }
    } else {
      $this->render('search');
    }
  }
}
