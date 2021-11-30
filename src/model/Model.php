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

    public function __get(string $name) {
        if (array_key_exists($name, $this->_v))
            return $this->_v[$name];
    }

    public function __set(string $name, $val) : void {
        $this->_v[$name] = $val;
    }

    public function delete() {
        //
        return Query::table(static::$table)
                    ->where(static::$idColumn, '=', $this->_v[static::$idColumn])
                    ->delete();
    }

    public function insert() {
        var_dump(static::$table);
        var_dump(static::$idColumn);
        var_dump($this->_v);

        // Insert les données, récupére la valeur de l'id auto incrémenté
        $lastInsertId = Query::table(static::$table)
                        ->insert($this->_v);
        
        // Stock l'id nouvellement créer dans le tableau d'attributs
        $this->_v[static::$idColumn] = $lastInsertId;

        var_dump($this->_v);
        return $lastInsertId;
    }

    public static function all() : array {
        $all = QUery::table(static::$table)->get();
        $return = [];
        foreach($all as $m) {
            $return[] = new static($m);
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