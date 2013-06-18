<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace IDCI\Bundle\WebPageScreenShotBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
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
        $base64Png = $this->get('idci_web_page_screen_shot.manager')->getBase64ScreenShot($request);

        $response = new Response();
        $response->headers->set('Content-Type', 'text/html');
        $response->setContent($base64Png);

        return $response;
    }
}