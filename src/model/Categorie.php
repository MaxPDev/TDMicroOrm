<?php

namespace hellokant\model;

/**
 * Class Categorie
 */

class Categorie extends Model {

    protected static $table = 'categorie';
    protected static $idColumn = 'id';

    public function articles()
    {
        return $this->has_many(Article::class, 'id_categ');
    }
    
}