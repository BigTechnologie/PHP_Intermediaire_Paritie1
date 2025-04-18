<?php
namespace App\HTML;

// Classe utilitaire pour générer des formulaires HTML avec gestion des valeurs et des erreurs
class Form {
    
    private $data; // Contient les données du formulaire (ex: $_POST ou un objet)

    private $errors; // Contient les messages d'erreurs liés à la validation des champs

    /**
     * Summary of __construct
     * @param mixed $data Données du formulaire (tableau ou objet)
     * @param array $errors Tableau associatif des erreurs par champ
     */
    public function __construct($data, array $errors)
    {
        $this->data = $data;
        $this->errors = $errors;
    }

    /**
    * Summary of getErrorFeedback
    * Génère le message d'erreur HTML (Bootstrap)
    * @param string $key
    * @return string
    */
    private function getErrorFeedback (string $key): string
    {
        if (isset($this->errors[$key])) {
            // Si plusieurs erreurs, on les affiche avec des <br>
            if (is_array($this->errors[$key])) {
                $error = implode('<br>', $this->errors[$key]);
            } else {
                $error = $this->errors[$key];
            }
            return '<div class="invalid-feedback">' . $error . '</div>';
        }
        return '';
    }

    /**
    * Retourne la classe CSS à appliquer au champ (ajoute is-invalid en cas d'erreur)
    * @param string $key
    * @return string
    */
    private function getInputClass (string $key): string 
    {
        $inputClass = 'form-control';
        if (isset($this->errors[$key])) {
            $inputClass .= ' is-invalid'; 
        }
        return $inputClass;
    }

    /**
     * Récupère la valeur du champ depuis $data (tableau ou objet)
     * @param string $key
     */
    private function getValue (string $key)
    {
        if (is_array($this->data)) {
            // Si $data est un tableau, on récupère la clé
            return $this->data[$key] ?? null; 
        }

        // Si $data est un objet, on tente d'appeler un getter (ex: getName pour name)
        $method = 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
        $value = $this->data->$method(); 

        // Si la valeur est une date, on la formate en chaîne
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }

        return $value;
    }


    /**
     * Génère un champ input HTML
     * @param string $key  Nom du champ
     * @param string $label Étiquette affichée
     * @return string Code HTML du champ
     */
    public function input (string $key, string $label): string
    {
        $value = $this->getValue($key); // Récupère la valeur pré-remplie du champ 
        $type = $key === "password" ? "password" : "text"; 
        return <<<HTML
          <div class="form-group">
            <label for="field{$key}">{$label}</label>
            <input type="{$type}" id="field{$key}" class="{$this->getInputClass($key)}" name="{$key}" value="{$value}" required>
            {$this->getErrorFeedback($key)}
        </div>
HTML;
    }

    /**
     * Génère un champ textarea HTML
     * @param string $key Nom du champ
     * @param string $label Étiquette affichée
     * @return string
     */
    public function file (string $key, string $label): string
    {
        return <<<HTML
          <div class="form-group">
            <label for="field{$key}">{$label}</label>
            <input type="file" id="field{$key}" class="{$this->getInputClass($key)}" name="{$key}">
            {$this->getErrorFeedback($key)}
        </div>
HTML;
    }

    /**
     * Génère un champ select avec sélection multiple
     * @param string $key
     * @param string $label
     * @return string
     */
    public function textarea (string $key, string $label): string 
    {
        // Pré-remplit la zone de texte avec la valeur correspondante
        $value = $this->getValue($key); 
        return <<<HTML
          <div class="form-group">
            <label for="field{$key}">{$label}</label>
            <textarea type="text" id="field{$key}" class="{$this->getInputClass($key)}" name="{$key}" required>{$value}</textarea>
            {$this->getErrorFeedback($key)}
        </div>
HTML;
    }







}