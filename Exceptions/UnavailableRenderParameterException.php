<?php

/**
 * 
 * @author:  Baptiste BOUCHEREAU <baptiste.bouchereau@idci-consulting.fr>
 * @license: GPL
 *
 */

namespace IDCI\Bundle\WebPageScreenShotBundle\Exceptions;

class UnavailableRenderParameterException extends \Exception {
    
    public function __construct($parameter)
    {
        parent::__construct(sprintf("%s parameter isn't available", $parameter), 0, null);
    }
}