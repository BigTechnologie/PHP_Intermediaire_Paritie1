<?php

use App\Connection;
use App\Controller\CategoryController;

$pdo = Connection::getPDO();
$table = new CategoryController($pdo);
$table->delete($params['id']);
header('Location: ' . $router->url('admin_categories') . '?delete=1');