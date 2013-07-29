<?php

/**
 * 
 * @author:  Baptiste BOUCHEREAU <baptiste.bouchereau@idci-consulting.fr>
 * @license: GPL
 *
 */

namespace IDCI\Bundle\WebPageScreenShotBundle\Factory;

class ScreenshotFactory
{
    /**
     * getScreenshot
     *
     * @param $mode
     * @param $screenshotPath
     * @return Object
     */
    public static function getScreenshot($mode, $screenshotPath)
    {
        $mode = ucfirst(strtolower($mode));
        $screenshotClassName = sprintf('IDCI\Bundle\WebPageScreenShotBundle\Model\%sScreenshot', $mode);
        $screenshot = new $screenshotClassName($screenshotPath);

        return $screenshot;
    }
}
