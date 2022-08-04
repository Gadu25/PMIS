<template>
    <div v-if="token == 0">
        <router-view></router-view>
    </div>
    <div v-else >
        <div v-if="!loading" class="app-container">
            <div class="sidebar" :class="toggle ? 'hide' : ''">
                <div class="sidebar-header">
                    <router-link :to="{ name: 'Dashboard' }" class="sidebar-link">
                        <div class="d-flex justify-content-between">
                            <img src="/images/Logo.png" alt="logo" width="60">
                            <span>Project Management Info System</span>
                        </div>
                    </router-link>
                </div>
                <div class="sidebar-body">
                    <router-link :to="{ name: 'Dashboard' }">Dashboard</router-link>
                    <router-link :to="{ name: 'Divisions' }">Divisions and Units</router-link>
                    <router-link :to="{ name: 'Programs' }">Programs and Projects</router-link>
                    <router-link :to="{ name: 'Workshops' }">Budget Executive Documents</router-link>
                    <a href="/login" @click="logout">Logout</a>
                </div>
            </div>
            <div class="body">
                <div class="topbar">
                    <div class=""><button class="btn btn-sm btn-outline-primary" @click="toggle = !toggle"><i class="fas fa-bars"></i></button></div>
                    <div style="min-width: 50vw; padding: 0px 20px"><input type="search" class="form-control form-control-sm" placeholder="Search"></div>
                    <div class="text-white p-1">
                        <span><i class="far fa-user-circle fa-2x"></i></span>
                    </div>
                </div>
                <div class="content">
                    <router-view></router-view>
                </div>
            </div>
        </div>
        
    </div>
</template>
<script>
import { mapActions, mapGetters } from 'vuex'
export default {
    name: 'App',
    data(){
        return {
            token: localStorage.getItem('token') || 0,
            toggle: false,
            loading: true
        }
    },
    methods: {
        ...mapActions('user', ['logout', 'fetchAuthUser']),
        getAuthenticatedUser(){
            if(this.token != 0){
                var authToken = JSON.parse(localStorage.getItem('token'))
                var authExpiry = authToken.expiry
                const now = new Date()
                const currTime = now.getTime()
                if(currTime > authExpiry){
                    this.logout()
                }
                axios.defaults.headers.common['Authorization'] = 'Bearer ' + authToken.value
                
                this.fetchAuthUser().then(res => {
                    this.loading = false
                })
            }
        },
    },
    computed: {
        ...mapGetters('user', ['getAuthUser'])
    },
    created(){
        this.getAuthenticatedUser()
    }
}
</script>
<style scoped>
.app-container{
    background: linear-gradient(to bottom right, hsl(215, 100%, 60%) 0%, hsl(215, 100%, 80%) 100%);
    display: flex;
}
.body{
    width: 100%;
    transition: all 0.5s;
}
.sidebar{
    height: 100vh;
    width: 350px;
    background: rgba(0, 0, 0, 0.6);
    color: white;
    transition: all 0.5s;
    position: relative;
    left: 0;
}
.sidebar.hide{
    width: 0px;
    opacity: 0;
    position: fixed;
    left: -500px;
}
.sidebar-header{
    width: 100%;
    background: rgba(0, 0, 0, 0.8);
    padding: 14px;
    text-align: center;
    cursor: pointer;
}
.sidebar-link{
    text-decoration: none;
    color: white;
}
.sidebar-link>div>span{
    font-size: 20px;
}
.sidebar-body{
    height: 80vh;
    overflow: auto;
}
.sidebar-body>a{
    padding: 0.5rem;
    text-decoration: none;
    position: relative;
    display: block;
    color: rgb(211, 211, 211);
    background-image: linear-gradient(rgba(0, 0, 0, 0.3) , rgba(0, 0, 0, 0.3));
    background-position: 50% 50%;
    background-repeat: no-repeat;
    background-size: 0% 100%;
    transition: background-size .3s, color .3s;
}
.sidebar-body>a.active {
    background: linear-gradient(to left, #3399ff -19%, #66ffcc 114%);
    color: black;
}
.sidebar-body>a:hover:not(.active, .header) {
    background-size: 100% 100%;
    color: #fff;
}
.sidebar-body>a.router-link-exact-active{
    background-color: rgba(0, 0, 0, 0.3);
    color: #fff;
}
.sidebar-body>a::before{
    font-family: "Font Awesome 5 Pro";
    font-weight: 600;
    width:30px;
    display: inline-block;
    text-align: center;
}
.topbar{
    padding: 5px;
    height: 5vh;
    background: rgba(0, 0, 0, 0.8);
    display: flex;
    justify-content: space-between;
}
.content{
    background: white;
    height: 95vh;
    overflow: auto;
}
</style>
<style>
input[type="date"]::-webkit-calendar-picker-indicator {
    background: transparent;
    bottom: 0;
    color: transparent;
    cursor: pointer;
    height: auto;
    left: 0;
    position: absolute;
    right: 0;
    top: 0;
    width: 100%;
}
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}
  
/* Track */
::-webkit-scrollbar-track {
    background: #f1f1f1; 
}
   
/* Handle */
::-webkit-scrollbar-thumb {
    background: #888; 
}
  
/* Handle on hover */
::-webkit-scrollbar-thumb:hover {
    background: #555; 
}

</style>