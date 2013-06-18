var page = require('webpage').create();
var args = require('system').args;

var url = args[1];
var format = args[2];
var path = args[3];

page.viewportSize = { width: 1024, height: 768 };
page.clipRect = { top: 0, left: 0, width: 1024, height: 768 };
page.open(url, function () {
    var image = page.renderBase64(format);
    console.log(image);
    phantom.exit();
});