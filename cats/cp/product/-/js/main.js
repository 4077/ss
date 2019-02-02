// head {
var __nodeId__ = "ss_cats_cp_product__main";
var __nodeNs__ = "ss_cats_cp_product";
// }

(function (__nodeNs__, __nodeId__) {
    $.widget(__nodeNs__ + "." + __nodeId__, $.ewma.node, {
        options: {},

        __create: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            this.bind();
            this.bindEvents();
        },

        bindEvents: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            w.e('ss/product/update', function (data) {
                if (o.productId === data.id) {
                    if (isset(data.enabled)) {
                        $(".enabled.button", $w).toggleClass("pressed", data.enabled).html(data.enabled ? 'включен' : 'выключен');
                    }

                    if (isset(data.published)) {
                        $(".published.button", $w).toggleClass("pressed", data.published).html(data.published ? 'опубликован' : 'не опубликован');
                    }

                    if (isset(data.imagesOthers)) {
                        w.mr('reload');
                    }

                    if (isset(data.status)) {
                        w.mr('reload');
                    }
                }
            });
        },

        bind: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            //

            $(".status_selector > .button", $w).on("click", function (e) {
                $(".status_selector > .dropdown", $w).toggle();

                e.stopPropagation();
            });

            $(window).rebind("click." + __nodeId__, function () {
                $(".status_selector > .dropdown", $w).hide();
            });

            $w.closest(".ui-dialog").rebind("click." + __nodeId__, function () {
                $(".status_selector > .dropdown", $w).hide();
            });

            //

            $("input.product_field[field]", $w).rebind("keyup cut paste", function (e) {
                if (e.which !== 9) {
                    updateField($(this));
                }
            });

            $("input.product_field[field]", $w).rebind("blur", function (e) {
                w.r('reloadField', {
                    field:   $(this).attr("field"),
                    product: o.product
                });
            });

            var updateTimeout;

            function updateField($field) {
                var field = $field.attr("field");
                var value = $field.val();

                clearTimeout(updateTimeout);
                updateTimeout = setTimeout(function () {
                    w.r('updateField', {
                        field:   field,
                        value:   value,
                        product: o.product
                    });

                    $field.addClass("updating");
                }, 400);

                if (field === 'name') {
                    var name = $("input[field='name']", $w).val();

                    ewma.trigger('ss/product/update', {
                        id:   o.productId,
                        name: name
                    });

                    $w.closest(".ui-dialog").find(".ui-dialog-titlebar .title").html(name || '&nbsp;');
                }
            }
        },

        savedHighlight: function (field) {
            var $field = $("input.product_field[field='" + field + "']", this.element);

            $field.removeClass("updating").addClass("saved");

            setTimeout(function () {
                $field.removeClass("saved");
            }, 1000);
        },

        setFieldValue: function (data) {
            var $field = $("input.product_field[field='" + data.field + "']", this.element);

            $field.val(data.value).removeClass("updating");
        }
    });
})(__nodeNs__, __nodeId__);
