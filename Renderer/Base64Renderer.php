<?php

/**
 * 
 * @author:  Baptiste BOUCHEREAU <baptiste.bouchereau@idci-consulting.fr>
 * @license: GPL
 *
 */

namespace IDCI\Bundle\WebPageScreenShotBundle\Renderer;

class Base64Renderer extends AbstractRenderer
{
    public function render()
    {
        
    }

    /**
     * Encode an image in base64
     * 
     * @return string: the base64-encoded image
     */
    protected function base64EncodeImage($filePath)
    {
        $pathParts = pathinfo($filePath);

        if (file_exists($filePath)) {
            $imgBinary = fread(fopen($filePath, "r"), filesize($filePath));

            return sprintf("data:image/%s;base64,%s", $pathParts['extension'], base64_encode($imgBinary));
        }
    }
}