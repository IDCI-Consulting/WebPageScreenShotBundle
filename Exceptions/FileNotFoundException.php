<?php

/**
 * 
 * @author:  Baptiste BOUCHEREAU <baptiste.bouchereau@idci-consulting.fr>
 * @license: GPL
 *
 */

namespace IDCI\Bundle\WebPageScreenShotBundle\Exceptions;

class FileNotFoundException extends \Exception {
    
    public function __construct($file)
    {
        parent::__construct(sprintf("The file %s could not be found", $file), 0, null);
    }
}
