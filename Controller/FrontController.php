<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace IDCI\Bundle\WebPageScreenShotBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class FrontController
{
    public function screenAction(Request $request)
    {
        $request->query->get('render');
        //get defaults if not defined
        
        //Service call
        
        $response = new Response();
       // $response->setContent());
       // $response->headers->set('Content-Type', );

        return $response;
    }

}