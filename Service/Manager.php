<?php

/**
 * 
 * @author:  Baptiste BOUCHEREAU <baptiste.bouchereau@idci-consulting.fr>
 * @license: GPL
 *
 */

namespace IDCI\Bundle\WebPageScreenShotBundle\Service;

use IDCI\Bundle\WebPageScreenShotBundle\Exceptions\UnavailableRenderFormatException;
use IDCI\Bundle\WebPageScreenShotBundle\Exceptions\UnavailableRenderModeException;
use Gregwar\ImageBundle\Services\ImageHandling;

class Manager
{
    protected $configurationParameters;
    protected $imageHandling;
    protected $givenParameters;

    public function __construct($configurationParameters, ImageHandling $imageHandling)
    {
        $this->setConfigurationParameters($configurationParameters);
        $this->setImageHandling($imageHandling);
    }

    /**
     * Get image handling service
     */
    public function getImageHandling()
    {
        return $this->imageHandling;
    }

    /**
     * Set image handling service
     */
    public function setImageHandling($imageHandling)
    {
        $this->imageHandling = $imageHandling;
    }
    
    /**
     * Get configuration Parameters
     *
     * @return array 
     */
    public function getConfigurationParameters()
    {
        return $this->configurationParameters;
    }

    /**
     * Set configuration Parameters
     *
     * @param array
     */
    public function setConfigurationParameters($defaultParameters)
    {
        $this->configurationParameters = $defaultParameters;
    }

    /**
     * Get given Parameters
     *
     * @return array 
     */
    public function getGivenParameters()
    {
        return $this->givenParameters;
    }

    /**
     * Set given Parameters
     *
     * @param array
     */
    public function setGivenParameters($givenParameters)
    {
        $this->givenParameters = $givenParameters;
    }

    /**
     * Create a screenshot
     *
     * @param string: a website url
     * @param array: parameters about the screenshot to be generated
     * @return string: the path of the generated screenshot 
     */
    public function createScreenShot($url, $givenParameters = array())
    {
        //we retrieve and check parameters
        $this->setGivenParameters($givenParameters);

        $availableModes = array("base64", "file");
        $availableFormats = array("gif", "png", "jpeg", "jpg");
        
        $mode = $this->getRenderParameter('mode');
        if (!in_array($mode, $availableModes)) {
            throw new UnavailableRenderModeException($mode);
        }

        $format = $this->getRenderParameter('format');
        if (!in_array($format, $availableFormats)) {
            throw new UnavailableRenderFormatException($format);
        }

        $width = $this->getRenderParameter('width');
        $height = $this->getRenderParameter('height');

        // we create and resize the screenshot according to the cache configuration values, and what's in the cache
        if($this->configurationParameters['cache']['enabled']) {

            $fullSizeScreenshotName = $this->getFileName($url, $format);
            $fullSizeScreenshotPath = sprintf("%s/screenshots/full_size/%s", getcwd(), $fullSizeScreenshotName);
            $thumbScreenshotName = sprintf("%sx%s%s", $width, $height, $fullSizeScreenshotName);
            $thumbScreenshotPath = sprintf("%s/screenshots/thumb/%s", getcwd(), $thumbScreenshotName);

            if(file_exists($thumbScreenshotPath)) {
                    return $thumbScreenshotPath;
            }

            if(file_exists($fullSizeScreenshotPath)) {
                    $this->resizeScreenShot($fullSizeScreenshotPath, $thumbScreenshotPath, $width, $height, $format);
                    return $thumbScreenshotPath;
            }
        }

        //we launch the screenshot generation
        $command = sprintf("%s %s/../Lib/imageRender.js %s %s",
                $this->configurationParameters['phantomjs_bin_path'],
                __DIR__,
                $url,
                $format
        );
        $fullSizeScreenshotPath = sprintf("%s/%s", getcwd(), shell_exec($command));

        //we resized the screenshot
        sleep(5);
        $thumbScreenshotName = sprintf("%sx%s%s", $width, $height, $fullSizeScreenshotName);
        $thumbScreenshotPath = sprintf("%s/screenshots/thumb/%s", getcwd(), $thumbScreenshotName);
        $this->resizeScreenShot($fullSizeScreenshotPath, $thumbScreenshotPath, $width, $height, $format);
        
        return $thumbScreenshotPath;
    }

    
    /**
     * Resize an image
     *
     * @param string: the path of the image to be resized
     * @param string: the path of the resized image
     * @param integer: the width of the image
     * @param integer: the height of the image
     * @param string : the format of the image (png, gif or jpg)
     */
    public function resizeScreenShot($fullSizeScreenshotPath, $thumbScreenshotPath ,$width, $height, $format)
    {
        $this->getImageHandling()->open($fullSizeScreenshotPath)->resize($width, $height)->save($thumbScreenshotPath, $format);
    }

    /**
     * Get either a query parameter, or a conf parameter if none given
     *
     * @param string: the name of the parameter to get
     * @return string: the value of the parameter 
     */
    public function getRenderParameter($name)
    {
        if (isset($this->givenParameters[$name])) {
            return $this->givenParameters[$name];
        } else if (isset($this->configurationParameters['render'][$name])) {
            return $this->configurationParameters['render'][$name];
        } else {
            throw new \Exception(sprintf("Parameter '%s' is missing", $name));
        }
    }
    
    /**
     * Get the name of a screenshot according to a given url
     *
     * @param string: the url
     * @return string: the file name
     */
    public function getFileName($url, $format)
    {
        if (strpos($url, "http://www.") === 0)
            $fileName = sprintf("%s.%s", substr($url, 11), $format);
        else
            $fileName = sprintf("%s.%s", substr($url, 7), $format);

        return str_replace(array("/", "?", "="),".", $fileName);
    }
}

?>