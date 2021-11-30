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
use hellokant\model\Categorie;

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

// $a = new Article();
// $a->nom = 'velo'; 
// $a->tarif = 273;
// $a->id_categ = 1;
// $a->insert();

// $a2 = new Article();
// $a2->nom = 'tableau'; 
// $a2->tarif = 78;
// $a2->descr = "Tableau d'école";
// $a2->id_categ = 1;
// $a2->insert();

// $a3 = new Article();
// $a3->nom = 'velo'; 
// $a3->tarif = 2000;
// $a3->descr = "Tableau d'école";
// $a3->id_categ = 1;
// $a3->insert();


// $a->delete();

// $liste = Article::all();
// foreach ($liste as $article) {
//     echo $article->nom . PHP_EOL;
// }

// $findTest1 = Article::find(64);
// print_r($findTest1);


// $findTest2 = Article::find([['nom','like','velo'], ['tarif','<=', 100]], ['nom','tarif']);
// print_r($findTest2);

$firstTest1 = Article::first(64);
// print_r($firstTest1);

// // $firstTest2 = Article::first(['tarif', '<=', 100 ]); //vérifier demander si example ok
// $firstTest2 = Article::first([['tarif', '<=', 100 ]]);
// print_r($firstTest2);

$categorie1 = $firstTest1->categorie();
print_r($categorie1);

$categorie2 = Categorie::first(1);

$list_article1 = $categorie1->articles();
$list_article2 = $categorie2->articles();

print_r($list_article1);
// print_r($list_article2);

