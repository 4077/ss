// head {
var __nodeId__ = "ss_flow_ui_channels__main_channelContextmenu";
var __nodeNs__ = "ss_flow_ui_channels";
// }

(function (__nodeNs__, __nodeId__) {
    $.widget(__nodeNs__ + "." + __nodeId__, $.ewma.node, {
        options: {},

        __create: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            w.bind();
        },

        channelId: false,

        bind: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            var $deleteButton = $(".delete_button", $w);

            $deleteButton.click(function () {
                if (w.channelId) {
                    w.r('delete', {
                        channel_id: w.channelId
                    });
                }
            });
        }
    });
})(__nodeNs__, __nodeId__);
