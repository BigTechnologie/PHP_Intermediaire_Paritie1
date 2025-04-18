<?php

use App\Connection;
use App\Controller\CategoryController;
use App\Auth;

Auth::check();

$pdo = Connection::getPDO();
$table = new CategoryController($pdo);
$table->delete($params['id']);
header('Location: ' . $router->url('admin_categories') . '?delete=1');