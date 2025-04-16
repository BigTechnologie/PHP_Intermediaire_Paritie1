<?php 
namespace App;
class URL {

    /**
     *  Permet d'extraire un entier à partir des parametres de l'URL
     * @param mixed $name
     * @param mixed $default
     * @throws \Exception
     * @return int|null
     */
    public static function getInt($name, ?int $default = null): ?int
    {

        if(!isset($_GET[$name])) return $default;
        if($_GET[$name] === '0' ) return 0;

        //Verifier si la valeur du parametre est un entier valide
        if(!filter_var($_GET[$name], FILTER_VALIDATE_INT)) {
            throw new \Exception("Le parametre '$name' dans l'url n'est pas un entier");
        }

        // Dans la mésure ou tout est bon
        return (int)$_GET[$name];
    }

    /**
     * Summary of getPositiveInt
     * @param mixed $name
     * @param mixed $default
     * @throws \Exception
     * @return int|null
    */
    public static function getPositiveInt($name, ?int $default = null): ?int 
    {
        $param = self::getInt($name, $default);
        if($param !== null && $param <= 0) {
            throw new \Exception("Le parametre '$name' dans l'url n'est pas un entier positif");
        }
        return $param;
    }

}