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
     * @Route("/", name="webpagescreenshot_api")
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

        $screenshot = $this->get('idci_web_page_screen_shot.manager')->createScreenshot($url, $params);

        if ($callback = $request->query->get("jsoncallback")) {
            //json
            try {
                /*return a query like ?url=http://website.com&height=200, of an image already created.
                  The web service (domain.com/screenshot) url is client side.*/
                //file
                $image = new \Symfony\Component\HttpFoundation\File\File($screenshot);
                $params = array_diff(
                    $request->query->all(),
                    array("jsoncallback" => $request->query->get('jsoncallback'))
                );

                $query = '?';
                $lenght = count($params);
                $i = 0;
                foreach ($params as $key => $value) {
                    if($i == $lenght - 1) {
                        $query = sprintf("%s%s=%s", $query, $key, $value);
                    } else {
                        $query = sprintf("%s%s=%s&", $query, $key, $value);
                    }
                    $i++;
                }
                $json = json_encode($query);
            } catch(\Exception $e) {
                //base64
                $json = json_encode($screenshot);
            }
            $response = new Response(sprintf("%s(%s)", $callback, $json));
            $response->headers->set('Content-Type', 'application/json');
        } else {
            try {
                //file
                $image = new \Symfony\Component\HttpFoundation\File\File($screenshot);
                $imgData = file_get_contents($image);
                $response = new Response($imgData);
                $response->headers->set('Content-Type', $image->getMimeType());
            } catch(\Exception $e) {
                //base64
                $response = new Response($screenshot);
                $response->headers->set('Content-Type', 'text/html');
            }
        }

        $response->setStatusCode(200);

        return $response;
    }
}