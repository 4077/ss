// head {
var __nodeId__ = "ss_flow_ui_channels__main";
var __nodeNs__ = "ss_flow_ui_channels";
// }

(function (__nodeNs__, __nodeId__) {
    $.widget(__nodeNs__ + "." + __nodeId__, $.ewma.node, {
        options: {},

        __create: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            w.canvasInit();
            w.initialRender();

            w.bind();
            w.render();
        },

        $canvas: null,
        canvas:  null,
        width:   0,
        height:  0,

        canvasInit: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            w.$canvas = $("canvas", $w);

            paper.setup(w.$canvas.get(0));

            w.width = $w.width();
            w.height = $w.height();
        },

        render: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            w._renderBg();
            w._renderChannels();
        },

        _renderBg: function () {
            var w = this;

            paper.view.viewSize.width = w.width;
            paper.view.viewSize.height = w.height;

            paper.view.draw();
        },

        _renderChannels: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            for (var channelId in o.channels) {
                var channel = o.channels[channelId];

                var $source = $(".node[node_id='" + channel[0] + "']", $w);
                var $target = $(".node[node_id='" + channel[1] + "']", $w);

                var channelPathData = w._renderChannelPathData($source, $target);

                var path = w.canvasObjects.channelsPaths[channelId];
                var eventPath = w.canvasObjects.channelsEventsPaths[channelId];

                path.removeSegments();
                eventPath.removeSegments();

                path.addSegments([
                    new paper.Segment(channelPathData.out, null, channelPathData.outHandle),
                    new paper.Segment(channelPathData.in, channelPathData.inHandle, null),
                ]);

                eventPath.addSegments([
                    new paper.Segment(channelPathData.out, null, channelPathData.outHandle),
                    new paper.Segment(channelPathData.in, channelPathData.inHandle, null),
                ]);

                path.strokeColor = '#676767';
                eventPath.strokeColor = '#000000';
            }
        },

        offset: {
            left: 0,
            top:  0
        },

        $nodes: [],

        canvasObjects: {
            mainRect: null,

            channelsPaths:       {},
            channelsEventsPaths: {},
        },

        mouseTarget: {
            type: false,
            data: {}
        },

        setMouseTarget: function (type, data) {
            this.mouseTarget = {
                type: type,
                data: data || {}
            }
        },

        unsetMouseTarget: function () {
            this.mouseTarget = {
                type: false,
                data: {}
            }
        },

        mouseAction: {
            type: false,
            data: {}
        },

        setMouseAction: function (type, data) {
            this.mouseAction = {
                type: type,
                data: data || {}
            }
        },

        unsetMouseAction: function () {
            this.mouseAction = {
                type: false,
                data: {}
            }
        },

        mouseWasMoved: false,

        initialRender: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            w.offset = {
                left: $w.position().left,
                top:  $w.position().top
            };

            var channelsIdsByPathsIds = {};

            for (var channelId in o.channels) {
                var eventPath = w.canvasObjects.channelsEventsPaths[channelId] = new paper.Path();
                var path = w.canvasObjects.channelsPaths[channelId] = new paper.Path();

                channelsIdsByPathsIds[eventPath._id] = channelId;

                path.strokeWidth = 1;
                eventPath.strokeWidth = 20;
                eventPath.opacity = 0;

                path.on('mouseenter', function () {
                    this.strokeColor = '#ff4b57';
                });

                path.on('mouseleave', function () {
                    this.strokeColor = '#676767';
                });

                eventPath.on('mouseenter', function (e) {
                    w.setMouseTarget('channel', {
                        id: channelsIdsByPathsIds[e.target._id]
                    });

                    w.canvasObjects.channelsPaths[channelsIdsByPathsIds[e.target._id]].strokeColor = '#ff4b57';
                });

                eventPath.on('mouseleave', function (e) {
                    w.unsetMouseTarget();

                    w.canvasObjects.channelsPaths[channelsIdsByPathsIds[e.target._id]].strokeColor = '#676767';
                });
            }

            for (var nodeId in o.nodes) {
                w.$nodes[nodeId] = $(".node[node_id='" + nodeId + "']", $w);

                w.$nodes[nodeId].css(o.nodes[nodeId].position).show();
            }
        },

        bind: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            $(window).bind("click." + __nodeId__, function () {
                w._hideContextmenus();
            });

            $w.contextmenu(function (e) {
                e.preventDefault();
            });

            //
            // nodes
            //

            var $nodes = $(".node", $w);

            $nodes.draggable({
                drag: function (e, ui) {
                    w.render();
                },

                stop: function (e, ui) {
                    w.r('updateNodePosition', {
                        node_id: ui.helper.attr("node_id"),
                        left:    ui.position.left,
                        top:     ui.position.top
                    });
                }
            });

            $nodes.mouseenter(function () {
                w.setMouseTarget('node', {
                    id: $(this).attr("node_id")
                });
            });

            $nodes.mouseleave(function () {
                w.unsetMouseTarget();
            });

            //
            //
            //

            $w.mousedown(function (e) {
                w.mouseWasMoved = false;

                if (e.which === 3) {
                    if (w.mouseTarget.type === 'node') {
                        w.setMouseAction('createChannel', {
                            sourceNodeId: w.mouseTarget.data.id
                        });
                    }
                }
            });

            $w.mousemove(function (e) {
                w.mouseWasMoved = true;

                if (w.mouseAction.type === 'createChannel') {
                    p(e);
                }
            });

            $w.mouseup(function (e) {
                if (e.which === 3) {
                    if (w.mouseWasMoved) {
                        if (w.mouseAction.type === 'createChannel') {
                            if (w.mouseTarget.type === 'node') {
                                var sourceNodeId = w.mouseAction.data.sourceNodeId;
                                var targetNodeId = w.mouseTarget.data.id;

                                if (sourceNodeId !== targetNodeId) {
                                    w.r('createChannel', {
                                        source_id: sourceNodeId,
                                        target_id: targetNodeId
                                    });
                                }
                            }
                        }
                    } else {
                        if (w.mouseTarget.type === false) {
                            w._contextmenuShow({
                                left: e.clientX,
                                top:  e.clientY - w.offset.top
                            });
                        }

                        if (w.mouseTarget.type === 'node') {
                            w.w('nodeContextmenu').nodeId = w.mouseTarget.data.id;

                            w._nodeContextmenuShow({
                                left: e.clientX,
                                top:  e.clientY - w.offset.top
                            });
                        }

                        if (w.mouseTarget.type === 'channel') {
                            w.w('channelContextmenu').channelId = w.mouseTarget.data.id;

                            w._channelContextmenuShow({
                                left: e.clientX,
                                top:  e.clientY - w.offset.top
                            });
                        }
                    }
                }

                if (e.which === 1) {
                    if (w.mouseTarget.type === 'channel') {
                        w.r('openChannel', {
                            channel_id: w.mouseTarget.data.id
                        });
                    }
                }

                w.unsetMouseAction();
            });

            //
            // resize
            //

            $(window).resize(function () {
                w.width = $w.width();
                w.height = $w.height();

                w.render();
            });
        },

        _renderChannelPathData: function ($source, $target) {
            var w = this;

            var sourcePosData = w._renderNodePosData($source);
            var targetPosData = w._renderNodePosData($target);

            var sw = sourcePosData.width;
            var sh = sourcePosData.height;
            var sl = sourcePosData.left;
            var st = sourcePosData.top;
            var sc = sourcePosData.center;

            var tw = targetPosData.width;
            var th = targetPosData.height;
            var tl = targetPosData.left;
            var tt = targetPosData.top;
            var tc = targetPosData.center;

            var targetVector = {
                h: tc.left - sc.left,
                v: tc.top - sc.top
            };

            var targetVectorLength = Math.sqrt(Math.pow(targetVector.h, 2) + Math.pow(targetVector.v, 2));

            var sourceDirection;
            var targetDirection;
            var axis;

            if (Math.abs(targetVector.h) < Math.abs(targetVector.v)) {
                axis = 'y';
                sourceDirection = targetVector.v > 0 ? 'n' : 's';
                targetDirection = targetVector.v > 0 ? 's' : 'n';
            } else {
                axis = 'x';
                sourceDirection = targetVector.h > 0 ? 'w' : 'e';
                targetDirection = targetVector.h > 0 ? 'e' : 'w';
            }

            var outputPosition;
            var outputBasePointVector;

            var inputPosition;
            var inputBasePointVector;

            if (axis === 'y') {
                outputPosition = {top: sourceDirection === 'n' ? st + sh : st, left: sc.left};
                inputPosition = {top: targetDirection === 'n' ? tt + th : tt, left: tc.left};

                outputBasePointVector = {h: 0, v: sourceDirection === 'n' ? 1 : -1};
                inputBasePointVector = {h: 0, v: sourceDirection === 'n' ? -1 : 1};
            } else {
                outputPosition = {top: sc.top, left: sourceDirection === 'w' ? sl + sw : sl};
                inputPosition = {top: tc.top, left: targetDirection === 'w' ? tl + tw : tl};

                outputBasePointVector = {h: sourceDirection === 'w' ? 1 : -1, v: 0};
                inputBasePointVector = {h: sourceDirection === 'w' ? -1 : 1, v: 0};
            }

            var handleVectorLength = targetVectorLength / 3;

            return {
                out:       [outputPosition.left, outputPosition.top],
                outHandle: [outputBasePointVector.h * handleVectorLength, outputBasePointVector.v * handleVectorLength],
                inHandle:  [inputBasePointVector.h * handleVectorLength, inputBasePointVector.v * handleVectorLength],
                in:        [inputPosition.left, inputPosition.top]
            };
        },

        _renderNodePosData: function ($node) {
            var w = $node.outerWidth();
            var h = $node.outerHeight();
            var l = $node.position().left;
            var t = $node.position().top;
            var c = {
                left: l + w / 2,
                top:  t + h / 2
            };

            return {
                width:  w,
                height: h,
                left:   l,
                top:    t,
                center: c
            };
        },

        _hideContextmenus: function () {
            $(".contextmenu", this.element).hide();
            $(".node_contextmenu", this.element).hide();
            $(".channel_contextmenu", this.element).hide();
        },

        _contextmenuShow: function (position) {
            this._hideContextmenus();
            $(".contextmenu", this.element).css(position).show();
        },

        _nodeContextmenuShow: function (position) {
            this._hideContextmenus();
            $(".node_contextmenu", this.element).css(position).show();
        },

        _channelContextmenuShow: function (position) {
            this._hideContextmenus();
            $(".channel_contextmenu", this.element).css(position).show();
        }
    });
})(__nodeNs__, __nodeId__);
