<template>

    <form class="">
        <div class="bl-top">
            <p>{{label}}</p>
            <div class="form-group has-success">
                <input-tag ref="inputTag" :tags="frm.msg"></input-tag>
            </div>
            <div class="form-group form-inline">
                <label for="nodename">Node Name</label>
                <input class="form-control" placeholder="Optional" v-model="frm.name" id="nodename">
            </div>
            <div class="form-group form-inline">
                <label for="rememberuser">Remember User Input?</label>
                <div class="radio-type">
                    <div class="radio-botanic">
                        <input type="radio" name="rememberuser" id="n" value="N" v-model="remember">
                        <label for="n"><div class="label-text">N</div></label>
                    </div>
                    <div class="radio-botanic">
                        <input type="radio" name="rememberuser" id="y" value="Y" v-model="remember" >
                        <label for="y"><div class="label-text">Y</div></label>
                    </div>
                </div> 
            </div>                                     
        </div>    
        <div v-show="remember == 'Y'" class="bl-bottom">
            <div class="form-group form-inline">
                <label for="name">Saved Input Request</label>
                <div class="button-group">
                    <input class="form-control w330" v-model="frm.fieldId">
                </div>
                <div class="icTool">                                        
                    <a href="#" data-toggle="tooltip" class="question-circle" @click="savedInputRequestTooltip = !savedInputRequestTooltip"></a>
                    <div class="blTooltip" v-bind:class="{'active': savedInputRequestTooltip}">
                        <div class="btclose" @click="savedInputRequestTooltip = false">x</div>
                        <div class="arrow1"></div>
                        <div class="arrow"></div>
                        <div class="content">                            
                            <p>To save a user's answer to a Bot Output question, create an Input Request.</p>
                            <p>The user's next input will be saved in the system for future use under this name.</p>
                        </div>
                    </div>
                </div>
            </div>
            <hr>  
            <div class="form-group form-inline">
                <label for="name">Saved Form</label>
                <div class="button-group">
                    <input class="form-control w380" placeholder="Create New Form" v-model="frm.formId">
                </div>
                <div class="icTool">                                        
                    <a href="#" data-toggle="tooltip" class="question-circle" @click="savedFormTooltip = !savedFormTooltip"></a>
                    <div class="blTooltip" v-bind:class="{'active': savedFormTooltip}">
                        <div class="btclose" @click="savedFormTooltip = false">x</div>
                        <div class="arrow1"></div>
                        <div class="arrow"></div>
                        <div class="content">
                            <p>To save multiple user inputs, create a Form.</p>
                            <p>Each User Input Request associated with the Form will be saved together and available in the system.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group form-inline list existing-form">
                <p class="sub-label">or</p>
                <div class="button-group">
                    <button type="button" id="dropbtn" class="btn btn-sm dropdown-toggle form-control" data-toggle="dropdown"><span class="select-channel-txt">Select Existing Form</span></button>
                    <ul class="dropdown-menu">
                        <li v-for="f in forms" v-on:click="frm.formId = f"><a href="#" class="small" :data-value="f" tabIndex="-1" >{{f}}</a></li>
                    </ul>
                </div>
            </div>                                 
        </div>                            
    </form>
</template>

<script>
    /**
     * User input component
     * 
     */

    import InputTag from './InputTag.vue';

    export default {
        name: 'TextInput',
        components: {
            'input-tag': InputTag
        },
        props: ['forms', 'label', 'frm'],
        data() {
            return {
                savedInputRequestTooltip: false,
                savedFormTooltip: false,
                remember: ''
            };
        },

        mounted() {
            if (this.frm.fieldId) {
                this.remember = 'Y';
            } else {
                this.remember = 'N';
            }
        }
    }

</script>
