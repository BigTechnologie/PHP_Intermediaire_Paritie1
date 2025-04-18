<?php

use App\Attachment\ArticleAttachment;
use App\Connection;
use App\Controller\ArticleController;
use App\Controller\CategoryController;
use App\HTML\Form;
use App\ObjectHelper;
use App\Validators\ArticleValidator;
use App\Auth;

Auth::check();


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

    // Si les données sont valides
    if($v->validate()) {

        $pdo->beginTransaction();
        ArticleAttachment::upload($article);

        $articleController->updateArticle($article);
        $articleController->attachCategories($article->getID(), $_POST['categories_ids']);
        $pdo->commit();

        $categoryTable->hydrateArticles([$article]);
        $success = true;

    } else {
        $errors = $v->errors();
    }
}

$form = new Form($article, $errors);

?>

<?php if($success): ?>
    <div class="alert alert-success">
        L'article a bien été modifié
    </div>
<?php endif ?>

<?php if(isset($_GET['created'])): ?>
    <div class="alert alert-success">
        L'article a bien été crée
    </div>
<?php endif ?>

<?php if(!empty($errors)): ?>
    <div class="alert alert-danger">
        L'article n'a pas pu être modifié, merci de corriger vos erreurs
    </div>
<?php endif ?>

<h1>Editer l'article <?= htmlentities($article->getName()) ?></h1>

<?php require('_form.php') ?>