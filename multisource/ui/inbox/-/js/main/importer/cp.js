// head {
var __nodeId__ = "ss_multisource_ui_inbox__main_importer_cp";
var __nodeNs__ = "ss_multisource_ui_inbox";
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

            w.e('ss/multisource/inbox/importerDetected', function (data) {
                if (data.attachmentXPack === o.attachmentXPack) {
                    w.mr('reload', {
                        attachment: data.attachmentXPack
                    });
                }
            });

            w.e('ss/multisource/inbox/importComplete', function (data) {
                if (data.attachmentXPack === o.attachmentXPack) {
                    w.mr('reload', {
                        attachment: data.attachmentXPack
                    });
                }
            });
        },
    });
})(__nodeNs__, __nodeId__);
