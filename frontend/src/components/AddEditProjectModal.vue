<template>

    <div :id="`modal${_uid}`" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 v-show="frm.id" class="modal-title">Project Settings</h5>
                    <h5 v-show="!frm.id" class="modal-title">New Project</h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form>
                        
                        <div class="form-group form-inline">
                            <label for="name">Name</label>
                            <input class="form-control" v-model="frm.name" autofocus>
                        </div>  
                        
                    </form>
                </div>
                <div class="modal-footer">
                    <div>
                        <button type="button" class="btn btn-secondary mr-btn" data-dismiss="modal">Cancel</button>
                        <button v-show="frm.id" type="button" class="btn btn-delete" v-on:click="showDeleteProjectModal(frm.id)">Delete</button>
                    </div>
                    <button type="button" class="btn btn-botanic" v-on:click="save">Save</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    /**
     * Project CRUD modal
     */

    import Bootstrap4Modal from './Bootstrap4Modal.vue';
    import { EventBus } from '../eventBus.js';
    import Vue from 'vue';

    export default {
        name: 'AddEditProjectModal',
        extends: Bootstrap4Modal,
        data() {

            return {

                frm: {
                    id: 0,
                    name: '',
                    status: 'enabled',
                    modes: [],
                    channels: []
                }

            };
        },

        methods: {
            /**
             * Launch the modal
             * 
             * @param {Object} frm Object with form data             
             * @returns {Promise} Promise which is going to be resolved on updating/saving or rejected on cancel
             */
            show(frm) {
                return this.showModal(frm);
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
             * Launch delete project modal
             * 
             * @param {Int} projectId Project ID
             */
            showDeleteProjectModal(projectId) {
                EventBus.$emit('showDeleteProjectModal', {projectId: projectId});
                this.hideModal();
            }

        }
    }
</script>

