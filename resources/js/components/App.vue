<template>
    <div v-if="token == 0">
        <router-view></router-view>
    </div>
    <div v-else >
        <div v-if="!loading" class="app-container">
            <div class="sidebar" :class="toggle ? 'hide' : ''">
                <div class="sidebar-header">
                    <router-link :to="{ name: 'Dashboard' }" class="sidebar-link">
                        <div class="d-flex flex-col">
                            <div>
                            <img :src="'/images/Logo.png'" alt="logo" width="50">
                            </div>
                            <div class="w-100 text-center ms-3">
                                <span style="" id="pmis">PMIS</span>
                            </div>
                        </div>
                        
                    </router-link>
                    <!-- <button id="toggleBtn" @click="toggle = !toggle" ><i class="fas fa-bars"></i></button> -->
                </div>
                <div class="sidebar-body">
                    <router-link active-class="active glow" class="px-4 py-3" :to="{ name: 'Dashboard' }"><i class="far fa-home"></i>&nbsp;&nbsp;Dashboard</router-link>
                    <template v-for="val, key in auth.active_profile.groupedroles" :key="key">
                        <router-link active-class="active glow" class="px-4 py-3" :to="{ name: setRouteName(key)}"><i class="far fa-circle "></i>&nbsp;&nbsp;{{key}}</router-link>
                    </template>
                    <router-link active-class="active glow" class="px-4 py-3" :to="{ name: 'About' }"><i class="far fa-circle "></i>&nbsp;&nbsp;About System</router-link>
                    <!-- <router-link active-class="active" :to="{ name: 'Programs' }">Programs and Projects</router-link>
                    <router-link active-class="active" :to="{ name: 'Workshops' }">Budget Executive Documents</router-link>
                    <router-link active-class="active" :to="{ name: 'Users' }">User Management</router-link> -->
                    <a href="/login" class="px-4 py-3" @click="logout"><i class="far fa-arrow-left"></i>&nbsp;&nbsp;Logout</a>
                </div>
            </div>
            <div class="body" :style="!toggle ? 'width: calc(100vw - 280px)' : 'width: 100vw'">
                <div class="topbar">
                    <div><button class="btn btn-sm" @click="toggle = !toggle"><i v-bind:class="[toggle ? 'fas fa-bars fa-2x' : 'fas fa-arrow-left fa-2x']" v-bind:style="{'color': '#3CAEA3'}"></i></button></div>
                    <!-- <div style="min-width: 50vw; padding: 0px 20px"><input type="search" class="form-control form-control-sm" placeholder="Search"></div> -->
                    <div class="p-1 position-relative">
                        <router-link class="text-decoration-none d-flex align-items-center" v-bind:style="{'color': '#F5F5F5'}" :to="{ name: 'Profile' }"><strong>{{ auth.firstname + " " + auth.lastname }}</strong> &nbsp; &nbsp; <i class="far fa-user-circle fa-2x" v-bind:style="{'color': '#3CAEA3'}"></i> </router-link>
                        <span v-if="auth.active_profile.unread > 0" class="red-dot"></span>
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
            loading: true,
            width: document.documentElement.clientWidth,
            height: document.documentElement.clientHeight
        }
    },
    mounted() {
        window.addEventListener('resize', this.getDimensions);
    },
    unmounted() {
        window.removeEventListener('resize', this.getDimensions);
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
        getDimensions() {
            this.width = document.documentElement.clientWidth;
            this.height = document.documentElement.clientHeight;
            if(this.width < 960){
                this.toggle = true;
            } else {
                this.toggle = false;
            }
        },
        setRouteName(name){
            // Divisions and Units
            // Programs and Projects
            // Budget Executive Documents
            // User Management
            if(name == 'Divisions and Units'){
                return 'Divisions'
            }
            if(name == 'Programs and Projects'){
                return 'Programs'
            }
            if(name == 'Events Management'){
                return 'Events'
            }
            if(name == 'Budget Executive Documents'){
                return 'Workshops'
            }
            if(name == 'Resources and Publications'){
                return 'Publications'
            }
            if(name == 'Reports'){
                return 'Reports'
            }
            if(name == 'Strategic Planning'){
                return 'StrategicPlanning'
            }
            if(name == 'User Management'){
                return 'Users'
            }
            return name
        }
    },
    computed: {
        ...mapGetters('user', ['getAuthUser']),
        auth(){ return this.getAuthUser }
    },
    created(){
        this.getAuthenticatedUser()
    },
    watch: {
        $route: {
            immediate: true,
            handler(to, from) {
                document.title = 'PMIS | ' + this.$route.meta.title;
            }
        },
    },
}
</script>
<style scoped>
.glow { 
    text-shadow: 0 0 5px rgba(81, 203, 238, 1);
}
.app-container{
    background: linear-gradient(to bottom right, hsl(215, 100%, 60%) 0%, hsl(215, 100%, 80%) 100%);
    /* background: #ffffff; */
    display: flex;
}
.sidebar{
    height: 100vh;
    width: 280px;
    background: #20639B;
    color: white;
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
    height: 130px;
    background: #173F5F;
    padding-left: 14px;
    padding-right: 14px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}
.sidebar-header>#toggleBtn{
    position: fixed;
    top: -200px;
    background-color: #F6D55C;
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
    color: white;
    background-image: linear-gradient(rgba(0, 0, 0, 0.3) , rgba(0, 0, 0, 0.3));
    background-position: 50% 50%;
    background-repeat: no-repeat;
    background-size: 0% 100%;
    /* transition: background-size .3s, color .3s; */
}
/* .sidebar-body>a.active {
    background: linear-gradient(to left, #3399ff -19%, #66ffcc 114%);
    color: black;
} */
.sidebar-body>a:hover:not(.active, .header) {
    background-size: 100% 100%;
    color: #fff;
}
.sidebar-body>a.active,
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
    padding-left: 15px;
    padding-right: 15px;
    height: 60px;
    background: #173F5F;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.red-dot{
    width: 11px;
    height: 11px;
    display: block;
    border-radius: 50%;
    background: red;
    position: absolute;
    bottom: 0;
    right: 0;
}
.content{
    background: white;
    height: calc(100vh - 60px);
    overflow: auto;
    overflow-x: hidden;
}
@media only screen and (max-width: 600px) {
    .body{
        width: 100vw !important;
    }
    .sidebar{
        position: fixed;
        background: linear-gradient(to bottom right, hsl(215, 100%, 60%) 0%, hsl(215, 100%, 80%) 100%);
        z-index: 999;
        width: 300px;
    }
    .sidebar-header{
        position: relative;
    }
    .sidebar-header>#toggleBtn{
        position: absolute;
        top: 10px;
        right: -35px;
    }
    .sidebar-header>a>div{
        display: flex;
    }
    .sidebar-body{
        background-color: rgba(0, 0, 0, 0.6);
        height: 100%;
    }
    .sidebar-body>a.router-link-exact-active{
        background-color: rgba(0, 0, 0, 0.8);
        color: #fff;
    }
}
</style>
<style>
.btn-xs{
    height: 23px !important;
    padding: 0px 7px;
    font-size: .875rem;
    border-radius: 0.2rem;
}

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
#pmis{
    font-size: 40px;
    font-weight: bold;
    color: #F6D55C;
}
</style>