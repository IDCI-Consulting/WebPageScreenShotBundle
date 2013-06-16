<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace IDCI\Bundle\WebPageScreenShotBundle\Exceptions;

/**
 * Description of urlMissingException
 *
 * @author baptiste
 */
class UrlMissingException extends \Exception {
    
    public function __construct()
    {
        parent::__construct("Error : Url is missing", 0, null);
    }
}

?>
