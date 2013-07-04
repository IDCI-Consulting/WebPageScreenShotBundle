<?php

/**
 * 
 * @author:  Baptiste BOUCHEREAU <baptiste.bouchereau@idci-consulting.fr>
 * @license: GPL
 *
 */

namespace IDCI\Bundle\WebPageScreenShotBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use IDCI\Bundle\WebPageScreenShotBundle\Exceptions\MissingUrlException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FrontController extends Controller
{
    /**
     * Controller
     *
     * @Route("/screenshot")
     */
    public function screenAction(Request $request)
    {
        if (!($url = $request->query->get('url'))) {
            throw new MissingUrlException();
        }

        $params = array_diff(
            $request->query->all(),
            array("url" => $url)
        );

        $screenshot = $this->get('idci_web_page_screen_shot.manager')->createScreenShot($url, $params);
        $response = new Response($screenshot);
        $response->headers->set('Content-Type', 'text/plain');

        return $response;
    }
}