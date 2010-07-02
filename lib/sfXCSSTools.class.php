<?php

class sfXCSSTools
{
  const EXCEPTION_SAVE_FOR_WEB_DISABLED = 'The save for web options has been disabled';

  const EXCEPTION_COULD_NOT_CREATE_SAVE_FOR_WEB_DIR = 'The configured save for web directory does not exist and could not be created.';

  /**
   * Save compiled content from xCSS to a given file in web directory.
   *
   * @throws RuntimeException If configuration is not active.
   * @throws RuntimeException If save for web directory is not accessible.
   *
   * @param string $filename The file where to save the compiled string into.
   * @param string $xcss The compiled CSS code.
   *
   * @return bool Success or Error.
   */
  public static function saveForWeb($filename, $xcss)
  {
    if ($folder = sfConfig::get('app_xcssplugin_saveforweb', false))
    {
      // replace doubled slashed from configuration value, if any
      $folder = str_replace('//', '/', sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR);
      if (!file_exists($folder))
      {
        if (!mkdir($folder, 0777, true))
        {
          throw new RuntimeException(self::EXCEPTION_COULD_NOT_CREATE_SAVE_FOR_WEB_DIR, 2);
        }
      }

      if (file_exists($folder) and is_writable($folder))
      {
        $filename = $folder . $filename;
        return (bool) file_put_contents($filename, $xcss);
      }
      else
      {
        throw new RuntimeException(self::EXCEPTION_COULD_NOT_CREATE_SAVE_FOR_WEB_DIR, 3);
      }
    }
    else
    {
      throw new RuntimeException(self::EXCEPTION_SAVE_FOR_WEB_DISABLED . $folder, 1);
    }
  }

  /**
   * Returns the directory in which xCSS generated files will be put into.
   *
   * @return string
   */
  public static function getCacheOutputDirectory()
  {
    static $dir = '';

    if ($dir === '')
    {
      $dir = sfConfig::get('sf_cache_dir') . '/' . sfContext::getInstance()->getConfiguration()->getApplication() . '/' . sfContext::getInstance()->getConfiguration()->getEnvironment() . '/xcss/';
    }

    return $dir;
  }

  /**
   * Creates the cache directory for generated xCSS files.
   * It creates a directory 'xcss' in the cache/app/env/ folder, if this does not exist.
   *
   * @return bool
   */
  public static function createCacheDirectory()
  {
    if (!file_exists(self::getCacheOutputDirectory()))
    {
      return mkdir(self::getCacheOutputDirectory(), 0777, true);
    }
    else
    {
      return is_writable(self::getCacheOutputDirectory());
    }
  }
}