<?php

namespace Clara\app\controllers;

use Clara\core\Controller;

class Home extends Controller
{
  function index()
  {
    $this->view('home.index');
  }

  function test()
  {
    $this->response->back();
  }
}
