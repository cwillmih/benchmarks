<?php

if(php_sapi_name() != "cli")
	echo "<pre>";

if(!extension_loaded("apc"))
	die("APC extension not loaded");

$file = "test-file.txt";

// Test settings
$itr = 100;

/*
100
Test Complete
 |- file_exists 5.3.5
 |  * Setup: 0.046ms
 |  * Check 100x: 3.956ms
 |  * Avg. Check Time: 0.024ms
 `- apc_fetch 3.1.6
    * Setup: 0.078ms
    * Check 100x: 2.937ms
    * Avg. Check Time: 0.014ms

1,000
Test Complete
 |- file_exists 5.3.5
 |  * Setup: 0.049ms
 |  * Check 1000x: 38.987ms
 |  * Avg. Check Time: 0.023ms
 `- apc_fetch 3.1.6
    * Setup: 0.063ms
    * Check 1000x: 29.376ms
    * Avg. Check Time: 0.014ms

10,000
Test Complete
 |- file_exists 5.3.5
 |  * Setup: 0.046ms
 |  * Check 10000x: 388.047ms
 |  * Avg. Check Time: 0.023ms
 `- apc_fetch 3.1.6
    * Setup: 0.064ms
    * Check 10000x: 292.615ms
    * Avg. Check Time: 0.014ms

 */

// Run test against file_exists
$file_exists_avg = 0;
$file_exists_t1 = microtime(true);
$file_exists_setup = file_exists($file);
$file_exists_t2 = microtime(true);
for($i = 0; $i < $itr; $i++) {
    $avg_t1 = microtime(true);
    $tmp = file_exists($file);
    $avg_t2 = microtime(true);
    $file_exists_avg += (($avg_t2 - $avg_t1) * 1000);
    unset($tmp, $avg_t1, $avg_t2);
}
$file_exists_t3 = microtime(true);

// Run test against apc_fetch
$apc_fetch_avg = 0;
$apc_fetch_t1 = microtime(true);
$apc_store_key = "fe_".md5($file);
apc_store($apc_store_key, file_exists($file));
$apc_fetch_t2 = microtime(true);
for($i = 0; $i < $itr; $i++) {
    $avg_t1 = microtime(true);
    $tmp = apc_fetch($apc_store_key);
    $avg_t2 = microtime(true);
    $apc_fetch_avg += (($avg_t2 - $avg_t1) * 1000);
    unset($tmp, $avg_t1, $avg_t2);
}
$apc_fetch_t3 = microtime(true);

// Cleanup
apc_delete($apc_store_key);

echo "Test Complete\n";

echo " |- file_exists ".phpversion()."\n";
echo " |  * Setup: ".number_format(($file_exists_t2 - $file_exists_t1) * 1000, 3)."ms\n";
echo " |  * Check ".$itr."x: ".number_format(($file_exists_t3 - $file_exists_t2) * 1000, 3)."ms\n";
echo " |  * Avg. Check Time: ".number_format($file_exists_avg / $itr, 3)."ms\n";

echo " `- apc_fetch ".phpversion("apc")."\n";
echo "    * Setup: ".number_format(($apc_fetch_t2 - $apc_fetch_t1) * 1000, 3)."ms\n";
echo "    * Check ".$itr."x: ".number_format(($apc_fetch_t3 - $apc_fetch_t2) * 1000, 3)."ms\n";
echo "    * Avg. Check Time: ".number_format($apc_fetch_avg / $itr, 3)."ms\n";
