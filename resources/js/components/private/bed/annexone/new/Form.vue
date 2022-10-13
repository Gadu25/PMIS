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
                <tbody>
                    <template v-for="sources, div in annexones" :key="div">
                        <tr class="fw-bold" style="background: orange">
                            <td colspan="2">{{div}}</td>
                        </tr>
                        <template v-for="headers, source in sources" :key="source">
                            <tr v-if="div != 'STSD'">
                                <td colspan="2">{{source}}</td>
                            </tr>
                            <template v-for="annexones, header in headers" :key="header">
                                <tr class="fw-bold">
                                    <td colspan="2">{{header}}</td>
                                </tr>
                                <template v-for="annexone in annexones" :key="annexone.id">
                                    <tr>
                                        <td><div class="ms-1">{{annexone.project.title}}</div></td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-primary me-1" @click="editForm(annexone)"><i class="far fa-pencil-alt"></i></button>
                                            <button class="btn btn-sm btn-danger"><i class="far fa-trash-alt"></i></button>
                                        </td>
                                    </tr>
                                </template>
                            </template>
                        </template>
                        
                    </template>
                </tbody>
            </table>
        </div>
    </div>
    <div v-else>
        <div class="d-flex justify-content-between mb-2">
            <button class="btn btn-sm btn-outline-secondary" @click="hideForm()"><i class="fas fa-times"></i> Close</button>
            <button class="btn btn-sm btn-primary"><i class="fas fa-save"></i> Save changes</button>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-bordered">
                <TableHead />
            </table>
        </div>
    </div>
    <div class="modal fade" id="modal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{formpart <= 1 ? '1. Project Filters' : '2. Annex 1 Details'}}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" ref="Close"></button>
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
                                        <option :value="null">Not Applicable</option>
                                    </select>
                                    <label for="Unit">Unit</label>
                                </div>
                            </div>
                            <div class="col-sm-4" v-if="subunits.length > 0">
                                <div class="form-floating mb-3">
                                    <select class="form-select" id="SubUnit" v-model="form.subunit_id">
                                        <option value="" selected hidden disabled>Select Sub-Unit</option>
                                        <option :value="subunit.id" v-for="subunit in subunits" :key="subunit.id">{{subunit.name}}</option>
                                        <option :value="null">Not Applicable</option>
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
                                        <option :value="null">Not Applicable</option>
                                    </select>
                                    <label for="subProgram">Sub-Program</label>
                                </div>
                            </div>
                            <div class="col-sm-4" v-if="clusters.length > 0">
                                <div class="form-floating mb-3">
                                    <select class="form-select" id="cluster" v-model="form.cluster_id">
                                        <option value="" selected hidden disabled>Select Cluster</option>
                                        <option :value="cluster.id" v-for="cluster in clusters" :key="cluster.id">{{cluster.title}}</option>
                                        <option :value="null">Not Applicable</option>
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
                                    <select class="form-select" id="Source" v-model="form.source">
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
                                    <select class="form-select" id="headerType" v-model="form.headerType">
                                        <option value="" selected hidden disabled>Select Header Type</option>
                                        <option value="Subprogram">Subprogram</option>
                                        <option value="None">None</option>
                                        <option value="Unit">Unit</option>
                                    </select>
                                    <label for="headerType">Header Type</label>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mb-2" >
                            <div id="tooltip">
                                <i class="far fa-question-circle fa-lg"></i>
                                <span id="tooltiptext">Projects shown on select options are based on selected filters during the first part of the form. Check your selected project filters or contact your System Administrator for further clarifications.</span>
                            </div>
                            <button class="btn btn-sm btn-success bg-gradient" @click="addProject()"><i class="fas fa-plus"></i> Project</button>
                        </div>
                        <div class="overflow-auto" style="max-height: 50vh;">
                            <div class="position-relative" v-for="fp, key in form.projects" :key="key">
                                <div class="form-floating mb-3">
                                    <button class="btn btn-sm btn-outline-danger border-0 shadow-none position-absolute end-0" @click="removeProject(fp)"><i class="fas fa-times"></i></button>
                                    <select class="form-select" id="floatingSelect" @change="changeProject(fp)" v-model="fp.project_id">
                                        <option value="" hidden disabled selected>Select Project</option>
                                        <template v-for="project in projects" :key="project.id">
                                            <option :value="project.id" v-if="formMatchProject(project) && !used.includes(project.id) || project.id == fp.project_id">{{project.title}}</option>
                                        </template>
                                    </select>
                                    <label for="floatingSelect">Project</label>
                                </div>
                                <div class="d-flex flex-wrap px-2" v-if="fp.subprojects.length > 0">
                                    <div class="w-100"><strong>Subprojects</strong></div>
                                    <div class="form-check w-50" v-for="subproject in fp.subprojects" :key="subproject.id">
                                        <input class="form-check-input" type="checkbox" :value="subproject.id" :id="subproject.title" v-model="fp.subprojectIds">
                                        <label class="form-check-label" :for="subproject.title">
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
import { dragscroll } from 'vue-dragscroll'
import TableHead from './TableHead.vue'
export default {
    name: 'Form',
    emits: ['clicked'],
    setup({emit}){},
    directives: {
        dragscroll: dragscroll,
    },
    components: {
        TableHead
    },
    data(){
        return {
            editmode: false,
            formshow: false,
            form: {
                id: '',
                program_id: '',
                subprogram_id: '',
                cluster_id: '',
                division_id: '',
                unit_id: '',
                subunit_id: '',
                projects: [],
                source: '',
                headerType: '',
                workshop_id: this.$route.params.workshopId
            },
            subprograms: [],
            clusters: [],
            units: [],
            subunits: [],
            formpart: 1,
            used: [],
            processing: false,
            form2:{
                id: '',

            }
        }
    },
    methods: {
        ...mapActions('workshop', ['fetchOptions']),
        ...mapActions('annexone', ['fetchAnnexOnes', 'saveAnnexOne']),
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
            this.used = []
            this.formpart = 1
        },
        editForm(annexone){
            this.formshow = true
            this.childClick()
            // console.log(annexone)
        },
        addProject(){
            this.form.projects.push({
                id: '',
                project_id: '',
                projectTitle: '',
                subprojects: [],
                subprojectIds: []
            })
        },
        removeProject(formproject){
            this.form.projects.remove(formproject)
            this.used.remove(formproject.project_id)
        },
        async formValidated(form){
            const promise = await new Promise((resolve, reject) => {
                var time = 0
                setTimeout(async () => {
                    var start = new Date().getTime();
                    
                    if(form.source == ''){ reject('Source of Funds required') }
                    if(form.headerType == ''){ reject('Header type required') }

                    var projects = form.projects
                    if(projects.length == 0){ reject('Please add at least one (1) Project') }
                    for(let project of projects){
                        if(project.project_id == ''){ reject('Empty Project found, Please select a Project or remove the Select Field')}
                        if(project.subprojects.length > 0){
                            if(project.subprojectIds.length == 0){
                                var message = project.projectTitle + ' have Sub-Projects yet none is selected. Continue?'
                                await this.swalConfirmCancel('Sub-Projects', message).then(res => {
                                    if(!res){
                                        reject('Cancelled')
                                    }
                                })

                            }
                        }
                    }

                    var end = new Date().getTime();
                    time = end - start;
                    resolve('Validated')
                }, time)
            })
            return promise
        },
        async submitForm(){
            this.processing = true
            await this.formValidated(this.form)
                .then(res => {
                    this.toastMsg('success', res)
                    this.saveAnnexOne(this.form).then(res => {
                        var icon = res.errors ? 'error' : 'success'
                        this.toastMsg(icon, res.message)
                        this.processing = false
                        if(!res.errors){
                            this.$refs.Close.click()
                        }
                    })
                })
                .catch((err) => {
                    var icon = err == 'Cancelled' ? 'info' : 'warning'
                    var msg  = err == 'Cancelled' ? 'Form submission cancelled' : err
                    this.toastMsg(icon, msg)
                    this.processing = false
                })
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
        formMatchProject(project){
            var projectIds = {
                division_id:   project.division_id,
                unit_id:       project.unit_id,
                subunit_id:    project.subunit_id,
                program_id:    project.program_id,
                subprogram_id: project.subprogram_id,
                cluster_id:    project.cluster_id
            }
            var form = this.form
            var formIds = {
                division_id:   form.division_id,
                unit_id:       form.unit_id       === '' ? null : form.unit_id,
                subunit_id:    form.subunit_id    === '' ? null : form.subunit_id,
                program_id:    form.program_id,
                subprogram_id: form.subprogram_id === '' ? null : form.subprogram_id,
                cluster_id:    form.cluster_id    === '' ? null : form.cluster_id
            }
            
            return JSON.stringify(projectIds) === JSON.stringify(formIds)
        },
        checkSelectedProject(){
            for(let projectId of this.used){
                var project = this.form.projects.find(elem => elem.project_id == projectId)
                if(!project){
                    this.used.remove(projectId)
                }
            }
        },
        changeProject(formproject){
            var projects = this.editmode ? this.usedprojects : this.projects
            var projectId = formproject.project_id
            var project = projects.find(elem => elem.id == projectId)
            formproject.projectTitle = project.title
            formproject.subprojects = project.subprojects
            if(!this.used.includes(projectId)){
                this.used.push(projectId)
            }
            this.checkSelectedProject()
        },
        changeProgram(){
            var program = this.programs.find(elem => elem.id == this.form.program_id)
            this.subprograms = program.subprograms
            this.clusters = []
            this.form.subprogram_id = ''
            this.form.cluster_id = ''
        },
        changeSubprogram(){
            this.clusters = []
            this.form.cluster_id = ''
            var subprogram = this.subprograms.find(elem => elem.id == this.form.subprogram_id)
            if(subprogram){ this.clusters = subprogram.clusters }
        },
        changeDivision(){
            this.subunits = []
            this.form.unit_id = ''
            this.form.subunit_id = ''
            var division = this.divisions.find(elem => elem.id == this.form.division_id)
            this.units = division.units
        },
        changeUnit(){
            this.form.subunit_id = ''
            this.subunits = []
            var unit = this.units.find(elem => elem.id == this.form.unit_id)
            if(unit){ this.subunits = unit.subunits }
        },
        toastMsg(icon, msg){
            toast.fire({
                icon: icon,
                title: msg
            })
        },
        async swalConfirmCancel(title, message){
            const result = await swal.fire({
                title: title,
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Continue!',
                reverseButtons: true
                }).then((result) => {
                    return result.isConfirmed
                })
            return result
        },
    },
    computed:{
        ...mapGetters('workshop', ['getOptions']),
        ...mapGetters('annexone', ['getAnnexOnes']),
        divisions(){ return this.getOptions.divisions },
        programs(){ return this.getOptions.programs },
        projects(){ return this.getOptions.projects },
        usedprojects(){ return this.getOptions.used_projects },
        annexones(){ return this.getAnnexOnes }
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

#tooltip {
  position: relative;
  display: inline-block;
}
#tooltip #tooltiptext {
  visibility: hidden;
  width: 300px;
  background-color: rgba(0, 72, 215, 0.8);
  color: #fff;
  text-align: center;
  padding: 8px;
  border-radius: 0.75em;
  display: none;
  box-shadow: rgba(0, 0, 0, 0.17) 0px -23px 25px 0px inset, rgba(0, 0, 0, 0.15) 0px -36px 30px 0px inset, rgba(0, 0, 0, 0.1) 0px -79px 40px 0px inset, rgba(0, 0, 0, 0.06) 0px 2px 1px, rgba(0, 0, 0, 0.09) 0px 4px 2px, rgba(0, 0, 0, 0.09) 0px 8px 4px, rgba(0, 0, 0, 0.09) 0px 16px 8px, rgba(0, 0, 0, 0.09) 0px 32px 16px;
  /* Position the tooltip text - see examples below! */
  position: absolute;
  z-index: 1;
  left: 25px;
  top: -10px; 
}

/* Show the tooltip text when you mouse over the tooltip container */
#tooltip:hover #tooltiptext {
  visibility: visible;
  display: block;
  transition: all 0.3s ease-in;
}
</style>