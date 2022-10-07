<template>
    <div v-if="!formshow">
        <div class="d-flex justify-content-end mb-2">
            <button class="btn btn-sm btn-success bg-gradient" data-bs-target="#modal" data-bs-toggle="modal" @click="newForm()"><i class="fas fa-plus"></i> Create New</button>
        </div>
        <div class="table-responsive" id="project-list">
            <table class="table table-sm table-bordered">
                <thead class="text-center">
                    <tr>
                        <th>Programs / Projects / Activities</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div v-else>
        <div class="d-flex justify-content-between">
            <button class="btn btn-sm btn-outline-secondary" @click="hideForm()"><i class="fas fa-times"></i> Close</button>
        </div>
    </div>
    <div class="modal fade" id="modal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{formpart <= 1 ? '1. Project Filters' : '2. Annex 1 Details'}}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <template v-if="formpart == 1">
                        <div class="form-group row">
                            <div :class="setColumn('division')">
                                <div class="form-floating mb-3">
                                    <select class="form-select" id="Division" v-model="form.division_id" @change="changeDivision()">
                                        <option value="" selected hidden disabled>Select Division</option>
                                        <option :value="division.id" v-for="division in divisions" :key="division.id">{{division.name}}</option>
                                    </select>
                                    <label for="Division">Division</label>
                                </div>
                            </div>
                            <div :class="setColumn('unit')" v-if="units.length > 0">
                                <div class="form-floating mb-3">
                                    <select class="form-select" id="Unit" v-model="form.unit_id" @change="changeUnit()">
                                        <option value="" selected hidden disabled>Select Unit</option>
                                        <option :value="unit.id" v-for="unit in units" :key="unit.id">{{unit.name}}</option>
                                    </select>
                                    <label for="Unit">Unit</label>
                                </div>
                            </div>
                            <div class="col-sm-4" v-if="subunits.length > 0">
                                <div class="form-floating mb-3">
                                    <select class="form-select" id="SubUnit" v-model="form.subunit_id">
                                        <option value="" selected hidden disabled>Select Sub-Unit</option>
                                        <option :value="subunit.id" v-for="subunit in subunits" :key="subunit.id">{{subunit.name}}</option>
                                    </select>
                                    <label for="SubUnit">Sub-Unit</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div :class="setColumn('program')">
                                <div class="form-floating mb-3">
                                    <select class="form-select" id="Program" v-model="form.program_id" @change="changeProgram()">
                                        <option value="" selected hidden disabled>Select Program</option>
                                        <option :value="program.id" v-for="program in programs" :key="program.id">{{program.title}}</option>
                                    </select>
                                    <label for="Program">Program</label>
                                </div>
                            </div>
                            <div :class="setColumn('subprogram')" v-if="subprograms.length > 0">
                                <div class="form-floating mb-3">
                                    <select class="form-select" id="subProgram" v-model="form.subprogram_id" @change="changeSubprogram()">
                                        <option value="" selected hidden disabled>Select Sub-Program</option>
                                        <option :value="subprogram.id" v-for="subprogram in subprograms" :key="subprogram.id">{{subprogram.title}}</option>
                                    </select>
                                    <label for="subProgram">Sub-Program</label>
                                </div>
                            </div>
                            <div class="col-sm-4" v-if="clusters.length > 0">
                                <div class="form-floating mb-3">
                                    <select class="form-select" id="cluster" v-model="form.cluster_id">
                                        <option value="" selected hidden disabled>Select Cluster</option>
                                        <option :value="cluster.id" v-for="cluster in clusters" :key="cluster.id">{{cluster.title}}</option>
                                    </select>
                                    <label for="cluster">Cluster</label>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template v-if="formpart == 2">
                        <div class="form-group row">
                            <div class="col-sm-7">
                                <div class="form-floating mb-3">
                                    <select class="form-select" id="Source">
                                        <option value="" selected hidden disabled>Select Source of Funds</option>
                                        <option value="2A1">2A1</option>
                                        <option value="2A1-AC">2A1-AC</option>
                                        <option value="2A2">2A2</option>
                                        <option value="Capital Outlay">Capital Outlay</option>
                                        <option value="Other Source of Funds">Other Source of Funds</option>
                                    </select>
                                    <label for="Source">Charging</label>
                                </div>
                            </div>
                            <div class="col-sm-5">
                                <div class="form-floating mb-3">
                                    <select class="form-select" id="headerType">
                                        <option value="" selected hidden disabled>Select Header Type</option>
                                        <option value="Subprogram">Subprogram</option>
                                        <option value="None">None</option>
                                        <option value="Unit">Unit</option>
                                    </select>
                                    <label for="headerType">Header Type</label>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mb-2" >
                            <button class="btn btn-sm btn-success bg-gradient" @click="addProject()"><i class="fas fa-plus"></i> Project</button>
                        </div>
                        <div class="overflow-auto" style="max-height: 50vh;">
                            <div class="position-relative" v-for="fp, key in form.projects" :key="key">
                                <div class="form-floating mb-3">
                                    <button class="btn btn-sm btn-outline-danger border-0 shadow-none position-absolute end-0" @click="removeProject(fp)"><i class="fas fa-times"></i></button>
                                    <select class="form-select" id="floatingSelect" @change="changeProject(fp)" v-model="fp.project_id">
                                        <option value="" hidden disabled selected>Select Project</option>
                                        <template v-for="project in projects" :key="project.id">
                                            <option :value="project.id" v-if="!used.includes(project.id) || project.id == fp.project_id">{{project.title}}</option>
                                        </template>
                                    </select>
                                    <label for="floatingSelect">Project</label>
                                </div>
                                <div class="d-flex flex-wrap px-2" v-if="fp.subprojects.length > 0">
                                    <div class="w-100"><strong>Subprojects</strong></div>
                                    <div class="form-check w-50" v-for="subproject in fp.subprojects" :key="subproject.id">
                                        <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                                        <label class="form-check-label" for="flexCheckDefault">
                                            {{subproject.title}}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
                <div class="modal-footer">
                    <button class="btn bg-gradient shadow-none btn-outline-primary rounded-circle" id="circle-btn" @click="formpart = 1">1</button>
                    <button class="btn bg-gradient shadow-none rounded-circle" id="circle-btn" @click="nextPart()" :class="formpart <= 1 ? 'btn-outline-secondary' : 'btn-outline-primary'">2</button>
                    <button class="btn bg-gradient shadow-none rounded-pill" :class="formpart <= 1 ? 'btn-secondary' : 'btn-primary'" style="min-width: 100px" @click="formpart <= 1 ? nextPart() : submitForm()">{{formpart <= 1 ? 'Next' : 'Submit'}}</button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { mapActions, mapGetters } from 'vuex'
export default {
    name: 'Form',
    emits: ['clicked'],
    setup({emit}){},
    data(){
        return {
            editmode: false,
            formshow: false,
            form: {
                program_id: '',
                subprogram_id: '',
                cluster_id: '',
                division_id: '',
                unit_id: '',
                subunit_id: '',
                projects: []
            },
            subprograms: [],
            clusters: [],
            units: [],
            subunits: [],
            formpart: 1,
            used: []
        }
    },
    methods: {
        ...mapActions('workshop', ['fetchOptions']),
        newForm(){
            this.form.program_id = ''
            this.form.subprogram_id = ''
            this.form.cluster_id = ''
            this.form.division_id = ''
            this.form.unit_id = ''
            this.form.subunit_id = ''
            this.form.projects = []
            this.subprograms = []
            this.clusters = []
            this.units = []
            this.subunits = []
            this.formpart = 1
        },
        addProject(){
            this.form.projects.push({
                id: '',
                project_id: '',
                subprojects: [],
                subprojectIds: []
            })
        },
        removeProject(formproject){
            this.form.projects.remove(formproject)
        },
        submitForm(){
            alert('form submitted')
        },
        hideForm(){
            this.formshow = false
            this.childClick()
        },
        childClick(){
            this.$emit('clicked')
        },
        nextPart(){
            if(this.form.division_id == ''){ this.toastMsg('warning', 'Please select a Division'); return false }
            if(this.form.program_id == ''){ this.toastMsg('warning', 'Please select a Program'); return false }
            this.formpart++
        },
        setColumn(type){
            var array  = type == 'program' || type == 'subprogram' ? this.subprograms : this.units
            var array2 = type == 'program' || type == 'subprogram' ? this.clusters    : this.subunits
            if(type == 'program' || type == 'division'){
                return array.length == 0 ? 'col-sm-12' : array2.length == 0 ? 'col-sm-6' : 'col-sm-4'
            }
            if(type == 'subprogram' || type == 'unit'){
                return array2.length == 0 ? 'col-sm-6' : 'col-sm-4'
            }
        },
        checkSelectedProject(){
            for(let project of this.form.projects){
                
            }
        },
        changeProject(formproject){
            var projects = this.editmode ? this.usedprojects : this.projects
            var projectId = formproject.project_id
            var project = projects.find(elem => elem.id == projectId)
            formproject.subprojects = project.subprojects
            if(!this.used.includes(projectId)){
                this.used.push(projectId)
            }
            this.checkSelectedProject()
        },
        changeProgram(){
            var program = this.programs.find(elem => elem.id == this.form.program_id)
            this.subprograms = program.subprograms
            this.form.subprogram_id = ''
            this.form.cluster_id = ''
        },
        changeSubprogram(){
            var subprogram = this.subprograms.find(elem => elem.id == this.form.subprogram_id)
            this.clusters = subprogram.clusters
            this.form.cluster_id = ''
        },
        changeDivision(){
            var division = this.divisions.find(elem => elem.id == this.form.division_id)
            this.units = division.units
            this.subunits = []
            this.form.unit_id = ''
            this.form.subunit_id = ''
        },
        changeUnit(){
            var unit = this.units.find(elem => elem.id == this.form.unit_id)
            this.subunits = unit.subunits
            this.form.subunit_id = ''
        },
        toastMsg(icon, msg){
            toast.fire({
                icon: icon,
                title: msg
            })
        },
    },
    computed:{
        ...mapGetters('workshop', ['getOptions']),
        divisions(){ return this.getOptions.divisions },
        programs(){ return this.getOptions.programs },
        projects(){ return this.getOptions.projects },
        usedprojects(){ return this.getOptions.used_projects }
    },
    created(){
        if(this.getOptions.length == 0){
            this.fetchOptions({workshopId: this.$route.params.workshopId, annex: 'one'})
        }

    }
}
</script>
<style scoped>
#project-list{
    /* background: red; */
    position: relative;
    height: calc(100vh - 242px);
}
#circle-btn{
    width: 40px;
    padding: 6px;
    font-weight: bold;
}
</style>