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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Api controller.
 *
 * @Route("/")
 */
class ApiController extends Controller
{
    /**
     * Controller
     *
     * @Route("/screenshot", name="webpagescreenshot_api")
     */
    public function screenAction(Request $request)
    {
        $screenshotManager = $this->get('idci_web_page_screen_shot.manager');

        $screenshot = $screenshotManager->capture($request->query->all());
        var_dump($screenshot); die("fin");

        if ($callback = $request->query->get("jsoncallback")) {
            $json = json_encode($image);
            $response = new Response(sprintf("%s(%s);", $callback, $json));
            $response->headers->set('Content-Type', 'application/json');
        } else {
            $response = new Response($image);
            $response->headers->set('Content-Type', $image->getMimeType());
        }

        $response->setStatusCode(200);

        return $response;
    }
}