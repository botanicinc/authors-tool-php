<template>
    <div>

        <div :id="`modal${_uid}`" class="modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">                        
                        <h5 v-show="!frm.id" class="modal-title">New Flow</h5>
                        <h5 v-show="frm.id" class="modal-title">Edit Flow</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <br>
                    <div class="modal-body">
                        <div class="form-group form-inline">
                            <label for="name">Name</label>
                            <input class="form-control" v-model="frm.name" id="name" placeholder="Intro Flow" autofocus>
                        </div>  
                    </div>
                    <br>
                    <div class="modal-footer">
                        <div>
                            <button type="button" class="btn btn-secondary mr-btn" data-dismiss="modal">Cancel</button>
                            <button v-show="frm.id && !isRootFlow" type="button" class="btn btn-delete" data-dismiss="modal" v-on:click="showDeleteFlowModal">Delete</button>
                        </div>
                        <button type="button" class="btn btn-botanic" v-on:click="save">Save</button>
                    </div>
                </div>
            </div>
        </div>

    </div>

</template>

<script>
    /**
     * Flow CRUD modal 
     * 
     */

    import Bootstrap4Modal from './Bootstrap4Modal.vue';
    import { EventBus } from '../eventBus.js';

    export default {
        name: 'AddEditFlowModal',
        extends: Bootstrap4Modal,
        data() {
            return {
                isRootFlow: null,
                frm: {
                    name: ''
                }
            };
        },
        methods: {
            /**
             * Launch the modal
             * 
             * @param {Object} frm Object with form data
             * @param {Bool} isRootFlow TRUE if the flow is the root flow, FALSE otherwise
             * @returns {Promise} Promise which is going to be resolved on updating/saving or rejected on cancel
             */
            show(frm, isRootFlow) {
                
                var ret = this.showModal(frm);
                
                this.isRootFlow = isRootFlow;
                
                return ret;
                
            },
            
            /**
             * Resolve the modal promise with updated form data
             * 
             * @returns {Promise} resolved promise with updated data
             */
            save() {
                return this.actionModal(this.frm);
            },
            
            /**
             * Launch delete flow modal
             * 
             */
            showDeleteFlowModal() {
                EventBus.$emit('showDeleteFlowModal', {flow: this.frm});
                this.hideModal();                                
            }
        }

    }

</script>
