// head {
var __nodeId__ = "ss_cats_ui__main";
var __nodeNs__ = "ss_cats_ui";
// }

(function (__nodeNs__, __nodeId__) {
    $.widget(__nodeNs__ + "." + __nodeId__, $.ewma.node, {
        options: {},

        __create: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            w.e('ss/cat/update_cats.' + o.catId, function (data) {
                if (o.catId === data.id) {
                    w.mr('reload');
                }
            });

            $(window).on("beforeunload", function () {
                w.mr('pageClose');
            });
        }
    });
})(__nodeNs__, __nodeId__);
