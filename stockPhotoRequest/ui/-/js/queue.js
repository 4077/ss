// head {
var __nodeId__ = "ss_stockPhotoRequest_ui__queue";
var __nodeNs__ = "ss_stockPhotoRequest_ui";
// }

(function (__nodeNs__, __nodeId__) {
    $.widget(__nodeNs__ + "." + __nodeId__, $.ewma.node, {
        options: {},

        __create: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            w.bind();
            w.bindEvents();
        },

        bind: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            $(".camera_button", $w).on("click", function () {
                w.r('camera', {
                    request_id: $(this).closest(".request").attr("request_id")
                });
            });
        },

        bindEvents: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            w.e('ss/stockPhotoRequest/update', function () {
                w.r('reload');
            });
        }
    });
})(__nodeNs__, __nodeId__);
