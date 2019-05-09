// head {
var __nodeId__ = "ss_multisource_ui_inbox__main_importer_procDispatcher";
var __nodeNs__ = "ss_multisource_ui_inbox";
// }

(function (__nodeNs__, __nodeId__) {
    $.widget(__nodeNs__ + "." + __nodeId__, $.ewma.node, {
        options: {},

        __create: function () {
            var w = this;
            var o = w.options;

            w.bindEvents();
            w.handleImportXPids();
        },

        bindEvents: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            w.e('ss/multisource/inbox/importStart', function (data) {
                var $importer = $(".inbox_attachment_importer_pivot[pivot_id='" + data.aiPivotId + "']");

                if ($importer.length) {
                    w.handleImportProc(data.xpid, $importer);
                }
            });
        },

        //
        //
        //

        handleImportXPids: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            if (o.importXPids) {
                $.each(o.importXPids, function (n, proc) {
                    var $importer = $(".inbox_attachment_importer_pivot[pivot_id='" + proc.aiPivotId + "']");

                    if ($importer.length) {
                        w.handleImportProc(proc.xpid, $importer);
                    }
                })
            }
        },

        handleImportProc: function (xpid, $importer) {
            var $idle = $(".idle", $importer);
            var $proc = $(".proc", $importer);
            var $bar = $(".bar", $proc);
            var $position = $(".position", $proc);
            var $percent = $(".percent", $proc);
            var $comment = $(".comment", $proc);

            $idle.hide();
            $proc.show();

            $position.html('');
            $percent.html('');
            $comment.html('');
            $bar.css({width: 0});

            var proc = ewma.proc(xpid);

            var prevPercent = 0;

            proc.loop(function (progress, output, errors) {
                $position.html(progress.current + '/' + progress.total);
                $percent.html(progress.percent_ceil + '%');
                $comment.html(progress.comment);

                $bar.toggleClass("no_transition", progress.percent < prevPercent);

                $bar.css({
                    width: progress.percent + '%'
                });

                prevPercent = progress.percent;
            });

            proc.terminate(function (output, errors) {
                $idle.show();
                $proc.hide();
            });
        }
    });
})(__nodeNs__, __nodeId__);
