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
use Doctrine\Common\Cache\PhpFileCache;

class Manager
{
    protected $configurationParameters;
    protected $imageHandling;
    protected $cache;
    protected $givenParameters;

    public function __construct($configurationParameters, ImageHandling $imageHandling, PhpFileCache $cache)
    {
        $this->setConfigurationParameters($configurationParameters);
        $this->setImageHandling($imageHandling);
        $this->setCache($cache);
    }

    /**
     * Get the cache
     * 
     * @return PhpFileCache
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * Set the cache
     */
    public function setCache($cache)
    {
        $this->cache = $cache;
    }

    /**
     * Get image handling
     * 
     * @return ImageHandling
     */
    public function getImageHandling()
    {
        return $this->imageHandling;
    }

    /**
     * Set image handling
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
        $conf = $this->getConfigurationParameters();

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
        if($conf['cache']['enabled']) {

            $renderedScreenshotName = $this->getFileName($url, $format);
            $renderedScreenshotPath = sprintf("%s/screenshots/rendered/%s", getcwd(), $renderedScreenshotName);
            $resizedScreenshotName = sprintf("%sx%s%s", $width, $height, $renderedScreenshotName);
            $resizedScreenshotPath = sprintf("%s/screenshots/resized/%s", getcwd(), $resizedScreenshotName);

            if($cachedResizedScreenshotName = $this->getImageFromCache($resizedScreenshotName)) {
                return $cachedResizedScreenshotName;
            }

            if($cachedRenderedScreenshotName = $this->getImageFromCache($renderedScreenshotName)) {
                $this->resizeScreenShot($renderedScreenshotPath, $resizedScreenshotPath, $width, $height, $format);
                $this->cacheImage($resizedScreenshotName);
                return $resizedScreenshotName;
            }
        }

        //we generate the screenshot
        $command = sprintf("%s %s/../Lib/imageRender.js %s %s",
                $conf['phantomjs_bin_path'],
                __DIR__,
                $url,
                $format
        );
        $renderedScreenshotPath = sprintf("%s/%s", getcwd(), shell_exec($command));
        $this->cacheImage($renderedScreenshotName, $conf['cache']['delay']);
        
        //we resized the screenshot
        $renderedScreenshotName = $this->getFileName($url, $format);
        $resizedScreenshotName = sprintf("%sx%s%s", $width, $height, $renderedScreenshotName);
        $resizedScreenshotPath = sprintf("%s/screenshots/resized/%s", getcwd(), $resizedScreenshotName);
        $this->resizeScreenShot($renderedScreenshotPath, $resizedScreenshotPath, $width, $height, $format);
        $this->cacheImage($resizedScreenshotName, $conf['cache']['delay']);
        
        return $resizedScreenshotPath;
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
    public function resizeScreenShot($renderedScreenshotPath, $resizedScreenshotPath ,$width, $height, $format)
    {
        $this->getImageHandling()
             ->open($renderedScreenshotPath)
             ->resize($width, $height)
             ->save($resizedScreenshotPath, $format);
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
        //TODO check parse_url() function
        if (strpos($url, "http://www.") === 0) {
            $fileName = sprintf("%s.%s", substr($url, 11), $format);
        } else {
            $fileName = sprintf("%s.%s", substr($url, 7), $format);
        }

        return str_replace(array("/", "?", "="),".", $fileName);
    }

    /**
     * Get hash from image
     *
     * @param $image_name string
     * @return string : md5
     */
    public function imageToHash($image_name)
    {
        return md5($image_name);
    }

    /**
     * Cache an image
     *
     * @param $image_name string
     * @param $ttl integer
     */
    public function cacheImage($image_name)
    {
        if($this->configurationParameters['cache']['enabled']) {
            $cache = $this->getCache();
            $cache->save(
                $this->imageToHash($image_name),
                $image_name,
                $this->configurationParameters['cache']['delay']
            );
        }
    }

    /**
     * Get an image from the cache
     *
     * @param $image_name string
     * @return string
     */
    public function getImageFromCache($image_name)
    {
        $cache = $this->getCache();

        return $cache->fetch($this->imageToHash($image_name));
    }
}

?>