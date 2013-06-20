var page = require('webpage').create();
var args = require('system').args;

var url = args[1];
var format = args[2];
var serverName = args[3];

page.viewportSize = { width: 1024, height: 768 };
page.clipRect = { top: 0, left: 0, width: 1024, height: 768 };
page.open(url, function () {
    var imageName = getFileName(url)+"."+format;
    page.render("screenshots/"+imageName);
    console.log("http://"+serverName+"/screenshots/"+imageName);
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