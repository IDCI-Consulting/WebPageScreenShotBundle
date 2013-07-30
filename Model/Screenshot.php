<?php

/**
 * @author baptiste
 */

namespace IDCI\Bundle\WebPageScreenShotBundle\Model;

abstract class Screenshot
{
    protected $content;
    protected $mimeType;

    public function __toString()
    {
        return $this->getContent();
    }

    /**
     * Get the screenshot content
     * 
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set the screenshot content
     * 
     * @param mixed content
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get the screenshot mime-type
     * 
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * Set the screenshot mime-type
     * 
     * @param string $mimeType
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;

        return $this;
    }
}

?>
