// head {
var __nodeId__ = "ss_cats_ui_cpanel__main";
var __nodeNs__ = "ss_cats_ui_cpanel";
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
        },

        bind: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            var $buttons = $(".buttons", $w);
            var $mainButton = $(".global_editable_toggle_button", $w);

            if (o.buttonsVisible && o.editable) {
                $buttons.css({display: "flex"});
            }

            // $w.css({display: "flex"});
            // $w.appendTo("body");//.css({display: "flex"});

            var mainTouch;
            var buttonsTouch;

            $w.mouseleave(function () {
                if (mainTouch > buttonsTouch) {
                    w._hideButtons($buttons);

                    w.r('setButtonsVisible', {value: false});
                }

                mainTouch = Date.now();
            });

            $mainButton.mouseenter(function () {
                mainTouch = Date.now();

                w._showButtons($buttons);

                w.r('setButtonsVisible', {value: true});
            });

            $buttons.mouseenter(function () {
                buttonsTouch = Date.now();
            });

            $buttons.mouseleave(function () {
                setTimeout(function () {
                    buttonsTouch = Date.now();
                });
            });
        },

        _showButtons: function ($buttons) {
            if (this.options.editable) {
                $buttons.fadeIn(200, function () {
                    $(this).css({display: "flex"});
                });
            }
        },

        _hideButtons: function ($buttons) {
            $buttons.fadeOut();
        },

        buttonsToggle: function (data) {
            var w = this;
            var $w = w.element;

            var $buttons = $(".buttons", $w);

            if (data.visible) {
                w._showButtons($buttons);
            } else {
                w._hideButtons($buttons);
            }
        }
    });
})(__nodeNs__, __nodeId__);
