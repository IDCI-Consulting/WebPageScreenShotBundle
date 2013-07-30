<?php

/**
 * 
 * @author:  Baptiste BOUCHEREAU <baptiste.bouchereau@idci-consulting.fr>
 * @license: GPL
 *
 */

namespace IDCI\Bundle\WebPageScreenShotBundle\Model;

use Symfony\Component\HttpFoundation\File\File;

class FileScreenshot extends Screenshot
{
    protected $file;

    public function __construct($screenshotPath)
    {
        $image = new File($screenshotPath);
        $imgData = file_get_contents($image);

        $this->setMimeType($image->getMimeType())
             ->setFile($image)
             ->setContent($imgData);
    }

    public function __toString()
    {
        return $this->getFile()->__toString();
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setFile($file)
    {
        $this->file = $file;
        
        return $this;
    }
}

?>
