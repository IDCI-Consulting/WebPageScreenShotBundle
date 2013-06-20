<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace IDCI\Bundle\WebPageScreenShotBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class FrontController extends Controller
{
    /**
     * Controller
     *
     * @Route("/screenshot")
     */
    public function screenAction(Request $request)
    {
        $response = $this->get('idci_web_page_screen_shot.manager')->getScreenShot($request);

        return $response;
    }
}