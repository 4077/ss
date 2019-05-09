// head {
var __nodeId__ = "ss_multisource_ui_inbox__main";
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
            w.bind();

            w.handleDetectImportersXPid();
        },

        bindEvents: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            w.e('ss/multisource/inbox/importerDetected', function (data) {
                var $attachment = $(".attachment[xpack='" + data.attachmentXPack + "']", $w);

                if ($attachment) {
                    w.r('reloadImporter', {
                        attachment: data.attachmentXPack
                    });
                }
            });

            w.e('ss/multisource/inbox/importersDetectionStart', function (data) {
                w.handleDetectImportersProc(data.xpid);
            });
        },

        bind: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            var $detectImportersButton = $(".detect_importers_button", $w);

            $detectImportersButton.on("click", function () {
                w.r('detectImporters', {}, false, function (data) {
                    w.handleDetectImportersProc(data.xpid);
                });
            });

            //

            $(".messages .message td.info").click(function () {
                var $message = $(this).closest(".message");

                w.r('openMessage', {
                    message_id: $message.attr("message_id")
                });
            });
        },

        //
        // detectImportersProc
        //

        handleDetectImportersXPid: function () {
            var w = this;
            var o = w.options;

            if (o.detectImportersXPid) {
                w.handleDetectImportersProc(o.detectImportersXPid);
            }
        },

        handleDetectImportersProc: function (xpid) {
            var w = this;
            var o = w.options;
            var $w = w.element;

            var $detectImportersButton = $(".detect_importers_button", $w);
            var $idle = $(".idle", $detectImportersButton);
            var $proc = $(".proc", $detectImportersButton);
            var $bar = $(".bar", $proc);
            var $position = $(".position", $proc);
            var $percent = $(".percent", $proc);

            $idle.hide();
            $proc.show();

            var proc = ewma.proc(xpid);

            proc.loop(function (progress, output, errors) {
                $position.html(progress.current + '/' + progress.total);
                $percent.html(progress.percent_ceil + '%');

                $bar.css({
                    width: progress.percent + '%'
                });
            });

            proc.terminate(function (output, errors) {
                $idle.show();
                $proc.hide();
            });
        }
    });
})(__nodeNs__, __nodeId__);
