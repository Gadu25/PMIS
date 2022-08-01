<template>
    <div v-if="token == 0">
        <router-view></router-view>
    </div>
    <div v-else class="app-container">
        <div class="sidebar" :class="toggle ? 'hide' : ''">
            <div class="sidebar-header">
                <router-link :to="{ name: 'Dashboard' }" class="sidebar-link">
                    <div class="d-flex justify-content-between">
                        <img src="/images/Logo.png" alt="logo" width="50">
                        <span>Project Management Info System</span>
                    </div>
                </router-link>
            </div>
            <div class="sidebar-body">
                <router-link :to="{ name: 'Dashboard' }">Dashboard</router-link>
                <router-link :to="{ name: 'Divisions' }">Divisions and Units</router-link>
            </div>
        </div>
        <div class="body">
            <div class="topbar">
                <button class="btn btn-outline-primary" @click="toggle = !toggle"><i class="fas fa-bars"></i></button>{{toggle}}
            </div>
            <div class="content">
                <router-view></router-view>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'App',
    data(){
        return {
            token: localStorage.getItem('token') || 0,
            toggle: false
        }
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
    height: 8vh;
    background: rgba(0, 0, 0, 0.8);
    color: white;
}
.content{
    background: white;
    height: 92vh;
    overflow: auto;
}
</style>