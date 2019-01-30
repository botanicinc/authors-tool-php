<template>
    <div>
        <div class="modal" id="myLoginModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="navbar-brand col-8 logo-cointaner"><img src="../assets/images/logo.png" alt="Botanic" class="logo"><span class="author">Author</span></div>
                    </div>
                    <div class="modal-body">
                        <form class="form-horizontal" role="form">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group has-danger">
                                        <div class="input-group mb-2 mr-sm-2 mb-sm-0">
                                            <input type="text" v-model="username" class="form-control" placeholder="User Name" required autofocus>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group last">
                                        <div class="input-group mb-2 mr-sm-2 mb-sm-0">
                                            <input type="password"  v-model="password" class="form-control" placeholder="Password" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12 error-msg">
                                    <div v-show="errorMessage"><i class="fa fa-exclamation-circle">&nbsp;</i>{{errorMessage}}</div>
                                </div>
                                
                                <div class="col-md-12" align="center">
                                    <button type="submit" class="btn btn-botanic" v-bind:class="{ 'btn-inactive': !username || !password }" v-on:click.prevent="login">Sign In</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    /**
     * Login page
     * 
     */
    export default {
        name: 'Login',
        data() {
            return {
                username: '',
                password: '',
                errorMessage: ''
            };
        },
        mounted: function () {//signup box is using modal classes, so I'm forced to include this...(I know...these html designers nowadays..)
            $(document).ready(function () {
                $('#myLoginModal').modal({backdrop: 'static', keyboard: false, show: true});
            });
        },
        methods: {
            /**
             * Login
             * 
             */
            login() {

                this.$http.get('/api/system/login',
                        {
                            params: {
                                username: this.username,
                                password: this.password
                            }
                        })
                        .then(r => {

                            localStorage.setItem('token', r.data.token);
                            localStorage.setItem('userName', this.username);

                            $('#myLoginModal').modal('hide');//more uglyness

                            this.$router.push({name: 'ProjectsList'});


                        })
                        .catch(e => {
                            console.log(e);
                            if (e.response) {
                                if (e.response.status == 401) {
                                    this.errorMessage = 'Invalid credentials';
                                } else {
                                    this.errorMessage = 'System error';
                                }
                            }
                        });


            }
        }
    }
</script>
