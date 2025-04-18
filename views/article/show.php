<?php 

use App\Connection;
use App\Controller\ArticleController;
use App\Controller\CategoryController;

//Récuperation des parametres de l'URL(id,slug)
$id = (int)$params['id'];
$slug = $params['slug'];


$pdo = Connection::getPDO();

$article = (new ArticleController($pdo))->find($id);

(new CategoryController($pdo))->hydrateArticles([$article]);

if($article->getSlug() !== $slug) {
    // Génère la bonne URL avec le bon slug et l'id
    $url = $router->url('article', ['slug' => $article->getSlug(), 'id' => $id]);
    http_response_code(301);
    header('Location: ' . $url);
}

?>

<h1><?= htmlentities($article->getName()) ?></h1>

<p><?= $article->getCreatedAt()->format('d F Y') ?></p>

<?php foreach($article->getCategories() as $k => $category):

    if($k > 0):
        echo ', '; // Ajoute une virgule entre les categories à partir de la deuxième
    endif;
    // On genère l'URL pour acceder à une categorie specifique
    $category_url = $router->url('category', ['id' => $category->getID(), 'slug' => $category->getSlug()]);
 ?>

    <!-- Affichage du nom de la categorie en tant que lien -->
    <a href="<?=  $category_url ?>"><?= htmlentities($category->getName()) ?></a>
 <?php endforeach ?>

 <!-- Affichage de l'image de l'article si elle existe -->
 <?php if($article->getImage()): ?>

    <p>
        <img src="<?= $article->getImageURL('large') ?>" style="width: 100%" alt="">
    </p>

<?php endif ?>

<p><?= $article->getFormattedContent() ?></p>






