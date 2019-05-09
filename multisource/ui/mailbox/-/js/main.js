// head {
var __nodeId__ = "ss_multisource_ui_mailbox__main";
var __nodeNs__ = "ss_multisource_ui_mailbox";
// }

(function (__nodeNs__, __nodeId__) {
    $.widget(__nodeNs__ + "." + __nodeId__, $.ewma.node, {
        options: {},

        __create: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            w.bind();
            w.bindEvents();

            w.handleUpdateXPid();
        },

        bindEvents: function () {
            var w = this;

            w.e('ss/multisource/inbox/updateStart', function (data) {
                w.handleUpdateProc(data.xpid);
            });
        },

        bind: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            var $updateButton = $(".update_button", $w);

            $updateButton.on("click", function (e) {
                var sync = e.ctrlKey;

                if (sync) {
                    w.r('update', {
                        sync: true
                    });
                } else {
                    w.r('update', {}, false, function (data) {
                        w.handleUpdateProc(data.xpid);
                    });
                }
            });
        },

        //
        // update proc
        //

        handleUpdateXPid: function () {
            var w = this;
            var o = w.options;

            if (o.updateXPid) {
                w.handleUpdateProc(o.updateXPid);
            }
        },

        handleUpdateProc: function (xpid) {
            var w = this;
            var o = w.options;
            var $w = w.element;

            var $updateButton = $(".update_button", $w);
            var $updateProcessIndicator = $(".update_process_indicator", $w);
            var $updateProcessStatus = $(".status", $updateProcessIndicator);
            var $updateProcessProgress = $(".progress", $updateProcessIndicator);

            $updateProcessStatus.html("");
            $updateProcessIndicator.removeClass("error");

            var proc = ewma.proc(xpid);

            proc.loop(function (progress, output, errors) {
                $updateProcessStatus.html(output.status);
                $updateProcessProgress.html(progress.percent + '%');

                $updateButton.find(".icon").addClass("fa-spin");
            });

            proc.terminate(function (output, errors) {
                $updateButton.find(".icon").removeClass("fa-spin");

                if (errors) {
                    $updateProcessIndicator.addClass("error");

                    $updateProcessStatus.html(errors[0]);
                }
            });
        }
    });
})(__nodeNs__, __nodeId__);
