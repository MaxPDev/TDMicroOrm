<?php

namespace hellokant\query;

class Query {

    private $sqltable;
    private $fields = '*';
    private $where = null;
    private $args = [];
    private $sql = '';

    /**
     * Query constructor
     * @params string $table : nom de la table sur laquelle porte la requête
     */
    private function __construct(string $table) {
        $this->sqltable = $table;
    }

    /**
     * Retourne Objet Query
     */
    public static function table(string $table) : Query {
        $query = new Query($table);
        return $query;
    }

    public function select(array $fields): Query {
        $this->fields = implode(',', $fields);
        return $this;
    }

    // mixed only in php8
    public function where(string $col, string $op, $val) : Query {
        // $this->args[] = $col;
        // $this->args[] = $op;
        // $this->args[] = $val;

        // $this->args = implode(',', $this->args);

        if (!is_null($this->where)) {
            $this->where .= ' and ';
        }
        $this->where .= ' ' . $col . ' ' . $op . ' ? ';
        $this->args[]=$val;

        return $this;
    }

//     public static functin orWhere
        /**
     * if (!is_null($this->where)) $this->where .= ' or ';
     * $this->where .= ' ' . $col . ' ' . $op . ' ? ';
     * $this->args[]=$val;
     * return $this;
     */


      /* Query::table('client')->select(['nom', 'mail'])
                    ->where('ville', 'like', 'nancy')
                    ->where('age','=','12)
                    ->get()
                    */
    //public function get() : Array { // Array quand connextion à DB
    public function get() : string {
        $this->sql = 'select ' . $this->fields . // later : ?.
                     ' from ' . $this->sqltable;

        return $this->sql; //pour echo pour check

        // // if (!is_null($this->where))
        // //         $this->sql .= ' where ' . $this->where;

        // idem avec order, groupBy, si on veut

        // $stmt = $pdo->prepare($this->sql);
        // $stmt->execute($this->args);
        // return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        

    }

    
    public function insert(array $datas) : string {

        $atts = "";
        $vals = "";

        foreach ($datas as $attribut => $value) {

            if ($attribut !== array_key_last($datas)) {
                $atts .= $attribut . ', ';
                $vals .= $value . ', '; 
            } else {
                $atts .= $attribut;
                $vals .= $value ;
            }
        }

        $this->sql = 'insert into ' . $this->sqltable .
                    ' (' . $atts . ')' . ' values ' .
                    '(' . $vals . ');';

        return $this->sql;
        
        // INSERT INTO client (prenom, nom, ville, age)
        // VALUES
        // ('Rébecca', 'Armand', 'Saint-Didier-des-Bois', 24),
        // ('Aimée', 'Hebert', 'Marigny-le-Châtel', 36),
        // ('Marielle', 'Ribeiro', 'Maillères', 27),
        // ('Hilaire', 'Savary', 'Conie-Molitard', 58);
    }


}