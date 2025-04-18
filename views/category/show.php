<?php 

use App\Connection;
use App\Controller\ArticleController;
use App\Controller\CategoryController;

//Récuperation des parametres de l'URL(id,slug)
$id = (int)$params['id'];
$slug = $params['slug'];


$pdo = Connection::getPDO();

$category = (new CategoryController($pdo))->find($id);

// Redirection 301 si le slug de l'URL ne correspond pas à celui de la categorie
if($category->getSlug() !== $slug) {
    // Génère la bonne URL avec le bon slug et l'id
    $url = $router->url('category', ['slug' => $category->getSlug(), 'id' => $id]);
    http_response_code(301);
    header('Location: ' . $url);
}

$title = "Categorie {$category->getName()}";

// On recupère les articles liés à la categorie avec pagination. On recupère à la fois le tableau d'articles et un objet de pagination)
[$articles, $paginatedQuery] = (new ArticleController($pdo))->findPaginatedForCategory($category->getID());

// On génère un lien de base utilisé pour les liens de pagination
$link = $router->url('category', ['id' => $category->getID(), 'slug' => $category->getSlug()]);

?>

<h1><?= htmlentities($title) ?></h1>

<div class="row">
    <?php foreach($articles as $article): ?>
        <div class="col-md-3">
            <?php require dirname(__DIR__) . '/article/card.php' ?>
        </div>
    <?php endforeach ?>
</div>

<div class="d-flex justify-content-between my-4">
    <?= $paginatedQuery->previousLink($link); ?>
    <?= $paginatedQuery->nextLink($link); ?>
</div>