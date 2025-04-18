<?php

use App\Connection;
use App\Controller\CategoryController;
use App\HTML\Form;
use App\Validators\CategoryValidator;
use App\ObjectHelper;
use App\Model\Category;


$errors = [];
$item = new Category();

if (!empty($_POST)) {
    $pdo = Connection::getPDO();
    $table = new CategoryController($pdo);
    
    $v = new CategoryValidator($_POST, $table);
    ObjectHelper::hydrate($item, $_POST, ['name', 'slug']);
    if ($v->validate()) {
        $table->create([
            'name' => $item->getName(),
            'slug' => $item->getSlug()
        ]);
        header('Location: ' . $router->url('admin_categories') . '?created=1');
        exit();
    } else {
        $errors = $v->errors();
    }
}
$form = new Form($item, $errors);
?>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger">
    La catégorie n'a pas pu être entregistrée, merci de corriger vos erreurs
</div>
<?php endif ?>

<h1>Créer une catégorie</h1>

<?php require('_form.php') ?>