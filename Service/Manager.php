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
use IDCI\Bundle\WebPageScreenShotBundle\Exceptions\UnavailableRenderParameterException;
use IDCI\Bundle\WebPageScreenShotBundle\Exceptions\UrlNotValidException;
use IDCI\Bundle\WebPageScreenShotBundle\Exceptions\MissingParameterException;
use IDCI\Bundle\WebPageScreenShotBundle\Exceptions\MissingUrlException;
use IDCI\Bundle\WebPageScreenShotBundle\Renderer\RendererFactory;
use Gregwar\ImageBundle\Services\ImageHandling;
use Doctrine\Common\Cache\PhpFileCache;

class Manager
{
    public static $AVAILABLE_FORMATS    = array("gif", "png", "jpeg", "jpg");
    public static $AVAILABLE_MODES      = array("url", "file", "base64");
    public static $RENDER_PARAMETERS    = array("mode", "format", "width", "height");
    public static $CACHE_PARAMETERS     = array("enabled", "delay");

    protected $configurationParameters;
    protected $givenParameters;
    protected $imageHandler;
    protected $cache;
    protected $screenshotPath;

    public function __construct($configurationParameters, ImageHandling $imageHandler, PhpFileCache $cache)
    {
        $this->setConfigurationParameters($configurationParameters);
        $this->setImageHandler($imageHandler);
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
    protected function setCache($cache)
    {
        $this->cache = $cache;
    }

    /**
     * Get image handler
     * 
     * @return ImageHandling
     */
    public function getImageHandler()
    {
        return $this->imageHandler;
    }

    /**
     * Set image handler
     * 
     * @param ImageHandling $imageHandler
     */
    protected function setImageHandler($imageHandler)
    {
        $this->imageHandler = $imageHandler;
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
     * @param array $defaultParameters
     */
    protected function setConfigurationParameters($defaultParameters)
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
     * @param array $givenParameters
     * @throw MissingUrlException
     */
    protected function setGivenParameters($givenParameters)
    {
        if(!isset($givenParameters['url'])) {
            throw new MissingUrlException();
        }
        $this->givenParameters['url'] = $givenParameters['url'];
        $this->givenParameters['render'] = array();

        // To prevent hack
        foreach($givenParameters as $parameter => $value) {
            if($parameter != 'url' && !in_array($parameter, self::$RENDER_PARAMETERS)) {
                throw new UnavailableRenderParameterException($parameter);
            }
         
            $check = sprintf('check%s', ucfirst(strtolower($parameter)));
            $this->givenParameters['render'][$parameter] = self::$check($value);
        }
    }

    /**
     * Get screenshot path
     * 
     * @return string
     */
    protected function getScreenshotPath()
    {
        return $this->screenshotPath;
    }

    /**
     * Set screenshot path
     * 
     * @param string $path
     */
    protected function setScreenshotPath($path)
    {
        $this->screenshotPath = $path;
    }

    /**
     * Find Parameter
     *
     * @param array $haystack
     * @param array $needle
     * @return mixed | null 
     */
    public static function findParameter($haystack, $needle)
    {
        if(count($needle) > 1) {
            $key = array_shift($needle);
            if(!isset($haystack[$key])) {
                return null;
            }
            return self::findParameter($haystack[$key], $needle);
        }

        return isset($haystack[$needle[0]]) ? 
            $haystack[$needle[0]] : 
            null
        ;
    }

    /**
     * Get Parameter
     *
     * @param mixed $parameterName
     * @return mixed | null
     */
    public function getParameter($parameterPath)
    {
        $parameterPath = is_array($parameterPath) ? $parameterPath : array($parameterPath);
        $value = null;

        if($value = self::findParameter($this->getGivenParameters(), $parameterPath)) {
            return $value;
        }

        if($value = self::findParameter($this->getConfigurationParameters(), $parameterPath)) {
            return $value;
        }

        throw new MissingParameterException(implode(' > ', $parameterPath));
    }

    /**
     * Get a screenshot
     * 
     * @param array $givenParameters Parameters about the screenshot to be generated
     * @return string The path of the generated screenshot 
     */
    public function capture($givenParameters = array())
    {
        $this->setGivenParameters($givenParameters);

        self::checkFormat($this->getParameter(array('render', 'format')));

        // Check if the cache is enabled and if the image is in cache
        if ($this->isCacheEnabled()) {
            $imagePath = $this->getCache()->fetch($this->getImageIdentifier(true));
        }

        if(!$imagePath) {
            // Generating the screenshot
            $this->generateScreenshotImage();

            if($this->isCacheEnabled()) {
                // Add the captured image in the cache
                $this->cacheImage($this->getImageIdentifier(true));
            }
        }
        
        return $this;
    }

    /**
     * 
     */
    public function render()
    {
        return RendererFactory::getRenderer(
            $this->getParameter(array('render', 'mode')),
            $imagePath
        );
    }

    /**
     * Generate a screenshot
     * 
     * @return imageName
     */
    public function generateScreenshotImage($url = null, $output = null)
    {
        $url = is_null($url) ? $this->getUrl() : $url;
        $output = is_null($output) ? $this->getImagePath() : $output;

        // Generating the screenshot using phantomjs
        $command = sprintf("%s %s/../Lib/imageRender.js %s %s",
            $this->getParameter("phantomjs_bin_path"),
            __DIR__,
            $url,
            $output
        );

        // How check if the command works ?
        $this->setScreenshotPath(trim(shell_exec($command)));
        
        return $this->getScreenshotPath();
    }

    /**
     * Get the url
     * 
     * @return string url
     */
    public function getUrl()
    {
        return $this->getParameter('url');
    }

    /**
     * Is cache enabled
     * 
     * @return boolean
     */
    public function isCacheEnabled()
    {
        return $this->getParameter(array("cache", "enabled"));
    }

    /**
     * Get cache ttl
     * 
     * @return int
     */
    public function getCacheTTL()
    {
        return $this->getParameter(array("cache", "delay"));
    }

    /**
     * Encode an image in base64
     * 
     * @return string: the base64-encoded image
     */
    public function base64EncodeImage($fileName)
    {
        $pathParts = pathinfo($fileName);

        if (file_exists($fileName)) {
            $imgbinary = fread(fopen($fileName, "r"), filesize($fileName));
            return sprintf("data:image/%s;base64,%s", $pathParts['extension'], base64_encode($imgbinary));
        }
    }

    /**
     * Resize an image
     *
     * @param string $url the url to generate the name of the image
     * @param array $params image height, width, format
     * @param string $imageName the path of the image to be resized
     */
    public function resizeScreenShotImage($url, $params, $imageName)
    {   
        return $this->getImageHandler()
             ->open(sprintf("%s%s", $this->getCacheDirectory(), $imageName))
             ->resize($params["width"], $params["height"])
             ->save(sprintf("%s%s",
                 $this->getCacheDirectory(), $this->generateImageName($url, $params)),
                 $params["format"]
              )
        ;
    }

    /**
     * Generate an identifier for an image
     *
     * @param boolean $hash
     * @return string
     */
    public function getImageIdentifier($hash = false)
    {
        $urlArray = parse_url($this->getUrl());
        if(isset($urlArray['path'])) {
            $imageName = str_replace("/","_", sprintf("%s%s",
                $urlArray['host'],
                $urlArray['path']
            ));
        } else {
            $imageName = $urlArray['host'];
        }

        $id = sprintf("%s.%s", $imageName, $this->getParameter(array('render', 'format')));
        
        return $hash ? md5($id) : $id;
    }

    /**
     * Cache an image
     *
     * @param string $cacheId
     */
    protected function cacheImage($cacheID)
    {
        $this->getCache()->save(
            $cacheID,
            $this->getScreenshotPath(),
            $this->getCacheTTL()
        );
    }
    
    /**
     * Get the image path
     * 
     * @return string : the image full path
     */
    protected function getImagePath()
    {
        return sprintf('%s/%s', $this->getCacheDirectory(), $this->getImageIdentifier());
    }

    /**
     * Get the cache directory path
     *
     * @return string : the directory path
     */
    public function getCacheDirectory()
    {
        return $this->getParameter(array('cache', 'directory'));
    }
    
    
    /**
     * Check the given url
     * 
     * @param string $url
     */
    public static function checkUrl($url)
    {
        if(!filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED | FILTER_FLAG_HOST_REQUIRED)) {
            throw new UrlNotValidException($url);
        }

        return $url;
    }

    /**
     * Check the given format
     * 
     * @param string $format
     * @throws UnavailableRenderFormatException
     */
    public static function checkFormat($format)
    {
        if (!in_array($format, self::$AVAILABLE_FORMATS)) {
            throw new UnavailableRenderFormatException($format);
        }

        return $format;
    }

    /**
     * Check the given mode
     * 
     * @param string $mode
     * @throws UnavailableRenderModeException
     */
    public static function checkMode($mode)
    {
        if (!in_array($mode, self::$AVAILABLE_MODES)) {
            throw new UnavailableRenderModeException($mode);
        }

        return $mode;
    }
    
    /**
     * Check the given width
     * 
     * @param int $width
     */
    public static function checkWidth($width)
    {
        //TODO
        return $width;
    }
    
    /**
     * Check the given height
     * 
     * @param int $height
     */
    public static function checkHeight($height)
    {
        //TODO
        return $height;
    }
}