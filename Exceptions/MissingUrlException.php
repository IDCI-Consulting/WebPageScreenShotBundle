<?php

/**
 * 
 * @author:  Baptiste BOUCHEREAU <baptiste.bouchereau@idci-consulting.fr>
 * @license: GPL
 *
 */

namespace IDCI\Bundle\WebPageScreenShotBundle\Exceptions;

class MissingUrlException extends \Exception {
    
    public function __construct()
    {
        parent::__construct("Url is missing", 0, null);
    }
}

?>
