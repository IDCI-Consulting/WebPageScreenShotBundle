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
        $response = new Response();

        $output = $this->get('idci_web_page_screen_shot.manager')->getScreenShot($request);

        //TODO
        //$image = imagecreatefrompng(getcwd()."/screen_cache/tata.com.png"   );
        $response->headers->set('Content-Type', 'text/html');
        $response->setContent("");
        return $response;
    }
}