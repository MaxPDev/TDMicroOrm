<?php

declare(strict_types=1);

// Affichage des erreurs
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Autoloader
require __DIR__ . '/vendor/autoload.php';

// Query use
use hellokant\query\Query;

//////////////////////////////:://///////

$q = Query::table('article');
$q = $q->select(['id', 'nom']);
$q = $q->where('tarif','<',1000);
$req = $q->get();

var_dump($req);

$id = Query::table('article')->insert(['nom'=>'grovelo', 'tarif'=>200]);

echo 'article inséré id : ' . $id .'\n';

$qd = Query::table('article')->where('id', '=', $id) ;
echo($qd->delete());

$q = Query::table('article')
    ->select(['id', 'nom', 'descr', 'tarif'])
    ->where('tarif', '<', 1000)
    ->get();

var_dump($q);
