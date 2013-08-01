<?php

/**
 * 
 * @author:  Baptiste BOUCHEREAU <baptiste.bouchereau@idci-consulting.fr>
 * @license: GPL
 *
 */

namespace IDCI\Bundle\WebPageScreenShotBundle\Renderer;

use Symfony\Component\HttpFoundation\File\File;

class FileRenderer extends AbstractRenderer
{
    protected $mimeType;

    public function getName()
    {
        return "file";
    }

    /**
     * Get mime-type
     * 
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * Set mime-type
     * 
     * @param string the mime-type
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;
    }

    public function render()
    {
        parent::render();
        $image = new File($this->getScreenshotPath());
        $this->setMimeType($image->getMimeType());
        $imgData = file_get_contents($image);

        return $imgData;
    }

}
