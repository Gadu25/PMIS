<template>
    <div class="profile-container">
        <div class="gen-info">
            <button class="btn-settings"><i class="far fa-cog"></i> Settings</button>
            <h4>{{authuser.firstname+' '+authuser.lastname}}, <small></small></h4>
            <small><i class="far fa-user-circle"></i> {{authuser.active_profile.title.name}}</small><br>
            <small><i class="far fa-building"></i> <strong>{{authuser.division.name}}</strong> <span v-if="authuser.unit_id">, {{authuser.unit.name}}</span> <span v-if="authuser.sybunit_id"> - {{authuser.subunit.name}}</span> </small>
            <br><small><i class="far fa-envelope"></i> <i>{{authuser.email}}</i></small>
        </div>
        <div class="details">
            <div class="main-container">
                <div class="card">
                    <div class="card-body">
                        User Activities. Coming soon...
                    </div>
                </div>
            </div>
            <div class="side-container">
                <div class="notification-container" :class="shownotification ? 'show' : ''">
                    <div class="title"><span><i class="far fa-bell"></i> Notifications</span> <i class="far fa-times" @click="shownotification = false"></i></div>
                    <div class="notification" @click="gotoNotificationLink(notification)" :class="!notification.is_read ? 'unread' : ''" v-for="notification in authuser.active_profile.notifications" :key="'notification'+notification.id">
                        <div class="title"><span v-html="notification.title"></span></div>
                        <div class="body" v-html="notification.body"></div>
                        <div class="footer">
                            <div class="user-from"><small>By: {{notification.from.user.firstname + ' ' + notification.from.user.lastname}}</small></div>
                            <div class="date-created"><small>{{notification.created_at}}</small></div>
                        </div>
                    </div>
                </div>
                <button class="notification-btn" @click="shownotification = true">
                    <i class="far fa-bell"></i>
                    <div class="notification-count" v-if="authuser.active_profile.unread > 0"><span>{{authuser.active_profile.unread < 9 ? authuser.active_profile.unread : '9+'}}</span></div>    
                </button>
            </div>
        </div>
    </div>
</template>
<script>
import { mapActions, mapGetters } from 'vuex';
export default {
    name: 'Profile',
    data(){
        return {
            shownotification: false
        }
    },
    methods: {
        ...mapActions('user', ['fetchAuthUser', 'updateNotification']),
        gotoNotificationLink(notification){
            this.updateNotification(notification.id).then(res => {
                this.fetchAuthUser().then(res => {
                    this.$router.push(notification.link)
                })
            })
        }
    },
    computed: {
        ...mapGetters('user', ['getAuthUser']),
        authuser(){ return this.getAuthUser }
    }
}
</script>
<style>
    .profile-container{
        padding: 10px 20px;
        height: calc(100vh - 45px);
        overflow: auto;
    }
    .gen-info{
        padding: 10px;
        border-bottom: 1px solid;
        position: relative;
    }
    .gen-info>h4{
        margin-bottom: 0px;
    }
    .gen-info>.btn-settings{
        border: 1px solid rgba(0,0,0,1);
        border-radius: 0.25em;
        position: absolute;
        right: 0;
        box-shadow: 2px 2px rgb(107, 107, 107);
        padding: 4px 8px;
    }
    .gen-info>.btn-settings:hover{
        background: rgba(0,0,0,0.25)
    }
    
    .details{
        /* height: 200px; */
        display: flex;
        gap: 10px;
    }
    .details>.main-container{
        /* background: blue; */
        padding: 8px 10px;
        width: calc(100% - 82px);
    }
    .details>.side-container{
        padding: 8px 10px;
        width: 70px;
    }

    .notification-btn{
        border: 0;
        border-radius: .25em;
        padding: .5em;
        font-size: 20px;
        position: relative;
        width: 50px;
    }
    .notification-btn:hover{
        background: rgba(0,0,0,0.25);
    }
    .notification-count{
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: rgb(255, 32, 32);
        color: white;
        position: absolute;
        bottom: 10px;
        right: 6px;
    }
    .notification-count{
        font-size: 10px;
        font-weight: bold;
    }

    .notification-container.show{
        right: 50px !important;
    }
    .notification-container{
        border-radius: 0.5em;
        border: 1px solid rgba(0,0,0,0.25);
        max-height: 400px;
        overflow: auto;
        box-shadow: 4px 4px 4px 4px rgba(0,0,0,0.25);
        position: fixed;
        width: 300px;
        right: -500px;
        background: white;
        z-index: 100;
        transition: all 0.3s ease;
    }
    .notification-container>.title{
        font-weight: bold;
        padding: 4px;
        border-bottom: 1px solid rgba(0,0,0,0.25);
        display: flex;
        justify-content: space-between;
    }
    .notification-container>.title>i{
        font-size: 20px;
        padding: 4px 8px 0px 0px;
        cursor: pointer;
        color: gray;
    }
    .notification{
        padding: 4px 6px;
        border-bottom: 1px solid rgba(0,0,0,0.25);
        cursor: pointer;
    }
    .notification:hover{
        background: rgba(0,0,0,0.1) !important;
    }
    .notification.unread{
        background: rgba(0,0,0,0.05);
        border-bottom: 2px solid white;
    }
    .notification.unread>.title>span::after{
        content: "\f111";
        font-size: 6px;
        font-family: "Font Awesome 5 Pro";
        font-weight: 600;
        color: red;
        margin-left: 4px;
    }
    .notification>.title{
        border-bottom: 1px solid rgba(0,0,0,0.25);
    }
    .notification>.footer{
        display: flex;
        justify-content: space-between;
        margin-top: 4px;
        font-style: italic;
    }
    @media only screen and (max-width: 600px) {
        .gen-info>h4{
            margin-bottom: 12px;
        }
        /* .details { flex-wrap: wrap-reverse; }
        .details>.main-container {width: 100%;}
        .details>.side-container {width: 100%;} */
    }

</style>