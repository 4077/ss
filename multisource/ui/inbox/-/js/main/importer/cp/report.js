// head {
var __nodeId__ = "ss_multisource_ui_inbox__main_importer_cp_report";
var __nodeNs__ = "ss_multisource_ui_inbox";
// }

(function (__nodeNs__, __nodeId__) {
    $.widget(__nodeNs__ + "." + __nodeId__, $.ewma.node, {
        options: {},

        __create: function () {
            var w = this;
            var o = w.options;

            w.bindEvents();

            setTimeout(function () {
                w.handleImportXPids();
            });
        },

        bindEvents: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            w.e('ss/multisource/inbox/importStart', function (data) {
                var $importer = $(".importer[pivot_id='" + data.aiPivotId + "']", $w);

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
                    var $importer = $(".importer[pivot_id='" + proc.aiPivotId + "']", $w);

                    if ($importer.length) {
                        w.handleImportProc(proc.xpid, $importer);
                    }
                })
            }
        },

        handleImportProc: function (xpid, $importer) {
            var $importButton = $(".import_button", $importer);
            var $breakButton = $(".break_button", $importer);

            var $idle = $(".idle", $importer);
            var $proc = $(".proc", $importer);
            var $bar = $(".bar", $proc);
            var $position = $(".position", $proc);
            var $percent = $(".percent", $proc);
            var $comment = $(".comment", $proc);

            $position.html('');
            $percent.html('');
            $comment.html('');
            $bar.css({width: 0});

            $idle.hide();
            $proc.show();

            $importButton.hide();
            $breakButton.css({display: 'flex'});

            var proc = ewma.proc(xpid);

            $breakButton.rebind("click", function (e) {
                proc.break();

                $position.html('');
                $percent.html('');
                $comment.html('');
                $bar.css({width: 0});

                e.stopPropagation();
            });

            proc.loop(function (progress, output, errors) {
                $position.html(progress.current + '/' + progress.total);
                $percent.html(progress.percent_ceil + '%');
                $comment.html(progress.comment);

                $bar.css({
                    width: progress.percent + '%'
                });
            });

            proc.terminate(function (output, errors) {
                $idle.show();
                $proc.hide();

                $importButton.show();
                $breakButton.hide();
            });
        }
    });
})(__nodeNs__, __nodeId__);
