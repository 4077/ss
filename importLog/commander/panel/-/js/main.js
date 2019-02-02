// head {
var __nodeId__ = "ss_importLog_commander_panel__main";
var __nodeNs__ = "ss_importLog_commander_panel";
// }

(function (__nodeNs__, __nodeId__) {
    $.widget(__nodeNs__ + "." + __nodeId__, $.ewma.node, {
        options: {},

        __create: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            w.render();
            w.bind();
            w.bindEvents();

            w.attach();
        },

        attach: function () {
            var w = this;
            var o = w.options;

            setTimeout(function () {
                w.w('main').w(o.panelName).attachPlugin(w);
            });
        },

        render: function () {
            this.renderFocus();
            // this.renderSelection();
        },

        bind: function () {
            var w = this;
            var $w = w.element;

            $w.on("click", function () {
                w.w('panel').focus('plugins');
            });

            var rowClickTimeout;

            var $rows = $(".row", $w);

            $rows.on("click", function () {
                var $row = $(this);

                rowClickTimeout = setTimeout(function () {
                    w.setFocus($row);
                    w.updateFocus();
                }, 100);
            });

            $rows.on("dblclick", function () {
                clearTimeout(rowClickTimeout);

                w.select($(this));
                // w.updateSelection();
            });

            $rows.on("contextmenu", function (e) {
                w.setFocus($(this));
                w.open();

                e.preventDefault();
            });

            $(".b table", $w).on("contextmenu", function (e) {
                e.preventDefault();
            });

            $(".status.updated", $w).on("click", function () {
                w.r('diffDialog', {
                    change_id: $(this).parent().attr("change_id")
                });
            });
        },

        bindEvents: function () {

        },

        handleKeyboardEvent: function (e) {
            var which = e.which;
            var type = e.type;
            var prevent = false;

            if (type === 'keydown' && which === 38) { // arrow up
                this.moveFocus(-1);
                this.updateFocus();

                prevent = true;
            }

            if (type === 'keydown' && which === 40) { // arrow down
                this.moveFocus(1);
                this.updateFocus();

                prevent = true;
            }

            if (type === 'keydown' && which === 13) { // enter
                this.updateSelection();

                prevent = true;
            }

            if (type === 'keydown' && which === 32) { // space
                this.open();

                prevent = true;
            }

            if (prevent) {
                e.preventDefault();
            }
        },

        /**
         * FOCUS
         */

        setFocus: function ($row) {
            this.options.focus = $row.attr("change_id");

            this.renderFocus();

            this.w('panel').focus('plugins');
        },

        renderFocus: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            var $row = $(".row[change_id='" + o.focus + "']", $w);

            if (!$row.length) {
                $row = $(".row:first", $w);
            }

            if ($row.length) {
                o.focus = $row.attr("change_id");

                $(".row", $w).removeClass("focus");
                $row.addClass("focus");

                this.scrollToRow($row);
            }
        },

        updateFocusTimeout: 0,

        updateFocus: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            clearTimeout(this.updateFocusTimeout);

            w.w('main').focus(o.panelName);

            this.updateFocusTimeout = setTimeout(function () {
                w.mr('focus', {
                    id: o.focus
                });
            }, 100);
        },

        moveFocus: function (delta) {
            var w = this;
            var o = w.options;
            var $w = w.element;

            var n = parseInt($(".row[change_id='" + o.focus + "']", $w).attr("n"));

            n += delta;

            var $row = $(".row[n='" + n + "']", $w);

            if ($row.length) {
                w.setFocus($row);
            }
        },

        /**
         * SELECTION
         */

        /*renderSelection: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            $(".row", $w).removeClass("selected");

            var $row = $(".row[change_id='" + o.selection.id + "']");

            if ($row.length) {
                $row.addClass("selected");
            }
        },

        updateSelection: function () {
            request(this.options.paths.select, this.options.focus);
        },*/

        /**
         * SCROLL
         */

        scrollToRow: function ($row) {
            var w = this;
            var o = w.options;
            var $w = w.element;

            var $port = $("> .b", $w);
            var $table = $("> table", $port);

            var padding = 0; // hardcode padding

            var portHeight = $port.height();
            var tableTop = $table.position().top - padding;
            var rowTop = $row.position().top - tableTop;

            var minTop = -$table.position().top + padding;
            var maxTop = -$table.position().top + portHeight + padding;

            if (rowTop > maxTop) {
                $port.scrollTop(rowTop - portHeight + $row.height());
            }

            if (rowTop < minTop) {
                $port.scrollTop(rowTop);
            }
        },

        /**
         * ACTIONS
         */

        select: function ($row) {
            var w = this;
            var o = this.options;

            w.w('content').r('select', {
                target: {
                    type: 'product',
                    id:   $row.attr("product_id")
                }
            });
        },

        open: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            var $row = $(".row[change_id='" + o.focus + "']", $w);

            w.w('content').r('open', {
                type: 'product',
                id:   $row.attr("product_id")
            });
        }
    });
})(__nodeNs__, __nodeId__);
