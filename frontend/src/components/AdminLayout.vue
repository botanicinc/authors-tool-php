<template>
    <!-- APP LAYOUT -->

    <div id="project-list-container">
        <nav class="navbar navbar-expand-md navbar-dark fixed-top nav-project" :style="`background-color: ${headerBackgroundColor} ;`">
            <div class="navitems">

                <router-link class="navbar-brand col-8 logo-cointaner" :to="{ name: 'ProjectsList'}"><img src="../assets/images/logo.png" alt="Botanic" class="logo"><span class="author">Author {{headerBotanicEnvDisplay}}</span></router-link>

                <div class="icons col-4">

                    <div class="user-company">
                        <div class="user-txt">{{userName}}</div>
                    </div>

                    <div class="user-icon">
                        <div class="collapse navbar-collapse" id="navbarCollapse">     
                            <ul class="navbar-nav ml-auto">                    
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle user" id="navbarDropdownMenuLink2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <div class="user-pic"></div>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink2">
                                        <a class="dropdown-item" v-on:click="signOut">Sign Out</a>
                                    </div>
                                </li> 
                            </ul>     
                        </div>
                    </div>
                    
                    <router-link class="help-icon"  :to="{ name: 'QuickTips'}">
                        <a class="help-link" ><div class="help-pic"></div></a>
                    </router-link>
                </div>
            </div>
        </nav>

        <main role="main" class="container">
            <div class="mx-auto">

                <router-view></router-view>

            </div>
        </main>
    </div>


</template>

<script>
    /**
     * App layout
     * 
     */

    export default {
        name: 'AdminLayout',
        data() {
            return {
                headerBackgroundColor: '',
                headerBotanicEnvDisplay: '',
                userName: localStorage.getItem('userName')
            };
        },
        mounted() {
            if (BOTANIC_ENV == 'production') {//production will inherit background color from the css file
                this.headerBackgroundColor = '';
                this.headerBotanicEnvDisplay = '';

            } else if (BOTANIC_ENV == 'stage') {//stage server for beta testing
                this.headerBackgroundColor = 'darkgreen';
                this.headerBotanicEnvDisplay = 'INTERNAL TEST';
            } else {
                this.headerBackgroundColor = 'grey';
                this.headerBotanicEnvDisplay = 'LOCAL DEV';
            }
        },

        methods: {
            signOut() {
                localStorage.removeItem('token');
                localStorage.removeItem('userName');
                this.$router.replace({name: 'Login'});
            }
        }

    }

</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style>

    .btn {
        cursor: pointer;
    }
    /* Show it is fixed to the top */
    body {
        /*   min-height: 75rem;*/
        padding-top: 4.5rem;

        background-color: rgb(240,240,240)
    }


</style>