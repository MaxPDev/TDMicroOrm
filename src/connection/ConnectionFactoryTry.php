<?php

namespace hellokant\connection;

use \PDO;

class ConnectionFactoryTry {

    private static $db;


    public static function makeConnection(array $conf) {
        $params = array(
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES=>false, 
            PDO::ATTR_STRINGIFY_FETCHES=>false
        );
        
        // Data source name
        $server = $conf["serveur"];
        $dbname = $conf["database"];
        $dsn="mysql:host=$server;dbname=$dbname";
        $db = new \PDO($dsn, $conf["username"], $conf["password"], $params);
        var_dump($conf);
        var_dump($db);
        echo "HERE";
        return self::$db;
    }

    public static function getConnection() {
        if(!isset(self::$db)) {
            var_dump(self::$db);
            echo "No connection";
            return false;
        } else {
            return self::$db;
        };
    }
}

// FINIR TD2, MODEL
// PUIS MONGO DB