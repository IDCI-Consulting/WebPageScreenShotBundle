var page = require('webpage').create();
var args = require('system').args;

var url = args[1];
var format = args[2];
var cacheDir = args[3];

page.viewportSize = { width: 1200, height: 800 };
page.clipRect = { top: 0, left: 0, width: 1200, height: 800 };
page.open(url, function () {
    var imageName = getFileName(url, format);
    page.render(cacheDir+imageName);
    console.log(cacheDir+imageName);
    phantom.exit();
});

function getFileName(url, format)
{
    var fileName = url.substring(7)+"."+format;
    return fileName.replace("/",".");;
}