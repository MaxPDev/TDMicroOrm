<?php

namespace hellokant\model;

/**
 * Class Categorie
 */

class Categorie extends Model {

    protected static $table = 'categorie';
    protected static $idColumn = 'id';

    /**
     * Appelle la fonction has_many de la classe abstraite model
     * afin de récupérer les models liés.
     */
    public function articles()
    {
        return $this->has_many(Article::class, 'id_categ');
    }
    
}