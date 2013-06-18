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
        $this->parameters = $parameters;
    }

    public function getBase64ScreenShot(Request $request)
    {
        $availableFormats = array("pdf", "gif", "png", "jpeg", "jpg");
        
        if (!($url = $request->query->get('url'))) {
            throw new UrlMissingException();
        }

        $format = $this->getRenderParameter('format', $request);
        if (!in_array($format, $availableFormats)) {
            throw new UnavailableRenderFormatException($format);
        }
        
        $path = $this->getRenderParameter('path', $request);

        $command = "phantomjs"
                ." ../vendor/idci/webpagescreenshot-bundle/IDCI/Bundle/WebPageScreenShotBundle/Service/pageRender.js "
                .$url. " "
                .$format. " "
                .$path
        ;

        return shell_exec($command);
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
