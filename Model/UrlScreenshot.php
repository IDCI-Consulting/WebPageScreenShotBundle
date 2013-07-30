<?php

/**
 * 
 * @author:  Baptiste BOUCHEREAU <baptiste.bouchereau@idci-consulting.fr>
 * @license: GPL
 *
 */

namespace IDCI\Bundle\WebPageScreenShotBundle\Model;

class UrlScreenshot extends Screenshot
{
    protected $query;

    public function __construct($screenshotPath)
    {
        $this->setMimeType("text/plain")
             ->setQuery($this->getQueryFromPath($screenshotPath));
    }

    public function __toString()
    {
        return $this->getQuery();
    }

    /**
     * Get the screenshot query
     * 
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Set the screenshot query
     * 
     * @param string query
     */
    public function setQuery($query)
    {
        $this->query = $query;

        return $this;
    }

    /**
     * Get the screenshot query from his path
     * 
     * @param screenshot the screenshot path
     * @return string the query
     */
    public function getQueryFromPath($screenshotPath)
    {
        //get the width
        preg_match( '/\/\d+x/', $screenshotPath, $match);
        $width = substr(implode("", $match), 1, -1);

        //get the height
        preg_match( '/x\d+_/', $screenshotPath, $match);
        $height = substr(implode("", $match), 1, -1);

        //get the format
        $pathParts = pathinfo($screenshotPath);
        $format = $pathParts['extension'];

        //get the website url
        preg_match( '/_.+'.$format.'/', $screenshotPath, $match);
        $websiteString = implode("", $match);
        $websiteString = substr($websiteString, 1, strrpos($websiteString, '.')-1);
        $websiteUrl = str_replace("_", "/", $websiteString);

        $query = sprintf("?url=http://%s&width=%s&height=%s&mode=file&format=%s", $websiteUrl, $width, $height, $format);

        return $query;
    }
}

?>
