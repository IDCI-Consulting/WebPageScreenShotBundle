<?php

/**
 *
 * @author:  Baptiste BOUCHEREAU <baptiste.bouchereau@idci-consulting.fr>
 * @license: GPL
 *
 */

namespace IDCI\Bundle\WebPageScreenShotBundle\Renderer;

use IDCI\Bundle\WebPageScreenShotBundle\Exceptions\RenderException;

abstract class AbstractRenderer implements RendererInterface
{
    protected $screenshotPath;

    /**
     * Get screenshot path
     * 
     * @return string
     */
    public function getScreenshotPath()
    {
        return $this->screenshotPath;
    }

    /**
     * Set screenshot path
     * 
     * @param string $path
     */
    public function setScreenshotPath($screenshotPath)
    {
        $this->screenshotPath = $screenshotPath;
    }

    function render()
    {
        if (!$this->getScreenshotPath()) {
            throw new RenderException();
        }
    }
}