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
use Symfony\Component\HttpFoundation\Response;

/**
 * Description of Manager
 *
 * @author baptiste
 */
class Manager
{
    protected $parameters;

    public function __construct($parameters)
    {
        $this->setParameters($parameters);
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
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

        $command = "phantomjs"
            ." ../vendor/idci/webpagescreenshot-bundle/IDCI/Bundle/WebPageScreenShotBundle/Resources/public/js/imageRender.js "
            .$url. " "
            .$format. " "
            .$request->getHost()
        ;

        $screenshot = shell_exec($command);

        //TODO - use lipimaginebundle to resize the picture and delete the big one, then if asked encode it in base64

        $response = new Response($screenshot);
        $response->headers->set('Content-Type', 'text/plain');

        return $response;
    }

    public function getRenderParameter($parameter, $request)
    {
        $parameterValue = $this->parameters['render'][$parameter];
        if ($request->query->get($parameter) != null) {
            $parameterValue = $request->query->get($parameter);
        }

        return $parameterValue;
    }
}

?>
