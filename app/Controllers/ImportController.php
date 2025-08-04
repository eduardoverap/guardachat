<?php

namespace App\Controllers;

class ImportController extends BaseController
{
  public function index(): void
  {
    if ($this->databaseExists()) {
      $button = "<input id=\"btn-import\" type=\"button\" value=\"Reset database\" title=\"Recreate your database from scratch. WARNING: It will delete your custom data!\" />";
    } else {
      $button = "<input id=\"btn-import\" type=\"button\" value=\"Import my chats\" title=\"Build a chat database from your JSON ChatGPT files.\" />";
    };
    $data = ['button' => $button];
    $this->render('import', $data);
  }
}
