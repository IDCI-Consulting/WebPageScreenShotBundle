<?php

/**
 * 
 * @author:  Baptiste BOUCHEREAU <baptiste.bouchereau@idci-consulting.fr>
 * @license: GPL
 *
 */

namespace IDCI\Bundle\WebPageScreenShotBundle\Exceptions;

class UnavailableRenderModeException extends \Exception {
    
    public function __construct($mode)
    {
        parent::__construct(sprintf("%s mode isn't available", $mode), 0, null);
    }
}
