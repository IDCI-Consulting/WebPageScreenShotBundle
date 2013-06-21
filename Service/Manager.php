<?php

/**
 * 
 * @author:  Baptiste BOUCHEREAU <baptiste.bouchereau@idci-consulting.fr>
 * @licence: GPL
 *
 */

namespace IDCI\Bundle\WebPageScreenShotBundle\Service;

use Symfony\Component\HttpFoundation\Request;
use IDCI\Bundle\WebPageScreenShotBundle\Exceptions\UrlMissingException;
use IDCI\Bundle\WebPageScreenShotBundle\Exceptions\UnavailableRenderFormatException;
use IDCI\Bundle\WebPageScreenShotBundle\Exceptions\UnavailableRenderModeException;

/**
 * Description of Manager
 *
 * @author baptiste
 */
class Manager
{
    protected $webPageScreenShotParameters;

    public function __construct($webPageScreenShotParameters)
    {
        $this->setWebPageScreenShotParameters($webPageScreenShotParameters);
    }

        public function getWebPageScreenShotParameters()
    {
        return $this->webPageScreenShotParameters;
    }

    public function setWebPageScreenShotParameters($parameters)
    {
        $this->webPageScreenShotParameters = $parameters;
    }

    public function getScreenShot(Request $request)
    {
        $availableModes = array("base64", "file");
        $availableFormats = array("gif", "png", "jpeg", "jpg");

        $mode = $this->getRenderParameter('mode', $request);
        if (!in_array($mode, $availableModes)) {
            throw new UnavailableRenderModeException($mode);
        }

        $format = $this->getRenderParameter('format', $request);
        if (!in_array($format, $availableFormats)) {
            throw new UnavailableRenderFormatException($format);
        }

        if (!($url = $request->query->get('url'))) {
            throw new UrlMissingException();
        }

        $screenshotBundlePath = "vendor/idci/webpagescreenshot-bundle/IDCI/Bundle/WebPageScreenShotBundle";
        $command = sprintf("phantomjs ../%s/Lib/imageRender.js %s %s %s",
                $screenshotBundlePath,
                $url,
                $format,
                $request->getHost()
        );

        $screenshot = shell_exec($command);

        return $screenshot;
    }

    public function getRenderParameter($parameter, $request)
    {
        $parameterValue = $this->webPageScreenShotParameters['render'][$parameter];
        if ($request->query->get($parameter) != null) {
            $parameterValue = $request->query->get($parameter);
        }

        return $parameterValue;
    }
}

?>
