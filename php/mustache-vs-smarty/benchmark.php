<?php

// Test settings
$itr = 100;

/*
100
Test Complete
 |- Smarty 3.1.14
 |  * Load Template: 0.149ms
 |  * Render 100x: 37.679ms
 |  * Avg. Render Time: 0.331ms
 `- Mustache 2.4.1
    * Load Template: 2.406ms
    * Render 100x: 89.731ms
    * Avg. Render Time: 0.852ms

1,000
Test Complete
 |- Smarty 3.1.14
 |  * Load Template: 0.149ms
 |  * Render 1000x: 366.961ms
 |  * Avg. Render Time: 0.321ms
 `- Mustache 2.4.1
    * Load Template: 2.623ms
    * Render 1000x: 892.455ms
    * Avg. Render Time: 0.846ms

10,000
Test Complete
 |- Smarty 3.1.14
 |  * Load Template: 0.149ms
 |  * Render 10000x: 3,658.139ms
 |  * Avg. Render Time: 0.319ms
 `- Mustache 2.4.1
    * Load Template: 2.553ms
    * Render 10000x: 8,952.474ms
    * Avg. Render Time: 0.848ms

 */

// Set up Smarty
require_once("Smarty-3.1.14/libs/Smarty.class.php");
$smarty = new Smarty();
$smarty->setCacheDir("tmp");
$smarty->setCompileDir("tmp");
$smarty->setTemplateDir("templates-smarty");

// Set up Mustache
require_once("mustache.php/src/Mustache/Autoloader.php");
Mustache_Autoloader::register();

$mustache = new Mustache_Engine(array(
    "cache" => "tmp",
    "loader" => new Mustache_Loader_FilesystemLoader("templates-mustache", array("extension" => ".html")),
    ));

// Run test against Smarty
$smarty_avg = 0;
$smarty_t1 = microtime(true);
$smarty_template = $smarty->createTemplate("test-foreach.html");
$smarty_t2 = microtime(true);
for($i = 0; $i < $itr; $i++) {
    $array = array(
        array("name" => "bob".$i, "age" => $i),
        array("name" => "jim".$i, "age" => $i + 1),
        array("name" => "lil".$i, "age" => $i + 2),
        array("name" => "dan".$i, "age" => $i + 3),
        array("name" => "lin".$i, "age" => $i + 4),
        array("name" => "bob".$i, "age" => $i),
        array("name" => "jim".$i, "age" => $i + 1),
        array("name" => "lil".$i, "age" => $i + 2),
        array("name" => "dan".$i, "age" => $i + 3),
        array("name" => "lin".$i, "age" => $i + 4),
        array("name" => "bob".$i, "age" => $i),
        array("name" => "jim".$i, "age" => $i + 1),
        array("name" => "lil".$i, "age" => $i + 2),
        array("name" => "dan".$i, "age" => $i + 3),
        array("name" => "lin".$i, "age" => $i + 4),
        );
    $avg_t1 = microtime(true);
    $smarty_template->assign("array", $array);
    $tmp = $smarty_template->fetch();
    $avg_t2 = microtime(true);
    $smarty_avg += (($avg_t2 - $avg_t1) * 1000);
    unset($tmp, $array, $avg_t1, $avg_t2);
}
$smarty_t3 = microtime(true);

// Run test against Mustache
$mustache_avg = 0;
$mustache_t1 = microtime(true);
$mustache_template = $mustache->loadTemplate("test-foreach");
$mustache_t2 = microtime(true);
for($i = 0; $i < $itr; $i++) {
    $array = array(
        array("name" => "bob".$i, "age" => $i),
        array("name" => "jim".$i, "age" => $i + 1),
        array("name" => "lil".$i, "age" => $i + 2),
        array("name" => "dan".$i, "age" => $i + 3),
        array("name" => "lin".$i, "age" => $i + 4),
        array("name" => "bob".$i, "age" => $i),
        array("name" => "jim".$i, "age" => $i + 1),
        array("name" => "lil".$i, "age" => $i + 2),
        array("name" => "dan".$i, "age" => $i + 3),
        array("name" => "lin".$i, "age" => $i + 4),
        array("name" => "bob".$i, "age" => $i),
        array("name" => "jim".$i, "age" => $i + 1),
        array("name" => "lil".$i, "age" => $i + 2),
        array("name" => "dan".$i, "age" => $i + 3),
        array("name" => "lin".$i, "age" => $i + 4),
        );
    $avg_t1 = microtime(true);
    $tmp = $mustache_template->render(array("array" => $array));
    $avg_t2 = microtime(true);
    $mustache_avg += (($avg_t2 - $avg_t1) * 1000);
    unset($tmp, $array, $avg_t1, $avg_t2);
}
$mustache_t3 = microtime(true);

echo "Test Complete\n";

echo " |- Smarty 3.1.14\n";
echo " |  * Load Template: ".number_format(($smarty_t2 - $smarty_t1) * 1000, 3)."ms\n";
echo " |  * Render ".$itr."x: ".number_format(($smarty_t3 - $smarty_t2) * 1000, 3)."ms\n";
echo " |  * Avg. Render Time: ".number_format($smarty_avg / $itr, 3)."ms\n";

echo " `- Mustache 2.4.1\n";
echo "    * Load Template: ".number_format(($mustache_t2 - $mustache_t1) * 1000, 3)."ms\n";
echo "    * Render ".$itr."x: ".number_format(($mustache_t3 - $mustache_t2) * 1000, 3)."ms\n";
echo "    * Avg. Render Time: ".number_format($mustache_avg / $itr, 3)."ms\n";
