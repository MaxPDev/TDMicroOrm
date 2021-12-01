<?php

namespace hellokant\model;

use hellokant\query\Query;

// Doit être abstract ??

class Model {

    protected static $table;
    protected static $idColumn = 'id';
    
    public $_v = [];

    public function __construct(array $t = null) {
        if (!is_null($t)) $this->_v = $t;
    }

    public function __get(string $attr_name) {
        if (array_key_exists($attr_name, $this->_v)) {
            return $this->_v[$attr_name];
        }
        if (method_exists($this, $attr_name))
            return $this->attr_name;
    }

    public function __set(string $name, $val) : void {
        $this->_v[$name] = $val;
    }

    public function delete() {
        if (is_null($this->_v[static::$idColumn])) {
            throw new ModelException("Non PK, can't delete");
        }
        return Query::table(static::$table)
                    ->where(static::$idColumn, '=', $this->_v[static::$idColumn])
                    ->delete();
    }

    public function insert() {
        // Insert les données, récupére la valeur de l'id auto incrémenté
        $lastInsertId = Query::table(static::$table)
                        ->insert($this->_v);
        
        // Stock l'id nouvellement créer dans le tableau d'attributs
        $this->_v[static::$idColumn] = (int)$lastInsertId;
        return $lastInsertId;
    }

    public static function all() : array {
        $all = Query::table(static::$table)->get();
        $return = [];
        foreach($all as $m) {
            $return[] = new static($m);
        }
        return $return;
    }

    public static function one(int $id) {
        
        $row = Query::table(static::$table)
                    ->where(static::$idColumn, '=', $id)
                    ->one();

        return new static($row);
    }

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