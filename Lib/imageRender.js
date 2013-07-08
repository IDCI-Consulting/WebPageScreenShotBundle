var page = require('webpage').create();
var args = require('system').args;

var url = args[1];
var format = args[2];
var cacheDir = args[3];

page.viewportSize = { width: 1200, height: 800 };
page.clipRect = { top: 0, left: 0, width: 1200, height: 800 };
page.open(url, function () {
    var regex = new RegExp("[?=/]", "g");
    var imageName = getFileName(url)+"."+format;
    imageName = imageName.replace(regex,".");
    page.render(cacheDir+imageName);
    console.log(cacheDir+imageName);
    phantom.exit();
});

function getFileName(url)
{
    var fileName = url.substring(7);
    var match = url.match(/http:\/\/www\..*/);
    if (match)
        return  url.substring(11);
    else
        return fileName;
}