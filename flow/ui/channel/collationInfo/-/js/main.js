// head {
var __nodeId__ = "ss_flow_ui_channel_collationInfo__main";
var __nodeNs__ = "ss_flow_ui_channel_collationInfo";
// }

(function (__nodeNs__, __nodeId__) {
    $.widget(__nodeNs__ + "." + __nodeId__, $.ewma.node, {
        options: {},

        scrollMode: true,

        $content:     null,
        $products:    null,
        $productsIds: null,
        $connections: null,

        $panels: {
            all:         null,
            sources:     null,
            connections: null,
            targets:     null
        },

        $connectionsPanel: null,

        __create: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            w.$content = $(".content", $w);

            w.$panels.all = $(".panel", w.$content);
            w.$panels.connections = $(".connections", w.$content);
            w.$panels.sources = $(".sources", w.$content);
            w.$panels.targets = $(".targets", w.$content);

            w.$products = $(".product", w.$content);
            w.$productsIds = $(".product .id", w.$content);
            w.$connections = $(".connection", w.$panels.connections);

            w.renderScrolls();
            w.bind();
        },

        renderScrolls: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            w.$panels.connections.scrollTop(o.scrolls.connections);
            w.$panels.sources.scrollTop(o.scrolls.sources);
            w.$panels.targets.scrollTop(o.scrolls.targets);
        },

        bind: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            var mouseTargetPanel;

            $(".panel", w.$content).mouseenter(function () {
                mouseTargetPanel = $(this).attr("type");
            });

            w.$panels.all.click(function () {
                w.scrollMode = !w.scrollMode;

                $(".top_bar .counts", $w).toggleClass("scroll_mode", w.scrollMode);

                if (!w.scrollMode) {
                    w.mr('updateScrolls', {
                        connections: w.$panels.connections.scrollTop(),
                        sources:     w.$panels.sources.scrollTop(),
                        targets:     w.$panels.targets.scrollTop()
                    });
                }
            });

            w.$products.mouseenter(function () {
                if (w.scrollMode) {
                    var $product = $(this);

                    w.$products.removeClass("highlight");

                    var productId = $product.attr("product_id");
                    var connected = $product.hasClass("connected");

                    if (connected) {
                        var $connection = $("[" + mouseTargetPanel + "_id='" + productId + "']", w.$panels.connections);

                        if ($connection.length) {
                            w.$connections.removeClass("highlight");

                            var otherTargetType = mouseTargetPanel === 'source' ? 'target' : 'source';

                            var otherPanelProductId = $($connection.get(0)).attr(otherTargetType + "_id");

                            w.scrollConnectionsTo($connection);
                            w.scrollProductsTo(otherTargetType + 's', otherPanelProductId);
                        }
                    }
                }
            });

            w.$connections.mouseenter(function () {
                if (w.scrollMode) {
                    var $connection = $(this);

                    w.$connections.removeClass("highlight");

                    var sourceId = $connection.attr("source_id");
                    var targetId = $connection.attr("target_id");

                    w.$products.removeClass("highlight");

                    w.scrollProductsTo('sources', sourceId);
                    w.scrollProductsTo('targets', targetId);
                }
            });

            w.$productsIds.click(function (e) {
                var productId = $(this).closest(".product").attr("product_id");

                w.r('openProduct', {
                    product_id: productId
                });

                e.stopPropagation();
            });

            var updateScrollTimeout;

            w.$panels.all.mousewheel(function () {
                var $panel = $(this);

                clearTimeout(updateScrollTimeout);
                updateScrollTimeout = setTimeout(function () {
                    w.mr('updateScroll', {
                        type:  $panel.attr("type") + 's',
                        value: $panel.scrollTop()
                    });
                }, 400);
            });
        },

        scrollConnectionsTo: function ($connection) {
            var n = $connection.attr("n");

            var top = n * 20;

            this.$panels.connections.scrollTop(top);

            $connection.addClass("highlight");
        },

        scrollProductsTo: function (type, id) {
            var $product = $(".product[product_id='" + id + "']", this.$panels[type]);

            if ($product.length) {
                var n = $product.attr("n");

                var top = n * 20;

                this.$panels[type].scrollTop(top);

                $product.addClass("highlight");
            }
        }
    });
})(__nodeNs__, __nodeId__);
