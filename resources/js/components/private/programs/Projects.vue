<template>
    <div class="px-5 py-4">
        <button @click="moveBack()" class="btn btn-light btn-sm float-start"><i class="fas fa-arrow-left"></i> Back</button>
        <h2 class="text-center"> Projects of {{$route.params.selected}}</h2><hr>
        <h1 v-if="loading" class="text-center m-5 p-5">Loading Projects <i class="fas fa-spinner fa-spin"></i></h1>
        <div class="d-flex justify-content-center" id="wrapper">
            <div class="col-sm-10">
                <div class="accordion shadow-lg" id="accordionExample">
                    <div class="accordion-item" v-for="project in projects" :key="project.id+'_div'">
                        <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button shadow-none collapsed" type="button" data-bs-toggle="collapse" :data-bs-target="'#project'+project.id" expanded="false" aria-controls="collapseOne">
                            <strong>{{project.title}}</strong>
                        </button>
                        </h2>
                        <div :id="'project'+project.id" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <span><strong>Program: </strong> {{project.program.title}} </span><br>
                                <span v-if="project.subprogram"><strong>Subprogram: </strong> {{project.subprogram.title}}<br></span>
                                <span v-if="project.cluster"><strong>Cluster: </strong> {{project.cluster.title}}<br></span>
                                <span><strong>Project Leader: </strong> {{setProjectLeaderName(project.leader.profile.user)}}</span>
                                <div class="d-flex justify-content-end border-top pt-2 mt-2">
                                    <router-link :to="{ name: 'Portfolio', params: { id: project.id } }" class="btn btn-sm btn-outline-secondary">View Portfolio</router-link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import {mapActions, mapGetters} from 'vuex'
export default {
    name: 'Projects',
    data(){
        return {
            loading: true
        }
    },
    methods: {
        ...mapActions('project', ['fetchSortedProjects']),
        setProjectLeaderName(user){
            return user.firstname + ' ' + user.lastname
        },
        moveBack(){
            this.$emit('clicked', true)
            this.$router.push('/programs-and-projects')
        }
    },
    computed: {
        ...mapGetters('project', ['getSortedProjects']),
        projects(){ return this.getSortedProjects }
    },
    created(){
        this.fetchSortedProjects(this.$route.params.selected).then(res => {
            this.loading = false
        })
    }
}
</script>
<style scoped>
#wrapper{
    height: calc(100vh - 175px);
    overflow: auto;
}
</style>