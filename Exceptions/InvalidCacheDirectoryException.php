<?php

/**
 * 
 * @author:  Baptiste BOUCHEREAU <baptiste.bouchereau@idci-consulting.fr>
 * @license: GPL
 *
 */

namespace IDCI\Bundle\WebPageScreenShotBundle\Exceptions;

class InvalidCacheDirectoryException extends \Exception {
    
    public function __construct()
    {
        parent::__construct("The cache directory indicated in the parameters.yml file must end by '/'", 0, null);
    }
}

?>
