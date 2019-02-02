// head {
var __nodeId__ = "ss_cats_cp_pagesTree__main";
var __nodeNs__ = "ss_cats_cp_pagesTree";
// }

(function (__nodeNs__, __nodeId__) {
    $.widget(__nodeNs__ + "." + __nodeId__, $.ewma.node, {
        options: {},

        __create: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            w.bindEvents(w, o, $w);
        },

        // todo events
        bindEvents: function (w, o, $w) {
            $(".page_node", $w).each(function () {
                var $node = $(this);

                var catId = $node.attr("cat_id");

                w.e('ss/page/' + catId + '/update_name', function (data) {
                    $("> .name", $node).html(data.shortName || data.name);
                });

                w.e('ss/page/' + catId + '/toggle_enabled', function (data) {
                    $node.toggleClass("disabled", !data.enabled);
                });

                w.e('ss/page/' + catId + '/toggle_published', function (data) {
                    $node.toggleClass("not_published", !data.published);
                });
            });
        }
    });
})(__nodeNs__, __nodeId__);
