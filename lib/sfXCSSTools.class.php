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
      // replace doubled DS from configuration value, if any
      $folder = str_replace(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR);
      if (!file_exists($folder))
      {
        if (!@mkdir($folder, 0777, true))
        {
          throw new RuntimeException(self::EXCEPTION_COULD_NOT_CREATE_SAVE_FOR_WEB_DIR, 2);
        }
      }

      if (file_exists($folder) and is_writable($folder))
      {

        $filename = $folder . $filename;
        $result = (bool) file_put_contents($filename, $xcss);

        if ($result)
        {
          chmod($filename, 0666);
        }
        else
        {
          /*
           * In case we couldn't write it, we remove the file
           * to be sure there is nothing left, we don't like.
           */
          unlink($filename);
        }

        return $result;
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
      $dir = sfConfig::get('sf_cache_dir') . DIRECTORY_SEPARATOR . sfContext::getInstance()->getConfiguration()->getApplication() . DIRECTORY_SEPARATOR . sfContext::getInstance()->getConfiguration()->getEnvironment() . DIRECTORY_SEPARATOR . 'xcss' . DIRECTORY_SEPARATOR;
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
      return (mkdir(self::getCacheOutputDirectory(), 0777, true) and chmod(self::getCacheOutputDirectory(), 0777));
    }
    else
    {
      return is_writable(self::getCacheOutputDirectory());
    }
  }
}