<?php

/**
 * @author baptiste
 */

namespace IDCI\Bundle\WebPageScreenShotBundle\Model;

use Symfony\Component\HttpFoundation\File\File;

class FileScreenshot extends Screenshot
{
    public function __construct($screenshotPath)
    {
        $image = new File($screenshotPath);
        $imgData = file_get_contents($image);

        $this->setMimeType($image->getMimeType())
             ->setContent($imgData);
    }
}

?>
