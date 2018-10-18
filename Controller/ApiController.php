<?php

/**
 * 
 * @author:  Baptiste BOUCHEREAU <baptiste.bouchereau@idci-consulting.fr>
 * @license: GPL
 *
 */

namespace IDCI\Bundle\WebPageScreenShotBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use IDCI\Bundle\WebPageScreenShotBundle\Renderer\FileRenderer;
use IDCI\Bundle\WebPageScreenShotBundle\Renderer\Base64Renderer;
use IDCI\Bundle\WebPageScreenShotBundle\Renderer\UrlRenderer;

/**
 * Api controller.
 *
 * @Route("/screenshot")
 */
class ApiController extends Controller
{
    /**
     * Controller
     *
     * @Route("/capture", name="idci_webpagescreenshot_api_capture")
     */
    public function captureAction(Request $request)
    {
        $screenshotManager = $this->get('idci_web_page_screen_shot.manager');

        $renderer = $screenshotManager
            ->capture($request->query->all())
            ->resizeImage()
            ->getRenderer()
        ;

        if ($renderer instanceof FileRenderer) {
            $response = new Response($renderer->render());
            $response->headers->set('Content-Type', $renderer->getMimeType());
            $response->setStatusCode(200);

            return $response;
        }

        if ($renderer instanceof Base64Renderer || $renderer instanceof UrlRenderer) {
            $response = new Response($renderer->render());
            $response->headers->set('Content-Type', "text/plain");
            $response->setStatusCode(200);

            return $response;
        }
    }

    /**
     * Controller
     *
     * @Route("/get/{name}", name="idci_webpagescreenshot_api_getcapture")
     */
    public function getCaptureAction($name)
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
