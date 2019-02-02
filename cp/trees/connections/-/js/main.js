// head {
var __nodeId__ = "ss_cp_trees_connections__main";
var __nodeNs__ = "ss_cp_trees_connections";
// }

(function (__nodeNs__, __nodeId__) {
    $.widget(__nodeNs__ + "." + __nodeId__, $.ewma.node, {
        options: {},

        __create: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            $(".tree_selector .selected", $w).bind("click", function (e) {
                var $dropdown = $(this).find(".dropdown");

                $dropdown.toggle();

                e.stopPropagation();

                $(window).rebind("click." + __nodeId__, function () {
                    $dropdown.hide();
                });
            });
        }
    });
})(__nodeNs__, __nodeId__);
