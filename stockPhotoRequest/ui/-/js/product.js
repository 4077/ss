// head {
var __nodeId__ = "ss_stockPhotoRequest_ui__product";
var __nodeNs__ = "ss_stockPhotoRequest_ui";
// }

(function (__nodeNs__, __nodeId__) {
    $.widget(__nodeNs__ + "." + __nodeId__, $.ewma.node, {
        options: {},

        __create: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            w.bindEvents();
        },

        bindEvents: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            w.e('ss/stockPhotoRequest/capture', function (data) {
                if (o.productId === data.productId) {
                    w.r('reload');
                }
            });

            w.e('ss/stockPhotoRequest/update', function (data) {
                if (o.productId === data.productId) {
                    w.r('reload');
                }
            });
        }
    });
})(__nodeNs__, __nodeId__);
