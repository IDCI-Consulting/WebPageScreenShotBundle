<?php

/**
 * 
 * @author:  Baptiste BOUCHEREAU <baptiste.bouchereau@idci-consulting.fr>
 * @license: GPL
 *
 */

namespace IDCI\Bundle\WebPageScreenShotBundle\Exceptions;

class HeightNotValidException extends \Exception {
    
    public function __construct($height, $min, $max)
    {
        parent::__construct(sprintf("%s is not a valid height. Must be an integer between %d and %d", $height, $min, $max), 0, null);
    }
}
