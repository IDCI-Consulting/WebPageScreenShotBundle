var page = require('webpage').create()
var args = require('system').args;

var url = args[1];
var mode = args[2];
var format = args[3];
var path = args[4];

page.viewportSize = { width: 1024, height: 768 };
page.clipRect = { top: 0, left: 0, width: 1024, height: 768 };
page.open(url, function () {
    
    var image = page.renderBase64("png");
    console.log(image);
    phantom.exit();
});


//var image = path+getFileName(url)+'.'+format;
function getFileName(url)
{
    var fileName = url.substring(7);
    var match = url.match(/http:\/\/www\..*/);
    if (match)
        return  url.substring(11);
    else
        return fileName;
}