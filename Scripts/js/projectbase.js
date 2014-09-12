//Variables
var oldtime = new Date().getTime(), WIDTH = 1000, HEIGHT = 1000, FPS = 0, STATUS = 0, usedKeys = [];

/*Resize*/
$(function() {
    function resize() {
        var H = $("#admin-panel").height(),
                W = $("#admin-panel").width();

        $("#tabs").css({
            height: H - 60,
            width: W
        }, 250);

        $("#tabs-area").css({
            height: H - 121,
            width: W - 10
        }, 250);

        $(".body, .footbar").css({
            width: W - 368
        }, 250);
        
        $(".component").css({
            width: W - 368 - 250
        });

        $("#newsbody_ifr").css({
            height: H - 315
        });

        $("#newstags").css({
            width: W - 455
        });

        $(".msm").css({
            height: H - 260
        });

        $("#location").css({
            width: W - 127
        });
        
        $("#explorer").css({
            height: H - 160
        });
    }

    $(window).resize(function() {
        resize();
    });

    setTimeout(function() {
        resize();
    }, 250);
});


var uget = function(params) {
    return $.ajax({
        type: (params.type) ? params.type : 'GET',
        url: (params.url) ? params.url : '',
        data: (params.data) ? params.data : {},
        xhrFields: {
            withCredentials: true
        },
        crossDomain: true
    });
};


function Linker(url) {
    var baseUrl = (url.substring(url.length - 1) === "/") ? url.substring(0, url.length - 1) : url,
            extension = '.php?';

    this.Url = function(controller, action, get) {
        var strget = "";

        for (var key in get) {
            if (!isNaN(key)) {
                strget += get[key] + '/';
            } else {
                strget += key + '=' + get[key] + '&';
            }
        }

        strget = (strget.length > 0) ? strget.substring(0, strget.length - 1) : strget;
        if (controller.length > 0) {
            if (action.length > 0) {
                return baseUrl + '/' + controller + '/' + action + extension + strget;
            } else {
                return baseUrl + '/' + controller;
            }
        } else {
            return baseUrl;
        }
    };

    this.setBaseUrl = function(url) {
        baseUrl = (url.substring(url.length - 1) === "/") ? url.substring(0, url.length - 1) : url;
    };

    this.setExtension = function(ext) {
        extension = ext;
    };
}
;

//Image Loader
var Images = new (function() {
    var img = [], base = "", loaded = 0, count = 0;

    this.setBase = function(_base) {
        base = _base;
    };

    this.add = function(image, directory) {
        img[image] = new Image();
        img[image].src = base + directory;
    };

    this.get = function(image) {
        return img[image];
    };

    this.isLoaded = function() {
        loaded = 0;
        count = 0;
        for (var k in img) {
            if (img.hasOwnProperty(k))
                loaded += (img[k].complete) ? 1 : 0;
            count++;
        }

        return (loaded == count);
    };

    this.Percentloaded = function() {
        return loaded / count;
    };
})();

//Generate an Elastic Canvas
function Elastic(_draw, width, height) {
    var canvas = document.createElement('canvas');
    canvas.width = width;
    canvas.height = height;
    var ctx = canvas.getContext('2d');

    this.draw = function() {
        canvas.width = canvas.width;
        _draw(ctx, canvas);
    };

    this.getImage = function() {
        return canvas;
    };
}
;

//Generate a requestAnimationFrame for any Browser
(function() {
    var lastTime = 0;
    var vendors = ['ms', 'moz', 'webkit', 'o'];
    for (var x = 0; x < vendors.length && !window.requestAnimationFrame; ++x) {
        window.requestAnimationFrame = window[vendors[x] + 'RequestAnimationFrame'];
        window.cancelRequestAnimationFrame = window[vendors[x] +
                'CancelRequestAnimationFrame'];
    }

    if (!window.requestAnimationFrame)
        window.requestAnimationFrame = function(callback, element) {
            var currTime = new Date().getTime();
            var timeToCall = Math.max(0, 16 - (currTime - lastTime));
            var id = window.setTimeout(function() {
                callback(currTime + timeToCall);
            },
                    timeToCall);
            lastTime = currTime + timeToCall;
            return id;
        };

    if (!window.cancelAnimationFrame)
        window.cancelAnimationFrame = function(id) {
            clearTimeout(id);
        };
})();