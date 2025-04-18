<?php
namespace App\Controller;

use App\Controller\Controller;
use App\Model\Category;

use \PDO;


final class CategoryController extends Controller {

    protected $table = "category";
    protected $class = Category::class;

    
    /**
     * @param \App\Model\Article[] $articles
     */
    public function hydrateArticles (array $articles): void
    {
        $articlesByID = [];
        foreach($articles as $article) {
            $article->setCategories([]);
            $articlesByID[$article->getID()] = $article;
        }
        $categories = $this->pdo
            ->query('SELECT c.*, ac.article_id
                    FROM article_category ac
                    JOIN category c ON c.id = ac.category_id
                    WHERE ac.article_id IN (' . implode(',', array_keys($articlesByID)) . ')'
            )->fetchAll(PDO::FETCH_CLASS, $this->class);

        foreach($categories as $category) {
            $articlesByID[$category->getArticleID()]->addCategory($category);
        }
    }

    public function all (): array
    {
        return $this->queryAndFetchAll("SELECT * FROM {$this->table} ORDER BY id DESC");
    }
  
    public function list (): array
    {
        $categories = $this->queryAndFetchAll("SELECT * FROM {$this->table} ORDER BY name ASC");
        $results = [];
        foreach($categories as $category) {
            $results[$category->getID()] = $category->getName();
        }
        return $results;
    }




}