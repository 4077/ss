// head {
var __nodeId__ = "ss_multisource_ui_division_importersControls_warehouseSelector__eventsDispatcher";
var __nodeNs__ = "ss_multisource_ui_division_importersControls_warehouseSelector";
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

            w.e('ss/multisource/importers/warehouseSelect', function (data) {
                var $selector = $("." + o.nodeId + "[instance='" + data.importerId + "']");

                if ($selector.length) {
                    w.r('reload', {
                        importer: data.importerXPack
                    });
                }
            })
        }
    });
})(__nodeNs__, __nodeId__);
