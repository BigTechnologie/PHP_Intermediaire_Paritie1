<?php 
namespace App\Controller;

use App\Model\Article;
use App\PaginatedQuery;
use App\Controller\Controller;


final class ArticleController extends Controller {

    protected $table = "article";
    protected $class = Article::class;

    public function updateArticle (Article $article): void 
    {
       
        $this->update([
            'name' => $article->getName(),
            'slug' => $article->getSlug(),
            'content' => $article->getContent(),
            'created_at' => $article->getCreatedAt()->format('Y-m-d H:i:s'),
            'image' => $article->getImage()
        ], $article->getID()); 
    }
    public function createArticle (Article $article): void 
    {
      
        $id = $this->create([
            'name' => $article->getName(),
            'slug' => $article->getSlug(),
            'content' => $article->getContent(),
            'image' => $article->getImage(),
            'created_at' => $article->getCreatedAt()->format('Y-m-d H:i:s')
        ]);
        $article->setID($id); 
    }

  
    public function attachCategories (int $id, array $categories) 
    {
        
        $this->pdo->exec('DELETE FROM article_category WHERE article_id = ' . $id);
        $query = $this->pdo->prepare('INSERT INTO article_category SET article_id = ?, category_id = ?');

        foreach($categories as $category) {
            $query->execute([$id, $category]);
        }
    }

    public function findPaginated () 
    {
        
        $paginatedQuery = new PaginatedQuery(
            "SELECT * FROM {$this->table} ORDER BY created_at DESC",
            "SELECT COUNT(id) FROM {$this->table}",
            $this->pdo
        );

        $articles = $paginatedQuery->getItems(Article::class);

       
        (new CategoryController($this->pdo))->hydrateArticles($articles);

        return [$articles, $paginatedQuery]; 
    }

    public function findPaginatedForCategory (int $categoryID) 
    {
        $paginatedQuery = new PaginatedQuery(
            "SELECT a.*
                FROM {$this->table} a 
                JOIN article_category ac ON ac.article_id = a.id
                WHERE ac.category_id = {$categoryID}
                ORDER BY created_at DESC",
            "SELECT COUNT(category_id) FROM article_category WHERE category_id = {$categoryID}"
        );

        $articles = $paginatedQuery->getItems(Article::class); 
        (new CategoryController($this->pdo))->hydrateArticles($articles);
        return [$articles, $paginatedQuery]; 
    }





}