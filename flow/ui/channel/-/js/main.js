// head {
var __nodeId__ = "ss_flow_ui_channel__main";
var __nodeNs__ = "ss_flow_ui_channel";
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

            w.handleCollationXPid();
            w.handleUpdateXPid();
        },

        bindEvents: function () {
            var w = this;
            var o = w.options;

            w.e('ss/flow/channel/collationStart', function (data) {
                w.handleCollationProc(data.xpid);
            });

            w.e('ss/flow/channel/collationComplete', function (data) {
                if (data.channelId === o.channelId) {
                    w.r('reload');
                }
            });

            w.e('ss/flow/channel/updateStart', function (data) {
                w.handleUpdateProc(data.xpid);
            });
        },

        bind: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            //

            var $connectionsCount = $(".connections_count", $w);

            $connectionsCount.on("click", function () {
                w.r('collationInfo');
            });

            var $collationButton = $(".collation_button", $w);

            $collationButton.on("click", function (e) {
                var sync = e.ctrlKey;

                if (sync) {
                    w.r('collate', {
                        sync: true
                    });
                } else {
                    w.r('collate', {}, false, function (data) {
                        w.handleCollationProc(data.xpid);
                    });
                }
            });

            //

            var $updateButton = $(".update_button", $w);

            $updateButton.on("click", function () {
                w.r('update', {}, false, function (data) {
                    w.handleUpdateProc(data.xpid);
                });
            });
        },

        //
        // collation proc
        //

        handleCollationXPid: function () {
            var w = this;
            var o = w.options;

            if (o.collationXPid) {
                w.handleCollationProc(o.collationXPid);
            }
        },

        handleCollationProc: function (xpid) {
            var w = this;
            var o = w.options;
            var $w = w.element;

            var $connectionsCount = $(".connections_count", $w);
            var $collationButton = $(".collation_button", $w);
            var $idle = $(".idle", $collationButton);
            var $proc = $(".proc", $collationButton);
            var $bar = $(".bar", $proc);
            var $position = $(".position", $proc);
            var $percent = $(".percent", $proc);
            var $breakButton = $(".break_button", $proc);

            $idle.hide();
            $proc.css({display: 'flex'});

            $bar.css({
                width: '0%'
            });

            var proc = ewma.proc(xpid);

            var prevPercent = 0;

            proc.loop(function (progress, output, errors) {
                $position.html(progress.current + '/' + progress.total);
                $percent.html(progress.percent_ceil + '%');
                $connectionsCount.html(output.connections_count);

                $bar.toggleClass("no_transition", progress.percent < prevPercent);

                $bar.css({
                    width: progress.percent + '%'
                });

                prevPercent = progress.percent;
            });

            proc.terminate(function (output, errors) {
                $idle.show();
                $proc.hide();

                $connectionsCount.html(output.connections_count);
            });

            $breakButton.click(function (e) {
                proc.break();

                e.stopPropagation();
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
            var $idle = $(".idle", $updateButton);
            var $proc = $(".proc", $updateButton);
            var $bar = $(".bar", $proc);
            var $status = $(".status", $proc);
            var $position = $(".position", $proc);
            var $percent = $(".percent", $proc);
            var $breakButton = $(".break_button", $proc);

            $idle.hide();
            $proc.css({display: 'flex'});

            $bar.css({
                width: '0%'
            });

            var proc = ewma.proc(xpid);

            proc.loop(function (progress, output, errors) {
                if (output.status) {
                    $status.html(output.status);
                    $position.html('');
                    $percent.html('');
                } else {
                    $status.html('');
                    $position.html(progress.current + '/' + progress.total);
                    $percent.html(progress.percent_ceil + '%');
                }

                $bar.css({
                    width: progress.percent + '%'
                });
            });

            proc.terminate(function (output, errors) {
                $idle.show();
                $proc.hide();
            });

            $breakButton.click(function () {
                proc.break();
            });
        }
    });
})(__nodeNs__, __nodeId__);
