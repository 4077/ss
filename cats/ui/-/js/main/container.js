// head {
var __nodeId__ = "ss_cats_ui__main_container";
var __nodeNs__ = "ss_cats_ui";
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

            w.e('ss/container/update.' + o.catId, function (data) {
                if (o.catId === data.id && isset(data.published)) {
                    $("> .cp > .not_published_mark", $w).toggle(!data.published);
                }
            });
        }
    });
})(__nodeNs__, __nodeId__);
