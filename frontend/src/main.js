/**
 * Bootstrap file
 */

// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue';
import App from './App';
import router from './router';
import axios from 'axios';
import lodash from 'lodash';
import 'vue-super';
import 'bootstrap';
import 'bootstrap/dist/css/bootstrap.css';
import './assets/css/custom.css';
import './assets/css/workspace.css';
import './assets/css/quicktips.css';
import 'font-awesome/css/font-awesome.css';

Vue.config.productionTip = false;
Vue.config.silent = true;

axios.defaults.headers.common['X-BAT-Version'] = VERSION; //sends header with version to backend

axios.interceptors.request.use((request) => {
    //injects token to every request except login
    if (!request.url.includes('/api/system/login')) {
        var token = localStorage.getItem('token');
        if (token) {
            request.params = {token: token};
        }
    }
  
    return request;
});


axios.interceptors.response.use((response) => {
    return response;

}, (error) => {
    
    //checks if it's logged
    if (error.response.status == 401) {
        router.replace({name: 'Login'});
    }
    
    //checks version issue with backend. will reload page if needed
    if (process.env.NODE_ENV != 'development' && error.response.data.message == 'CLIENT_VERSION_MISMATCH') {
        alert('There is a new Author\'s Tool version. Will update now.');
        location.reload();
        throw new axios.Cancel('Operation canceled by frontend.');
    }
        
    return Promise.reject(error);
});
Vue.prototype.$http = axios;

//enable lodash inside templates
Vue.prototype._ = _;

//enable jquery inside templates
Vue.prototype.$ = $;

//set environment stuff
Vue.prototype.NODE_ENV = process.env.NODE_ENV;

/* eslint-disable no-new */
new Vue({
    el: '#app',
    router,
    template: '<App/>',
    components: {App}
});
