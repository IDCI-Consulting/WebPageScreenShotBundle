<?php

/**
 * 
 * @author:  Baptiste BOUCHEREAU <baptiste.bouchereau@idci-consulting.fr>
 * @license: GPL
 *
 */

namespace IDCI\Bundle\WebPageScreenShotBundle\Exceptions;

class MissingParameterException extends \Exception {
    
    public function __construct($param)
    {
        parent::__construct(sprintf("Parameter %s is missing", $param), 0, null);
    }
}

?>
