<template>
    <div class="p-2 profile-container" v-if="!loading">
        <div class="mb-2 pb-2 border-bottom">
            <button class="btn btn-sm btn-outline-secondary float-start" @click="childClick"><i class="fas fa-arrow-left"></i></button>
            <h4 class="text-center">{{profile.year}} Profile</h4>
        </div>
        <div class="content-container">
            <div class="content">
                <div class="d-flex justify-content-end mb-2">
                    <button class="btn btn-sm btn-primary"><i class="far fa-pencil-alt"></i></button>
                </div>
                <div class="card rounded-0 border-0 shadow">
                    <div class="card-body">
                        <h5 class="text-center text-uppercase">
                            {{tab == 'lib' ? 'Line-Item Budget' : 'Project Proposal'}} <br>
                            <small v-if="tab == 'lib'">(LIB)</small>
                        </h5>
                        <template v-if="tab == 'proposal'">
                            <div class="d-flex">
                                <div class="w-25 fw-bold">PROJECT TITLE: </div>
                                <div class="w-75 px-2">{{profile.title}}</div>
                            </div>
                            <div class="d-flex">
                                <div class="w-25 fw-bold">IMPLEMENTATION PERIOD: </div>
                                <div class="w-75 px-2">{{monthName(profile.start) + ' to ' + monthName(profile.end) + ', ' +profile.year }}</div>
                            </div>
                        </template>
                        <template v-if="tab == 'lib'">

                        </template>
                    </div>
                </div>
            </div>
            <div class="side-btns">
                <button @click="tab = 'proposal'" :class="tab == 'proposal' ? 'btn-secondary' : 'btn-outline-secondary'" class="bg-gradient btn border-0 shadow mb-2">Proposal</button>
                <button @click="tab = 'lib'"      :class="tab == 'lib'      ? 'btn-secondary' : 'btn-outline-secondary'" class="bg-gradient btn border-0 shadow mb-2">Line-Item Budget</button>
            </div>
        </div>
    </div>
    <div v-else class="p-5 m-5 text-center">
        <h1 class="mb-3">Loading Project Profile </h1>
        <i class="fas fa-spinner fa-spin fa-5x"></i>
    </div>
</template>
<script>
import { mapActions, mapGetters } from 'vuex'
export default {
    name: 'Profile',
    emits: ['clicked'],
    setup({emit}){},
    data(){
        return {
            tab: 'proposal',
            loading: true
        }
    },
    methods: {
        ...mapActions('project', ['fetchProfile']),
        childClick(){
            this.$emit('clicked')
        },
        monthName(month){
            if(month == 1){  return 'January' }
            if(month == 2){  return 'February' }
            if(month == 3){  return 'March' }
            if(month == 4){  return 'April' }
            if(month == 5){  return 'May' }
            if(month == 6){  return 'June' }
            if(month == 7){  return 'July' }
            if(month == 8){  return 'August' }
            if(month == 9){  return 'September' }
            if(month == 10){ return 'October' }
            if(month == 11){ return 'November' }
            if(month == 12){ return 'December' }
        },
    },
    props: {
        profileId: Number
    },
    computed: {
        ...mapGetters('project', ['getProfile']),
        profile(){ return this.getProfile }
    },
    created(){
        this.fetchProfile(this.profileId).then(res => {
            this.loading = false
        })
    }
}
</script>
<style scoped>
.profile-container{
    height: calc(100vh - 105px);
}
.content-container{
    max-height: calc(100vh - 175px);
    overflow: auto;
    padding: 10px;
    display: flex;
    /* flex-direction: row; */
}
.content{
    width: calc(100% - 100px);
}
.content>.card{
    height: calc(100vh - 235px)
}
.side-btns{
    width: 200px;
    text-align: center;
}
.side-btns>button{
    width: 150px;
}
.side-btns>button:hover{
    transform: scale(1.05);
}
@media only screen and (max-width: 600px) {
    .content-container{
        flex-wrap: wrap-reverse;
    }
    .content{
        width: 100%;
    }
    .content>.card{
        height: calc(100vh - 290px)
    }
    .side-btns{
        width: 100%;
        display: flex;
        flex-direction: column;
    }
    .side-btns>button{
        width: 100%;
    }
    
}
</style>