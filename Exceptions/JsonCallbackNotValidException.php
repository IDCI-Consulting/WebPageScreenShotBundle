<?php

/**
 * 
 * @author:  Baptiste BOUCHEREAU <baptiste.bouchereau@idci-consulting.fr>
 * @license: GPL
 *
 */

namespace IDCI\Bundle\WebPageScreenShotBundle\Exceptions;

class JsonCallbackNotValidException extends \Exception {
    
    public function __construct($jsoncallback)
    {
        parent::__construct(sprintf("%s is not a valid jsonCallback.", $jsoncallback), 0, null);
    }
}
