var page = require('webpage').create()
var args = require('system').args;

var url = args[1];
var mode = args[2];
var format = args[3];
var width = args[4];
var height = args[5];
var path = args[6];

/*args.forEach(function(arg, i) {
        console.log(i + ': ' + arg);
    }); //problem todo */

console.log(args[0]);
console.log(args[1]);

page.viewportSize = { width: 1024, height: 768 };
page.clipRect = { top: 0, left: 0, width: 1024, height: 768 };
page.open(url, function () {
    
    page.render('../vendor/idci/webpagescreenshot-bundle/IDCI/Bundle/WebPageScreenShotBundle/Service/render.png');
    console.log('Render done');
    phantom.exit();
});
