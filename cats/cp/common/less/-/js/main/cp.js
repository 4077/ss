// head {
var __nodeId__ = "ss_cats_cp_common_less__main_cp";
var __nodeNs__ = "ss_cats_cp_common_less";
// }

(function (__nodeNs__, __nodeId__) {
    $.widget(__nodeNs__ + "." + __nodeId__, $.ewma.node, {
        options: {},

        __create: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            $(".update_css_button", $w).click(function () {
                w.r('updateCss', {}, false, function (data) {
                    window.location.reload(true);
                });
            });
        }
    });
})(__nodeNs__, __nodeId__);
