<?php

namespace Clara\app\controllers;

use Clara\core\Controller;

class _404 extends Controller
{
  function index()
  {
    $this->setStatus(404);
    $this->view('_404.index');
  }
}
