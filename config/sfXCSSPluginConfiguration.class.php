<?php

class sfXCSSPluginConfiguration extends sfPluginConfiguration
{
  /**
   * (non-PHPdoc)
   * @see lib/config/sfPluginConfiguration::initialize()
   */
  public function initialize()
  {
    if (sfConfig::get('app_xcssplugin_routes_register', true) && in_array('xcss', sfConfig::get('sf_enabled_modules', array())))
    {
      $this->dispatcher->connect('routing.load_configuration', array('sfXCSSRouting', 'listenToRoutingLoadConfigurationEvent'));
    }

    return parent::initialize();
  }

  /**
   * Parses the given configuration from the app.yml into an xCSS config array.
   *
   * @param bool $force If true, loads the configuration again, otherwise returns cached config, if any.
   *
   * @return array
   */
  public static function getXCSSConfiguration($force = false)
  {
    static $xCSSConfiguration = array();

    if ($force || empty($xCSSConfiguration))
    {
      $configPrefix = 'app_xcssplugin_';
      foreach (sfConfig::getAll() as $entry => $value)
      {
        if (strstr($entry, $configPrefix) !== false)
        {
          $xCSSConfiguration[str_replace($configPrefix, '', $entry)] = $value;
        }
      }

      // set default config items, if not set by user
      $xCSSConfiguration = array_merge(self::getDefaultXCSSConfiguration(), $xCSSConfiguration);
    }

    return $xCSSConfiguration;
  }

  /**
   * Returns the default configuration for xCSS on this plugin.
   *
   * @return array
   */
  public static function getDefaultXCSSConfiguration()
  {
    return array(
      'path_to_css_dir' => sfConfig::get('sf_web_dir') . '/css/',
      'master_file' => false,
      'master_filename' => null,
      'reset_files' => null,
      'hook_files' => null,
      'construct_name' => 'self',
      'compress' => true,
      'debugmode' => false,
      'disable_xcss' => false,
      'minify_output' => true,
    );
  }
}