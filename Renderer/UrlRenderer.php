<?php

/**
 * 
 * @author:  Baptiste BOUCHEREAU <baptiste.bouchereau@idci-consulting.fr>
 * @license: GPL
 *
 */

namespace IDCI\Bundle\WebPageScreenShotBundle\Renderer;

use Symfony\Component\Routing\Router;

class UrlRenderer extends AbstractRenderer
{
    protected $router;

    public function getName()
    {
        return "url";
    }

    public function __construct(Router $router)
    {
        $this->setRouter($router);
    }

    /**
     * Get router
     * 
     * @return Router
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * Set Router
     * 
     * @param Router the router
     */
    public function setRouter($router)
    {
        $this->router = $router;
    }

    public function render()
    {
        parent::render();
        $pathParts = pathinfo($this->getScreenshotPath());
        $url = $this
            ->getRouter()
            ->generate(
                'idci_webpagescreenshot_api_getcapture',
                array('name' => $pathParts['basename']),
                true
            )
        ;

        return $url;
    }
}
