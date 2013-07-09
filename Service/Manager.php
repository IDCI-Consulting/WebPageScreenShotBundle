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
    protected $givenParameters;
    protected $imageHandling;
    protected $cache;
    protected $kernel;

    public function __construct($configurationParameters, ImageHandling $imageHandling, PhpFileCache $cache, $kernel)
    {
        $this->setConfigurationParameters($configurationParameters);
        $this->setImageHandling($imageHandling);
        $this->setCache($cache);
        $this->setKernel($kernel);
    }

    /**
     * Get the kernel
     * 
     * @return Kernel
     */
    public function getKernel()
    {
        return $this->kernel;
    }

    /**
     * Set the kernel
     */
    public function setKernel($kernel)
    {
        $this->kernel = $kernel;
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
    public function setImageHandling($image_handling)
    {
        $this->imageHandling = $image_handling;
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
    public function setConfigurationParameters($default_parameters)
    {
        $this->configurationParameters = $default_parameters;
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
    public function setGivenParameters($given_parameters)
    {
        $this->givenParameters = $given_parameters;
    }

    /**
     * Create a screenshot
     *
     * @param string: a website url
     * @param array: parameters about the screenshot to be generated
     * @return string: the path of the generated screenshot 
     */
    public function createScreenshot($url, $given_parameters = array())
    {
        // Retrieve and check parameters
        $this->setGivenParameters($given_parameters);
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

        $renderedScreenshotName = $this->getFileName($url, $format);
        $resizedScreenshotName  = sprintf("%sx%s%s", $width, $height, $renderedScreenshotName);
        
        // Creating and resizing the screenshot according to the "cache enabled" value, and what's in the cache
        if($conf['cache']['enabled']) {

            $renderedScreenshotAbsolutePath = sprintf("%s%s", $this->getAbsoluteCacheDirectory(), $renderedScreenshotName);
            $resizedScreenshotAbsolutePath  = sprintf("%s%s", $this->getAbsoluteCacheDirectory(), $resizedScreenshotName);

            if($cachedResizedScreenshotName = $this->getImageFromCache($resizedScreenshotName)) {
                return $this->getImage($resizedScreenshotName, $mode, $format);
            }

            if($cachedRenderedScreenshotName = $this->getImageFromCache($renderedScreenshotName)) {
                $this->resizeScreenShot($renderedScreenshotAbsolutePath, $resizedScreenshotAbsolutePath, $width, $height, $format);
                $this->cacheImage($resizedScreenshotName);
                return $this->getImage($resizedScreenshotName, $mode, $format);
            }
        }

        // Generating the screenshot
        $command = sprintf("%s %s/../Lib/imageRender.js %s %s %s",
                $conf['phantomjs_bin_path'],
                __DIR__,
                $url,
                $format,
                $this->getAbsoluteCacheDirectory()
        );
        $renderedScreenshotAbsolutePath = trim(shell_exec($command));
        $this->cacheImage($renderedScreenshotName, $conf['cache']['delay']);

        // Resizing the screenshot
        $this->resizeScreenShot($renderedScreenshotAbsolutePath, $resizedScreenshotAbsolutePath, $width, $height, $format);
        $this->cacheImage($resizedScreenshotName, $conf['cache']['delay']);
        
        return $this->getImage($resizedScreenshotName, $mode, $format);
    }

    /**
     * Get an image according to the mode
     * 
     * @return string: either the path or a base64string
     */
    function getImage($fileName, $mode, $format) {
        if ($mode == "file") {
            $filePath = sprintf("%s%s", $this->getCacheDirectory(), $fileName);
            return $filePath;
        } else {
            $absoluteFilePath = sprintf("%s%s", $this->getAbsoluteCacheDirectory(), $fileName);
            return $this->base64_encode_image($absoluteFilePath, $format);
        }
    }
    
    /**
     * Encode an image in base64
     * 
     * @return string: the base64-encoded image
     */
    function base64_encode_image ($file_name, $file_type)
    {
        if (file_exists($file_name)) {
            $imgbinary = fread(fopen($file_name, "r"), filesize($file_name));
            return "data:image/" . $file_type . ";base64," . base64_encode($imgbinary);
        }
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
    public function resizeScreenShot($rendered_screenshot_path, $resized_screenshot_path ,$width, $height, $format)
    {
        $this->getImageHandling()
             ->open($rendered_screenshot_path)
             ->resize($width, $height)
             ->save($resized_screenshot_path, $format);
    }

    /**
     * Get either a query parameter, or a conf parameter if none given
     *
     * @param string: the name of the parameter to get
     * @return string: the value of the parameter 
     */
    public function getRenderParameter($name)
    {
        $params = $this->getGivenParameters();
        $conf = $this->getConfigurationParameters();
        if (isset($params[$name])) {
            return $params[$name];
        } else if (isset($conf['render'][$name])) {
            return $conf['render'][$name];
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
        $urlArray = parse_url($url);
        if(isset($urlArray['path'])) {
            $fileName = str_replace("/",".", sprintf("%s%s.%s", $urlArray['host'], $urlArray['path'], $format));
        } else {
            $fileName = sprintf("%s.%s", $urlArray['host'], $format);
        }

        return $fileName;
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
        $conf = $this->getConfigurationParameters();
        if($conf['cache']['enabled']) {
            $cache = $this->getCache();
            $cache->save(
                $this->imageToHash($image_name),
                $image_name,
                $conf['cache']['delay']
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

    /**
     * Get the absolute cache directory path
     *
     * @return string : the absolute directory path
     */
    public function getAbsoluteCacheDirectory() {
        return sprintf("%s/../web/screenshots_cache/", $this->getKernel()->getRootDir());
    }

    /**
     * Get the cache directory path
     *
     * @return string : the directory path
     */
    public function getCacheDirectory() {
        return "/screenshots_cache/";
    }
}

?>