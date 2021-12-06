<?php

namespace hellokant\model;

use hellokant\query\Query;

// Doit être abstract ??

abstract class Model {

    protected static $table;
    protected static $idColumn = 'id';
    
    public $_v = [];

    /**
     * Construit un objet Model
     * Prepare le tableau d'attributs
     */
    public function __construct(array $t = null) {
        if (!is_null($t)) $this->_v = $t;
    }

    /**
     * Fonction magique __get
     * Vérifie l'existence d'un attribut parmis les clé du tableau $_v
     * et le retourne si c'est le cas.
     * Après le parcours des clés, vérifie si une méthode de la classe correspond
     * au paramètre de la fonction et l'éxécute, pour permette un sucre synthaxique à l'utilisation
     */
    public function __get(string $attr_name) {
        if (array_key_exists($attr_name, $this->_v)) {
            return $this->_v[$attr_name];
        }
        if (method_exists($this, $attr_name))
            return $this->attr_name;

        throw new ModelException(get_called_class() . " Attribut or Method doesn't exist");
    }

    /**
     * fonction magique __set
     * Prends en paramètre le nom d'un attribut et sa valeur,
     * et la stock dans l'attribut de class $_v
     */
    public function __set(string $name, $val) : void {
        $this->_v[$name] = $val;
    }

    /**
     * Supprime la ligne de la table correspondant à l'objet instancié
     * en utilisant son id
     */
    public function delete() {

        $id_to_delete = isset($this->_v[static::$idColumn]) ? $this->_v[static::$idColumn] : null;

        if (is_null($id_to_delete)) {
            throw new ModelException(get_called_class() . " ID doesn't exists, can't delete");
        }

        if (is_null($this->_v[static::$idColumn])) {
            throw new ModelException("Non PK, can't delete");
        }
        return Query::table(static::$table)
                    ->where(static::$idColumn, '=', $id_to_delete)
                    ->delete();
    }

    /**
     * Insert une ligne dnas la base selon les attribut de l'objet en cours
     * L'id créé et récupérer, converti en entier et est mis à jour dans l'objet courant
     */
    public function insert() {
        // Insert les données, récupére la valeur de l'id auto incrémenté
        $lastInsertId = Query::table(static::$table)
                        ->insert($this->_v);
        
        // Stock l'id nouvellement créer dans le tableau d'attributs
        $this->_v[static::$idColumn] = (int)$lastInsertId;
        return $lastInsertId;
    }

    /**
     * Récupère l'ensemble des lignes de la table
     * Créer un objet du model (instancié depuis une classe concrète) pour chaque ligne
     * et le stock dans un tableau
     * Renvoie le tableau d'objet représentant les lignes de la table.
     */
    public static function all() : array {
        $all = Query::table(static::$table)->get();
        $return = [];
        foreach($all as $m) {
            $return[] = new static($m);
        }
        return $return;
    }

    /**
     * Récupère une ligne de la table en utilisant l'identifiant passé en parmètre.
     * Instancie depuis la classe concrète hérité de cette class et la retourne
     */
    public static function one(int $id) {
        
        $row = Query::table(static::$table)
                    ->where(static::$idColumn, '=', $id)
                    ->one();

        return new static($row);
    }

    /**
     * find() (corrigé)
     * Retourne les lignes sous forme d'un tableau d'objet instancié
     * de la class concrète de cette classe.
     * 
     * Si 1 paramètre : Si c'est un entier, c'est un id,
     *                  Si c'est un tableau, ce sont des conditions where
     * Si 2 paramètres : 1er param ; tableau de colonne
     *                   2em param  : entier ou tableau de where
     */
    public static function find($wheres, array $cols = ['*']) : array {
        $return = [];
    
        $find = Query::table(static::$table)
                    ->select($cols);

        if (gettype($wheres) == "integer" || gettype($wheres) == "string") {
            $find = $find->where(static::$idColumn, '=', $wheres);
        }

        if (gettype($wheres) == "array") {
            foreach ($wheres as $where) {
                $find = $find->where($where[0], $where[1], $where[2]);
            }
        }
       
       $find = $find->get();

       $return[] = new static($find[0]);
       return $return;
    }


    public static function first($wheres, array $cols = ['*']) : Model {
        $return = [];
    
        $find = Query::table(static::$table)
                    ->select($cols);

        if (gettype($wheres) == "integer" || gettype($wheres) == "string") {
            $find = $find->where(static::$idColumn, '=', $wheres);
        }

        if (gettype($wheres) == "array") {
            foreach ($wheres as $where) {
                $find = $find->where($where[0], $where[1], $where[2]);
            }
        }
       
       $find = $find->get()[0];

       $return[] = new static($find);
       return $return[0];
    }

    public function belongs_to($f_model_name, $fk_name) { 
        $f_table = Query::table($f_model_name::$table)
                        ->where($f_model_name::$idColumn, '=', $this->_v[$fk_name]) 
                        ->get()[0];  // !!!!!!!!! Résoudre ce 0;
        return(new $f_model_name($f_table));

    }

    public function has_many($f_model_name, $id_name) {
        $f_tables = Query::table($f_model_name::$table)
                         ->where($id_name, '=', $this->_v[static::$idColumn])
                         ->get();
        // var_dump($f_tables);
        $return = [];

        // intancié chaque objet pdo reçu avec la class Model associé
        foreach ($f_tables as $table) {
            $return[] = new $f_model_name($table);
        }

        return $return;
    }

    // public static function findOne(int $id): Model {
    //     $row = Query::table(static::$table)
    //                 ->where(static::$idColumn, '=', $id)
    //                 ->one();

    //     return new static($row);
    // }

}