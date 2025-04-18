<?php 

namespace App\Controller;

use App\Controller\Exception\NotFoundException;
use App\Model\User;
use App\Controller\Controller;

final class UserController extends Controller {

    protected $table = "user";
    protected $class = User::class;

    // Permet de recuperer un utilisateur à partir de son nom d'utilisateur
    public function findByUsername (string $username)
    {
        //On prepare notre requete SQL avec un parametre nommé :username pour eviter les injections SQL
        $query = $this->pdo->prepare('SELECT * FROM ' . $this->table . ' WHERE username = :username');

        $query->execute(['username' => $username]);

        $query->setFetchMode(\PDO::FETCH_CLASS, $this->class);

        //On recupère le premier et unique resultat de la requete

        $result = $query->fetch();

        //Si aucun resultat trouvé
        if($result === false) {
            throw new NotFoundException($this->table, $username);
        }

        // On retourne l'objet utilisateur trouvé en BDD
        return $result;




    }

} 