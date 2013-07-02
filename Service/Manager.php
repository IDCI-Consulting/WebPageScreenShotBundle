<?php

/**
 * 
 * @author:  Baptiste BOUCHEREAU <baptiste.bouchereau@idci-consulting.fr>
 * @licence: GPL
 *
 */

namespace IDCI\Bundle\WebPageScreenShotBundle\Service;

use IDCI\Bundle\WebPageScreenShotBundle\Exceptions\UnavailableRenderFormatException;
use IDCI\Bundle\WebPageScreenShotBundle\Exceptions\UnavailableRenderModeException;

class Manager
{
    protected $configurationParameters;
    protected $givenParameters;

    public function __construct($defaultParameters)
    {
        $this->setDefaultParameters($defaultParameters);
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

        $command = sprintf("%s %s/../Lib/imageRender.js %s %s",
                $this->configurationParameters['phantomjs_bin_path'],
                __DIR__,
                $url,
                $format
        );

        $path = shell_exec($command);

        return $path;
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
}

?>
