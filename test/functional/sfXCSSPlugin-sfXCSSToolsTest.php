<?php
require_once(dirname(__FILE__) . '/../../bootstrap/functional.php');

$browser = new sfTestFunctional(new sfBrowser());
$limeTest = $browser->test();

$cacheDir = sfXCSSTools::getCacheOutputDirectory();

$limeTest->ok(!file_exists($cacheDir), 'Cache dir does not exist.');
$limeTest->ok(!empty($cacheDir), 'Cache dir configured.');
$limeTest->ok(sfXCSSTools::createCacheDirectory(), 'Cache dir created.');
$limeTest->ok(is_writable($cacheDir), 'Cache dir writable.');
$limeTest->ok(rmdir($cacheDir), 'Cache dir removed.');

sfConfig::set('app_xcssplugin_saveforweb', false);
try
{
  sfXCSSTools::saveForWeb('style.css', 'some css');
  $limeTest->fail('Exception not thrown.');
}
catch (RuntimeException $e)
{
  $limeTest->is($e->getMessage(), sfXCSSTools::EXCEPTION_SAVE_FOR_WEB_DISABLED, 'Exception caught.');
}

sfConfig::set('app_xcssplugin_saveforweb', 'tmp');
$folder = sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . 'tmp';
$limeTest->ok(mkdir($folder, 0000), 'Save For Web dir not writable.');

try
{
  sfXCSSTools::saveForWeb('style.css', 'some css');
  $limeTest->fail('Exception not thrown.');
}
catch (RuntimeException $e)
{
  $limeTest->is($e->getMessage(), sfXCSSTools::EXCEPTION_COULD_NOT_CREATE_SAVE_FOR_WEB_DIR, 'Exception caught.');
}

$limeTest->ok(!is_writable($folder . DIRECTORY_SEPARATOR . 'style.css'), 'File is not writable.');
$limeTest->ok(rmdir($folder), 'Save For Web dir removed.');