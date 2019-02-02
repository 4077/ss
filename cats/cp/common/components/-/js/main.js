// head {
var __nodeId__ = "ss_cats_cp_common_components__main";
var __nodeNs__ = "ss_cats_cp_common_components";
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

            w.e('ss/cat/components_update.' + o.catId, function () {
                w.mr('reload');
            });
        }
    });
})(__nodeNs__, __nodeId__);
