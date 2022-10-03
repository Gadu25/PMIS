<template>
    <div v-if="!formshow && profileId == 0">
        <button class="btn btn-sm btn-outline-secondary float-start" @click="childClick()"><i class="fas fa-arrow-left"></i></button>
        <button class="btn btn-sm btn-success bg-gradient float-end" @click="formshow = true, editmode = false"><i class="fas fa-plus"></i></button>
        <h5 class="text-center pb-3 mb-2 border-bottom ">Line-Item Budgets and Project Proposals</h5>
        <div class="row flex-wrap p-2">
            <div class="col-sm-3 mb-3" v-for="profile in project.profiles" :key="profile.id">
                <div class="card shadow border-0" style="cursor: pointer;" @click="profileId = profile.id">
                    <div class="card-body text-center">
                        <i id="close" class="far fa-folder fa-5x"></i>
                        <i id="open" class="far fa-folder-open fa-5x"></i>
                        <br>
                        <strong>{{profile.year}}</strong>
                    </div>
                </div>
            </div>
            <div class="text-center p-5" v-if="project.profiles.length == 0">
                <h1> No Profiles found for this Project</h1>
                <h3><a href="#" @click="formshow = true, editmode = false">Click here to add one</a> </h3>
            </div>
        </div>
    </div>
    <Form :editmode="editmode" @clicked="formshow = false" v-if="formshow" />
    <Profile :profileId="profileId" @clicked="profileId = 0" v-if="profileId != 0" />
</template>
<script>
import {mapGetters} from 'vuex'
import Form from './libs/Form.vue'
import Profile from './libs/Profile.vue'
export default {
    name: 'Libs',
    emits: ['clicked'],
    setup({emit}){},
    components: {
        Form, Profile
    },
    data(){
        return {
            formshow: false,
            editmode: false,
            profileId: 0
        }
    },
    methods: {
        childClick(){
            this.$emit('clicked')
        },
    },
    computed: {
        ...mapGetters('project', ['getProject']),
        project(){ return this.getProject }
    }
}
</script>
<style scoped>
.card:hover>div>i#close{
    display: none;
}
.card>div>i#open{

    display: none;
}
.card:hover>div>i#open{
    display: inline;
}
</style>