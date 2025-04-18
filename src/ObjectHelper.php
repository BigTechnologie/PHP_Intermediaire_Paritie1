<?php

namespace App;

// Classe utilitaire qui permet d’hydrater dynamiquement un objet à partir d’un tableau de données
class ObjectHelper {

 
    public static function hydrate ($object, array $data, array $fields): void
     {
        // Parcourt tous les champs à hydrater
        foreach($fields as $field) {
            // Transforme le nom du champ en nom de méthode "setX". Exemple : 'first_name' devient 'setFirstName'
            $method = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $field)));

            // Appelle dynamiquement la méthode setter correspondante sur l’objet
            $object->$method($data[$field]);
        }
    }

}

