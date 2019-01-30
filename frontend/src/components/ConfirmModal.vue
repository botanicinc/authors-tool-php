<template>

    <div :id="`modal${_uid}`" class="modal show modalPop" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{title}}</h5>
                    <a href="javascript:;" class="close"><span aria-hidden="true">&times;</span></a>
                </div>
                <div class="modal-body">        
                    <p>{{text}}</p>
                    <button type="button" class="btn btn-secondary mr-btn" data-dismiss="modal">Cancel</button>

                    <button type="button" class="btn btn-delete float-right" v-on:click="confirmed">{{confirmButton}}</button>

                </div>
            </div>
        </div>
    </div>


</template>

<script>
    /**
     * Confirm modal
     * 
     */

    import Bootstrap4Modal from './Bootstrap4Modal.vue';

    export default {
        name: 'ConfirmModal',
        extends: Bootstrap4Modal,
        props: ['title', 'text', 'confirmButton'],
        data() {
            return {
                title: "Confirm?",
                text: "Are you sure?",
                confirmButton: "Accept"
            };
        },

        methods: {
            /**
             * Launches modal
             * 
             * @param {Object} obj
             * @returns {Promise}
             */
            confirm(obj) {
                return this.showModal(obj);
            },

            /**
             * Accepts confirmation
             * 
             * @returns {Promise}
             */
            confirmed() {
                return this.actionModal();
            }
        }
    }

</script>

