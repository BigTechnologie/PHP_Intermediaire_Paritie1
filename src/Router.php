<?php
namespace App;

use AltoRouter;
use App\Security\ForbidenException;

class Router {

    /**
     * @var string
     */
    private $viewPath; 

    /**
     * @var AltoRouter
     */
    private $router; 

    public function __construct(string $viewPath) 
    {
        $this->viewPath = $viewPath; 
        $this->router = new AltoRouter(); 
    }

    public function get(string $url, string $view, ?string $name = null): self 
    {
        $this->router->map('GET', $url, $view, $name); 

        return $this; 
    }

    public function post(string $url, string $view, ?string $name = null): self
    {
        $this->router->map('POST', $url, $view, $name); // var_dump de map et toutes les autres

        return $this;
    }

    public function match(string $url, string $view, ?string $name = null): self
    {
        $this->router->map('POST|GET', $url, $view, $name);

        return $this;
    }

    public function url (string $name, array $params = []) 
     {
        return $this->router->generate($name, $params);
    }

    public function run(): self
    {
        $match = $this->router->match(); 
        //var_dump($match);
        if ($match === false) {
            $view = 'e404'; 
            $params = []; 
        } else {
            $view = $match['target'] ?: 'e404'; 
            $params = $match['params']; 
        }
    
        $router = $this;

        $isAdmin = strpos($view, 'admin/') !== false;

        $layout = $isAdmin ? 'admin/layouts/default' : 'layouts/default';

        // Construction du chemin complet vers le fichier de la vue à inclure
        $viewFile = $this->viewPath . DIRECTORY_SEPARATOR . $view . '.php';
        //
        try {
            if(!file_exists($viewFile)) {
                $view = 'e404';
                $viewFile = $this->viewPath . DIRECTORY_SEPARATOR . $view . '.php';

                if(!file_exists($viewFile)) {
                    throw new \Exception("La vue 'e404' est introuvable");
                }
            }

            // Demarre la temporisation de sortie qui permet de capturer le contenu de la vue
            ob_start();
            require $viewFile;
            $content = ob_get_clean();

            require $this->viewPath . DIRECTORY_SEPARATOR . $layout . '.php';

        } catch (ForbidenException $e) {
            header('Location: ' .$this->url('login') . '?forbidden=1');
            exit();
        } catch(\Exception $e) {

            echo "Une erreur s'est produite : " . htmlentities($e->getMessage());
        }

        return $this;

    }

    



}