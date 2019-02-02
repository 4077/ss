// head {
var __nodeId__ = "ss_moderation_commander_panel__main";
var __nodeNs__ = "ss_moderation_commander_panel";
// }

(function (__nodeNs__, __nodeId__) {
    $.widget(__nodeNs__ + "." + __nodeId__, $.ewma.node, {
        options: {
            focus: {
                type: null,
                id:   null
            }
        },

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
            // this.renderScroll();
            this.renderFocus();
            // this.renderSelection();
        },

        bind: function () {
            var w = this;
            var o = w.options;
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

                w.setFocus($(this));
                w.select($(this));
            });

            $rows.on("contextmenu", function (e) {
                w.setFocus($(this));
                w.open();

                e.preventDefault();
            });

            $(".b table", $w).on("contextmenu", function (e) {
                e.preventDefault();
            });
        },

        bindEvents: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            w.e('ss/product/update.' + o.panelName, function (data) {
                var $product = $(".product[row_id='" + data.id + "']", $w);

                if ($product.length) {
                    if (isset(data.enabled)) {
                        $product.toggleClass("disabled", !data.enabled);
                    }

                    if (isset(data.published)) {
                        $product.toggleClass("not_published", !data.published);
                    }

                    if (isset(data.name)) {
                        $("> .name .value", $product).html(data.name);
                    }

                    if (isset(data.price)) {
                        $("> .price", $product).html(data.formatted);
                    }

                    if (isset(data.stock)) {
                        $("> .stock", $product).html(data.formatted);
                    }

                    if (isset(data.images)) {
                        $(".has_image.icon", $product).toggleClass("has", data.images.has).attr("title", data.images.count);
                    }

                    if (isset(data.status)) {
                        var $status = $(".status.icon", $product);

                        for (var status in o.statuses) {
                            $status.removeClass(status).find("div").removeClass(o.statuses[status].icon);
                        }

                        $status.addClass(data.status).find("div").addClass(o.statuses[data.status].icon);
                    }
                }
            });
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
                this.select();

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
            this.options.focus = $row.attr("product_id");

            this.renderFocus();

            this.w('panel').focus('plugins');
        },

        renderFocus: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            var $row = $(".row[product_id='" + o.focus + "']", $w);

            if (!$row.length) {
                $row = $(".row:first", $w);
            }

            if ($row.length) {
                o.focus = $row.attr("product_id");

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
            var n = parseInt($(".row[product_id='" + this.options.focus + "']", this.element).attr("n"));

            n += delta;

            var $row = $(".row[n='" + n + "']", this.element);

            if ($row.length) {
                this.setFocus($row);
            }
        },

        /**
         * SELECTION
         */

        renderSelection: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            $(".row", $w).removeClass("selected");

            var $row = $(".row[product_id='" + o.selection.id + "']");

            if ($row.length) {
                $row.addClass("selected");
            }
        },

        /**
         * SCROLL
         */

        scrollToRow: function ($row) {
            var w = this;
            var o = w.options;
            var $w = w.element;

            var $port = $("> .b", w.element);
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

            w.w('content').r('open', {
                type: 'product',
                id:   o.focus
            });
        }
    });
})(__nodeNs__, __nodeId__);
