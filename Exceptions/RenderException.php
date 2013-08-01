<?php

/**
 * 
 * @author:  Baptiste BOUCHEREAU <baptiste.bouchereau@idci-consulting.fr>
 * @license: GPL
 *
 */

namespace IDCI\Bundle\WebPageScreenShotBundle\Exceptions;

class RenderException extends \Exception {
    
    public function __construct()
    {
        parent::__construct("Render is not possible as there is no screenshot available from the renderer", 0, null);
    }
}
