<template>
    <div id="app">
        <router-view/>
    </div>
</template>

<script>
    /**
     * Root component
     * 
     */
    export default {
        name: 'Bot Author\'s Tool',
        created() {
            //this will check updates every 5 minutes using the axios interceptor defined in main.js
           
            if (process.env.NODE_ENV != 'development') {//check versions with ping only on production
                window.setInterval(() => {
                    this.$http.get('/api/system/ping', {});
                }, 1000 * 60 * 5);
            }
        }
    }
</script>

<style lang="scss">
    
 
</style>