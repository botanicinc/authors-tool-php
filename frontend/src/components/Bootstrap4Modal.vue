
<script>
    //vuejs can't do html inheritance (and I won't use Pug!). Define full template on each parent
    
    /**
     * Boostrap modal vuejs wraper
     * 
     */
    
    import Vue from 'vue';

    //makes all modals to autofocus input when launched
    $(document).ready(function () {
        $(document).on('shown.bs.modal', '.modal', function () {
            $(this).find('[autofocus]').focus();
        });
    });


    export default {
        name: 'Bootstrap4Modal',
        mounted() {

            //rejects promise on modal hide (pressing ESC, clicking Cancel or background)
            $(`#modal${this._uid}`).on('hidden.bs.modal', (e) => {
                if (!this.finished) {//if it's closing but caused by a resolving, do not try to reject it
                    this.reject();
                }
            });
        },

        methods: {
            /**
             * Launches modal
             * 
             * @param {Object} frm Form data
             * @returns {Promise} Modal promise
             */
            showModal(frm) {

                if (!$(`#modal${this._uid}`).length) {
                    console.error('Bootstrap4Modal: Modal uid missing. Add this id attr in the top level modal div elment: :id="`modal${_uid}`"');
                    return;
                }
                //intializes data
                this.resetForm(frm);

                $(`#modal${this._uid}`).modal();

                this.promise = new Promise((resolve, reject) => {

                    this.resolve = resolve;
                    this.reject = reject;
                });

                this.finished = false;

                this.promise.then(() => {
                    this.finished = true;
                    $(`#modal${this._uid}`).modal('hide');

                })
                        .catch(() => {
                            this.finished = true;
                            $(`#modal${this._uid}`).modal('hide');
                        });

                return this.promise;

            },

            /**
             * Closes modal
             * 
             */
            hideModal() {
                this.reject();
            },

            /**
             * Resolve modal with new data
             * 
             * @param {Object} val Form data
             * @returns {Promise}
             */
            actionModal(val) {
                if (val) {
                    var ret = _.cloneDeep(val);
                } else {
                    var ret = _.cloneDeep(this.$data);
                }

                this.resolve(ret);

            },
            
            /**
             * Resets form
             * 
             * @param {Object} frm Form data
             */
            resetForm(frm) {

                //intializes data with default values from child
                _.assign(this.$data, this.$options.data());

                //assign data with values from props (as it's duplicated with data() we need to do this explicitly)
                _.merge(this.$data, this.$options.propsData);

                //assign data with values assigned programatically to the component show method               
                if (this.$data.frm && frm) {
                    _.merge(this.$data.frm, frm);
                }
            }

        }
    }

</script>


