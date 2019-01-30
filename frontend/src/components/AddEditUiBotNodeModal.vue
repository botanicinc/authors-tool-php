<template>
    <div class="modal show" :id="`modal${_uid}`" tabindex="-1" role="dialog">

        <div id="botOutputModal" class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Bot Output</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <ul class="nav nav-tabs bot-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a v-on:click="setTabId('text')" class="nav-link" id="text-tab" data-toggle="tab" href="#text" role="tab" aria-controls="text" aria-selected="true">Text</a>
                        </li>
                        <li class="nav-item">
                            <a v-on:click="setTabId('media')" class="nav-link" id="media-tab" data-toggle="tab" href="#media" role="tab" aria-controls="media" aria-selected="false">Media</a>
                        </li>
                        <li class="nav-item">
                            <a v-on:click="setTabId('process')" class="nav-link" id="action-tab" data-toggle="tab" href="#process" role="tab" aria-controls="action" aria-selected="false">Action</a>
                        </li>
                        <li class="nav-item ml-auto">
                            <a v-on:click="setTabId('flowId')" class="nav-link" id="connect-tab" data-toggle="tab" href="#flowId" role="tab" aria-controls="connect" aria-selected="false">Connect</a>
                        </li>
                    </ul>

                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="text" role="tabpanel" aria-labelledby="text-tab">
                            <div class="blText scrollbar">
                                <text-input v-if="isLoaded.text" ref="textInput" :frm="frm" :forms="forms" :label="'Add Bot Output text below. Adding more than one output will alternate between them.'"></text-input>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="media" role="tabpanel" aria-labelledby="media-tab">
                            <div class="blText scrollbar">
                                <form class="">
                                    <div class="form-group form-inline list media">
                                        <div class="button-group">
                                            <button type="button" id="dropbtn" class="btn btn-sm dropdown-toggle form-control" data-toggle="dropdown"><span class="select-channel-txt" v-bind:class="{'dropdown-selected': frm.mediaCard.type}">{{mediaCardTypeDisplayMap[frm.mediaCard.type]}}</span></button>
                                            <ul class="dropdown-menu">
                                                <li v-for="(mediaCardLabel, mediaCardId) in mediaCardTypeDisplayMap" v-on:click="frm.mediaCard.type = mediaCardId">
                                                    <a href="#" class="small" data-value="option1" tabIndex="-1">{{mediaCardLabel}}</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div v-show="tabId == 'media' && frm.mediaCard.type">
                                        <div class="form-group form-inline">
                                            <label for="name">Media Title</label>
                                            <input class="form-control" v-model="frm.mediaCard.title">
                                        </div>
                                        <div class="form-group form-inline">
                                            <label for="name">Subtitle</label>
                                            <input class="form-control" v-model="frm.mediaCard.subtitle"  placeholder="Optional">
                                        </div>
                                        <div v-if="showField('image_uri')" v-model="frm.mediaCard.info.image_uri" class="form-group form-inline">
                                            <label for="name">Image URL</label>
                                            <input class="form-control" v-model="frm.mediaCard.info.image_uri">
                                        </div>
                                        <div class="form-group form-inline">
                                            <label for="name">{{getDisplayMediaCardType(frm.mediaCard.type)}} URL</label>
                                            <input class="form-control" v-model="frm.mediaCard.info.media_uri" >
                                        </div>
                                    </div>
                                    <text-input v-if="isLoaded.media" ref="textInput" :frm="frm" :forms="forms" :label="'If a textual Bot Output needs to accompany this media card, add below.'"></text-input>

                                </form>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="process" role="tabpanel" aria-labelledby="action-tab">
                            <div class="blText scrollbar">
                                <form class="">
                                    <div class="form-group form-inline list action">
                                        <div class="button-group">
                                            <button type="button" id="dropbtn" class="btn btn-sm dropdown-toggle form-control" data-toggle="dropdown">
                                                <span class="select-channel-txt" v-bind:class="{'dropdown-selected': frm.subType}">{{processes[frm.subType]}}</span>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li v-for="(processLabel, processId) in processes" v-on:click="frm.subType = processId">
                                                    <a href="#" class="small" data-value="option1" tabIndex="-1">{{processLabel}}</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div v-show="frm.type == 'process'">
                                        <div v-show="frm.subType == 'sendEmail'">
                                            <div class="form-group form-inline">
                                                <label for="name">Recipient</label>
                                                <input class="form-control" v-model="frm.sendEmail.recipient">
                                            </div>
                                            <div class="form-group form-inline">
                                                <label for="name">Subject</label>
                                                <input class="form-control"  v-model="frm.sendEmail.subject">
                                            </div>
                                            <div class="form-group form-inline list form">
                                                <label for="name">Form</label>
                                                <div class="button-group">
                                                    <button type="button" id="dropbtn" class="btn btn-sm dropdown-toggle form-control" data-toggle="dropdown"><span class="select-channel-txt" v-bind:class="{'dropdown-selected': frm.sendEmail.formId}">{{frm.sendEmail.formId || 'Optional'}}</span></button>
                                                    <ul class="dropdown-menu">
                                                        <li v-for="form in forms" v-on:click="frm.sendEmail.formId = form">
                                                            <a href="#" class="small" data-value="option1" tabIndex="-1">{{form}}</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="form-group form-inline emailbody">
                                                <label for="name">Email Body</label>
                                                <textarea class="form-control" rows="3"  v-model="frm.sendEmail.body"></textarea>
                                            </div>
                                        </div>   
                                    </div>

                                    <text-input v-if="isLoaded.process" ref="textInput" :frm="frm" :forms="forms" :label="'If a textual Bot Output needs to accompany this video, add below.'"></text-input>

                                </form>    
                            </div>
                        </div>
                        <div class="tab-pane fade" id="flowId" role="tabpanel" aria-labelledby="connect-tab">
                            <div id="connect">

                                <p>You can choose to connect this path with a Bot Output node inside this flow, or you can connect to a different flow.</p>

                                <div class="form-group list">
                                    <div class="radio-type">
                                        <div class="radio-botanic">
                                            <input type="radio" name="connectto" id="op1" value="node" v-model="connectto">
                                            <label for="op1"><div class="label-text">Connect to Node</div></label>
                                        </div>                                               
                                    </div>
                                </div>

                                <div class="form-group form-inline list connectType">
                                    <div class="button-group">
                                        <button type="button" id="dropbtn" class="btn btn-sm dropdown-toggle form-control" data-toggle="dropdown"><span class="select-channel-txt" v-bind:class="{'dropdown-selected': gotoNode}">{{nodeNames[gotoNode] || 'Select Node'}}</span></button>
                                        <ul class="dropdown-menu">
                                            <li v-for="(nodeName, nodeId) in nodeNames" v-on:click="gotoNode = nodeId; connectto = 'node';">
                                                <a href="#" class="small" data-value="option1" tabIndex="-1">{{nodeName}}</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="form-group list">
                                    <div class="radio-type">
                                        <div class="radio-botanic">
                                            <input type="radio" name="connectto" id="op2" value="flow" v-model="connectto">
                                            <label for="op2"><div class="label-text">Connect to Flow</div></label>
                                        </div>                                               
                                    </div>
                                </div>

                                <div class="form-group form-inline list connectType">
                                    <div class="button-group">
                                        <button type="button" id="dropbtn" class="btn btn-sm dropdown-toggle form-control" data-toggle="dropdown"><span class="select-channel-txt" v-bind:class="{'dropdown-selected': frm.flowId}">{{getDisplayFlowId(frm.flowId) || 'Select Flow'}}</span></button>
                                        <ul class="dropdown-menu">
                                            <li v-for="flow in flows" v-on:click="frm.flowId = flow.id;connectto = 'flow';">
                                                <a href="#" class="small" data-value="option1" tabIndex="-1">{{flow.name}}</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="form-group form-inline">
                                    <label class="connect-label" for="nodename">Or create a new flow</label>
                                    <input class="form-control w340" placeholder="Optional" v-model="newFlowId" v-on:click="connectto = 'flow'" id="nodename" >
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <div class="col-md-6">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                    <div class="col-md-6" align="right">
                        <button type="submit" class="btn btn-botanic" v-on:click="save" :disabled="!saveButtonEnabled">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
    /**
     * Flow bot node CRUD modal
     * 
     */

    import Bootstrap4Modal from './Bootstrap4Modal.vue';
    import TextInput from './TextInput.vue';
    import { EventBus } from '../eventBus.js';

    export default {
        name: 'AddEditUiBotNodeModal',
        extends: Bootstrap4Modal,
        components: {
            'text-input': TextInput
        },
        props: ['flows', 'processes', 'project', 'nodeNames'],
        data() {
            return {
                isLoaded: {
                    text: false,
                    media: false,
                    process: false
                },
                saveButtonEnabled: false,
                isEditing: null,
                tabId: 'text',
                newFlowId: '',
                gotoNode: null,
                connectto: 'node',
                forms: [],

                tabIdNodeTypeMap: {
                    choice: 'message',
                    text: 'message',
                    media: 'card',
                    flowId: 'flowId',
                    process: 'process'
                },
                mediaCardTypeDisplayMap: {
                    video: 'Video',
                    audio: 'Audio',
                    image: 'Image'
                },
                defaultValues: {//hardcoded default values
                    mediaCard: {
                        video: {
                            info: {
                                profiles: 'hd',
                                aspect: "16:9",
                                autostart: true,
                                autoloop: true,
                                mute: false,
                                controls: true
                            }
                        },
                        audio: {
                            info: {
                                autostart: true,
                                autoloop: true,
                                mute: false,
                                controls: true
                            }
                        },
                        image: {
                            info: {}
                        }
                    }
                },
                frm: {
                    type: 'message',
                    subType: 'text',
                    name: '',
                    msg: [],
                    fieldId: null,
                    formId: null,
                    flowId: null,
                    sendEmail: {
                        recipient: null,
                        subject: null,
                        body: null,
                        formId: null

                    },
                    mediaCard: {

                        type: null,
                        title: null,
                        subtitle: null,
                        info: {
                            image_uri: null,
                            media_uri: null,
                            alt_text: null
                        }


                    },
                    connections: [//@TODO this is causing issues. CHECK and REMOVE
                        {
                            'default': 'end'
                        }
                    ]
                }
            };
        },
        watch: {
            //@TODO handle this with a proper library!
            
            'frm.name': function () {
                this.checkSaveValidation();
            },
            'frm.subType': function () {
                this.checkSaveValidation();
            },
            'frm.mediaCard.type': function () {
                this.checkSaveValidation();
            },
            'connectto': function () {
                this.checkSaveValidation();
            },
            'frm.flowId': function () {
                this.checkSaveValidation();
            },
            'newFlowId': function () {
                this.checkSaveValidation();
            },
            'gotoNode': function () {
                this.checkSaveValidation();
            },
            'frm.mediaCard.title': function () {
                this.checkSaveValidation();
            },
            'frm.mediaCard.subTitle': function () {
                this.checkSaveValidation();
            },
            'frm.mediaCard.info.image_uri': function () {
                this.checkSaveValidation();
            },
            'frm.mediaCard.info.media_uri': function () {
                this.checkSaveValidation();
            },
            'frm.sendEmail.recipient': function () {
                this.checkSaveValidation();
            },
            'frm.sendEmail.subject': function () {
                this.checkSaveValidation();
            },
            'frm.sendEmail.body': function () {
                this.checkSaveValidation();
            }

        },

        created() {
            EventBus.$on('inputTagKeyup', (newTag) => {
                this.checkSaveValidation();
            });
            EventBus.$on('inputTagChange', (newTag) => {
                this.checkSaveValidation();
            });
        },

        beforeDestroy() {
            EventBus.$off('inputTagKeyup');
            EventBus.$off('inputTagChange');
        },

        methods: {//@TODO too many changes in these methods. this needs refector
            
            /**
             * Launch the modal
             * 
             * @param {Object} frm Object with form data             
             * @returns {Promise} Promise which is going to be resolved on updating/saving or rejected on cancel
             */
            show(frm) {

                frm = _.cloneDeep(frm);

                if (frm) {
                    //set data to the tabs
                    if (frm.type == 'card') {
                        if (frm.subType == 'media') {
                            frm.mediaCard = frm.info;
                        }
                    }
                    if (frm.type == 'process') {
                        if (frm.subType == 'sendEmail') {
                            frm.sendEmail = frm.info;
                        }
                    }
                    delete(frm['info']);

                    var ret = this.showModal(frm);

                    if (frm.type == 'flowId') {
                        //check radiobutton for connect to node and flow
                        if (frm.flowId) {
                            this.connectto = 'flow';
                        } else {
                            this.connectto = 'node';

                            //load gotoNode value                             
                            this.gotoNode = frm.connections[0].default;
                        }
                    }


                    this.isEditing = true;
                    //select tab
                    this.setTabId(this.getTabId());
                    $(`.bot-tabs a[href="#${this.tabId}"]`).tab('show');

                } else {

                    var ret = this.showModal();

                    this.setTabId(this.getTabId());
                    //showModal will reset initial values so tabId  should be 'text'
                    $(`.bot-tabs a[href="#${this.tabId}"]`).tab('show');
                    this.isEditing = false;

                }

                this.checkSaveValidation();

                this.loadForms();

                return ret;
            },

            /**
             * Resolve the modal promise with updated form data
             * 
             * @returns {Promise} resolved promise with updated data
             */
            save() {

                //try to add input the devbot forgot to add
                if (this.$refs.textInput && typeof this.$refs.textInput.$refs.inputTag != 'undefined') {
                    this.$refs.textInput.$refs.inputTag.addNew();
                }

                this.frm.subType = this.getNodeSubType();


                //set default connection if there is just one connection.
                //this means if just default and better to set as default if it happened to be connectto node before
                if (this.frm.connections.length == 1) {
                    this.frm.connections = [{'default': 'end'}];//@TODO this must be removed, it's causing an issue which is being fixed in the fix function 
                }

                //set data to info attr
                if (this.frm.type == 'card') {
                    if (this.frm.subType == 'media') {

                        //fill default hardcoded values for card media type
                        this.frm.info = this.defaultValues.mediaCard[this.frm.mediaCard.type];
                        if (!this.frm.info) {
                            this.frm.info = {};
                        }
                        //fill fields from the form
                        _.defaultsDeep(this.frm.info, this.frm.mediaCard);
                    }
                }
                if (this.frm.type == 'process') {
                    if (this.frm.subType == 'sendEmail') {
                        this.frm.info = this.frm.sendEmail;
                    }
                }
                delete(this.frm['mediaCard']);
                delete(this.frm['sendEmail']);


                //if choosing new flow to connect
                if (this.frm.type == 'flowId') {
                    if (this.connectto == 'flow') {
                        if (this.newFlowId) {
                            //create new flow, get id and connect it (we are using autoincremental uuid for flows so we need to create it first)
                            EventBus.$emit('addFlow', {name: this.newFlowId, projectId: this.project.id, callback: (newFlowResponse) => {
                                    //assign id to the new flow and call actionModal() to resolve the modal promise so calling code adds the new node
                                    this.frm.flowId = newFlowResponse.data.id;
                                    this.actionModal(this.frm);
                                }});

                            return;

                        }

                    } else if (this.connectto == 'node') {//node default should go to this node
                        this.frm.flowId = null;
                        this.frm.connections = [{'default': this.gotoNode}];
                    }
                }

                this.actionModal(this.frm);

                this.isLoaded = {text: false, media: false, process: false};

            },
            
            /**
             * Sets type of node on add node modal
             * 
             * @param {String} tabId             
             */
            setTabId(tabId) {

                this.tabId = tabId;
                this.frm.type = this.getNodeType();
                //  this.frm.subType = this.getNodeSubType();

                this.isLoaded = {text: false, media: false, process: false};
                this.isLoaded[tabId] = true;

                this.checkSaveValidation();

            },
            
            /**
             * Set node type based on tab id and other things
             * 
             */
            getNodeType() {
                return this.tabIdNodeTypeMap[this.tabId];
            },

            /**
             * Returns node subtype based on tabId
             * 
             * @returns {String} Node subtype
             */
            getNodeSubType() {
                if (this.tabId == 'media') {
                    return 'media';
                }
                if (this.tabId == 'text') {
                    return 'text';
                }
                if (this.tabId == 'flowId') {
                    return null;
                }
                if (this.tabId == 'process') {
                    return this.frm.subType;
                }

            },

            /**
             * Returns card type based on tab id
             * 
             * @returns {String} Card type
             */
            getCardType() {
                if (this.tabId == 'media') {
                    return 'media';
                }
            },

            /**
             * Returns tab id based on node type and subtype
             * 
             * @returns {String} Tab id
             */
            getTabId() {
                var tabId = (_.invert(this.tabIdNodeTypeMap))[this.frm.type];
                if (this.frm.type == 'message' || this.frm.type == 'choice') {
                    if (this.frm.type == 'card' && this.frm.subType == 'media') {
                        tabId = this.frm.mediaCard.type;
                    } else {
                        tabId = 'text';
                    }
                }

                return tabId;
            },

            /**
             * Closes current bot modal and launches user modal
             * 
             * @param {Int} uiNodeSrcId User node id
             */
            showAddUiNodeModal(uiNodeSrcId) {
                this.reject();
                this.$parent.showAddUiNodeModal(uiNodeSrcId);
            },

            /**
             * Controls which fields are shown on th form based on media card type
             * 
             * @param {Int} fieldId Form field id to show or not
             * @returns {Boolean} True to show, False to hide
             */
            showField(fieldId) {
                var cardTypeFieldMap = {
                    video: ['image_uri']
                }

                if (cardTypeFieldMap[this.frm.mediaCard.type] == fieldId) {
                    return true;
                } else {
                    return false;
                }
            },

            /**
             * Returns media card friendly name based on card type
             * 
             * @param {String} cardType
             * @returns {String} Media card friendly name
             */
            getDisplayMediaCardType(cardType) {
                return this.mediaCardTypeDisplayMap[cardType];
            },

            /**
             * Deprecated
             * 
             * @param {type} type
             * @returns {undefined}
             */
            setMediaCardType(type) {
                this.frm.mediaCard.info = {
                    image_uri: this.frm.mediaCard.info.image_uri,
                    media_uri: '',
                    alt_text: this.frm.mediaCard.info.alt_text
                };
            },

            /**
             * Returns flow name based on flow id
             * 
             * @param {Int} flowId
             * @returns {String} Flow name
             */
            getDisplayFlowId(flowId) {
                return _.get(_.find(this.flows, ['id', flowId]), 'name');
            },

            /**             
             * Checks form preflight validation
             * Sets this.saveButtonEnabled to True/False            
             * @TODO handle this with a proper library!
             */
            checkSaveValidation() {
                this.$nextTick(() => {
                    //@TODO use a library to handle this or discard enable/disable save button to move all this logic to backend
                    this.saveButtonEnabled =
                            (this.tabId == 'flowId' && (this.connectto == 'flow' && (this.frm.flowId || this.newFlowId) || (this.connectto == 'node' && this.gotoNode)))
                            || (this.tabId == 'process' && this.frm.subType == 'sendEmail' && this.frm.sendEmail.recipient && this.frm.sendEmail.subject && this.frm.sendEmail.body)
                            || (this.tabId == 'media' && (this.frm.mediaCard.type && this.frm.mediaCard.title && this.frm.mediaCard.info.image_uri && this.frm.mediaCard.info.media_uri))
                            || (this.tabId == 'text' && (this.frm.msg.length || this.$refs.textInput.$refs.inputTag.newTag)) ? true : false;
                });

            },

            /**
             * Loads all forms from the project
             *
             */
            loadForms() {
                //load field ids from all flows of the project
                this.$http.get(`/api/project/${this.project.id}/formIds`, {})
                        .then(
                                (response) => {

                            this.forms = response.data;
                        },
                                (error) => {

                            console.log(error.response);
                        }
                        );

            }

        }
    }


</script>
