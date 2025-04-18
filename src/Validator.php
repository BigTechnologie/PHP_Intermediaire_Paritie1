<?php
namespace App;

use finfo; 
use Valitron\Validator as ValitronValidator; // Import de la classe Validator de Valitron avec un alias


class Validator extends ValitronValidator { 

    // On force la langue utilisée par Valitron à être le français
    protected static $_lang = "fr";

    /**
    * Constructeur de la classe Validator
    * 
    * @param array $data   Les données à valider
    * @param array $fields Les champs spécifiques à valider
    * @param mixed $lang   (Optionnel) Langue personnalisée
    * @param mixed $langDir (Optionnel) Répertoire de langue personnalisé
    */
    public function __construct($data = array(), $fields = array(), $lang = null, $langDir = null) 
    {
        // Appel du constructeur parent (ValitronValidator) pour initialiser les données
        parent::__construct($data, $fields, $lang, $langDir); 

        // Ajout d'une règle personnalisée appelée "image"
        self::addRule('image', function($field, $value, array $params, array $fields) {
            // Si aucun fichier n'a été envoyé (taille = 0), on considère que la règle est validée
            if ($value['size'] === 0) {
                return true;
            }

            // Déclaration des types MIME autorisés pour les images
            $mimes = ['image/jpeg', 'image/png'];

            // Création d'une instance de finfo pour récupérer le type MIME réel du fichier
            $finfo = new finfo(); 

            // Récupération du type MIME à partir du fichier temporaire uploadé
            $info = $finfo->file($value['tmp_name'], FILEINFO_MIME_TYPE); 
           
            // On vérifie si le type MIME détecté est dans la liste des formats valides
            return in_array($info, $mimes); // Ici on n'a finalement notre valadation
        }, 'Le fichier n\'est pas une image valide'); // Message d’erreur si la règle échoue
    }

    
    /**
     * Méthode surchargée pour enlever le nom du champ dans les messages d'erreur
     * 
     * @param string $field   Le nom du champ
     * @param string $message Le message d'erreur original
     * @param array  $params  Les paramètres éventuels
     * @return string         Le message d'erreur sans le nom du champ
    */
    protected function checkAndSetLabel($field, $message, $params)
    {
        // Remplace la balise {field} dans le message par une chaîne vide
        return str_replace('{field}', '', $message);
    }


}