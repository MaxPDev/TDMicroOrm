<?php

namespace hellokant\model;

/**
 * Class Article
 */

class Article extends Model {

    protected static $table = 'article';
    protected static $idColumn = 'id';

    /**
     * Appelle la fonction belongs_to de la classe abstraite model
     * afin de récupérer le model lié.
     */
    public function categorie() : Categorie
    {
        return $this->belongs_to(Categorie::class, 'id_categ');
    }
    
}