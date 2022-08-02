<template>
    <div class="px-5 py-4" v-if="!loading">
        <h2 class="text-center">Programs and Projects</h2><hr>
        <template v-if="!manageprojects">
            <div class="d-flex justify-content-end">
                <button class="btn btn-sm btn-outline-secondary" @click="manageprojects = true"><i class="far fa-cog"></i> Manage Projects</button>
            </div>
            <div v-for="program in programs" :key="program.id+'_program'">
                <h3><router-link :to="{ name: 'Projects', params: { selected: program.title } }">{{program.title}}</router-link></h3>
                <div class="ms-3" v-for="subprogram in program.subprograms" :key="subprogram.id+'_subprogram'">
                    <h6><router-link :to="{ name: 'Projects', params: { selected: subprogram.title } }"><strong>{{subprogram.title}}</strong></router-link></h6>
                    <div class="ms-3" v-for="cluster in subprogram.clusters" :key="cluster.id+'_cluster'">
                        <h6><router-link :to="{ name: 'Projects', params: { selected: cluster.title } }">{{cluster.title}}</router-link></h6>
                    </div>
                </div><hr>
            </div>
        </template>
        <div v-else>
            <button class="btn btn-sm btn-outline-secondary float-start" @click="manageprojects = false"><i class="fas fa-arrow-left"></i></button>
            <div class="d-flex justify-content-center">
                <div class="col-sm-12">
                    <form @submit.prevent="submitForm()" class="p-4" v-if="formshow">
                        <div class="mb-2"><h4>Form</h4></div>
                        <div class="form-group row mb-3">
                            <div :class="'mb-2 col-sm-'+ ((subprograms.length > 0 && clusters.length > 0) ? 4 : (subprograms.length > 0) ? 6 : 12 )">
                                <div class="form-floating">
                                    <select class="form-select" id="Program" v-model="form.program_id" @change="progChange()">
                                        <option value="" selected hidden disabled>Select Program</option>
                                        <option :value="program.id" v-for="program in programs" :key="program.id+'_progOption'">{{program.title}}</option>
                                    </select>
                                    <label for="Program">Program</label>
                                </div>

                            </div>
                            <div :class="'mb-2 col-sm-' + (clusters.length > 0 ? 4 : 6)" v-if="subprograms.length > 0">
                                <div class="form-floating">
                                    <select class="form-select" id="Subprogram" v-model="form.subprogram_id" @change="subpChange()">
                                        <option value="" selected hidden disabled>Select Sub-Program</option>
                                        <option :value="subprogram.id" v-for="subprogram in subprograms" :key="subprogram.id+'_subpOption'">{{subprogram.title}}</option>
                                        <option value="0" >Not Applicable</option>
                                    </select>
                                    <label for="Subprogram">Sub-Program</label>
                                </div>
                            </div>
                            <div class="mb-2 col-sm-4" v-if="clusters.length > 0">
                                <div class="form-floating">
                                    <select class="form-select" id="Cluster" v-model="form.cluster_id">
                                        <option value="" selected hidden disabled>Select Cluster</option>
                                        <option :value="cluster.id" v-for="cluster in clusters" :key="cluster.id+'_clusOption'">{{cluster.title}}</option>
                                        <option value="0">Not Applicable</option>
                                        
                                    </select>
                                    <label for="Cluster">Cluster</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col">
                                <div class="form-floating">
                                    <select class="form-select" id="Division" v-model="form.division_id">
                                        <option value="" selected hidden disabled>Select Division</option>
                                        <option value="1">One</option>
                                        <option value="2">Two</option>
                                        <option value="3">Three</option>
                                    </select>
                                    <label for="Division">Division</label>
                                </div>

                            </div>
                            <div class="col">
                                <div class="form-floating">
                                    <select class="form-select" id="Unit" v-model="form.unit_id">
                                        <option value="" selected hidden disabled>Select Unit</option>
                                        <option value="1">One</option>
                                        <option value="2">Two</option>
                                        <option value="3">Three</option>
                                    </select>
                                    <label for="Unit">Unit</label>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-floating">
                                    <select class="form-select" id="Subunit" v-model="form.subunit_id">
                                        <option value="" selected hidden disabled>Select Sub-Unit</option>
                                        <option value="1">One</option>
                                        <option value="2">Two</option>
                                        <option value="3">Three</option>
                                    </select>
                                    <label for="Subunit">Sub-Unit</label>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-3">
                            <button style="min-width: 100px;" class="btn btn-outline-secondary rounded-pill me-2" @click="formshow = false">Cancel</button>
                            <button style="min-width: 100px;" class="btn btn-primary rounded-pill" type="submit">Submit</button>
                        </div>
                    </form>
                    <div v-else>
                        <div class="d-flex justify-content-end mb-2">
                            <button class="btn btn-sm btn-success" @click="formshow = true"><i class="fas fa-plus"></i></button>
                        </div>
                        <table class="table table-bordered">
                            <caption>List of Projects</caption>
                            <thead>
                                <tr>
                                    <th>test</th>
                                    <th>test</th>
                                    <th>test</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>test</td>
                                    <td>test</td>
                                    <td>test</td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <h1 class="text-center p-5" v-else><i class="fas fa-spinner fa-spin fa-5x"></i></h1>
</template>
<script>
import { mapActions, mapGetters } from 'vuex'
export default {
    name: 'Programs',
    data(){
        return {
            loading: true,
            manageprojects: false,
            formshow: false,
            form: {
                id: '',
                type: 'single',
                program_id: '',
                subprogram_id: '',
                cluster_id: '',
                division_id: '',
                unit_id: '',
                subunit_id: '',
                title: '',
                num: '',
                titles: [
                    { title: '', num: '' }
                ]
            },
            subprograms: [],
            clusters: []
        }
    },
    methods: {
        ...mapActions('program', ['fetchPrograms']),
        submitForm(){
            console.log('form submitted')
        },
        progChange(){
            var program = this.programs.find(elem => elem.id == this.form.program_id)
            this.form.subprogram_id = ''
            this.form.cluster_id = ''
            this.subprograms = program.subprograms
            this.clusters = []
        },
        subpChange(){
            this.form.cluster_id = ''
            var clusters = []
            if(this.form.subprogram_id != 0){
                var subprogram = this.subprograms.find(elem => elem.id == this.form.subprogram_id)
                clusters = subprogram.clusters
            }
            this.clusters = clusters
        }
    },
    computed: {
        ...mapGetters('program', ['getPrograms']),
        programs(){ return this.getPrograms }
    },
    created(){
        this.fetchPrograms().then(res => {
            this.loading = false
        })
    }
}
</script>
<style scoped>
h3, h6{
    padding: 2px 4px;
    border-radius: 0.25rem;
    cursor: pointer;
}
h3:hover, h6:hover{
    background: rgba(0,0,0,0.1);
}
h3>a,h6>a{
    text-decoration: none;
    color: black;
}
</style>