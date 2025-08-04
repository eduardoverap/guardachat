<?php

namespace App\Controllers;

class AboutController extends BaseController{
  public function index(): void
  {
    $this->render('about');
  }
}
