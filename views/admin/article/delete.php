<?php 

use App\Attachment\ArticleAttachment;
use App\Connection;
use App\Controller\ArticleController;
use App\Auth;

Auth::check();

$pdo = Connection::getPDO();

$table = new ArticleController($pdo);

$article = $table->find($params['id']);

ArticleAttachment::detach($article);

$table->delete($params['id']);

// Redirection

header('Location: ' . $router->url('admin_articles') . '?delete=1');