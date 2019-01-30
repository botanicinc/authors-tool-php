<template>
    <div @click="focusNewTag()" v-bind:class="{'read-only': readOnly}">
        <div class="select-options">
            <span v-for="(tag, index) in tags" v-bind:key="index" class="setting-tag">
                <span class="text"><pre>{{tag}}</pre></span>
                <span v-if="!readOnly" @click.prevent.stop="edit(index)" class="cog-edit"></span>
                <span v-if="!readOnly" @click.prevent.stop="remove(index)" data-role="settings" class="cog-delete"></span>
            </span>
        </div>
        <div class="content-keywords">
            <a href="javascript:;" class="btn ico-vb" v-bind:class="{'disabled': !newTag}" v-on:click="addNew()"></a>
            <textarea v-if="type == 'textarea'" class="form-control new-tag" id="keywords-textarea" rows="3" :readonly="readOnly" v-bind:placeholder="placeholder" type="text" v-model="newTag" v-on:keyup="keyup"></textarea>
            <input v-if="type == 'input'" class="form-control new-tag" id="keywords" :readonly="readOnly" v-bind:placeholder="placeholder" type="text" v-model="newTag" v-on:keyup="keyup" />
        </div> 
    </div>

</template>

<script>
     /**
      * Input tag component (github matiastucci/vue-input-tag fork)
      * This fork uses bootstrap 4 and has several customizations for Author's Tool
      *       
      */
    
    import Vue from 'vue';
    import { EventBus } from '../eventBus.js';

    /*eslint-disable*/
    const validators = {
        email: new RegExp(/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/),
        url: new RegExp(/^(https?|ftp|rmtp|mms):\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)(:(\d+))?\/?/i),
        text: new RegExp(/^[a-zA-Z]+$/),
        digits: new RegExp(/^[\d() \.\:\-\+#]+$/),
        isodate: new RegExp(/^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])$/)
    };
    /*eslint-enable*/

    export default {
        name: 'InputTag',

        props: {
            tags: {
                type: Array,
                default: () => []
            },
            placeholder: {
                type: String,
                default: ''
            },
            readOnly: {
                type: Boolean,
                default: false
            },
            validate: {
                type: String,
                default: ''
            },
            type: {
                type: String,
                default: 'textarea'
            }
        },

        data() {
            return {
                newTag: ''
            };
        },

        methods: {
            reset() {
                Object.assign(this.$data, this.$options.data.call(this));
                Object.assign(this.$data, this.$options.propsData);

            },

            focusNewTag() {
                if (this.readOnly) {
                    return;
                }
                this.$el.querySelector('.new-tag').focus();
            },

            addNew(tag) {
                if (typeof (tag) == 'undefined') {
                    tag = this.newTag;
                }

                tag = tag.replace(/^\s+|\s+$/g, '');

                if (tag.length && this.tags.indexOf(tag) === -1 && this.validateIfNeeded(tag)) {
                    this.tags.push(tag);
                    this.tagChange();
                }
                this.newTag = '';
            },

            validateIfNeeded(tagValue) {
                if (this.validate === '' || this.validate === undefined) {
                    return true;
                } else if (Object.keys(validators).indexOf(this.validate) > -1) {
                    return validators[this.validate].test(tagValue);
                }
                return true;
            },

            remove(index) {
                this.tags.splice(index, 1);
                this.tagChange();
            },

            removeLastTag() {
                if (this.newTag) {
                    return;
                }
                this.tags.pop();
                this.tagChange();
            },

            tagChange() {
                EventBus.$emit('inputTagChange', {newTag: this.newTag});
            },

            edit(index) {
                //first check if newTag has text and add it again to the tags
                if (this.newTag !== '') {
                    this.addNew();
                }

                this.newTag = this.tags[index];
                this.remove(index);
                this.focusNewTag();

            },

            keyup() {
                EventBus.$emit('inputTagKeyup', {newTag: this.newTag});
            }

        }
    }


    /*
     MIT License
     
     Copyright (c) 2016 Matias Tucci
     
     Permission is hereby granted, free of charge, to any person obtaining a copy of 
     this software and associated documentation files (the "Software"), to deal in the 
     Software without restriction, including without limitation the rights to use, copy, 
     modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, 
     and to permit persons to whom the Software is furnished to do so, subject to the 
     following conditions:
     
     The above copyright notice and this permission notice shall be included in all 
     copies or substantial portions of the Software.
     
     THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR 
     IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, 
     FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE 
     AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER 
     LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, 
     OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
     SOFTWARE.
     */
</script>

<style>

</style>
