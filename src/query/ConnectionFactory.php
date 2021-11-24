<?php

namespace hellokant\query;

class ConnectionFactory {

    private static $db;

    public static function makeConnection(array $conf) {
        $params = array(
            PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES=>false, // comprendre !
            PDO::ATTR_STRINGIFY_FETCHES=>false
        );
        
        // Data source name
        $dsn="mysql:host=$conf[serveur];dbname=$conf[database]";
        $db = new \PDO($dsn, $conf[user], $conf[pass], $params);
        return self::$db;
    }

    public static function getConnection() {
        echo "getConnection to do";
    }
}

// FINIR TD2, MODEL
// PUIS MONGO DB