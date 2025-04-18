<?php 

use App\Attachment\ArticleAttachment;
use App\Connection;
use App\Controller\ArticleController;
use App\Controller\CategoryController;
use App\HTML\Form;
use App\Model\Article;
use App\Validators\ArticleValidator;
use App\ObjectHelper;
use App\Auth;

Auth::check();

$errors = [];

//Création d'un nouvel objet Article (vide pour l'instant)
$article = new Article();
$pdo = Connection::getPDO();

// Recuperation des catégories disponibles via le controller
$categoryTable = new CategoryController($pdo);
$categories = $categoryTable->list();

// On defini la date de création de l'article au moment actuel
$article->setCreatedAt(date('Y-m-d H:i:s'));

if (!empty($_POST)) {

    $articleController = new ArticleController($pdo);

      // Fusionne les données issues du formulaire et des fichiers uploadés
      $data = array_merge($_POST, $_FILES);

         // Création d'un validateur pour vérifier les données
    $v = new ArticleValidator($data, $articleController,$categories, $article->getID());

    ObjectHelper::hydrate($article, $data, ['name', 'content', 'slug', 'created_at', 'image']);

    if($v->validate()) {

        $pdo->beginTransaction();
        ArticleAttachment::upload($article);
        $articleController->createArticle($article);
        $articleController->attachCategories($article->getID(), $_POST['categories_ids']);

        $pdo->commit();

        header('Location: ' . $router->url('admin_article', ['id' => $article->getID()]) . '?created=1');
        exit();

    } else {
        $errors = $v->errors();
    }
}

// On prépare le formulaire HTML avec les données de notre article et les erreurs
$form = new Form($article, $errors);

?>

<?php if(!empty($errors)): ?>
    <div class="alert alert-danger">
        L'article n'a pas pu être modifié, merci de corriger vos erreurs
    </div>
<?php endif ?>

<h1>Créer un article</h1>

<?php require('_form.php') ?>