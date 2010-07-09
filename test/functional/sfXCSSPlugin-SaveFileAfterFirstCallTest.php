<?php
require_once(dirname(__FILE__) . '/../../bootstrap/functional.php');

// set up test env correctly
sfConfig::set('app_xcssplugin_path_to_css_dir', sfConfig::get('sf_plugins_dir') . DIRECTORY_SEPARATOR . 'sfXCSSPlugin' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR);
sfConfig::set('app_xcssplugin_saveforweb', 'xcss');

$browser = new sfTestFunctional(new sfBrowser());
$limeTest = $browser->test();

$limeTest->plan++;
$limeTest->ok(!file_exists($filename), 'File does not exist.');

$limeTest->plan++;
$limeTest->is(sfConfig::get('app_xcssplugin_saveforweb'), 'xcss', 'Save For Web enabled.');

$browser->getAndCheck('xcss', 'process', '/xcss/stylesheet.css', 200);
$browser->responseContains('h1 {border: #F00 1px solid;}');
$browser->isResponseHeader('Content-Type', 'text/css; charset=utf-8');

$limeTest->plan++;
$filename = sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . 'xcss' . DIRECTORY_SEPARATOR . 'stylesheet.css';
$limeTest->ok(file_exists($filename), 'File has been saved for web access.');

// cleanup
$limeTest->plan++;
$limeTest->ok(unlink($filename), 'File has been removed.');