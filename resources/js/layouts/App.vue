<template>
    <div>
        <div>
            <router-link :to="{name: 'Home'}">Home {{ $store.getters.getToken }} </router-link>
            <router-link :to="{name: 'Dashboard'}">Dashboard</router-link>
            <router-link :to="{name: 'Login'}">Login</router-link>
            <router-link :to="{name: 'Register'}">Register</router-link>
        </div>

        <router-view></router-view>
    </div>
</template>
<script>
// import { defineComponent } from 'vue'
// export default defineComponent({
    
// })
</script>