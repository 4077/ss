// head {
var __nodeId__ = "ss_cats_cp_container__main_dialogTitle";
var __nodeNs__ = "ss_cats_cp_container";
// }

(function (__nodeNs__, __nodeId__) {
    $.widget(__nodeNs__ + "." + __nodeId__, $.ewma.node, {
        options: {},

        __create: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            w.e('ss/container/update.' + w.uuid, function (data) {
                if (o.catId === data.id) {
                    if (isset(data.name)) {
                        $(".label", $w).html(data.shortName);
                    }

                    if (isset(data.enabled)) {
                        $w.toggleClass("disabled", !data.enabled);
                    }

                    if (isset(data.published)) {
                        $w.toggleClass("not_published", !data.published);
                    }
                }
            });
        }
    });
})(__nodeNs__, __nodeId__);
