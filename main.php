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

// // 2. Gestion de la connexion à la base
$conf = parse_ini_file('conf/conf.ini');
ConnectionFactory::makeConnection($conf);

// use hellokant\model\Model;
use hellokant\model\Article;
use hellokant\model\Categorie;

//////////////////////////////:://///////

// // 1. La classe Query - 3. Finaliser la classe Query
// 1 - 5 : Créer la classe Query, 
// les méthodes table(...), select(...), where(...), get(...)
$q = Query::table('article');
$q = $q->select(['id', 'nom']);
$q = $q->where('tarif','<', 200);
$req = $q->get();

print_r($req);

// 7. Methode insert()
$id = Query::table('article')->insert(['nom'=>'Trombone', 'tarif'=>3500, 'descr'=>'Qualité exceptionnel, amélioré, à hydrogène', 'id_categ'=>1]);
echo 'article inséré, id : ' . $id . PHP_EOL;

// 6. Méthode delete()
$qd = Query::table('article')->where('id', '=', $id);
echo($qd->delete());
echo PHP_EOL;

// 8. Chainage des méthodes where()
$q2 = Query::table('article')
    ->select(['id', 'nom', 'descr', 'tarif'])
    ->where('tarif', '<', 200)
    ->where('id','>=', 65)
    ->get();
    
echo "Chainage des where()" . PHP_EOL;
print_r($q2);

// Methode one()
$q3 = Query::table('article')
            ->select(['id', 'nom', 'descr', 'tarif'])
            ->one();

echo "Méthode one()" . PHP_EOL;
print_r($q3);

// // 4. La classe Model
// 1 - 4 : $_v, _get(), set(), création d'un article
$a = new Article();
$a->nom = 'velo'; 
$a->descr = 'vélo 72 vitesses';
$a->tarif = 376;
$a->id_categ = 1;

// accès aux attributs
echo "Article créé : " . PHP_EOL;
echo "nom : " . $a->nom . PHP_EOL;
echo "description : " . $a->descr . PHP_EOL;
echo "tarif : " . $a->tarif . PHP_EOL;
echo "id_categ : " . $a->id_categ . PHP_EOL;


// 5 - 7 : insert(), delete()
$a->insert();
echo 'article inséré, id : ' . $a->id . PHP_EOL;
echo "Suppression de cet article : ";
echo $a->delete() . " ligne supprimée" . PHP_EOL;

// Création et insertion de nouveaux articles, 
// utilisés pour illustrer le fonctionnement des finders:
$a2 = new Article();
$a2->nom = 'velo'; 
$a2->tarif = 99;
$a2->descr = "velo noir";
$a2->id_categ = 1;

$a3 = new Article();
$a3->nom = 'velo'; 
$a3->tarif = 9947;
$a3->descr = "velo nucléaire";
$a3->id_categ = 1;

$a2->insert();

echo PHP_EOL . PHP_EOL;
// // 5. Les "Finders"
echo "--> Finders : " . PHP_EOL;
// 1. all()
echo "methode all() :" . PHP_EOL;
$liste = Article::all();
foreach ($liste as $article) {
    echo "id : $article->id, nom : $article->nom, tarif : $article->tarif, etc..." . PHP_EOL;
}
echo PHP_EOL;

// 2. a) find(id)
echo "Article::find(64) : " . PHP_EOL;
$find_test_1 = Article::find(64);
print_r($find_test_1[0]); 

//    b) find(id, [cols])
echo "Article::find(64, ['id', 'nom', 'tarif']" . PHP_EOL;
$find_test_2 = Article::find(64, ['id', 'nom', 'tarif']);
print_r($find_test_2[0]);

//    c) find([[where]], [cols])
echo "Article::find([['tarif', '<', 200]], ['id', 'nom', 'tarif']" . PHP_EOL;
print_r( Article::find([['tarif', '<', 200]], ['id', 'nom', 'tarif']) );

//    d) find([[wheres]], [cols])
echo "Article::find([['nom', 'like', '%velo%'], ['tarif', '<', 200]], ['id', 'nom', 'tarif'])" . PHP_EOL;
print_r( Article::find([['nom', 'like', '%velo%'], ['tarif', '<', 200]], ['id', 'nom', 'tarif']) );

//    e) first([[wheres]], [cols])
echo "Article::first([['nom', 'like', '%velo%'], ['tarif', '<', 200]], ['id', 'nom', 'tarif'])" . PHP_EOL;
print_r( Article::first([['nom', 'like', '%velo%'], ['tarif', '<', 200]], ['id', 'nom', 'tarif']) );

// // 6. Gestion des associations
// 1. belongs_to()
echo "belongs_to : Récupération de la catégorie associée un article" . PHP_EOL;
$art = Article::first(65);
$categorie = $a->belongs_to(Categorie::class, 'id_categ');
print_r($categorie);

// 2. has_many()
echo "has_many() : Récupération des articles associé à une catégorie";
$cat = Categorie::first(1);
$list_article = $cat->has_many(Article::class, 'id_categ');
echo "$";
// 3.
// 4.




// print_r($list_article1);
// // print_r($list_article2);

// $c = Categorie::first(1);
// $list_art = $c->articles;

// print_r($list_art);

// $art65 = Article::find("*");
// $cat_art65 = $art65->categorie;

// print_r($art65);

// $z = new Article();
// $z->delete();

// $t = Article::one(106);
// var_dump($t);
// foreach ($t as $tu) {
//     echo $tu['id'] . PHP_EOL;
// }


/**
 * Test des finders
 * (exemple correction vidéo)
 */

//  $articles = Article::all();
//  var_dump($articles);

// $a = new Article();
// $a->deletse;

// $articles = Article::first([['nom','=','velo']]);
// print_r($articles);
// print_r($articles->categorie);

// $cat = Categorie::first(1);
// print_r($cat);
// print_r($cat->articles);
//  $articles = Article::find(106);
//  var_dump($articles);

//  $articles = Article::find(['nom','tarif'], 64);
//  var_dump($articles);

//  $articles = Article::find(['nom','tarif'], ['id','=', 64]);
//  var_dump($articles);

//  $articles = Article::find(['nom','tarif'], ['nom','like','$velo$']);
//  var_dump($articles);
