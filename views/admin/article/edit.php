<?php

use App\Connection;
use App\Controller\ArticleController;
use App\Controller\CategoryController;
use App\ObjectHelper;
use App\Validators\ArticleValidator;


$pdo = Connection::getPDO();
$articleController = new ArticleController($pdo);
$categoryTable = new CategoryController($pdo);

$categories = $categoryTable->list();

$article = $articleController->find($params['id']);

$categoryTable->hydrateArticles([$article]);

$success = false;

$errors = [];


if (!empty($_POST)) {

    // Fusionne les données issues du formulaire et des fichiers uploadés
    $data = array_merge($_POST, $_FILES);

    // Création d'un validateur pour vérifier les données
    $v = new ArticleValidator($data, $articleController,$categories, $article->getID());

    ObjectHelper::hydrate($article, $data, ['name', 'content', 'slug', 'created_at', 'image']);

  



}