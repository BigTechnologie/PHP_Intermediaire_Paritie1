<?php
namespace App\Validators;

use App\Controller\ArticleController;

class ArticleValidator extends AbstractValidator {

    /**
     * Le constructeur prend :
     * @param array $data les données du formulaire à valider
     * @param \App\Controller\ArticleController $table une instance de ArticleController (accès à la base pour vérifier les doublons)
     * @param array $categories tableau de toutes les catégories possibles
     * @param mixed $articleID l'ID de l’article courant (null si c’est un nouvel article, utile pour exclure l'article courant en cas de modification)
     */
    public function __construct(array $data, ArticleController $table, array $categories, ?int $articleID = null)
    {
        // Appelle le constructeur parent pour initialiser $this->data et $this->validator
        parent::__construct($data);

        // Vérifie que les champs 'name' et 'slug' sont obligatoires
        $this->validator->rule('required', ['name', 'slug']);

        // Vérifie que les champs 'name' et 'slug' ont une longueur entre 3 et 200 caractères
        $this->validator->rule('lengthBetween', ['name', 'slug'], 3, 200);

        // Vérifie que le champ 'slug' respecte un format de type "slug" (ex : lettres, chiffres, tirets)
        $this->validator->rule('slug', 'slug');

        // Vérifie que les catégories sélectionnées (categories_ids) font partie de la liste autorisée (array_keys($categories))
        $this->validator->rule('subset', 'categories_ids', array_keys($categories));

        // Vérifie que le champ 'image' contient une image valide (si fourni)
        $this->validator->rule('image', 'image');

        // Règle personnalisée : vérifie que le slug et le name ne sont pas déjà utilisés dans un autre article
        $this->validator->rule(function ($field, $value) use ($table, $articleID) {
            return !$table->exists($field, $value, $articleID);
        }, ['slug', 'name'], 'Cette valeur est déjà utilisée');
    }
}


