// head {
var __nodeId__ = "ss_cats_cp_page_common__main";
var __nodeNs__ = "ss_cats_cp_page_common";
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

            var aliasTimeout;

            w.e('+ss/page/update', function (data) {
                if (o.catId === data.id) {
                    if (isset(data.enabled)) {
                        $(".enabled.button", $w).toggleClass("pressed", data.enabled).html(data.enabled ? 'включена' : 'выключена');
                    }

                    if (isset(data.published)) {
                        $(".published.button", $w).toggleClass("pressed", data.published).html(data.published ? 'опубликована' : 'не опубликована');
                    }

                    if (isset(data.alias)) {
                        $(".route_cache a", $w).html(data.route).attr("href", "/" + data.route + "/");

                        clearTimeout(aliasTimeout);

                        aliasTimeout = setTimeout(function () {
                            $("input[field='alias']", $w).val(data.alias);
                        }, 2000);
                    }
                }
            });
        },

        bind: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            $("input[field]", $w).rebind("keyup cut paste", function (e) {
                if (e.keyCode !== 9) {
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
                        value: value
                    });

                    $field.addClass("updating");
                }, 400);

                if (field === 'name' || field === 'short_name') {
                    var name = $("input[field='name']", $w).val();
                    var shortName = $("input[field='short_name']", $w).val();

                    ewma.trigger('ss/page/update', {
                        id:        o.catId,
                        name:      name || shortName,
                        shortName: shortName || name
                    });
                }
            }
        },

        savedHighlight: function (field) {
            var $field = $("input[field='" + field + "']", this.element);

            $field.removeClass("updating").addClass("saved");

            setTimeout(function () {
                $field.removeClass("saved");
            }, 1000);
        },

    });
})(__nodeNs__, __nodeId__);
