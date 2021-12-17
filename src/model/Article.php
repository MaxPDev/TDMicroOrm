<?php

namespace hellokant\model;

/**
 * Class Article
 */

class Article extends Model {

    protected static $table = 'article';
    protected static $idColumn = 'id';

    public function categorie() : Categorie
    {
        return $this->belongs_to(Categorie::class, 'id_categ');
    }
    
}