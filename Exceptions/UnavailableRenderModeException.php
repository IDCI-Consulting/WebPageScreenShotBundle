<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace IDCI\Bundle\WebPageScreenShotBundle\Exceptions;

/**
 * Description of UnavailableRenderFormatException
 *
 * @author baptiste
 */
class UnavailableRenderModeException extends \Exception {
    
    public function __construct($mode)
    {
        parent::__construct(sprintf("%s mode isn't available", $mode), 0, null);
    }
}

?>
