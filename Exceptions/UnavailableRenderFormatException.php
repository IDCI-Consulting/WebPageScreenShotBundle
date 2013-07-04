<?php

/**
 * 
 * @author:  Baptiste BOUCHEREAU <baptiste.bouchereau@idci-consulting.fr>
 * @license: GPL
 *
 */

namespace IDCI\Bundle\WebPageScreenShotBundle\Exceptions;

class UnavailableRenderFormatException extends \Exception {
    
    public function __construct($format)
    {
        parent::__construct(sprintf("%s format isn't available", $format), 0, null);
    }
}

?>
