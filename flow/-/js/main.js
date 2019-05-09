// head {
var __nodeId__ = "ss_flow__main";
var __nodeNs__ = "ss_flow";
// }

(function (__nodeNs__, __nodeId__) {
    $.widget(__nodeNs__ + "." + __nodeId__, $.ewma.node, {
        options: {},

        w: 0,
        h: 0,
        c: 0,

        __create: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            w.canvasInit();
            w.w = $w.width();
            w.h = $w.height();

            w._renderBg();

            w.c = [w.w / 2, w.h / 2];

            $(window).resize(function () {
                w.w = $w.width();
                w.h = $w.height();

                w._renderBg();

                w.c = [w.w / 2, w.h / 2];
            });

            w.draw();
        },

        draw: function () {
            var w = this;
            var $w = w.element;

            var gridSize = 12;

            var spacing = 1.2;

            for (var y = 0; y < 256; y++) {
                for (var x = 0; x < 256; x++) {
                    var path = new paper.Path.Circle({
                        center: new paper.Point(x + 100, y +110),
                        radius: 1
                    });

                    var r = x & y;
                    var g = 0;
                    var b = 0;

                    path.fillColor = new paper.Color(r, g, b);
                }
            }
        },

        __draw: function () {
            var w = this;
            var $w = w.element;

            function rad(deg) {
                return deg * Math.PI / 180;
            }

            var t = 0;

            var x = setInterval(function () {
                var path = new paper.Path();

                path.strokeWidth = 1;

                t += 1;

                var lfo1 = Math.sin(rad(t));
                var lfo2 = Math.sin(rad(t / 2)) * 5;
                var lfo3 = Math.sin(rad(t)) / 5;

                var x1 = Math.sin(rad(t - 600)) * 700;
                var y1 = Math.cos(rad(t)) * lfo2;

                var x2 = Math.sin(rad(t + 500)) * 1000;
                var y2 = Math.cos(rad(t - 300)) * 200;


                // var x1 = Math.sin(rad(t % lfo1 - lfo2 / 2)) * 300 * lfo1;
                // var y1 = Math.cos(rad(t % lfo1 - lfo2 / 2)) * 300 * lfo1;
                //
                // var x2 = x1 - Math.sin(rad(t % 90 - 45)) * 100;
                // var y2 = y1 - Math.cos(rad(t % 90 - 45)) * 100;


                path.addSegments([[x1 + w.c[0], y1 + w.c[1]], [x2 + w.c[0], y2 + w.c[1]]]);

                var r = lfo3;
                var g = lfo1;
                var b = lfo2;

                path.strokeColor = new paper.Color(r, g, b);

                if (t > 360 * 4) {
                    clearInterval(x);
                }

            }, 1);

            $w.click(function () {
                clearInterval(x);
            });
        },

        canvasInit: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            w.$canvas = $("canvas", $w);

            paper.setup(w.$canvas.get(0));

            w.w = $w.width();
            w.h = $w.height();
        },

        _renderBg: function () {
            var w = this;

            paper.view.viewSize.width = w.w;
            paper.view.viewSize.height = w.h;

            paper.view.draw();
        },
    });
})(__nodeNs__, __nodeId__);
