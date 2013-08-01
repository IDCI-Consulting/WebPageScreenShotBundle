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
use IDCI\Bundle\WebPageScreenShotBundle\Exceptions\UndefinedRendererException;
use IDCI\Bundle\WebPageScreenShotBundle\Exceptions\UrlNotValidException;
use IDCI\Bundle\WebPageScreenShotBundle\Exceptions\WidthNotValidException;
use IDCI\Bundle\WebPageScreenShotBundle\Exceptions\HeightNotValidException;
use IDCI\Bundle\WebPageScreenShotBundle\Exceptions\MissingParameterException;
use IDCI\Bundle\WebPageScreenShotBundle\Exceptions\MissingUrlException;
use IDCI\Bundle\WebPageScreenShotBundle\Renderer\RendererInterface;
use Gregwar\ImageBundle\Services\ImageHandling;
use Doctrine\Common\Cache\PhpFileCache;

class Manager
{
    const MAX_WIDTH = 1440;
    const MAX_HEIGHT = 900;
    public static $AVAILABLE_FORMATS    = array("gif", "png", "jpeg", "jpg");
    public static $AVAILABLE_MODES      = array("url", "file", "base64");
    public static $RENDER_PARAMETERS    = array("mode", "format", "width", "height");
    public static $CACHE_PARAMETERS     = array("enabled", "delay");
    protected $configurationParameters;
    protected $givenParameters;
    protected $imageHandler;
    protected $cache;
    protected $renderers;
    protected $screenshotPath;
    protected $resizedScreenshotPath;

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
     * Get resized screenshot path
     * 
     * @return string
     */
    protected function getResizedScreenshotPath()
    {
        return $this->resizedScreenshotPath;
    }

    /**
     * Set resized screenshot path
     * 
     * @param string $path
     */
    protected function setResizedScreenshotPath($path)
    {
        $this->resizedScreenshotPath = $path;
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
     * Add renderer
     *
     * @param RendererInterface $renderer
     */
    public function addRenderer(RendererInterface $renderer)
    {
        $this->renderers[$renderer->getName()] = $renderer;
    }

    /**
     * Get notifier
     *
     * @param string $notifierServiceName
     * @return NotifierInterface
     */
    public function getNotifier($rendererName)
    {
        if (!isset($this->renderers[$rendererName])) {
            throw new UndefinedRendererException();
        }

        return $this->notifiers[$rendererName];
    }

    /**
     * Get renderer
     * 
     * @return RendererInterface;
     */
    public function getRenderer()
    {
        $rendererName = $this->getParameter(array('render', 'mode'));
        if (!isset($this->renderers[$rendererName])) {
            throw new UndefinedRendererException();
        }

        $renderer = $this->renderers[$rendererName];
        $renderer->setScreenshotPath($this->getResizedScreenshotPath());

        return $renderer;
    }

    /**
     * Renderer
     * 
     * @return RendererInterface;
     */
    public function render()
    {
        return $this->getRenderer()->render();
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
     * @return IDCI\Bundle\WebPageScreenShotBundle\Service\Manager
     */
    public function capture($givenParameters = array())
    {
        $this->setGivenParameters($givenParameters);
        self::checkFormat($this->getParameter(array('render', 'format')));

        // Check if the cache is enabled and if the image is in cache
        if ($this->isCacheEnabled()) {
            $imagePath = $this->getCache()->fetch($this->getImageIdentifier(true));
            $this->setScreenshotPath($imagePath);
        }

        if(!$imagePath) {
            // Generating the screenshot
            $this->generateScreenshot();
            if($this->isCacheEnabled()) {
                // Add the captured image in the cache
                $this->cacheImage($this->getImageIdentifier(true));
            }
        }

        return $this;
    }

    /**
     * Generate a screenshot
     * 
     * @return imageName
     */
    public function generateScreenshot($url = null, $output = null)
    {
        $url = is_null($url) ? $this->getUrl() : $url;
        $output = is_null($output) ? $this->getOutputPath() : $output;

        // Generating the screenshot using phantomjs
        $command = sprintf("%s %s/../Lib/imageRender.js %s %s",
            $this->getParameter("phantomjs_bin_path"),
            __DIR__,
            $url,
            $output
        );

        // How to check if the command works ?
        $this->setScreenshotPath(trim(shell_exec($command)));

        return $this->getScreenshotPath();
    }

    /**
     * Resize an image
     * 
     * @param string $imageName the path of the image to be resized
     * @param string $format the ouput format
     * @param string $width the ouput width
     * @param string $height the ouput height
     * 
     * @return mixed
     */
    public function resizeImage()
    {
        $resizedImageName = $this->getResizedImageName();

        $resizedImagePath = sprintf("%s%s",
           $this->getCacheDirectory(),
           $resizedImageName
        );

        // Check if the cache is enabled and if the row image is in cache
        if ($this->isCacheEnabled()) {
            $imagePath = $this->getCache()->fetch($this->getImageIdentifier(true));
        }

        // Check if the resized screenshot exists
        if ($imagePath && file_exists($resizedImagePath)) {
            $this->setResizedScreenshotPath($resizedImagePath);

            return $this;
        }

        if (!$imagePath) {
            throw new \Exception("Cannot resize an non existing image"); //TODO create the exception in the exception directory
        } else {
            //resize the screenshot
            $this
                ->getImageHandler()
                ->open(sprintf("%s%s",
                    $this->getCacheDirectory(),
                    $this->getImageIdentifier())
                )
                ->resize(
                    $this->getParameter(array('render', 'width')),
                    $this->getParameter(array('render', 'height'))
                )
                ->save(sprintf("%s%s",
                    $this->getCacheDirectory(), $resizedImageName),
                    $this->getParameter(array('render', 'format'))
                )
            ;
            $this->setResizedScreenshotPath($resizedImagePath);
        }

        return $this;
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
     * Get the identifier of an image
     *
     * @param boolean $hash
     * @return string
     */
    public function getImageIdentifier($hash = false)
    {
        $urlArray = parse_url($this->getUrl());
        if(isset($urlArray['path'])) {
            $imageName = str_replace("/", "_", sprintf("%s%s",
                $urlArray['host'],
                $urlArray['path']
            ));
        } else {
            $imageName = $urlArray['host'];
        }

        $id = sprintf("%s.%s",
            $imageName,
            $this->getParameter(array('render', 'format'))
        );

        return $hash ? md5($id) : $id;
    }

    /**
     * Get the resized name of an image
     *
     * @return string
     */
    public function getResizedImageName()
    {
        return sprintf("%sx%s_%s",
            $this->getParameter(array('render', 'width')),
            $this->getParameter(array('render', 'height')),
            $this->getImageIdentifier()
        );
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
     * Get the output path
     * 
     * @return string : the output full path
     */
    protected function getOutputPath()
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
        $minMax = array(
            "options"=> array(
                "min_range"=>0,
                "max_range"=>self::MAX_WIDTH
            )
        );

        if(!filter_var($width, FILTER_VALIDATE_INT, $minMax)) {
            throw new WidthNotValidException($width, 0, self::MAX_WIDTH);
        }

        return $width;
    }

    /**
     * Check the given height
     * 
     * @param int $height
     */
    public static function checkHeight($height)
    {
        $minMax = array(
            "options"=> array(
                "min_range"=>0,
                "max_range"=>self::MAX_HEIGHT
            )
        );

        if(!filter_var($height, FILTER_VALIDATE_INT, $minMax)) {
            throw new HeightNotValidException($height, 0, self::MAX_HEIGHT);
        }

        return $height;
    }
}