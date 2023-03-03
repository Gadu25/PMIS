<template>
    <div class="profile-container">
        <div class="gen-info">
            <button class="btn-settings" data-bs-target="#settingModal" data-bs-toggle="modal"><i class="far fa-cog"></i>Settings</button>
            <h4>{{authuser.firstname+' '+authuser.lastname}},</h4>
            <small><i class="far fa-user-circle"></i> {{authuser.active_profile.title.name}}</small><br>
            <small><i class="far fa-building"></i> <strong>{{authuser.division.name}}</strong> <span v-if="authuser.unit_id">, {{authuser.unit.name}}</span> <span v-if="authuser.sybunit_id"> - {{authuser.subunit.name}}</span> </small>
            <br><small><i class="far fa-envelope"></i> <i>{{authuser.email}}</i></small>
        </div>
        <div class="details">
            <div class="main-container">
                <div class="mb-2">
                    <button @click="tab = 'projects'"   class="btn btn-sm shadow-none me-1" :class="tab == 'projects' ? 'btn-secondary' : 'btn-outline-secondary'">Projects</button>
                    <button @click="tab = 'activities'" class="btn btn-sm shadow-none" :class="tab == 'activities' ? 'btn-secondary' : 'btn-outline-secondary'">Activities</button>
                </div>
                <div class="card" id="activities" v-if="tab == 'activities'">
                    <div class="card-body">
                        User Activities. Coming soon...
                    </div>
                </div>
                <div class="projects-container" v-if="tab == 'projects'">
                    <div class="col-sm-3 px-1" v-for="data in authuser.active_profile.leader_of" :key="data.id">
                        <div class="card shadow" style="height: 130px;">
                            <div class="card-body">
                                {{data.project.title}}
                            </div>
                        </div>
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
                    <div class="text-center p-4" v-if="authuser.active_profile.notifications.length == 0">
                        <i>No notifications</i>
                    </div>
                </div>
                <button class="notification-btn" @click="shownotification = true">
                    <i class="far fa-bell"></i>
                    <div class="notification-count" v-if="authuser.active_profile.unread > 0"><span>{{authuser.active_profile.unread < 9 ? authuser.active_profile.unread : '9+'}}</span></div>    
                </button>
            </div>
        </div>
    </div>

    <!-- Settings Modal -->
    <div class="modal fade" id="settingModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">User Settings</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body my-1">
                    <div class="form-group px-4">
                        <div class="email mb-4 pb-2">
                            <div v-if="changeEmail == true">
                                <p><strong>Email address:</strong></p>
                                <input class="form-control" id="userEmail" type="text" placeholder="Enter your new email address" v-model="inputedEmail"/>
                                <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
                                <br>
                                <div class="mt-2" v-if="otpSentEmail == true">
                                    <p>One time pin:</p>
                                    <div class="input-group mb-3">
                                        <input class="form-control" id="otp" type="text" placeholder="Enter the otp you received" aria-describedby="basic-addon2" v-model="inputedOtpEmail">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-success" type="button" @click="verifyEmail">Verify</button>
                                        </div>
                                    </div>
                                </div>
                                <button class="btn btn-secondary btn-sm mt-2 me-2" @click="changeEmail = !changeEmail" title="Cancel">Cancel</button>
                                <button v-if="otpSentEmail == true" class="btn btn-primary disabled btn-sm mt-2">Resend OTP after {{ timerCountEmail }}</button>
                                <button v-else class="btn btn-primary btn-sm mt-2" title="Send OTP to your inputed email" @click="countDownEmail(30)">Send OTP</button>
                            </div>
                            <div v-else>
                                <p>Email address:&nbsp;<strong class="me-2">{{ authuser.email }}</strong><button class="btn btn-primary btn-sm" @click="changeEmail = !changeEmail"><i class="fa fa-pencil"></i></button></p>
                            </div>
                        </div>
                        <div v-if="changePassword == false">
                            <p>Password <button class="btn btn-primary btn-sm" @click="changePassword = !changePassword"><i class="fa fa-pencil"></i></button></p>
                        </div>
                        <div v-else>
                            <p><strong>Password:</strong></p>
                            <input class="form-control" id="userEmail" type="password" placeholder="Enter your new password" v-model="inputedPassword"/>
                            <small id="emailHelp" class="form-text text-muted">We'll never share your password with anyone else.</small>
                            <br>
                            <div class="mt-2" v-if="otpSentPassword == true">
                                <p>One time pin:</p>
                                <div class="input-group mb-3">
                                    <input class="form-control" id="otp" type="text" placeholder="Enter the otp you received" aria-describedby="basic-addon2" v-model="inputedOtpPassword">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-success" type="button" @click="verifyPassword">Verify</button>
                                    </div>
                                </div>
                            </div>
                            
                            <button class="btn btn-secondary btn-sm mt-2 me-2" @click="changePassword = !changePassword" title="Cancel">Cancel</button>
                            <button v-if="otpSentPassword == true" class="btn btn-primary disabled btn-sm mt-2">Resend OTP after {{ timerCountPassword }}</button>
                            <button v-else class="btn btn-primary btn-sm mt-2" title="Send OTP to your inputed email" @click="countDownPassword(30)">Send OTP</button>
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
            shownotification: false,
            tab: 'projects',
            changeEmail: false,
            changePassword: false,
            otpSentEmail: false,
            otpSentPassword: false,
            timerCountEmail: 0,
            timerCountPassword: 0,
            inputedOtpEmail: "",
            otpEmail: "",
            inputedOtpPassword: "",
            otpPassword: "",
            inputedPassword: "",
            inputedEmail: ""
        }
    },
    watch:{
        timerCountEmail:{
            handler(value){
                if (value > 0) {
                    setTimeout(() => {
                        this.timerCountEmail--;
                    }, 1000);
                }else{
                    this.otpSentEmail = false
                }
            }
        },
        timerCountPassword:{
            handler(value){
                if (value > 0) {
                    setTimeout(() => {
                        this.timerCountPassword--;
                    }, 1000);
                }else{
                    this.otpSentPassword = false
                }
            }
        }
    },
    methods: {
        ...mapActions('user', ['fetchAuthUser', 'updateNotification', 'changeEmailFinal', 'changePasswordFinal']),
        gotoNotificationLink(notification){
            this.updateNotification(notification.id).then(res => {
                this.fetchAuthUser().then(res => {
                    this.$router.push(notification.link)
                })
            })
        },
        countDownPassword($seconds){
            var otp = ((Math.random())*1000000).toFixed(0);
            console.log("testing", otp)
            this.otpPassword = otp;
            this.timerCountPassword = $seconds;
            this.otpSentPassword = true;
        },
        countDownEmail($seconds){
            var otp = ((Math.random())*1000000).toFixed(0);
            console.log("testing", otp)
            this.otpEmail = otp
            this.timerCountEmail = $seconds
            this.otpSentEmail = true
        },
        verifyEmail(){
            if(this.otpEmail == this.inputedOtpEmail){
                this.changeEmailFinal({ id: this.authuser.id, email: this.inputedEmail }).then(res => {
                    var icon = res.errors ? 'error' : 'success'
                    this.toastMsg(icon, res.message)
                })

                this.changeEmail = false
                this.otpSentEmail = false
                this.timerCountEmail = false
                this.inputedOtpEmail = ""
                this.otpEmail = ""
                this.inputedEmail = ""
            }else{
                this.toastMsg('error', 'Wrong OTP')
            }
        },
        verifyPassword(){
            if(this.otpPassword == this.inputedOtpPassword){
                if(this.inputedPassword.length < 6){
                    this.toastMsg('error', 'Password is too short')
                    console.log("password is too short")
                } else {
                    this.changePasswordFinal({ id: this.authuser.id, password: this.inputedPassword }).then(res => {
                    var icon = res.errors ? 'error' : 'success'
                    this.toastMsg(icon, res.message)

                    this.changePassword = false
                    this.otpSentPassword = false
                    this.timerCountPassword = false
                    this.inputedOtpPassword = ""
                    this.otpPassword = ""
                    this.inputedPassword = ""
                })
                }
                
            }else{
                this.toastMsg('error', 'Wrong OTP')
            }
        },
        toastMsg(icon, msg){
            toast.fire({
                icon: icon,
                title: msg
            })
        },
    },
    computed: {
        ...mapGetters('user', ['getAuthUser']),
        authuser(){ return this.getAuthUser }
    }
}
</script>
<style>
    .edit-email{
        color: #20639B
    }
    .edit-email:hover{
        transform: scale(1.3);
    }
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

    .projects-container{
        display: flex;
        flex-wrap: wrap;
    }
    .projects-container>div{
        /* padding: 0 0; */
        cursor: pointer;
    }
    .email{
        border-bottom-style: dashed;
        border-color: #3CAEA3;
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