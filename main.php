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

// Connection with DB
use hellokant\connection\ConnectionFactory;
// use hellokant\connection\ConnectionFactoryTry as ConnectionFactory;
$conf = parse_ini_file('conf/conf.ini');
ConnectionFactory::makeConnection($conf);

// use hellokant\model\Model;
use hellokant\model\Article;

//////////////////////////////:://///////

// $q = Query::table('article');
// $q = $q->select(['id', 'nom']);
// $q = $q->where('tarif','<',1000);
// $req = $q->get();

// var_dump($req);

// $id = Query::table('article')->insert(['nom'=>'grovelo', 'tarif'=>200, 'id_categ'=>1]);
// $id = Query::table('article')->insert(['nom'=>'Trombone4', 'tarif'=>3500, 'descr'=>'Qualité exceptionnel, amélioré, à hydrogène', 'id_categ'=>1]);

// echo 'article inséré id : ' . $id ."\n";

// $qd = Query::table('article')->where('id', '=', $id) ;
// echo($qd->delete());
// echo PHP_EOL;

// $q = Query::table('article')
//     ->select(['id', 'nom', 'descr', 'tarif'])
//     ->where('tarif', '<', 1000)
//     ->get();

// var_dump($q);

$a = new Article();
$a->nom = 'velo'; 
$a->tarif = 273;
$a->id_categ = 1;
$a->insert();

$a2 = new Article();
$a2->nom = 'tableau'; 
$a2->tarif = 78;
$a2->descr = "Tableau d'école";
$a2->id_categ = 1;
$a2->insert();


$a->delete();

// $list = Article::all();
// foreach ($liste as $article) {
//     echo $article->nom;
// }








