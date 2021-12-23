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

// use hellokant\model\Model;
use hellokant\model\Article;
use hellokant\model\Categorie;

// Connection with DB
use hellokant\connection\ConnectionFactory;

// // 2. Gestion de la connexion à la base
$conf = parse_ini_file('conf/conf.ini');
ConnectionFactory::makeConnection($conf);

//////////////////////////////:://///////

// // 1. La classe Query - 3. Finaliser la classe Query
// 1 - 5 : Créer la classe Query,
// les méthodes table(...), select(...), where(...), get(...)
$q = Query::table('article');
$q = $q->select(['id', 'nom']);
$q = $q->where('tarif', '<', 200);
$req = $q->get();

print_r($req);

// 7. Methode insert()
$id = Query::table('article')->insert(['nom' => 'Tricycle', 'tarif' => 3500, 'descr' => 'Qualité exceptionnel, amélioré, à hydrogène', 'id_categ' => 1]);
echo 'article inséré, id : ' . $id . PHP_EOL;

// 6. Méthode delete()
$qd = Query::table('article')->where('id', '=', $id);
echo $qd->delete();
echo PHP_EOL;

// 8. Chainage des méthodes where()
echo "Chainage des where()" . PHP_EOL;

$q2 = Query::table('article')
    ->select(['id', 'nom', 'descr', 'tarif'])
    ->where('tarif', '<', 200)
    ->where('id', '>=', 65)
    ->get();

print_r($q2);

// Methode one()
echo "Méthode one() de la classe Query" . PHP_EOL;

$q3 = Query::table('article')
    ->select(['id', 'nom', 'descr', 'tarif'])
    ->one();

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
$a3->descr = "velo électrique";
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
print_r(Article::find([['tarif', '<', 200]], ['id', 'nom', 'tarif']));

//    d) find([[wheres]], [cols])
echo "Article::find([['nom', 'like', '%velo%'], ['tarif', '<', 200]], ['id', 'nom', 'tarif'])" . PHP_EOL;
print_r(Article::find([['nom', 'like', '%velo%'], ['tarif', '<', 200]], ['id', 'nom', 'tarif']));

//    e) first([[wheres]], [cols])
echo "Article::first([['nom', 'like', '%velo%'], ['tarif', '<', 200]], ['id', 'nom', 'tarif'])" . PHP_EOL;
print_r(Article::first([['nom', 'like', '%velo%'], ['tarif', '<', 200]], ['id', 'nom', 'tarif']));

// methode one()
echo "Methode one() de la classe Model : Article::one(64)" . PHP_EOL;
$t = Article::one(64);
print_r($t);

// // 6. Gestion des associations
// 1. belongs_to()
echo "--> belongs_to : Récupération de la catégorie associée un article" . PHP_EOL;
$art = Article::first(65);
$categorie = $a->belongs_to(Categorie::class, 'id_categ');
print_r($categorie);
echo PHP_EOL;

// 2. has_many()
echo "-- > has_many() : Récupération des articles associés à une catégorie" . PHP_EOL;
$cat = Categorie::first(1);
$list_article = $cat->has_many(Article::class, 'id_categ');
echo "Liste des articles de la catégoprie $cat->nom : " . PHP_EOL;
foreach ($list_article as $article) {
    echo "id : $article->id, nom : $article->nom " . PHP_EOL;
}
echo PHP_EOL;

// 3. 1. categorie()
echo "-- > Méthode categorie() de la classe Article, retournant la catégorie d'un article :" . PHP_EOL;
echo "Article::first(64)->categorie()" . PHP_EOL;
$categ = Article::first(64)->categorie();
print_r($categ);
echo PHP_EOL;

//    2. article()
echo "-- > Méthode articles() de la classe Categorie, retournant les articles d'une catégorie :" . PHP_EOL;
echo "Categorie::first(1)->articles()" . PHP_EOL;
$list = Categorie::first(1)->articles();
foreach ($list as $article) {
    echo "id : $article->id, nom : $article->nom " . PHP_EOL;
}
echo PHP_EOL;

// 4.
// ->categorie
echo 'Affichage d\'une catégorie avec la syntaxe : $a->categorie' . PHP_EOL;
$a = Article::first(64);
$cat = $a->categorie;
print_r($cat);
echo PHP_EOL;

// ->article
echo 'Affichage des article avec la syntaxe : $c->articles' . PHP_EOL;
$c = Categorie::first(1);
$list_articles = $c->articles;
print_r($list_articles);
