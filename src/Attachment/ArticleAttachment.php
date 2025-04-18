<?php
namespace App\Attachment;

use Intervention\Image\ImageManager; // Importation du gestionnaire d'images Intervention
use App\Model\Article; // Importation du modèle Article (objet contenant les données de l'article)

/**
 * Classe responsable de gérer l'upload et la suppression d'images liées aux articles.
*/
class ArticleAttachment {

    // Chemin de base vers le répertoire "uploads"
    const UPLOAD_PATH = __DIR__ . '/../../public/uploads'; // Modif

    // Chemin spécifique pour les images d'articles dans post
    const DIRECTORY = self::UPLOAD_PATH . DIRECTORY_SEPARATOR . 'posts'; // Modif

    /**
     * Upload les images d’un article dans deux formats (small et large).
    */
    public static function upload(Article $article) // Modif
    {
        // Récupération de l'image temporaire (uploadée par formulaire)
        $image = $article->getImage();
        // Si aucune image ou pas besoin de téléverser, on quitte la méthode
        if (empty($image) || $article->shouldUpload() === false) {
            return;
        }

        // Création du répertoire s'il n'existe pas déjà
        $directory = self::DIRECTORY;
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true); // Permissions totales, récursif // 0777:
        }

        // Suppression des anciennes images si présentes
        if (!empty($article->getOldImage())) {
            $formats = ['small', 'large'];
            foreach ($formats as $format) {
                $oldFile = $directory . DIRECTORY_SEPARATOR . $article->getOldImage() . '_' . $format . '.jpg';
                if (file_exists($oldFile)) {
                    unlink($oldFile); // Suppression de l'ancien fichier
                }
            }
        }

        // Génération d'un nom de fichier unique
        $filename = uniqid("", true);

        // Initialisation du gestionnaire d'images avec le driver GD
        $manager = new ImageManager(['driver' => 'gd']);

        // Création de la version "small" (recadrée 350x200)
        $manager
            ->make($image)
            ->fit(350, 200) // Recadrage exact
            ->save($directory . DIRECTORY_SEPARATOR . $filename . '_small.jpg');

        // Création de la version "large" (redimensionnée à 1280px de large max, hauteur auto)
        $manager
            ->make($image)
            ->resize(1280, null, function ($constraint) {
                $constraint->aspectRatio(); // Garde les proportions
            })
            ->save($directory . DIRECTORY_SEPARATOR . $filename . '_large.jpg');

        // Mise à jour de l'objet Article avec le nouveau nom de fichier
        $article->setImage($filename);
    }

    /**
     * Supprime les images associées à un article (formats small et large).
    */
    public static function detach(Article $article) 
    {
        // Si une image est présente, on tente de supprimer les deux formats
        if (!empty($article->getImage())) {
            $formats = ['small', 'large'];
            foreach ($formats as $format) {
                $file = self::DIRECTORY . DIRECTORY_SEPARATOR . $article->getImage() . '_' . $format . '.jpg';
                if (file_exists($file)) {
                    unlink($file); // Suppression du fichier
                }
            }
        }
    }
}
