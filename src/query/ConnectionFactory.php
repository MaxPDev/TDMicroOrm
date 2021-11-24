<?php

namespace hellokant\query;

class ConnectionFactory {

    public static function makeConnection(array $conf) {
        var_dump($conf);
        echo "YES";
    }

    public static function getConnection() {
        echo "getConnection to do";
    }
}