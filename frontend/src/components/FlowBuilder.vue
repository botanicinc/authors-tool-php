<template>
    <div id="workspace">    

        <p class="back"><router-link :to="{ name: 'ProjectsList'}">&lt; Back to Projects</router-link></p>

        <button type="submit" class="btn btn-botanic btTest" v-on:click="sidebarShow(true)">Test</button>
        <span class="project-title">
            {{project.name}}
        </span>
        <br>


        <ul id="flowTabs" class="nav nav-tabs">
            <li class="nav-item" v-for="(flow, flowIdx) in flows.slice(0,9)" :key="flow.id" v-on:click.prevent="setFlowId(flow.id)">
                <a class="nav-link" href="#" :id="`flow${flow.id}`" :title="flow.name">{{ _.truncate(flow.name,{length: 13}) }}<span class="edit" @click.prevent.stop="showEditFlowModal(flow, flowIdx == 0)"></span></a>
            </li>          
            <li v-show="flows.length > 9" class="nav-item list">
                <a class="nav-link dropdown-toggle" v-bind:class="{'active': moreFlowName != 'More'}" data-toggle="dropdown"  id="dropbtn" href="#">
                    <span class="select-channel-txt" :title="moreFlowName">{{_.truncate(moreFlowName,{length: 13})}}</span>
                </a>
                <ul class="dropdown-menu">
                    <li v-for="moreFlow in flows.slice(9,1000)">
                        <a href="#" class="small" :data-value="moreFlow.id" tabIndex="-1" v-on:click.prevent="setFlowId(moreFlow.id)" :title="moreFlow.name">{{_.truncate(moreFlow.name,{length: 16})}}</a>
                    </li>
                </ul>
            </li>
            <li class="nav-item">
                <a class="nav-link btMas" href="#" v-on:click.prevent="showAddFlowModal">+</a>
            </li>
        </ul>               

        <flow-chart v-if="flowChartEnabled" :flowId="$route.params.flowId" :flows="flows" :project="project" />

        <div class="test-sidebar" v-show="sidebarShowFlag">
            <iframe :src="hadronUrl" width="100%" height="100%" id="hadronIframe"></iframe>           
        </div>

        <confirm-modal ref="confirmDeleteModal" title="Delete Flow" confirmButton="Delete" text="Deleting this flow will permanently delete all paths and nodes inside of it. Are you sure you want to delete?" />

        <add-edit-flow-modal ref="addEditFlowModal" />

    </div>
</template>

<script>
    /**
     * Workspace flowchart layout 
     * 
     */

    import Vue from 'vue';
    import FlowChart from './FlowChart.vue';
    import ConfirmModal from './ConfirmModal.vue';
    import AddEditFlowModal from './AddEditFlowModal.vue';
    import { EventBus } from '../eventBus.js';

    export default {
        name: 'FlowBuilder',
        components: {
            'flow-chart': FlowChart,
            'confirm-modal': ConfirmModal,
            'add-edit-flow-modal': AddEditFlowModal
        },

        data() {
            return {
                moreFlowName: 'More',
                flowChartEnabled: false,
                flows: [],
                project: {},
                sidebarShowFlag: false,
                hadronUrl: '',
                hadron: {
                    index: 0,
                    url: ['', ''],
                    enabled: [true, false],
                    show: [true, false]
                }
            };
        },

        watch: {
            '$route'(to, from) {
                //reload flow list only when project id changes
                if (from.params.projectId != to.params.projectId) {
                    this.init();
                }
            }

        },

        created() {
            EventBus.$on('addFlow', (args) => {
                this.addFlow(args.name, args.projectId).then((response) => {
                    if (args.callback) {
                        args.callback(response);
                    }
                });
            });

            EventBus.$on('reloadHadron', () => {
                this.resetHadron();
            });

            EventBus.$on('showDeleteFlowModal', (args) => {
                this.showDeleteFlowModal(args.flow);
            });


            window.addEventListener('message', (e) => {
                if (e.data == 'MinimizeIframe') {
                    this.sidebarShow(false);
                }
            });


            this.init();
        },
        
         beforeDestroy() {            
            EventBus.$off('addFlow');
            EventBus.$off('reloadHadron');
            EventBus.$off('showDeleteFlowModal');
        },

        methods: {
            /**
             * Initializes ui
             * 
             */
            init() {
                this.loadProjectFlows(this.$route.params.projectId).then(() => {


                    if (!this.flows.length) {
                        this.showAddFlowModal();
                        return;
                    }

                    var initFlowId = this.$route.params.flowId || this.flows[0].id;

                    this.setFlowId(initFlowId);

                    this.setHadronUrl();


                });
            },

            /**
             * Loads project's flows
             * 
             * @param {Int} projectId
             */
            loadProjectFlows(projectId) {
                this.flowChartEnabled = false;//forcing to reset chartflow to avoid weird vuejs vug with callbacks when adding new flows: Error in nextTick: "TypeError: Cannot read property '_withTask' of undefined"
                //Get project flows

                var project = {};

                return this.$http.get(`/api/project/${projectId}/flow`)
                        .then(response => {
                            this.flows = response.data;

                            this.project = {
                                id: this.$route.params.projectId,
                                name: this.$route.params.projectName
                            };

                            this.flowChartEnabled = true;



                        });
            },

            /**
             * Sets current flow
             * 
             * @param {Int} flowId Flow id
             */
            setFlowId(flowId) {//setting project through url as source of truth
                this.$router.replace({name: 'FlowBuilder', params: {projectId: this.$route.params.projectId, projectName: this.$route.params.projectName, flowId: flowId}});

                var idx = _.findIndex(this.flows, (o) => {
                    return o.id == flowId;
                });//avoid shorthand! flowId sometimes comes as string and shorthand test it with ===

                if (idx > 8) {//it's 'more' tab
                    $('#dropbtn').tab('show');
                    this.moreFlowName = this.flows[idx].name;

                } else {//it's a regular tab
                    this.moreFlowName = 'More';
                    $(`#flow${flowId}`).tab('show');
                }


            },

            /**
             * Launches flow modal
             * 
             * @param {Object} flow Flow data
             * @param {Bool} isRootFlow
             */
            showEditFlowModal(flow, isRootFlow) {

                this.$refs.addEditFlowModal.show(flow, isRootFlow)
                        .then((newFlow) => {
                            this.editFlow(newFlow, this.$route.params.projectId);

                            Object.assign(flow, newFlow);

                        })
                        .catch(() => {
                        });
            },

            /**
             * Edits flow data
             * 
             * @param {Object} newFlow Edited flow data
             * @param {Int} projectId Project id
             */
            editFlow(newFlow, projectId) {
                if (!newFlow.name || !newFlow.id) {
                    return;
                }

                var newFlowObj = {
                    name: newFlow.name,
                    flowId: newFlow.id //@TODO this might not be needed, remove from backend too                                        
                };

                //add new flow in db. get new id
                return this.$http.post(`/api/flow/${newFlow.id}`, newFlowObj);

            },

            /**
             * Launches flow modal
             * 
             * @param {Object} flow Flow data
             */
            showAddFlowModal(flow) {

                this.$refs.addEditFlowModal.showModal(flow)
                        .then((newFlow) => {
                            this.addFlowAndGo(newFlow);
                        })
                        .catch(() => {
                        });
            },

            /**
             * Adds a flow and set as current
             * 
             * @param {Object} newFlow New flow data
             */
            addFlowAndGo(newFlow) {

                this.addFlow(newFlow.name, this.$route.params.projectId)
                        .then(response => {
                            this.setFlowId(response.data.id);
                        })
                        .catch(error => {
                            console.log(error);
                        });

            },

            /**
             * Adds a flow to a project
             * 
             * @param {String} name Flow name
             * @param {Int} projectId Project id
             * @returns {Promise}
             * 
             */
            addFlow(name, projectId) {

                var newFlow = {
                    projectId: projectId,
                    name: name,
                    flowId: 0, //@TODO this might not be needed, remove from backend too                    
                    flow: {}
                };


                if (!newFlow.name || !newFlow.projectId) {
                    return;
                }

                //add new flow in db. get new id
                var promise = this.$http.post(`/api/flow/0`, newFlow);

                promise.then((response) => {
                    //component will reload the new flow
                    newFlow.flowId = response.data.id;
                    newFlow.id = response.data.id;
                    this.flows.push(newFlow);
                    this.flowChartEnabled = true;
                });

                return promise;

            },

            /**
             * Launches flow delete confirm modal
             * 
             * @param {Object} flow Flow data
             */
            showDeleteFlowModal(flow) {

                var text = {text: `Do you want to delete flow '${flow.name}'?`};//this will show a vuejs warn in console, just ignore it

                this.$refs.confirmDeleteModal.confirm(text)
                        .then(() => {
                            this.deleteFlow(flow);
                        })
                        .catch(() => {
                        });

            },

            /**
             * Deletes a flow from the project
             * 
             * @param {Object} flow Flow data
             */
            deleteFlow(flow) {

                this.$http.delete(`/api/flow/${flow.id}`)
                        .then(response => {

                            this.loadProjectFlows(this.$route.params.projectId).then(() => {

                                this.setFlowId(this.flows[0].id);

                            })

                        })
                        .catch(error => {
                            console.log(error);
                        });

            },

            /**
             * Sets test Hadron URL
             * 
             */
            setHadronUrl() {
                var rootFlowUUID = this.getMainFlow();
                this.hadronUrl = `${HADRON_URI}?botSizeClass=fullscreen&botUserData=${rootFlowUUID}&botTitle=Flow Preview&botIsSecure=true&botCloseIcon=close&botResetOnLoad=true`;
            },

            /**
             * Returs root flow id
             * 
             * @returns {String} Flow id
             */
            getMainFlow() {
                return this.flows[0].id;
            },

            /**
             * Sends reset signal to Hadron
             * 
             */
            resetHadron() {
                document.getElementById('hadronIframe').contentWindow.postMessage("refreshHadron", "*");
            },

            /**
             * Toggles test sidebar visibility
             * 
             * @param {Bool} status Sidebar status
             */
             sidebarShow(status) {
                this.sidebarShowFlag = status ? true : false;
            }

        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scooped>

    /* Original Styles */
    body {background-color: rgb(240, 240, 240);}


</style>


