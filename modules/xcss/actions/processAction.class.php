<?php

class processAction extends sfAction
{
  /**
   * (non-PHPdoc)
   * @see lib/action/sfAction::preExecute()
   */
  public function preExecute()
  {
    sfXCSSTools::createCacheDirectory();

    $this->setLayout(false);
    $this->getResponse()->setContentType('text/css');
  }

  /**
   * The xCSS processor itself.
   *
   * @param sfWebRequest $request
   *
   * @return string
   */
  public function execute($request)
  {
    // we do not care, whether this is a valid CSS file here
    $this->requestedCSSFile = $request->getParameter('file');
    $targetFile = sfXCSSTools::getCacheOutputDirectory() . $this->requestedCSSFile;

    // because we cache the generated files, we check for an existing one
    if (file_exists($targetFile) === false)
    {
      $config = sfXCSSPluginConfiguration::getXCSSConfiguration(true);

      $config['xCSS_files'] = array(
        $this->requestedCSSFile => $targetFile,
      );

      $xCSS = new xCSS($config);
      $xCSS->compile();
    }

    $this->xcss = file_get_contents($targetFile);

    return sfView::SUCCESS;
  }

  /**
   * (non-PHPdoc)
   * @see lib/action/sfAction::postExecute()
   */
  public function postExecute()
  {
    try
    {
      return sfXCSSTools::saveForWeb($this->requestedCSSFile, $this->xcss);
    }
    catch (RuntimeException $e)
    {
      if (in_array($e->getMessage(), array(sfXCSSTools::EXCEPTION_COULD_NOT_CREATE_SAVE_FOR_WEB_DIR, sfXCSSTools::EXCEPTION_SAVE_FOR_WEB_DISABLED)))
      {
        return false;
      }
      else
      {
        throw $e;
      }
    }
  }
}