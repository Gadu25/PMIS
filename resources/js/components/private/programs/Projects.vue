<template>
    <div class="px-3 py-4">
        <button @click="moveBack()" class="btn btn-light btn-sm float-start"><i class="fas fa-arrow-left"></i> Back</button>
        <h2 class="text-center color-darkBlue"> Projects of {{$route.params.selected}}</h2><hr>
        <h1 v-if="loading" class="text-center m-5 p-5">Loading Projects <i class="fas fa-spinner fa-spin"></i></h1>
        <div v-else class="d-flex justify-content-center" id="wrapper">
            <div class="col-sm-10">
                <div class="d-flex row align-items-stretch card-block pt-2">
                    <div class="col col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12 px-3 pb-3" v-for="project in projects" :key="project.id+'_div'">
                        <router-link :to="{ name: 'Portfolio', params: { id: project.id } }" v-bind:style="{'textDecoration': 'none'}">
                            <div class="card shadow" @click="">
                                <div class="card-body">
                                    <h5 class="color-mint">{{ project.title }}</h5>
                                    <hr class="color-blue">
                                    <span class="color-darkBlue"><strong>Program: </strong> {{project.program.title}} </span><br>
                                    <span class="color-darkBlue"><strong>Project Leader: </strong> {{setProjectLeaderName(project.leader.profile.user)}}</span><br>
                                    <span class="color-darkBlue" v-if="project.subprogram"><strong>Subprogram: </strong> {{project.subprogram.title}}<br></span>
                                    <span class="color-darkBlue" v-if="project.cluster"><strong>Cluster: </strong> {{project.cluster.title}}<br></span>
                                </div>
                            </div>
                        </router-link>
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
    height: calc(100vh - 205px);
    overflow: auto;
    padding-bottom: 100px;
}
.card{
    border-radius: 8px;
}
.card:hover{
    /* transition: 0.3s all ease; */
    /* background: linear-gradient(to bottom right, hsl(215, 100%, 60%) 0%, hsl(215, 100%, 80%) 100%);; */
    transform: scale(1.02);
    cursor: pointer;
}
.card-body{
    padding: 5%;
}
.darkBlue{
    background-color: #173F5F;
}
.blue{
    background-color:#20639B; 
}
.mint{
    background-color: #3CAEA3;
}
.yellow{
    background-color: #F6D55C;
}
.red{
    background-color: #ED553B;
}

.color-darkBlue{
    color: #173F5F
}
.color-blue{
    color: #20639B
}
.color-mint{
    color: #3CAEA3
}
.color-yellow{
    color: #F6D55C
}
.color-red{
    color: #ED553B
}

</style>