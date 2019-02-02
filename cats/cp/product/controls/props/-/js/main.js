// head {
var __nodeId__ = "ss_cats_cp_product_controls_props__main";
var __nodeNs__ = "ss_cats_cp_product_controls_props";
// }

(function (__nodeNs__, __nodeId__) {
    $.widget(__nodeNs__ + "." + __nodeId__, $.ewma.node, {
        options: {},

        __create: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;


        },

        onPropsUpdate: function (data) {
            var w = this;
            var o = w.options;

            if (o.productId === data.id && isset(data.props)) {
                w.mr('reload');
            }
        }
    });
})(__nodeNs__, __nodeId__);
