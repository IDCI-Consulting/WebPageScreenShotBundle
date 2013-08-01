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
use IDCI\Bundle\WebPageScreenShotBundle\Renderer\FileRenderer;

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
     * @Route("/screenshot/capture", name="idci_webpagescreenshot_api_capturescreen")
     */
    public function captureScreenAction(Request $request)
    {
        $screenshotManager = $this->get('idci_web_page_screen_shot.manager');

        $screenshot = $screenshotManager
            ->capture($request->query->all())
            ->resizeImage()
            ->getRenderer()
            ->render();
        ;

        $renderer = $screenshotManager->getRenderer();
        if ($callback = $request->query->get("jsoncallback")) {
            $json = json_encode($screenshot);
            $response = new Response(sprintf("%s(%s);", $callback, $json));
            $response->headers->set('Content-Type', 'application/json');
        } else {
            $response = new Response($screenshot);
            if ($renderer instanceof FileRenderer) {
                $response->headers->set('Content-Type', $renderer->getMimeType());
            } else {
                $response->headers->set('Content-Type', "text/plain");
            }
        }

        $response->setStatusCode(200);

        return $response;
    }

    /**
     * Controller
     *
     * @Route("/screenshot/get/{name}", name="idci_webpagescreenshot_api_getscreen")
     */
    public function getScreenAction($name)
    {
        $cacheDirectory = $this->container->getParameter('screenshot_cache_directory');

        $fileRenderer = new FileRenderer();
        $fileRenderer->setScreenshotPath(sprintf("%s%s", $cacheDirectory, $name));

        $response = new Response($fileRenderer->render());
        $response->headers->set('Content-Type', $fileRenderer->getMimeType());
        $response->setStatusCode(200);
 
        return $response;
    }
}