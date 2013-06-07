<?php

/**
 * 
 * @author:  Baptiste BOUCHEREAU <baptiste.bouchereau@idci-consulting.fr>
 * @licence: GPL
 *
 */

namespace IDCI\Bundle\WebPageScreenShotBundle\Service;

use Symfony\Component\HttpFoundation\Request;

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

    public function generateScreenShot(Request $request)
    {
        $url = $request->query->get('url');
        $mode = $this->getRenderParameter('mode', $request);
        $format = $this->getRenderParameter('format', $request);
        $width = $this->getRenderParameter('width', $request);
        $height = $this->getRenderParameter('height', $request);
        $path = $this->getRenderParameter('path', $request);

        return shell_exec(
            sprintf(
            'phantomjs ../vendor/idci/webpagescreenshot-bundle/IDCI/Bundle/WebPageScreenShotBundle/Service/pageRender.js 
            %s %s %s %s %s %s', $url, $mode, $format, $width, $height, $path
            )
        );
    }

    public function getRenderParameter($parameter, $request)
    {
        $parameterValue = $this->parameters['render'][$parameter];
        if ($request->query->get($parameter) != null) {
            $parameterValue = $request->query->get($parameter);
        }
        return $parameterValue;
    }

    public function getInCache($file)
    {
        
    }

    public function setInCache($file)
    {
        
    }
}

?>
