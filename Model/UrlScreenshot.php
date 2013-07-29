<?php

/**
 * @author baptiste
 */

namespace IDCI\Bundle\WebPageScreenShotBundle\Model;

class UrlScreenshot extends Screenshot
{
    public function __construct($screenshotPath)
    {
        $this->setMimeType("plain/text")
             ->setContent($this->getUrlFromPath($screenshotPath));
    }

    public function getUrlFromPath($screenshotPath)
    {
        //get the width
        preg_match( '/\/\d+x/', $screenshotPath, $match);
        $width = substr(implode("", $match), 1, -1);

        //get the height
        preg_match( '/\xd+_/', $screenshotPath, $match);
        $height = substr(implode("", $match), 1, -1);

        //get the format
        $pathParts = pathinfo($screenshotPath);
        $format = $pathParts['extension'];

        //TODO?
    }
}

?>
