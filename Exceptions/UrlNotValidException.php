<?php

/**
 * 
 * @author:  Baptiste BOUCHEREAU <baptiste.bouchereau@idci-consulting.fr>
 * @license: GPL
 *
 */

namespace IDCI\Bundle\WebPageScreenShotBundle\Exceptions;

class UrlNotValidException extends \Exception {
    
    public function __construct($url)
    {
        parent::__construct(sprintf("Url %s is not valid", $url), 0, null);
    }
}
