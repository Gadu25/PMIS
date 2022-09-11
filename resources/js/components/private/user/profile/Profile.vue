<template>
    <div class="profile-container">
        <div class="gen-info">
            <button class="btn-settings"><i class="far fa-cog"></i> Settings</button>
            <h4>{{authuser.firstname+' '+authuser.lastname}}, <small></small></h4>
            
            <small><strong><i class="far fa-building"></i> {{authuser.active_profile.title.name}}, {{authuser.division.name}}</strong> <span v-if="authuser.unit_id">, {{authuser.unit.name}}</span> <span v-if="authuser.sybunit_id"> - {{authuser.subunit.name}}</span> </small>
            <br><small><i class="far fa-envelope"></i> <i>{{authuser.email}}</i></small>
        </div>
        <div class="details">
            <div class="main-container">
                User Activities. Coming soon...
            </div>
            <div class="side-container">
                <div class="notification-container">
                    <div class="title"><i class="far fa-bell"></i> Notifications</div>
                    <div class="notification" v-for="notification in authuser.active_profile.notifications" :key="'notification'+notification.id">
                        <div class="title" v-html="notification.title"></div>
                        <div class="body" v-html="notification.body"></div>
                        <div class="footer">
                            <div class="user-from"><small>By: {{notification.from.user.firstname + ' ' + notification.from.user.lastname}}</small></div>
                            <div class="date-created"><small>{{notification.created_at}}</small></div>
                        </div>
                    </div>
                </div>
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
        /* background: gray; */
        width: 75%;;
    }
    .details>.side-container{
        /* background: gray; */
        padding: 8px 10px;
        width: 25%;;
    }
    .notification-container{
        /* background: gray; */
        /* padding: 4px 8px; */
        border-radius: 0.5em;
        border: 1px solid rgba(0,0,0,0.25);
        height: 400px;
        overflow: auto;
        box-shadow: 4px 4px 4px 4px rgba(0,0,0,0.25);
    }
    .notification-container>.title{
        font-weight: bold;
        padding: 4px;
        border-bottom: 1px solid rgba(0,0,0,0.25);
    }
    .notification{
        /* background: red; */
        padding: 4px 6px;
        border-bottom: 1px solid rgba(0,0,0,0.25);
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
        .details { flex-wrap: wrap-reverse; }
        .details>.main-container {width: 100%;}
        .details>.side-container {width: 100%;}
    }

</style>