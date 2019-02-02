// head {
var __nodeId__ = "ss_suppliers_ui_messages__main";
var __nodeNs__ = "ss_suppliers_ui_messages";
// }

(function (__nodeNs__, __nodeId__) {
    $.widget(__nodeNs__ + "." + __nodeId__, $.ewma.node, {
        options: {},

        __create: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            w.bindEvents();
            w.procFileBind();
        },

        bindEvents: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            w.e('ss/suppliers/messages/replicated', function (data) {
                clearInterval(w.procInterval);

                w.mr('reload', {});
            });

            w.e('ss/suppliers/messages/attachments/importerDetect', function (data) {
                clearInterval(w.procInterval);

                w.mr('reload', {});
            });
        },

        procInterval: 0,

        procFileBind: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            var prevProgress = 100;

            w.procInterval = setInterval(function () {
                $.getJSON(o.proc + '?t=' + Date.now(), function (data) {
                    var $attachment = $(".attachment[file_code='" + data.fileCode + "']", $w);

                    if ($attachment.length) {
                        var $progressBar = $(".progress_bar", $attachment);

                        $attachment.addClass("importing");

                        var progress = data.current / data.total * 100;

                        if (progress < prevProgress) {
                            $progressBar.addClass("no_transition");
                        } else {
                            $progressBar.removeClass("no_transition");
                        }

                        $progressBar.css({
                            width: progress + '%'
                        });

                        if (progress === 100) {
                            $attachment.addClass("imported").removeClass("importing");
                        }

                        prevProgress = progress;
                    }
                });
            }, 1000);
        }
    });
})(__nodeNs__, __nodeId__);
