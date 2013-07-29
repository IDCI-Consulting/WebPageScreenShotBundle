<?php

/**
 * 
 * @author:  Baptiste BOUCHEREAU <baptiste.bouchereau@idci-consulting.fr>
 * @license: GPL
 *
 */

namespace IDCI\Bundle\WebPageScreenShotBundle\Exceptions;

class WidthNotValidException extends \Exception {
    
    public function __construct($width, $min, $max)
    {
        parent::__construct(sprintf("%s is not a valid width. Must be an integer between %d and %d", $width, $min, $max), 0, null);
    }
}
