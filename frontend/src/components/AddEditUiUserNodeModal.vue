<template>

    <div class="modal show" :id="`modal${_uid}`" tabindex="-1" role="dialog">
        <div id="myUserModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">User Input</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <ul class="nav nav-tabs user-tabs" id="myTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="input-tab" data-toggle="tab" href="#input" role="tab" aria-controls="input" aria-selected="true" v-on:click="setTab('input')">Input</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="sentiment-tab" data-toggle="tab" href="#triggers" role="tab" aria-controls="sentiment" aria-selected="false" v-on:click="setTab('sentimentAnalysis')">Sentiment</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="conditional-tab" data-toggle="tab" href="#conditional" role="tab" aria-controls="conditional" aria-selected="false" v-on:click="setTab('conditional')">Conditional</a>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">

                            <div class="tab-pane fade show active" id="input" role="tabpanel" aria-labelledby="input-tab">
                                <div v-if="tabId == 'input'">
                                    <form class="">
                                        <div class="bl-top">

                                            <p>Name the direction the user can take and add any keywords or phrases they can type to connect to this path.</p>

                                            <div class="form-group form-inline">
                                                <label for="name">Node Name</label>
                                                <input class="form-control" v-model="frm.connection.name" placeholder="Optional" id="name">
                                            </div>  
                                            <div class="form-group form-inline icheck">
                                                <div class="checkbox-type">
                                                    <div class="chk-botanic">
                                                        <input type="checkbox" name="optionsRadios" id="displayasbutton" value="1" v-model="frm.connection.isButton" >
                                                        <label for="displayasbutton"><div class="label-text">Display as Button</div></label>
                                                    </div> 
                                                </div>                           
                                            </div>  
                                            <div v-bind:class="{'bl-overlay-on': acceptAnyInput, 'bl-overlay-off': !acceptAnyInput }">

                                                <div class="form-group has-success">
                                                    <label for="keywords">Keywords</label>
                                                    <div class="content-keywords">
                                                        <div id="botOutputModal"><!-- @TODO modify custom.css to avoid this -->
                                                            <input-tag ref="inputTag" :type="'input'" :tags="frm.connection.if.value"></input-tag>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group form-inline list">
                                                    <p class="sub-label">Or, choose a shortcut</p>
                                                    <div class="button-group">
                                                        <button type="button" id="dropbtn" class="btn btn-sm dropdown-toggle form-control" data-toggle="dropdown"><span class="select-channel-txt">Keywords Shortcuts</span></button>
                                                        <ul class="dropdown-menu">
                                                            <li v-for="(varId, varName) in concepts" v-on:click="setVariations(varId)"><a href="#" class="small" data-value="option1" tabIndex="-1" >All variations of "{{varName}}"</a></li>                                                            
                                                        </ul>
                                                    </div>
                                                </div>

                                                <div class="form-group form-inline list">
                                                    <p class="sub-label">Intent Match Type</p>
                                                    <div class="button-group">
                                                        <button type="button" id="dropbtn" class="btn btn-sm dropdown-toggle form-control" data-toggle="dropdown"><span class="select-channel-txt">{{op[frm.connection.if.op]}}</span></button>
                                                        <ul class="dropdown-menu">
                                                             <li v-for="(opName, opId) in op" v-on:click="frm.connection.if.op = opId"><a href="#" class="small" data-value="option1" tabIndex="-1" >{{opName}}</a></li>                                                            
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>        



                                        <div class="bl-bottom">
                                            <p>Or you can choose to allow any input to move the user forward.</p>
                                            <div class="form-group form-inline">
                                                <div class="checkbox-type">
                                                    <div class="chk-botanic">
                                                        <input type="checkbox" name="optionsRadios" id="acceptanykey" v-model="acceptAnyInput" v-on:click="toggleAcceptAnyInput" />
                                                        <label for="acceptanykey"><div class="label-text"><strong>Accept Any Input</strong></div></label>
                                                    </div>
                                                </div>                           
                                            </div>  

                                        </div>
                                    </form>

                                </div>
                            </div>

                            <div class="tab-pane fade" id="triggers" role="tabpanel" aria-labelledby="sentiment-tab">

                                <p>If you need to trigger a Bot Output based on user sentiment rather than specific keywords, select the sentiment below.</p> 
                                <div id="barra">
                                    <label>Sentiment Trigger</label>

                                    <div class="barraSentiment">
                                        <div class="linea"></div>
                                        <div class="item"><i class="fa fa-frown-o"></i></div>
                                        <div class="item"></div>
                                        <div class="item"></div>
                                        <div class="item"><i class="fa fa-meh-o"></i></div>
                                        <div class="item"></div>
                                        <div class="item"></div>
                                        <div class="item"><i class="fa fa-smile-o"></i></div>
                                    </div>


                                    <input id="sentimentSlider" 
                                           data-slider-id="ex1Slider"
                                           type="text" 
                                           data-slider-min="-5" 
                                           data-slider-max="5" 
                                           data-slider-step="1" 
                                           data-slider-value="0" />

                                </div>
                            </div>

                            <div class="tab-pane fade" id="conditional" role="tabpanel" aria-labelledby="conditional-tab">
                                <div  v-if="tabId == 'conditional'">

                                    <div class="blConditional scrollbar">
                                        <form class="">
                                            <div class="bl-top">

                                                <p>Name the direction the user can take and add any keywords or phrases they can type to connect to this path.</p>

                                                <div class="form-group form-inline">
                                                    <label for="name">Node Name</label>
                                                    <input class="form-control" v-model="frm.connection.name" id="name">
                                                </div>  
                                                <div class="form-group form-inline icheck">
                                                    <div class="checkbox-type">
                                                        <div class="chk-botanic">
                                                            <input type="checkbox" name="optionsRadios" id="displayasbutton" value="1" v-model="frm.connection.isButton" >
                                                            <label for="displayasbutton"><div class="label-text">Display as Button</div></label>
                                                        </div> 
                                                    </div>                           
                                                </div>  
                                                <div v-bind:class="{'bl-overlay-on': acceptAnyInput, 'bl-overlay-off': !acceptAnyInput }">
                                                    <div class="form-group has-success">
                                                        <label for="keywords">Keywords</label>
                                                        <div class="content-keywords">
                                                            <div id="botOutputModal"><!-- @TODO modify custom.css to avoid this -->
                                                                <input-tag ref="inputTag" :type="'input'" :tags="frm.connection.if.value"></input-tag>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="form-group form-inline list">
                                                        <p class="sub-label">Or, choose a shortcut</p>
                                                        <div class="button-group">
                                                            <button type="button" id="dropbtn" class="btn btn-sm dropdown-toggle form-control" data-toggle="dropdown"><span class="select-channel-txt">Keywords Shortcuts</span></button>
                                                            <ul class="dropdown-menu">
                                                                <li v-for="(varId, varName) in concepts" v-on:click="setVariations(varId)"><a href="#" class="small" data-value="option1" tabIndex="-1" >All variations of "{{varName}}"</a></li>                                                                                                                        </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>                                

                                            <div class="bl-bottom">
                                                <p>Or you can choose to allow any input to move the user forward.</p>
                                                <div class="form-group form-inline">
                                                    <div class="checkbox-type">
                                                        <div class="chk-botanic">
                                                            <input type="checkbox" name="optionsRadios" id="acceptanykey" v-model="acceptAnyInput" v-on:click="toggleAcceptAnyInput" />
                                                            <label for="acceptanykey"><div class="label-text"><strong>Accept Any Input</strong></div></label>
                                                        </div>
                                                    </div>                           
                                                </div>  

                                            </div>
                                            <br/>
                                            <br/>

                                            <div class="bl-bottom conditionalOption">

                                                <div class="form-group form-inline list listType">
                                                    <p class="sub-label"><strong>Conditional</strong></p>                                                    
                                                </div>
                                                <p class="sub-label">The user will continue on this path if ...</p>
                                                <div id="blTypeNumeric" class="blTypeOption">
                                                    <div class="form-group form-inline list">
                                                        <div class="listSaveUser">
                                                            <div class="button-group">
                                                                <button type="button" id="dropbtn" class="btn btn-sm dropdown-toggle form-control" data-toggle="dropdown"><span class="select-channel-txt" v-bind:class="{'dropdown-selected': frm.connection.if.pre.varName}" :title="frm.connection.if.pre.varName">{{_.truncate(frm.connection.if.pre.varName,{length:23}) || 'Saved User Input'}}</span></button>
                                                                <ul class="dropdown-menu">
                                                                    <li v-for="fieldId in fields" v-on:click="frm.connection.if.pre.varName = fieldId">
                                                                        <a href="#" class="small" data-value="option1" tabIndex="-1" >{{_.truncate(fieldId,{length:40})}}</a>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                        <p class="sub-label">is</p>
                                                        <div class="listEqual">
                                                            <div class="button-group">
                                                                <button type="button" id="dropbtn" class="btn btn-sm dropdown-toggle form-control" data-toggle="dropdown"><span class="select-channel-txt">{{conditionalOperators[frm.connection.if.pre.op]}}</span></button>
                                                                <ul class="dropdown-menu">
                                                                    <li v-for="(cOpLabel, cOpId) in conditionalOperators" v-on:click="frm.connection.if.pre.op = cOpId">
                                                                        <a href="#" class="small" data-value="option1" tabIndex="-1">{{cOpLabel}}</a>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                        <input class="form-control numeric" v-model="frm.connection.if.pre.value" placeholder="Value" id="name">

                                                    </div>
                                                </div>                                    
                                            </div>
                                        </form>
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
                            <button type="submit" class="btn btn-botanic" :disabled="!this.saveButtonEnabled" v-on:click="save">Save</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

</template>

<script>
    /**
     * Flow user node CRUD modal
     * 
     */

    import Bootstrap4Modal from './Bootstrap4Modal.vue';
    import InputTag from './InputTag.vue';//fork of vue-input-tags from npm
    import { EventBus } from '../eventBus.js';

    import 'bootstrap-slider/dist/bootstrap-slider.js';
    import 'bootstrap-slider/dist/css/bootstrap-slider.css';

    export default {
        name: 'AddEditUiUserNodeModal',
        extends: Bootstrap4Modal,
        props: ['project', 'triggers'],
        components: {
            'input-tag': InputTag
        },
        data() {
            return {
                tabId: '',
                isLoaded: false,
                fields: [],
                isEditing: null,
                acceptAnyInput: false,
                saveButtonEnabled: false,

                conditionalOperators: {
                    eq: 'equal',
                    neq: 'non-equal',
                    gt: 'greater than',
                    lt: 'lesser than'
                },

                concepts: {
                    yes: '~yes',
                    no: '~no',
                    hello: '~emohello'
                },

                op: {
                    chatscript: 'ChatScript Pattern',
                    pattern: 'Keyword-based',                    
                    regex: 'Regular Expression',
                    eq: 'Exact Phrase/Word'
                },

                frm: {
                    connection: {
                        name: '',
                        'if': {
                            context: 'userInput',
                            op: 'pattern',
                            value: [],
                            pre: {
                                varName: null,
                                op: null,
                                value: null
                            }
                        },
                        isButton: false
                    }
                }

            };
        },
        watch: {//@TODO handle this with a proper library
            'frm.connection.if.value': function (val) {
                this.checkInputs();
                this.checkSaveValidation();
            },
            'frm.connection.name': function () {
                this.checkSaveValidation();
            },
            'frm.connection.isButton': function () {
                this.checkSaveValidation();
            }

        },

        created() {
            EventBus.$on('inputTagKeyup', () => {
                this.checkSaveValidation();
            });
            EventBus.$on('inputTagChange', () => {
                this.checkSaveValidation();
            });

        },

        mounted() {
            $('#sentimentSlider').slider({});
        },

        beforeDestroy() {
            $('#sentimentSlider').slider('destroy');
            EventBus.$off('inputTagKeyup');
            EventBus.$off('inputTagChange');
        },

        methods: {//@TODO this needs refactor
            
            /**
             * Launches modal
             * 
             * @param {Object} frm Form object
             * @returns {Promise} Promise from modal
             */
            show(frm) {

                var ret;

                if (frm) {

                    //this will fix early nodes which had context with name value
                    if (frm.connection.if.context != 'sentimentAnalysis' && frm.connection.if.context != 'userInput') {
                        frm.connection.if.context = 'userInput';
                    }


                    if (frm.connection.if.context == 'sentimentAnalysis' && Array.isArray(frm.connection.if.value)) {
                        frm.connection.if.value = frm.connection.if.value[0];
                    }


                    ret = this.showModal(frm);


                    if (frm.connection.if.context == 'sentimentAnalysis') {
                        this.checkSaveValidation();
                        
                        this.tabId = 'sentimentAnalysis';

                        $(`.user-tabs a[href="#triggers"]`).tab('show');

                        $('#sentimentSlider').slider('setValue', frm.connection.if.value);

                    } else if (frm.connection.if.context == 'userInput') {

                        if (_.get(frm.connection.if, 'pre.varName')) {
                            this.tabId = 'conditional';
                            $(`.user-tabs a[href="#conditional"]`).tab('show');

                        } else {
                            this.tabId = 'input';
                            $(`.user-tabs a[href="#input"]`).tab('show');
                        }
                    }

                } else {

                    //set active tab default user input
                    $(`.user-tabs a[href="#input"]`).tab('show');

                    ret = this.showModal();

                    this.tabId = 'input';

                }

                this.isEditing = frm ? true : false;

                this.checkInputs();

                this.loadProjectFieldIds();

                this.isLoaded = true;

                return ret;
            },

            /**
             * Save form, resume modal with new data
             * 
             */
            save() {
                //try to save if the user didnt click on add text
                if (typeof this.$refs.inputTag != 'undefined') {
                    this.$refs.inputTag.addNew();
                }

                var frm = _.cloneDeep(this.frm);

                if (frm.connection.if.context == 'sentimentAnalysis') {
                    frm.connection.if.value = $('#sentimentSlider').slider('getValue');
                    frm.connection.if.op = 'eq';
                    
                }

                if (!frm.connection.if.pre.varName) {
                    delete(frm.connection.if.pre);
                }


                this.actionModal(frm);

            },

            /**
             * Toggles checkbox
             * 
             */
            toggleAcceptAnyInput() {
                this.acceptAnyInput = !this.acceptAnyInput;

                if (this.acceptAnyInput) {
                    this.$refs.inputTag.reset();
                    this.frm.connection.if.value = ['*'];
                    this.$refs.inputTag.readOnly = true;
                } else {
                    this.frm.connection.if.value = [];
                    this.$refs.inputTag.readOnly = false;
                }
            },

            /**
             * Checks accept any input on value
             * 
             */
            checkInputs() {
                if (_.isEqual(this.frm.connection.if.value, ['*'])) {
                    this.acceptAnyInput = true;
                } else {
                    this.acceptAnyInput = false;
                }
            },

            /**
             * Loads project's fields ids
             * 
             */
            loadProjectFieldIds() {//@TODO this should be fired from flowBuilder like formIds
                //load field ids from all flows of the project
                this.$http.get(`/api/project/${this.project.id}/fieldIds`, {})
                        .then(
                                (response) => {

                            this.fields = response.data;
                        },
                                (error) => {

                            console.log(error.response);
                        }
                        );

            },

            /**
             * Sets tab id
             * 
             * @param {String} tabId
             */
            setTab(tabId) {
                this.tabId = tabId;

                if (tabId == 'input' || tabId == 'conditional') {
                    this.setContext('userInput');
                }
                if (tabId == 'sentimentAnalysis') {
                    this.setContext('sentimentAnalysis');
                } else {
                    this.frm.connection.if.op = 'pattern';
                }
                
                this.checkSaveValidation();//sentiment analysis requires this
            },

            /**
             * Sets context
             * 
             * @param {String} context
             */
            setContext(context) {
                if (!Array.isArray(this.frm.connection.if.value)) {
                    this.frm.connection.if.value = [];
                }
                this.frm.connection.if.context = context;
            },

            /**
             * Set concepts
             * 
             * @param {String} concept             
             */
            setVariations(concept) {
                this.frm.connection.if.value.push(concept);
            },

            /**
             * Local frontend form validation. It should be called every time a field is updated
             */
            checkSaveValidation() {

                //will be true if value is not empty
                //and if isButton is true and name is not empty
                //if button is false, just evaluate value
                this.saveButtonEnabled =
                        ((this.frm.connection.if.value.length || (this.$refs.inputTag && this.$refs.inputTag.newTag))
                        && ((this.frm.connection.isButton && this.frm.connection.name) || !this.frm.connection.isButton)) 
                        || this.tabId == 'sentimentAnalysis' ? true : false;

            }
        }
    }


</script>
