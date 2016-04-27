<?php
/**
 * Author: Fabian Nachenius
 * Date: 2016-03-30
 * Time: 15:22
 */

require_once(dirname(__FILE__) . '/Model/Export.php');

$exportAsNominalValues = true;

$path = dirname(__FILE__) . '/Resources/export.arff';
$export = new Export();
$dump = $export->exportAll($exportAsNominalValues);
file_put_contents($path, $dump);
echo "Done!<br>";
echo nl2br($dump);