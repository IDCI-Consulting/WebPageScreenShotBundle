<?php

/**
 * 
 * @author:  Baptiste BOUCHEREAU <baptiste.bouchereau@idci-consulting.fr>
 * @license: GPL
 *
 */

namespace IDCI\Bundle\WebPageScreenShotBundle\Renderer;

interface RendererInterface
{
    /**
     * Render
     * 
     * @return mixed the rendered screenshot
     */
    public function render();

    /**
     * Get the name
     * 
     * @return string the name of the renderer
     */
    public function getName();
}