<?php

/**
 * 
 * @author:  Baptiste BOUCHEREAU <baptiste.bouchereau@idci-consulting.fr>
 * @license: GPL
 *
 */

namespace IDCI\Bundle\WebPageScreenShotBundle\Exceptions;

class UndefinedRendererException extends \Exception {
    
    public function __construct($rendererName)
    {
        parent::__construct(sprintf("The renderer %s is not defined", $rendererName), 0, null);
    }
}