<?php
namespace App\Validators;

use App\Controller\CategoryController;



class CategoryValidator extends AbstractValidator {

    /**
     * Summary of __construct
     * @param array $data les données du formulaire à valider
     * @param \App\Controller\CategoryController $table une instance de CategoryController (accès à la base pour vérification d’unicité)
     * @param mixed $id l’ID de la catégorie courante (utile pour ne pas déclencher d’erreur sur elle-même lors d'une modification)
     */
    public function __construct(array $data, CategoryController $table, ?int $id = null)
    {
        // Appelle le constructeur parent pour initialiser les propriétés et instancier Validator
        parent::__construct($data);

        // Les champs 'name' et 'slug' sont obligatoires
        $this->validator->rule('required', ['name', 'slug']); 

        // Leurs longueurs doivent être comprises entre 3 et 200 caractères
        $this->validator->rule('lengthBetween', ['name', 'slug'], 3, 200);

        // Le champ 'slug' doit correspondre au format d’un slug (ex : lettres, chiffres, tirets uniquement)
        $this->validator->rule('slug', 'slug');

        // Règle personnalisée : vérifier que le name et le slug ne sont pas déjà utilisés par une autre catégorie
        $this->validator->rule(function ($field, $value) use ($table, $id) {
            return !$table->exists($field, $value, $id); // Retourne false si une autre catégorie utilise déjà cette valeur
        }, ['slug', 'name'], 'Cette valeur est déjà utilisée'); // Champs concernés // Message d’erreur en cas d’échec
    }

}