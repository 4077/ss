// head {
var __nodeId__ = "ss_cats_cp_pageNode__main";
var __nodeNs__ = "ss_cats_cp_pageNode";
// }

(function (__nodeNs__, __nodeId__) {
    $.widget(__nodeNs__ + "." + __nodeId__, $.ewma.node, {
        options: {},

        scrollLock: false,

        scrollTop: 0,

        __create: function () {
            var w = this;
            var o = w.options;

            w.scrollTop = $(window).scrollTop();

            w._fixDialog();
            w.bindScrollers();

            w.bindEvents();
        },

        bind: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            $(window).rebind("mousewheel." + __nodeId__, function () {
                w.scrollTop = $(window).scrollTop();
            });
        },

        bindEvents: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            w.e('ss/cat/update_cats', function (data) {
                if (o.catId === data.id) {
                    w.mr('reload');
                }
            });

            w.e('ss/container/update', function (data) {
                var $container = $(o.containerSelector + "[instance='" + data.id + "']", $w); // $w &

                if ($container.length) {
                    if (isset(data.enabled)) {
                        $container.toggleClass("disabled", !data.enabled);
                    }

                    if (isset(data.published)) {
                        $container.toggleClass("not_published", !data.published);
                    }

                    if (isset(data.name)) {
                        $("> .name", $container).html(data.shortName);
                    }
                }
            });
        },

        _fixDialog: function () {
            var widget = this;

            var $widget = widget.element;
            var $dialog = $widget.closest(".ui-dialog");

            $dialog.css({
                position: 'fixed',
                top:      $dialog.offset().top - $(window).scrollTop()
            });
        },

        _scroll: function (top) {
            if (!this.scrollLock) {
                $("html, body").stop().animate({scrollTop: top}, {
                    duration: 300
                });
            }
        },

        _revertScroll: function () {
            var widget = this;

            $("html, body").stop().animate({scrollTop: widget.scrollTop}, {
                duration: 300
            });
        },

        bindScrollers: function () {
            var widget = this;

            var $widget = widget.element;
            var $scrollers = $(".scroller", $widget);
            var $cats = $(".cat", $widget);

            $scrollers.rebind("mouseenter." + __nodeId__, function (e) {
                var catId = $(this).attr("scroller_id");

                $(".icon", $(this)).removeClass("fa-cube").addClass("fa-arrow-left");

                var $cat = $(".ss_container[instance='" + catId + "']");

                if ($cat.length) {
                    widget._scroll($cat.offset().top);
                }

                e.stopPropagation();
            });

            $scrollers.rebind("mouseleave." + __nodeId__, function (e) {
                widget._revertScroll();

                $(".icon", $(this)).removeClass("fa-arrow-left").addClass("fa-cube");

                e.stopPropagation();
            });

            $scrollers.rebind("mousedown." + __nodeId__, function (e) {
                var catId = $(this).attr("scroller_id");

                var $cat = $(".ss_container[instance='" + catId + "']");

                if ($cat.length) {
                    var top = $cat.offset().top;

                    widget._scroll(top);
                    widget.scrollTop = top;
                }

                widget.scrollLock = true;

                // e.stopPropagation();
            });

            $scrollers.rebind("mouseup." + __nodeId__, function (e) {
                widget.scrollLock = false;

                var dialog = $widget.closest(".std_ui_dialogs__main_dialog").std_ui_dialogs__main_dialog("instance");

                $(".std_ui_dialogs__main_dialog").each(function () {
                    if (dialog.uuid !== $(this).std_ui_dialogs__main_dialog("instance").uuid) {
                        $(this).std_ui_dialogs__main_dialog("setPosition");
                    }
                });
            });

            $scrollers.rebind("click", function (e) {
                e.stopPropagation();
            });
        }
    });
})(__nodeNs__, __nodeId__);
