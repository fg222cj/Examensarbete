<?php
/**
 * Author: Fabian Nachenius
 * Date: 2016-03-30
 * Time: 15:22
 */

require_once(dirname(__FILE__) . '/Model/Export.php');

$path = dirname(__FILE__) . '/Resources/export.txt';
$export = new Export();
file_put_contents($path, $export->exportAll());
echo "Done!";