<?php
namespace App\Validators;

use App\Validator; 

// Déclaration d'une classe abstraite : elle ne peut pas être instanciée directement
abstract class AbstractValidator {

    protected $data; // Les données à valider (généralement issues d’un formulaire)
    protected $validator; // Instance de la classe Validator qui contient la logique de validation

    public function __construct(array $data)
    {
        $this->data = $data; // Enregistre les données dans l’objet
        $this->validator = new Validator($data); // Crée une instance de Validator avec ces données
    }

    // Méthode publique pour lancer la validation
    public function validate (): bool 
    {
        return $this->validator->validate();
    }

    // Méthode publique pour récupérer les erreurs après validation
    public function errors (): array 
    {
        return $this->validator->errors();
    }

}