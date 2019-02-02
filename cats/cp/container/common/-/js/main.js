// head {
var __nodeId__ = "ss_cats_cp_container_common__main";
var __nodeNs__ = "ss_cats_cp_container_common";
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
        },

        bindEvents: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            w.e('ss/container/update.' + o.catId, function (data) {
                if (isset(data.enabled)) {
                    $(".enabled.button", $w).toggleClass("pressed", data.enabled).html(data.enabled ? 'включен' : 'выключен');
                }

                if (isset(data.published)) {
                    $(".published.button", $w).toggleClass("pressed", data.published).html(data.published ? 'опубликован' : 'не опубликован');
                }

                if (isset(data.outputEnabled)) {
                    $(".output_enabled.button", $w).toggleClass("pressed", data.outputEnabled).html(data.outputEnabled ? 'результат отображается' : 'результат поглощается');
                }
            });
        },

        bind: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            $("input.cat_field[field]", $w).rebind("keyup cut paste", function (e) {
                if (e.which !== 9) {
                    updateField($(this));
                }
            });

            var updateTimeout;

            function updateField($field) {
                var field = $field.attr("field");
                var value = $field.val();

                clearTimeout(updateTimeout);
                updateTimeout = setTimeout(function () {
                    w.r('updateField', {
                        field: field,
                        value: value,
                        cat:   o.cat
                    });

                    $field.addClass("updating");
                }, 400);

                if (field === 'name' || field === 'short_name') {
                    var name = $("input[field='name']", $w).val();
                    var shortName = $("input[field='short_name']", $w).val();

                    ewma.trigger('ss/container/update', {
                        id:        o.catId,
                        name:      name || shortName,
                        shortName: shortName || name
                    });
                }

                if (field === 'description') {
                    ewma.trigger('ss/container/update', {
                        id:          o.catId,
                        description: $("input[field='description']", $w).val()
                    });
                }
            }
        },

        savedHighlight: function (field) {
            var widget = this;
            var $widget = widget.element;

            var $field = $("input.cat_field[field='" + field + "']", $widget);

            $field.removeClass("updating").addClass("saved");

            setTimeout(function () {
                $field.removeClass("saved");
            }, 1000);
        }
    });
})(__nodeNs__, __nodeId__);
