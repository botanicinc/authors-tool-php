<template>
    <div>
        <div id="container" :style="{ width: `${width}px`, height: `${height}px` }">
             <svg id="svg-canvas" :width=width :height=height></svg><!-- width and height needs to be set as attr here. dont change it! -->            
            <div id="zoom">
                <div style="cursor: pointer;" id="zoomin" v-on:click="zoom(true)">+</div>
                <div style="cursor: pointer;" id="zoomout" v-on:click="zoom(false)">_</div>
            </div>
        </div>


        <add-edit-uibotnode-modal ref="addEditUiBotNodeModal" v-if="addEditUiBotNodeModalEnabled" :project="project" :flows="flows" :lastFormSelected="lastFormSelected" :processes="processes" :nodeNames="nodeNames" />
        <add-edit-uiusernode-modal ref="addEditUiUserNodeModal" v-if="addEditUiUserNodeModalEnabled" :project="project" :triggers="triggers" />

        <confirm-modal ref="confirmDeleteModal" title="Delete Node" confirmButton="Delete" text="Deleting this node will delete all nodes below it. Are you sure you want to delete?" />

    </div>    
</template>

<script>
    /**
     * Flowchart component
     * 
     */
    import Vue from 'vue';
    import dagreD3 from 'dagre-d3';
    import {selection, select} from "d3-selection";

    import AddEditUiBotNodeModal from './AddEditUiBotNodeModal.vue';
    import AddEditUiUserNodeModal from './AddEditUiUserNodeModal.vue';
    import ConfirmModal from './ConfirmModal.vue';

    import { EventBus } from '../eventBus.js';

    export default {
        label: 'ConversationBuilder',
        components: {
            'add-edit-uibotnode-modal': AddEditUiBotNodeModal,
            'add-edit-uiusernode-modal': AddEditUiUserNodeModal,
            'confirm-modal': ConfirmModal
        },
        props: ['flowId', 'project', 'flows'],
        data() {
            return {
                width: 1140, //width of flowchart box component @TODO move this to props (check how-to default)
                height: 700, //height
                scale: 1, //zoom scale
                scaleStep: 0.1, //steps of zoom
                uiBotNodeWidth: 154,
                uiBotNodeHeight: 70,
                uiUserNodeWidth: 132,
                uiUserNodeHeight: 52,
                uiNodeTextMaxLength: 35, //max length for text inside the nodes
                uiNodeLabelMaxLength: 20,

                addEditUiUserNodeModalEnabled: false,
                addEditUiBotNodeModalEnabled: false,

                forms: [],
                flowLoaded: false,
                flow: [],
                lastFormSelected: '',
                nodeNames: {},
                processes: {
                    sendEmail: 'Send Email'
                },
                triggers: {
                    sentimentAnalysis: 'Sentiment Analysis'
                },
                nodeTypeDisplayMap: {
                    choice: 'Bot',
                    card: 'Bot',
                    message: 'Bot',
                    user: 'User',
                    flowId: 'Connector',
                    process: 'Process'
                }

            };
        },

        /**
         * Init
         */
        created() {

            //expose 'this' to access methods from the dagre-d3 flowchart in native js
            window.cb = this;

            this.initWidth = this.width;
            this.initHeight = this.height;

            this.loadFlow(this.flowId);


        },

        /**
         * Watchers
         */
        watch: {
            //watch when component caller changes the flow id and loadnew one
            flowId() {
                this.loadFlow(this.flowId);
            }
        },

        methods: {
            /**
             * Generate an UUID for new nodes
             * 
             * @returns {String} 
             */
            guid() {
                return this.s4() + this.s4();
            },

            s4() {
                return Math.floor((1 + Math.random()) * 0x10000)
                        .toString(16)
                        .substring(1);
            },

            /**
             * Load flow from database and show it on screen
             * 
             * @param {String} flowId            
             */
            loadFlow(flowId) {
                if (!flowId) {
                    return;
                }

                //reset component
                //intializes data with default values from child
                _.assign(this.$data, this.$options.data());
                //assign data with values from props (as its duplicated with data() we need to do this explicitly)
                _.merge(this.$data, this.$options.propsData);


                jQuery('#container').scrollTop(0);

                //Get flow data
                this.$http.get(`/api/flow/${flowId}`)
                        .then(response => {

                            this.flow = JSON.parse(_.get(response.data, 'flow'));
                            this.updatedAt = response.data.updated_at;

                            if (_.get(this.flow.nodes, 'length')) {

                                this.name = response.data.name;

                            } else {
                                //generate new flow object with just root node
                                this.name = 'flow' + flowId;
                                this.flow = {id: flowId, name: "flow" + flowId, nodes: [{id: "root-node", name: "flow" + flowId, type: "root", connections: [{default: "end"}]}]};

                            }

                            this.flowLoaded = true;

                            //generate graph data for ui representation of the json flow
                            this.processFlow();

                            //generate a canvas based on the ui graph data 
                            this.draw();

                        });

            },

            /**
             * Takes flow model and generate a ui model that represents bot and user nodes as individual nodes (2nd layer)
             * @TODO this 2nd layer should be removed and integrated with Dagre when using graphs in order to gain graph processing
             */
            processFlow() {

                this.uiNodes = [];
                this.uiLinks = [];

                this.nodeNames = {};

                if (_.get(this.flow.nodes, 'length') > 1) {

                    this.flow.nodes.forEach(flowNode => {
                        //root type node is not a flow node. It contains data about the flow
                        if (flowNode.type == 'root') {
                            return;
                        }

                        //flow model msg comes as string and as array in old flows
                        if (!Array.isArray(flowNode.msg)) {
                            flowNode.msg = [flowNode.msg];
                        }

                        //add all flow node attr to ui node attr
                        this.uiNodes.push(flowNode);

                        //build a list with node id/name
                        if (flowNode.name) {
                            Vue.set(this.nodeNames, flowNode.id, flowNode.name);
                        }


                        if (flowNode.type == 'message' || flowNode.type == 'question' || flowNode.type == 'card' || flowNode.type == 'choice' || flowNode.type == 'process') {

                            //looking for connections to add user uiNodes 
                            flowNode.connections.forEach((conn, connIdx) => {

                                if (conn.default) {
                                    if (conn.default != 'end') {
                                        //add link to the default uiNode
                                        this.uiLinks.push([flowNode.id, conn.default]);

                                    } else if (flowNode.connections.length == 1) {
                                        //if a node has only "default: 'end'" we add a plus node                                
                                        var newUiPlusNode = 'plus-' + flowNode.id;
                                        this.uiNodes.push({id: newUiPlusNode, type: 'plus', name: '', parentNodeId: flowNode.id, connectionIdx: connIdx});
                                        this.uiLinks.push([flowNode.id, newUiPlusNode]);
                                    }
                                }
                                if (conn.if) {
                                    //adding user uiNode based on connections
                                    var newUiNodeId = "connection-" + flowNode.id + "-" + connIdx;
                                    //link from bot uiNode
                                    this.uiLinks.push([flowNode.id, newUiNodeId]);
                                    //uiNode with user answer

                                    //flow model msg comes as string and as array in old flows
                                    if (!Array.isArray(conn.if.value)) {
                                        conn.if.value = [conn.if.value];
                                    }
                                    this.uiNodes.push({id: newUiNodeId, type: 'user', name: conn.name, msg: conn.if.value, parentNodeId: flowNode.id, connectionIdx: connIdx});
                                    //link from user answer node to specified node in if clausule form connection
                                    if (conn.if.then != 'end') {
                                        this.uiLinks.push([newUiNodeId, conn.if.then]);
                                    } else {
                                        var newUiPlusNode = 'plus-' + newUiNodeId;
                                        this.uiNodes.push({id: newUiPlusNode, type: 'plus', name: '', parentNodeId: newUiNodeId, connectionIdx: connIdx});
                                        this.uiLinks.push([newUiNodeId, newUiPlusNode]);
                                    }
                                }
                            });
                        }
                    });

                } else {
                    //empty flow with just root node. add plus bot node as starting point     
                    var rootNode = this.flow.nodes[0];
                    this.uiNodes.push({id: 'init-plus', type: 'plus', name: '', parentNodeId: rootNode.id, connectionIdx: null});

                }

            },
            /**
             * Uses Dagre-D3 to generate a canvas based on the ui node model
             */
            draw() {

                //initialices dagre
                this.g = new dagreD3.graphlib.Graph()
                        .setGraph({})
                        .setDefaultEdgeLabel(function () {
                            return {};
                        });

                //set nodes to dagre from the ui graph data
                var arrayLength = this.uiNodes.length;

                for (var i = 0; i < arrayLength; i++) {
                    var n = this.uiNodes[i];

                    var msg = '';
                    var label = '';

                    if (n.msg) {
                        if (Array.isArray(n.msg)) {
                            msg = '"' + n.msg.join('", "') + '"';
                        } else {
                            msg = '"' + n.msg + '"';
                        }

                    } else {
                        msg = '';
                    }

                    if (msg == '"*"') {
                        msg = '*';
                    }


                    if (!n.name) {
                        label = '';
                    } else {
                        label = n.name;
                    }


                    if (n.type == 'flowId') {
                        var flowToConnect;
                        if (n.flowId) {//find flow to get its name
                            flowToConnect = _.find(this.flows, function (o) {
                                return o.id == n.flowId;
                            });//using anonymous function because when using short-hand object lodash uses === internally and flow.id and label with connector id might be int/string 
                            

                        } else {//find node to get its name
                            flowToConnect = _.find(this.flow.nodes, function (o) {
                                return o.id == n.connections[0].default;

                            });

                        }

                        if (flowToConnect) {
                            label = flowToConnect.name;
                        }
                    }
                    if (n.type == 'process') {
                        label = this.processes[n.subType];
                    }

                    this.setDagreNode(n.id, n.type, label, msg, n.parentNodeId);

                }


                //set edges (ui links) to dagre from the ui graph data
                var arrayLength = this.uiLinks.length;
                for (var i = 0; i < arrayLength; i++) {
                    var n = this.uiLinks[i];
                    this.setDagreLink(n[0], n[1]);
                }

                // Create the renderer
                var render = new dagreD3.render();

                // Set up an SVG group so that we can translate the final graph.
                var svg = select('#svg-canvas');

                select('#flowchart').remove();

                svg.append('g').attr('id', 'flowchart');

                //we need to reset scale to avoid dagre going crazy. set scale back when finish
                $('#flowchart').css('transform', 'scale(1)');

                // Run the renderer. This is what draws the final graph.
                render(select("#flowchart"), this.g);

                $('#flowchart').css('transform', `scale(${this.scale})`);

                // Center the graph
                this.centerCanvas();
            },

            /**
             * Center the canvas
             */
            centerCanvas() {
                //@TODO still not perfect centering - check automatic center when adding/deleting nodes (dagre is not playing nice)

                //sync content and container to get scroll overflow
                var height = this.g.graph().height * this.scale * 1.3;
                if (height < this.height) {
                    height = this.height - 65;
                }
                var width = this.g.graph().width * this.scale * 1.05;
                if (width < 220) {
                    width = 220;
                }
                var svg = select('#svg-canvas');
                svg.attr("height", height);
                svg.attr("width", width);

                //Center canvas                
                var xCenterOffset = (this.initWidth / 2) - (this.g.graph().width / 2 * this.scale);
                if (xCenterOffset < 0) {
                    xCenterOffset = 20;
                }
                svg.attr("transform", `translate(${xCenterOffset}, 20)`);

                //center chart using scrollbar @TODO improve this
                //jQuery('#container').scrollLeft((this.g.graph().width - this.initWidth) / 2);

            },
            /**
             * Zooms the canvas in and out
             * 
             * @param {bool} inout true = zoom-in, false = zoom-out
             */
            zoom(inout) {
                this.scale = inout ? this.scale + this.scaleStep : this.scale - this.scaleStep;

                //using css to scale instead of svg scale. this supports html on svg nodes
                jQuery('#flowchart').css('transform', `scale(${this.scale})`);
                this.centerCanvas();
            },

            /**
             * Adds a Dagre node customized as bot, user or "plus" nodes with html 
             * 
             * @param {String} id Node UUID
             * @param {String} nodeType bot/user
             * @param {String} label Node text header
             * @param {String} text Node text content
             * @param {String} parentUiNodeId UUID of parent ui node
             */
            setDagreNode(id, nodeType, label, text, parentUiNodeId) {

                var node = _.find(this.uiNodes, ['id', id]);


                var displayNodeType = this.getDisplayNodeType(nodeType);


                if (nodeType == 'flowId') {

                    var labelTruncate = _.truncate(label, {length: 20});
                    label = _.escape(label);
                    labelTruncate = _.escape(labelTruncate);

                    //assign class if its flow or node connector                    
                    var connectorClass = 'nodeConnector';
                    var displayNodeType = 'Node Connector:';
                    if (node.flowId) {
                        connectorClass = 'flowConnector';
                        displayNodeType = 'Flow Connector:';
                    }

                    //node html
                    var html = `
                        <div class="${connectorClass}" style="height: 90px;">                            
                            <div class="actor">${displayNodeType}</div>
                            <div class="node-content">
                                <div title="${label}">${labelTruncate}</div>    
                             </div>
                             <div class="edit-icon">                                                               
                                <span style="cursor: pointer;" onclick="window.cb.showDeleteUiNodeModal('${id}', this);"></span>
                                <span style="cursor: pointer;" onclick="window.cb.showEditUiNodeModal('${id}', this);"></span> 
                            </div>
                        </div>`;

                    var meta = {
                        labelType: "html",
                        label: html,
                        rx: 10,
                        ry: 10,
                        paddingTop: 20,
                        paddingBottom: -2,
                        paddingLeft: 0,
                        paddingRight: -2,
                        height: 10,
                        width: 10
                    };

                    this.g.setNode(id, meta);

                } else if (nodeType == 'process') {

                    var labelTruncate = _.truncate(label, {length: 15});

                    label = _.escape(label);
                    labelTruncate = _.escape(labelTruncate);

                    //node html
                    var html = `
                        <div class="nodes process">                            
                            <div class="actor">${displayNodeType}</div>
                            <div class="node-content">
                                <div class="label" title="${label}">${labelTruncate}</div>                                
                            </div>
                             <div class="edit-icon">                               
                                <span style="cursor: pointer;" onclick="window.cb.showDeleteUiNodeModal('${id}', this);"></span>
                                <span style="cursor: pointer;" onclick="window.cb.showEditUiNodeModal('${id}', this);"></span>                               
                            </div>
                        </div>`;



                    var meta = {
                        labelType: "html",
                        label: html,
                        rx: 10,
                        ry: 10,
                        paddingTop: -2,
                        paddingBottom: -2,
                        paddingLeft: 0,
                        paddingRight: -2,
                        height: 10,
                        width: 10
                    };

                    this.g.setNode(id, meta);



                } else if (nodeType == 'plus') {

                    var parentNode = _.find(this.uiNodes, ['id', parentUiNodeId]);
                    var nextNodeType = 'Bot';
                    if (parentNode) {
                        nextNodeType = (parentNode.type == 'message' || parentNode.type == 'card' || parentNode.type == 'choice') ? 'User' : 'Bot';
                    }

                    var plusClass;
                    if (nextNodeType == 'User') {
                        plusClass = 'uiplusbig'; //@TODO weird name isn't it? change it
                    } else {
                        plusClass = 'plus';
                    }


                    var html = `
                    <div class="${plusClass}">
                        <div class="actor">${nextNodeType}</div>
                        <div style="cursor: pointer;" class="node-content" onclick="window.cb.showAddUiNodeModal('${parentUiNodeId}');">+</div>
                    </div>`;

                    var meta = {
                        labelType: "html",
                        label: html,
                        width: 50,
                        height: 50,
                        rx: 0,
                        ry: 0,
                        paddingTop: -30,
                        paddingBottom: 0,
                        paddingLeft: 0,
                        paddingRight: 0
                    };

                    this.g.setNode(id, meta);

                } else if (nodeType == 'message' || nodeType == 'choice' || nodeType == 'card') {

                    nodeType = 'message';

                    if (!label) {
                        label = text;
                    }

                    var labelTruncate = _.truncate(label, {length: 32});
                    label = _.escape(label);
                    labelTruncate = _.escape(labelTruncate) + "<br /><br />";

                    //node html
                    var displayStyle = '';
                    if (this.uiNodes.length == 2) {
                        displayStyle = 'style="display: none;"';
                    }


                    var html = `
                    <div style="margin-left: 15px; margin-top: 35px; width: 175px; height: 95px">
                        <div /*style="width: ${this.uiBotNodeWidth}px; height: ${this.uiBotNodeHeight}px;"*/ class="nodes ${nodeType}Type">                             
                            <div class="actor">${displayNodeType}</div>
                            <div class="node-content">                                
                                <div class="text" title="${label}">${labelTruncate}</div>                                
                            </div>
                            <div class="edit-icon">                                
                                <span style="cursor: pointer;" onclick="window.cb.showDeleteUiNodeModal('${id}', this);"></span>
                                <span style="cursor: pointer;" onclick="window.cb.showEditUiNodeModal('${id}', this);"></span>
                            </div>
                            <div ${displayStyle} class="add-input" onclick="window.cb.showAddUiNodeModal('${id}');">
                                <div class="icon">+</div>
                                <div class="text">Add Another User Input</div>
                            </div>
                        </div>
                    </div>`;



                    var meta = {
                        labelType: "html",
                        label: html,
                        rx: 10,
                        ry: 10,
                        paddingTop: -2,
                        paddingBottom: -2,
                        paddingLeft: 0,
                        paddingRight: -2,
                        height: this.uiBotNodeHeight - 5,
                        width: 150//this.uiBotNodeWidth
                    };

                    this.g.setNode(id, meta);


                } else if (nodeType == 'user') {

                    if (!label) {
                        label = text;
                    }

                    var labelTruncate = _.truncate(label, {length: 14});
                    label = _.escape(label);
                    labelTruncate = _.escape(labelTruncate);

                    //node html                    
                    var html = `
                        <div /*style="width: ${this.uiUserNodeWidth}px; height: ${this.uiUserNodeHeight}px;"*/ class="nodes ${nodeType}Type">                             
                            <div class="actor">${displayNodeType}</div>
                            <div class="node-content">
                                <div class="text" title="${label}">${labelTruncate}</div>                                
                            </div>
                            <div class="edit-icon">                                
                                <span style="cursor: pointer;" onclick="window.cb.showDeleteUiNodeModal('${id}', this);"></span>
                                <span style="cursor: pointer;" onclick="window.cb.showEditUiNodeModal('${id}', this);"></span>
                            </div>
                        </div>`;



                    var meta = {
                        labelType: "html",
                        label: html,
                        rx: 10,
                        ry: 10,
                        paddingTop: -2,
                        paddingBottom: -2,
                        paddingLeft: 0,
                        paddingRight: -2,
                        height: this.uiUserNodeHeight + 5,
                        width: 180//this.uiUserNodeWidth
                    };

                    this.g.setNode(id, meta);
                }



                return;

            },

            /**
             * Sets Dagre edges (links between nodes) customized for ui 
             * 
             * @param {String} from Node UUID
             * @param {String} to Node UUID
             * @param {Strin} type regular/dotted
             */
            setDagreLink(from, to, type) {
                type = type || 'regular';
                if (type == 'regular') {
                    this.g.setEdge(from, to, {arrowhead: 'undirected', style: "stroke: grey; stroke-width: 1px;", lineInterpolate: 'basis'});
                }
                if (type == 'dotted') {
                    this.g.setEdge(from, to, {arrowhead: 'undirected', style: "stroke: grey; stroke-width: 5px; stroke-dasharray: 10, 10;"});
                }
            },

            /**
             * Shows ui add node modal
             * 
             * @param {String} uiNodeId Node UUID where we want to add a new ui node
             * @param {Object} el UI Node DOM Element
             */
            showAddUiNodeModal(uiNodeId, el) {

                var uiNode = _.find(this.uiNodes, ['id', uiNodeId]);
                var type = _.get(uiNode, 'type') || 'root';//root type node is not added to the uiNodes so if it's undefined, it's root

                //show modal                
                if (type == 'user' || type == 'root') {

                    this.addEditUiBotNodeModalEnabled = true;
                    this.$nextTick(() => {
                        this.$refs.addEditUiBotNodeModal.show()
                                .then((newUiBotNode) => {

                                    this.addEditUiBotNodeModalEnabled = false;

                                    var addPromise = this.addFlowNode(uiNode, newUiBotNode);

                                    addPromise.then(() => {  //delete only when flow is updated
                                        EventBus.$emit('reloadHadron');
                                    });

                                    this.processFlow();
                                    this.draw();

                                }, (e) => {
                                    this.addEditUiBotNodeModalEnabled = false;
                                });
                    });

                } else {
                    this.addEditUiUserNodeModalEnabled = true;
                    this.$nextTick(() => {
                        this.$refs.addEditUiUserNodeModal.show()
                                .then((newConnections) => {

                                    this.addEditUiUserNodeModalEnabled = false;

                                    var addPromise = this.addFlowConnection(uiNode, newConnections);

                                    addPromise.then(() => {  //delete only when flow is updated
                                        EventBus.$emit('reloadHadron');
                                    });

                                    this.processFlow();
                                    this.draw();
                                }, (e) => {
                                    this.addEditUiUserNodeModalEnabled = false;
                                });

                    });
                }

            },

            /**
             * Adds flow node. Redraws canvas. Stores new flow in db
             * 
             * @param {Object} newUiNode 
             */
            addFlowNode(uiNodeSrc, newUiNode) {
                //@TODO validate user input

                //get type  of node which we are going to add a new node
                var nodeType = _.get(uiNodeSrc, 'type') || 'root'; //uiNode will be undefined if there is no ui nodes

                //Adding a bot uiNode to an user uiNode: add a bot flowNode and link connection to it (replaces 'end')
                //1. Add a new flowNode
                var newFlowNodeId = this.guid();

                var newFlowNode = newUiNode;

                newFlowNode.id = newFlowNodeId;


                this.flow.nodes.push(newFlowNode);
                //2. Add link from parentFlowNode to new flowNode                    
                if (nodeType == 'user') {
                    var flowNode = _.find(this.flow.nodes, ['id', uiNodeSrc.parentNodeId]);
                    flowNode.connections[uiNodeSrc.connectionIdx].if.then = newFlowNodeId;
                } else {
                    //we are adding the first ui bot node so now we link root node to the new node
                    this.flow.nodes[0].connections[0].default = newFlowNodeId;
                }


                return this.updateDB();
            },

            /**
             * Add a new connection to a node
             * 
             * @param {Object} flowNodeSrc Node object to add a connection
             * @param {Object} newConnection New connection to be added
             * @returns {Promise} Update promise 
             */
            addFlowConnection(flowNodeSrc, newConnection) {

                //Adding an user uiNode to a bot uiNode: add a connection to parent flowNode
                var flowNode = _.find(this.flow.nodes, ['id', flowNodeSrc.id]);

                //build new if connection 
                var newIf = newConnection.connection;
                newIf.if.then = 'end';

                //add conenction    

                flowNode.connections.unshift(newIf);

                this.nodeConnectionFix(flowNode);

                return this.updateDB();

            },

            /**
             * Shows ui edit node modal
             * 
             * @param {String} flowNodeId Node UUID
             * @param {Object} el UI Node DOM Element
             */
            showEditUiNodeModal(uiNodeId, el) {

                var uiNodeSrc = _.find(this.uiNodes, ['id', uiNodeId]);

                if (uiNodeSrc.type == 'user') {

                    var flowNodeSrc = _.find(this.flow.nodes, ['id', uiNodeSrc.parentNodeId]);

                    var connection = flowNodeSrc.connections[uiNodeSrc.connectionIdx];

                    var uiUserNodeModal = {
                        'connection': connection
                    };
                    this.addEditUiUserNodeModalEnabled = true;
                    this.$nextTick(() => {
                        this.$refs.addEditUiUserNodeModal.show(uiUserNodeModal)
                                .then((newConnection) => {
                                    this.addEditUiUserNodeModalEnabled = false;

                                    var editPromise = this.editFlowConnection(flowNodeSrc, uiNodeSrc.connectionIdx, newConnection);

                                    editPromise.then(() => {  //delete only when flow is updated
                                        EventBus.$emit('reloadHadron');
                                    });

                                    this.processFlow();
                                    this.draw();

                                }, (e) => {
                                    this.addEditUiUserNodeModalEnabled = false;
                                });
                    });
                } else {

                    var flowNodeSrc = _.find(this.flow.nodes, ['id', uiNodeId]);

                    this.addEditUiBotNodeModalEnabled = true;
                    this.$nextTick(() => {
                        this.$refs.addEditUiBotNodeModal.show(flowNodeSrc)
                                .then((newFlowBotNode) => {

                                    this.addEditUiBotNodeModalEnabled = false;

                                    var editPromise = this.editFlowNode(flowNodeSrc, newFlowBotNode);

                                    editPromise.then(() => {  //delete only when flow is updated
                                        EventBus.$emit('reloadHadron');
                                    });

                                    this.processFlow();
                                    this.draw();

                                }, (e) => {
                                    this.addEditUiBotNodeModalEnabled = false;
                                });
                    });

                }
            },

            /**
             * Edit a node
             * 
             * @param {Object} flowNodeSrc Node object to be edited
             * @param {Object} newFlowNode Edited node object data
             * @returns {Promise} Update promise
             */
            editFlowNode(flowNodeSrc, newFlowNode) {

                _.each(flowNodeSrc, (v, k) => {//we delete all obj attr first and then assign new values in order to get a fresh update deleting not needed old attrs and also keeping var reference
                    delete(flowNodeSrc[k]);
                });
                //console.log(flowNodeSrc); // ... why is this not deleting msg attr? .. msg is always there so it's ok
                Object.assign(flowNodeSrc, newFlowNode);


                return this.updateDB();
            },

            /**
             * Edit flow node. Redraws canvas. Store new fow in db
             * @param {Object} flowNodeSrc Connection object to be edited
             * @param {Int} connectionIdx Array connection index
             * @param {Object} newConnection Edited connection object
             * @returns {Promise} Update promise
             *              
             */
            editFlowConnection(flowNodeSrc, connectionIdx, newConnection) {

                //update connection
                flowNodeSrc.connections[connectionIdx] = newConnection.connection;

                this.nodeConnectionFix(flowNodeSrc);

                return this.updateDB();
            },
            
            /**
             * Moves sentiment analysis, wildcard user input and default at the bottom in that order and other stuff
             * 
             * @param {Object} flowNode             
             */
            nodeConnectionFix(flowNode) {

                //check if sentiment analysis is at the end
                _.forEach(flowNode.connections, (conn, key) => {
                    var ifc = conn.if;
                    if (ifc && ifc.context == 'sentimentAnalysis') {//sentiment analysis connection detected. move it to the bottom
                        var wcCopy = _.cloneDeep(flowNode.connections[key]);
                        flowNode.connections.splice(key, 1);
                        flowNode.connections.push(wcCopy);
                    }
                });

                //check if wildcard is at the end
                _.forEach(flowNode.connections, (conn, key) => {
                    var ifc = conn.if;
                    if (ifc && ifc.value[0] == '*') {//wildcard connection detected. move it to the bottom
                        var wcCopy = _.cloneDeep(flowNode.connections[key]);
                        flowNode.connections.splice(key, 1);
                        flowNode.connections.push(wcCopy);
                    }
                });

                //check if default is at the end
                _.forEach(flowNode.connections, (conn, key) => {
                    if (conn.default && !conn.if) {
                        var dcCopy = _.cloneDeep(flowNode.connections[key]);// default detected. move it to the bottom
                        flowNode.connections.splice(key, 1);
                        flowNode.connections.push(dcCopy);
                    } else if (conn.default && conn.if) {
                        //@TODO temp fix, remove when database is clean
                        delete conn.default;
                    }
                });

            },
            
            /**
             * Shows modal to confirm deleting a node
             * 
             * @param {String} id Node UUID
             * @param {Object} el UI Node DOM Element
             */
            showDeleteUiNodeModal(uiNodeId, el) {

                var text = {text: `Do you want to delete node?`};

                this.$refs.confirmDeleteModal.confirm(text)
                        .then(() => {
                            this.deleteUiNode(uiNodeId);
                        })
                        .catch(() => {
                        });

            },

            /**
             * Deletes a flow node based on the ui node model, its childs and parent's conections (just on tree mode). Redraws canvas. Stores new flow on db
             * 
             * @param {String} uiNodeId Node UUID
             */
            deleteUiNode(uiNodeId) {
                //look for the flowNode by uiNodeId
                var uiNode = _.find(this.uiNodes, ['id', uiNodeId]);
                var flowNodeId;
                if (uiNode.type != 'user') {//if it's a bot node, just delete it
                    flowNodeId = uiNode.id;
                } else {//if it's an user node, delete the connection and then delete the node pointing by it
                    var parentFlowNode = _.find(this.flow.nodes, ['id', uiNode.parentNodeId]);
                    //get flow node pointed by the connection
                    flowNodeId = parentFlowNode.connections[uiNode.connectionIdx].if.then;

                    //delete connection
                    parentFlowNode.connections.splice(uiNode.connectionIdx, 1);

                }

                //if connection points to end node id, no need to delete any node
                var deletePromise;
                if (flowNodeId != 'end') {
                    deletePromise = this.deleteFlowNode(flowNodeId);
                } else {
                    deletePromise = this.updateDB();
                }

                deletePromise.then(() => {  //delete only when flow is updated
                    EventBus.$emit('reloadHadron');
                });

                this.processFlow();
                this.draw();

            },

            /**
             * Deletes a flow node, its childs and parent's conections (just on tree mode)
             * 
             * @param {String} flowNodeId Node UUID
             * @returns {Promise} Update promise
             */
            deleteFlowNode(flowNodeId, updateDB = true) {
                var flowNode = _.find(this.flow.nodes, ['id', flowNodeId]);

                if (!flowNode) {
                    return;
                }


                //first look for connections and delete nodes pointed by them
                _.forEach(flowNode.connections, (c) => {
                    var ifc = _.get(c, 'if');
                    if (ifc) {
                        if (ifc.then != 'end') {//found a valid node for the connection, delete it                        
                            this.deleteFlowNode(ifc.then, false);
                        }
                    } else {                                                
                        var ifd = _.get(c, 'default');
                        if (flowNode.type != 'flowId') {//if it's a flow or node connection we stop looking for more
                            if (ifd && ifd != 'end') {//found a valid node for the connection, delete it                        
                                this.deleteFlowNode(ifd, false);
                            }
                        }
                    }
                });

                //delete the node                
                _.remove(this.flow.nodes, {id: flowNodeId});

                //check if there is any other node pointing to the node to be deleted and remove the connection
                _.forEach(this.flow.nodes, function (nc) {
                    //looking for defaults
                    var cd = _.find(nc.connections, function (c) {
                        return _.get(c, 'default') == flowNodeId;
                    });
                    if (cd) {
                        cd.default = 'end';
                    }
                    //looking for conditionals
                    var ci = _.find(nc.connections, function (c) {
                        return _.get(c, 'if.then') == flowNodeId;
                    });
                    if (ci) {
                        ci.if.then = 'end';
                    }
                    
                    //looking for node connection
                    
                });

                var updatePromise;
                if (updateDB) {//shouldn't update on nested calls
                    updatePromise = this.updateDB();
                }

                return updatePromise;

            },

            /**
             * Update flow model on db
             * 
             * @returns {Promise} Update promise
             * 
             */
            updateDB() {
                //removing null values of nodes
                _.forEach(this.flow.nodes, (n) => {
                    _.forEach(n, (v, k) => {
                        if (v === null) {
                            delete n[k];
                        }
                    });
                });

                var promise = this.$http.post(`/api/flow/${this.flowId}`, {
                    flowId: this.flowId,
                    flow: this.flow,
                    updatedAt: this.updatedAt
                })
                        .then(
                                (response) => {
                            this.updatedAt = response.data.updated_at;
                        },
                                (error) => {
                            if (error.response.data.message == 'UPDATEDAT_MISMATCH') {
                                alert('The flow was updated by another user. Your changes will be discarded.');
                                this.loadFlow(this.flowId);
                            } else {
                                console.log(error.response);
                                alert(error);
                            }
                        });

                return promise;
            },

            /**
             * Get display label for each node type
             * 
             * @param {type} nodeType
             * @returns {default.methods.nodeTypeUiMap}
             */
            getDisplayNodeType(nodeType) {
                return this.nodeTypeDisplayMap[nodeType];
            },

        }
    }
</script>




