<template>
    <template v-if="!childSelected">
        <div class="px-5 py-4" v-if="!loading">
            <h2 class="text-center">Programs and Projects</h2><hr>
            <template v-if="!manageprojects">
                <div class="d-flex justify-content-end">
                    <button class="btn btn-sm btn-outline-secondary" @click="manageprojects = true"><i class="far fa-cog"></i> Manage Projects</button>
                </div>
                <div v-for="program in programs" :key="program.id+'_program'">
                    <h3><router-link @click="childSelected = true" :to="{ name: 'Projects', params: { selected: program.title } }">{{program.title}}</router-link></h3>
                    <div class="ms-3" v-for="subprogram in program.subprograms" :key="subprogram.id+'_subprogram'">
                        <h6><router-link @click="childSelected = true" :to="{ name: 'Projects', params: { selected: subprogram.title } }"><strong>{{subprogram.title}}</strong></router-link></h6>
                        <div class="ms-3" v-for="cluster in subprogram.clusters" :key="cluster.id+'_cluster'">
                            <h6><router-link @click="childSelected = true" :to="{ name: 'Projects', params: { selected: cluster.title } }">{{cluster.title}}</router-link></h6>
                        </div>
                    </div><hr>
                </div>
            </template>
            <div v-else>
                <div class="d-flex justify-content-center">
                    <div class="col-sm-12">
                        <!-- <form @submit.prevent="submitForm()" class="p-4" v-if="formshow"> -->
                        <div class="px-2 py-4" v-if="formshow">
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
                                            <option value="0" >N/A</option>
                                        </select>
                                        <label for="Subprogram">Sub-Program</label>
                                    </div>
                                </div>
                                <div class="mb-2 col-sm-4" v-if="clusters.length > 0">
                                    <div class="form-floating">
                                        <select class="form-select" id="Cluster" v-model="form.cluster_id">
                                            <option value="" selected hidden disabled>Select Cluster</option>
                                            <option :value="cluster.id" v-for="cluster in clusters" :key="cluster.id+'_clusOption'">{{cluster.title}}</option>
                                            <option value="0">N/A</option>
                                            
                                        </select>
                                        <label for="Cluster">Cluster</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row mb-3">
                                <div :class="'mb-2 col-sm-'+ ((units.length > 0 && subunits.length > 0) ? 4 : (units.length > 0) ? 6 : 12 )">
                                    <div class="form-floating">
                                        <select class="form-select" id="Division" v-model="form.division_id" @change="divChange()">
                                            <option value="" selected hidden disabled>Select Division</option>
                                            <option :value="division.id" v-for="division in divisions" :key="division.id+'_divOption'">{{division.name}}</option>
                                        </select>
                                        <label for="Division">Division</label>
                                    </div>
                                </div>
                                <div :class="'mb-2 col-sm-' + (subunits.length > 0 ? 4 : 6)" v-if="units.length > 0">
                                    <div class="form-floating">
                                        <select class="form-select" id="Unit" v-model="form.unit_id" @change="unitChange()">
                                            <option value="" selected hidden disabled>Select Unit</option>
                                            <option :value="unit.id" v-for="unit in units" :key="unit.id+'_unitOption'">{{unit.name}}</option>
                                            <option value="0">N/A</option>
                                        </select>
                                        <label for="Unit">Unit</label>
                                    </div>
                                </div>
                                <div class="col-sm-4" v-if="subunits.length > 0">
                                    <div class="form-floating">
                                        <select class="form-select" id="Subunit" v-model="form.subunit_id">
                                            <option value="" selected hidden disabled>Select Sub-Unit</option>
                                            <option :value="subunit.id" v-for="subunit in subunits" :key="subunit.id+'_subuOption'">{{subunit.name}}</option>
                                            <option value="0">N/A</option>
                                        </select>
                                        <label for="Subunit">Sub-Unit</label>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end mb-2" v-if="form.id == ''">
                                <button tabindex="-1" class="btn btn-sm btn-secondary" @click="addProject()"><i class="fas fa-plus"></i> Project</button>
                            </div>
                            <div class="form-group row mb-3">
                                <div class="col-sm-6 mb-3" v-for="project, key in form.projects" :key="key+'_title'">
                                    <div class="card shadow">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-end mb-2">
                                                <button tabindex="-1" class="btn btn-sm btn-secondary" @click="addSubproject(key)"><i class="fas fa-plus"></i> Sub-Project</button>
                                                <button tabindex="-1" class="btn btn-sm btn-danger ms-1" @click="removeProject(project)" v-if="form.id == ''"><i class="fas fa-trash-alt"></i> Remove</button>
                                            </div>
                                            <div class="form-floating mb-3">
                                                <input type="text" class="form-control" id="floatingInput" placeholder="text" v-model="project.title">
                                                <label for="floatingInput">Project Title <span v-if="form.id == ''">No. {{key+1}}</span></label>
                                            </div>
                                            <strong v-if="project.subprojects.length > 0">Sub-Projects</strong>
                                            <div class="form-floating ms-4 my-3 position-relative" v-for="subp, skey in project.subprojects" :key="skey+'_subproject'">
                                                <button tabindex="-1" class="btn btn-danger btn-sm position-absolute top-0 end-0" @click="removeSubproject(key, subp)"><i class="far fa-times"></i></button>
                                                <input type="text" class="form-control" id="floatingInput" placeholder="text" v-model="subp.title">
                                                <label for="floatingInput">Sub-Project Title <span v-if="form.id == ''">No. {{skey+1}}</span></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end mt-3">
                                <button tabindex="-1" style="min-width: 100px;" class="btn btn-outline-secondary rounded-pill me-2" @click="formshow = false">Cancel</button>
                                <button style="min-width: 100px;" class="btn rounded-pill" :class="form.id == '' ? 'btn-success' : 'btn-primary'" @click="submitForm()">{{(form.id == '') ? 'Submit' : 'Save Changes'}}</button>
                            </div>
                        </div>
                        <!-- </form> -->
                        <div v-else>
                            <div class="d-flex justify-content-between mb-2">
                                <button class="btn btn-sm btn-outline-secondary" @click="manageprojects = false"><i class="fas fa-arrow-left"></i></button>
                                <button class="btn btn-sm btn-success" @click="resetForm()"><i class="fas fa-plus"></i></button>
                            </div>
                            <div class="d-flex justify-content-end">
                                <div class="form-floating mb-2" style="min-width: 200px;">
                                    <select class="form-select" id="DisplayType" v-model="displaytype" @change="displayTypeChange()">
                                        <option value="division">Division</option>
                                        <option value="program">Program</option>
                                    </select>
                                    <label for="DisplayType">Display Type</label>
                                </div>
                            </div>
                            <div class="table-container">
                                <table class="table table-sm table-bordered table-hover align-middle">
                                    <caption>List of Project Titles</caption>
                                    <thead>
                                        <tr>
                                            <th>Project Title</th>
                                            <th style="min-width: 80px">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template v-for="first, fkey in projects" :key="fkey+'_first'">
                                            <tr>
                                                <td><strong>{{fkey}}</strong></td>
                                                <td></td>
                                            </tr>
                                            <template v-for="second, skey in first" :key="skey+'_second'">
                                                <tr v-if="skey !=''">
                                                    <td><div class="ms-2"><strong>{{skey}}</strong></div></td>
                                                    <td></td>
                                                </tr>
                                                <template v-for="third, tkey in second" :key="tkey+'_third'">
                                                    <tr v-if="tkey != ''">
                                                        <td><div class="ms-3"><strong>{{tkey}}</strong></div></td>
                                                        <td></td>
                                                    </tr>
                                                    <template v-for="project in third" :key="project.id+'_project'">
                                                        <tr>
                                                            <td><div class="ms-4">{{project.title}}</div></td>
                                                            <td class="text-center">
                                                                <button @click="editForm(project)" class="btn btn-sm btn-primary me-1"><i class="far fa-pencil-alt"></i></button>
                                                                <button class="btn btn-sm btn-danger"><i class="far fa-trash-alt"></i></button>
                                                            </td>
                                                        </tr>
                                                        <tr v-for="subproject in project.subprojects" :key="subproject.id+'_subproj'">
                                                            <td colspan="2"><div class="ms-5">{{subproject.title}}</div></td>
                                                        </tr>
                                                    </template>
                                                </template>
                                            </template>
                                        </template>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <h1 class="text-center p-5" v-else><i class="fas fa-spinner fa-spin fa-5x"></i></h1>
    </template>
    <router-view @clicked="childSelected = false" v-else></router-view>
</template>
<script>
import { mapActions, mapGetters } from 'vuex'
export default {
    name: 'Programs',
    data(){
        return {
            childSelected: false,
            loading: true,
            manageprojects: false,
            formshow: false,
            form: {},
            subprograms: [],
            clusters: [],
            units: [],
            subunits: [],
            // Display
            displaytype: 'program',
            projects: []
        }
    },
    methods: {
        ...mapActions('program', ['fetchPrograms']),
        ...mapActions('project', ['fetchProjects', 'saveProject']),
        ...mapActions('division', ['fetchDivisions']),
        resetForm(){
            this.formshow = true
            this.form = {
                id: '',
                program_id: '',
                subprogram_id: '',
                cluster_id: '',
                division_id: '',
                unit_id: '',
                subunit_id: '',
                projects: [
                    { title: '', num: this.setUID(), status: 'New', subprojects: [] }
                ],
                tempIds: []
            }
            this.subprograms = []
            this.clusters = []
        },
        editForm(project){
            this.resetForm()
            this.form.id = project.id

            this.form.program_id = project.program_id
            this.progChange()
            this.form.subprogram_id = (project.subprogram_id) ? project.subprogram_id : 0
            this.subpChange()
            this.form.cluster_id = (project.cluster_id) ? project.cluster_id : 0

            this.form.division_id = project.division_id
            this.divChange()
            this.form.unit_id = (project.unit_id) ? project.unit_id : 0
            this.unitChange()
            this.form.subunit_id = (project.subunit_id) ? project.subunit_id : 0

            this.form.projects[0].title = project.title
            this.form.projects[0].num = project.num
            this.form.projects[0].status = project.status

            for(let i = 0; i < project.subprojects.length; i++){
                var subproject = project.subprojects[i]
                this.form.projects[0].subprojects.push({id: subproject.id, title: subproject.title})
                this.form.tempIds.push(subproject.id)
            }
        },
        submitForm(){
            if(this.formValidated()){
                this.saveProject(this.form).then(res => {
                    var icon = res.errors ? 'error' : 'success'
                    this.toastMsg(icon, res.message)
                    this.formshow = false
                    this.displayTypeChange()
                })
            }
        },
        formValidated(){
            if(this.form.program_id === ''){ this.toastMsg('warning', 'Please select a Program'); return false }
            if(this.form.subprogram_id === ''){ this.toastMsg('warning', 'Please select a Sub-Program'); return false }
            if(this.clusters.length > 0 && this.form.cluster_id === ''){ this.toastMsg('warning', 'Please select a Cluster'); return false }
            if(this.form.division_id === ''){ this.toastMsg('warning', 'Please select a Division'); return false }
            if(this.form.unit_id === ''){ this.toastMsg('warning', 'Please select a Unit'); return false }
            if(this.subunits.length > 0 && this.form.subunit_id === ''){ this.toastMsg('warning', 'Please select a Sub-Unit'); return false }
            for(let i = 0; i < this.form.projects.length; i++){
                if(this.form.projects[i].title == ''){ this.toastMsg('warning', 'Project Title No. '+(i+1)+' Empty'); return false }
                for(let j = 0; j < this.form.projects[i].subprojects.length; j++){
                    if(this.form.projects[i].subprojects[j].title == ''){ this.toastMsg('warning', 'Sub-Project Title No. '+(j+1)+' Empty'); return false }
                }
            }
            return true
        },
        // Form Behavioral Functions
        addProject(){
            this.form.projects.push({
                title: '',
                num: this.setUID(),
                status: 'New',
                subprojects: []
            })
        },
        removeProject(title){
            if(this.form.projects.length > 1){
                this.form.projects.remove(title)
            }
        },
        addSubproject(key){
            this.form.projects[key].subprojects.push({id: '', title: ''})
        },
        removeSubproject(key, title){
            this.form.projects[key].subprojects.remove(title)
        },
        progChange(){
            this.form.subprogram_id = ''
            this.form.cluster_id = ''
            var program = this.programs.find(elem => elem.id == this.form.program_id)
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
        },
        divChange(){
            this.form.unit_id = ''
            this.form.subunit_id = ''
            var division = this.divisions.find(elem => elem.id == this.form.division_id)
            this.units = division.units
            this.subunits = []
        },
        unitChange(){
            this.form.subunit_id = ''
            var subunits = []
            if(this.form.unit_id != 0){
                var unit = this.units.find(elem => elem.id == this.form.unit_id)
                subunits = unit.subunits
            }
            this.subunits = subunits
        },
        // Display Behavioral Functions
        displayTypeChange(){
            this.projects = (this.displaytype == 'program') ? this.program_projects : this.division_projects
        },
        // Other
        setUID(){
            var uid = 'TPN-' + Date.now().toString().slice(10) + Math.random().toString(36).slice(6).toUpperCase()
            return uid.replace(/\./g,'-')
        },
        toastMsg(icon, msg){
            toast.fire({
                icon: icon,
                title: msg
            })
        }
    },
    computed: {
        ...mapGetters('program', ['getPrograms']),
        programs(){ return this.getPrograms },
        ...mapGetters('project', ['getProjects']),
        division_projects(){ return this.getProjects.division_group },
        program_projects(){ return this.getProjects.program_group },
        ...mapGetters('division', ['getDivisions']),
        divisions(){ return this.getDivisions }
    },
    created(){
        this.fetchPrograms().then(res => {
            this.fetchProjects().then(res => {
                if(this.divisions.length == 0){
                    this.fetchDivisions()
                }
                this.projects = res.program_group
                this.loading = false
            })
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

.table-container{
    max-height: 70vh;
    overflow: auto;
}
th{
    text-align: center;
}
</style>