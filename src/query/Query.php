<?php

namespace hellokant\query;

use hellokant\connection\ConnectionFactory;
// use hellokant\connection\ConnectionFactoryTry as ConnectionFactory;

class Query {

    private $sqltable;
    private $fields = '*'; // par défaut, select *
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
     * Récupère le nom de la table en paramètre, l'utilise pour créer un objet Query
     * Retourne l'objet instancié
     */
    public static function table(string $table) : Query {
        $query = new Query($table);
        return $query;
    }

    /**
     * Récupère en pramètre un tableau contenant une liste de colonnes de colonnes de table.
     * La stock dans l'attribut $field, pour composer la requête grâce à une autre méthode.
     * Par défaut, $field  contient *, c'est à dire toutes les collonnes.
     * Retourne l'objet Query courant, pour permmettre le chainage des méthodes avec ->
     */
    public function select(array $fields): Query {
        $this->fields = implode(',', $fields);
        return $this;
    }


    /**
     * Récupère en paramètre une  ou plusieurs condition pour écrire un where :
     * une colonne, un opérateur et une valeure.
     * Stock la condition dans l'attribus $where et y modifie la valeur par ? pour le bindparam (éviter injection)
     * Stock la ou les valeurs dans l'attribut $args
     */
    public function where(string $col, string $op, $val) : Query {
        // mixed only in php8

        if (!is_null($this->where)) {
            $this->where .= ' and ';
        }
        $this->where .= ' ' . $col . ' ' . $op . ' ? ';
        $this->args[] = $val;

        return $this;
    }


    /**
     * Récupère en paramètre une ou plusieurs condition alternatives à une ou des conditions where :
     * une colonne, un opérateur et une valeure.
     * Stock la condition dans l'attribus $where et y modifie la valeur par ? pour le bindparam (éviter injection)
     * Stock la ou les valeurs dans l'attribut $args
     */
    public function orWhere(string $col, string $op, $val) : Query {
        if (!is_null($this->where)) {
            $this->where .= ' or ';
        }
        $this->where .= ' ' . $col . ' ' . $op . ' ? ';
        $this->args[]=$val;

        return $this;
    }

      /* Query::table('client')->select(['nom', 'mail'])
                    ->where('ville', 'like', 'nancy')
                    ->where('age','=','12)
                    ->get()
        */
    
    /**
     * Utilise les attribut $field, $sqltable et $where pour composer la requête
     * Récupère la connexion pdo vers la base de donnée
     * Lance la préparation de la requête (ici exécuter par le serveur)
     * Exécuter la requête
     * Récupère et retourne le résultat sous form d'un tableau de ligneq de table
     */        
    public function get() : array {
        $this->sql = 'select ' . $this->fields . // later : ?.
                     ' from ' . $this->sqltable;

        if (!is_null($this->where)) {
            $this->sql .= ' where ' . $this->where;
        }

        // return $this->sql; //pour echo pour check

        $pdo = ConnectionFactory::getConnection();

        // idem avec order, groupBy, si on veut

        $stmt = $pdo->prepare($this->sql);
        // var_dump($stmt);
        // var_dump($this->args);
        $stmt->execute($this->args);
        // var_dump($stmt);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
    }

    /**
     * Utilise les attribut $field, $sqltable et $where pour composer la requête
     * Récupère la connexion pdo vers la base de donnée
     * Lance la préparation de la requête (ici exécuter par le serveur)
     * Exécuter la requête
     * Récupère et retourne le résultat d'une ligne de table sous form d'un tableau.
     */ 
    public function one() {
        $this->sql = 'select ' . $this->fields . 
                ' from ' . $this->sqltable;

        if (!is_null($this->where)) {
            $this->sql .= ' where ' . $this->where;
        }

        $pdo = ConnectionFactory::getConnection();

        $stmt = $pdo->prepare($this->sql);
        $stmt->execute($this->args);
        // var_dump($stmt);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Récupère en paramètre un tableau contenant des colonnes en clés,
     * et des valeurs en valeur.
     * Compose la requête d'insertion
     * Récupère la connection pdo
     * Executer la recquête
     * Récupère en retour de la base de donnée l'id de la dernière ligne insérée
     * et le retourne.
     */
    public function insert(array $datas) : string {

        $atts = "";
        $vals = "";

        foreach ($datas as $attribut => $value) {

            if ($attribut !== array_key_last($datas)) {
                $atts .= $attribut . ', ';
                $vals .= ' ? ' . ', '; 
                $this->args[] = $value;
            } else {
                $atts .= $attribut;
                $vals .= ' ? ' ;
                $this->args[] = $value;
            }
        }

        $this->sql = 'insert into ' . $this->sqltable .
                    ' (' . $atts . ')' . ' values ' .
                    '(' . $vals . ');';

        $pdo = ConnectionFactory::getConnection();
        $stmt = $pdo->prepare($this->sql);
        // var_dump($stmt->debugDumpParams());
        $stmt->execute($this->args);

        return $pdo->lastInsertId();


        // // Autre possiblité. Plus optimisée ?

        // $pdo = ConnectionFactory::connection();
        // $this->sql = "INSERT INTO ".$this->sqltable." (".implode(",", array_keys($fields)).") VALUES (".str_repeat("?,", (count($fields)-1))."?)";
        // $stmt = $pdo->prepare($this->sql);
        // $stmt->execute(array_values($fields));
        // return $pdo->lastInsertId();
        
    }

    /**
     * Construit la réquête de suppression en utilisant le nom de la table stockée
     * dans l'attribut $sqltable, et la condition where stockée dans l'attribut $where.
     * Récupère la connexion pdo
     * Exécute la requête de suppression
     * Récupère de la base de donnée le nombre de lignes supprimées,
     * et le retourne 
     */
    public function delete() {
        // DELETE FROM `table`
        // WHERE condition
        
        $this->sql = 'delete from ' . $this->sqltable .
                    ' where ' . $this->where;

        // return $this->sql;
        
        $pdo = ConnectionFactory::getConnection();
        $stmt = $pdo->prepare($this->sql);
        // var_dump($stmt->debugDumpParams());
        $stmt->execute($this->args);
        return $stmt->rowCOunt();
    }



}