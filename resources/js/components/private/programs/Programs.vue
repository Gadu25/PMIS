<template>
    <template v-if="!childSelected">
        <div class="px-3 py-4" v-if="!loading">
            <h2 class="text-center">Programs and Projects</h2><hr>
            <div id="main-container">
                <template v-if="!manageprojects">
                    <div class="d-flex justify-content-end">
                        <button class="btn btn-sm btn-outline-secondary" @click="manageprojects = true"><i class="far fa-cog"></i> Manage Projects</button>
                    </div>
                    <div v-for="program in programs" :key="program.id+'_program'">
                        <h3><router-link @click="childSelected = true" :to="{ name: 'Projects', params: { selected: program.title } }">{{program.title}} </router-link></h3>
                        <div class="row flex-wrap px-2">
                            <div class="col-md-6 px-3 pb-3" v-for="subprogram in program.subprograms" :key="subprogram.id+'_subprogram'">
                                <div class="card darkBlue shadow">
                                    <div class="card-body">
                                        <div v-if="subprogram.clusters.length > 0">
                                            <h6><router-link v-bind:style="{'color': '#F5F5F5'}" @click="childSelected = true" :to="{ name: 'Projects', params: { selected: subprogram.title } }"><strong>{{subprogram.title}}</strong></router-link></h6>
                                            <hr>
                                            <div  class="ms-3" v-for="cluster in subprogram.clusters" :key="cluster.id+'_cluster'">
                                                <h6><router-link v-bind:style="{'color': '#F5F5F5'}" @click="childSelected = true" :to="{ name: 'Projects', params: { selected: cluster.title } }">- {{cluster.title}}</router-link></h6>
                                            </div>
                                        </div>
                                        <div v-else class="row h-100 justify-content-center align-items-center">
                                            <h3><router-link v-bind:style="{'color': '#F5F5F5'}" @click="childSelected = true" :to="{ name: 'Projects', params: { selected: subprogram.title } }"><strong>{{subprogram.title}}</strong></router-link></h3>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>    
                        <hr>
                    </div>
                </template>
                <div v-else>
                    <div class="d-flex justify-content-center">
                        <div class="col-sm-12">
                            <div class="px-2 py-4" v-if="formshow">
                                <div class="mb-2"><h4>Form</h4></div>
                                <div class="overflow-auto p-2" style="height: calc(100vh - 274px); overflow-x: hidden !important">
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
                                                <select class="form-select" id="Subunit" v-model="form.subunit_id" @change="subuChange()">
                                                    <option value="" selected hidden disabled>Select Sub-Unit</option>
                                                    <option :value="subunit.id" v-for="subunit in subunits" :key="subunit.id+'_subuOption'">{{subunit.name}}</option>
                                                    <option value="0">N/A</option>
                                                </select>
                                                <label for="Subunit">Sub-Unit</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end mb-2" v-if="form.id == ''">
                                        <button tabindex="-1" class="btn btn-sm btn-secondary" @click="addProject()" title="Add"><i class="fas fa-plus"></i> Project</button>
                                    </div>
                                    <div class="form-group row mb-3">
                                        <div :class="form.id ? 'col-sm-12' : 'col-sm-6'" class="mb-3" v-for="project, key in form.projects" :key="key+'_title'">
                                            <div class="card shadow">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <strong>Project</strong>
                                                        <div>
                                                            <button tabindex="-1" class="btn btn-sm btn-secondary me-1" @click="addSubproject(key)" title="Add"><i class="fas fa-plus"></i></button>
                                                            <button tabindex="-1" class="btn btn-sm btn-danger" @click="removeProject(project)" v-if="form.id == ''"><i class="fas fa-trash-alt"></i> </button>
                                                        </div>
                                                    </div>
                                                    <div class="form-floating mb-3">
                                                        <input type="text" class="form-control" id="floatingInput" placeholder="text" v-model="project.title">
                                                        <label for="floatingInput">Project  Title</label>
                                                    </div>
                                                    <strong v-if="project.subprojects.length > 0">Sub-Project/s</strong>
                                                    <div class="form-floating ms-4 mb-3 position-relative" v-for="subp, skey in project.subprojects" :key="skey+'_subproject'">
                                                        <button tabindex="-1" class="btn btn-outline-danger border-0 rounded-0 h-100 btn-sm position-absolute top-0 end-0" @click="removeSubproject(key, subp)"><i class="far fa-times"></i></button>
                                                        <input type="text" class="form-control" id="floatingInput" placeholder="text" v-model="subp.title">
                                                        <label for="floatingInput">Sub-Project Title</label>
                                                    </div>
                                                    <strong>Description</strong>
                                                    <div class="form-floating mb-3">
                                                        <textarea class="form-control" id="floatingInput" placeholder=" " style="height: 100px" v-model="project.description"></textarea>
                                                        <label for="floatingInput">Brief Description</label>
                                                    </div>
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <strong>Staff</strong>
                                                        <div>
                                                            <button tabindex="-1" class="btn btn-sm btn-success me-1" @click="addStaff(key)"><i class="fas fa-user-plus"></i></button>
                                                        </div>
                                                    </div>
                                                    <div class="form-floating mb-3 position-relative" :class="staff.type == 'Encoder' ? 'ms-4' : ''" v-for="staff, skey in project.staffs" :key="key+'_staff_'+skey">
                                                        <button class="btn btn-sm btn-outline-danger border-0 rounded-0 position-absolute h-100 end-0 top-0" @click="removeStaff(key, staff)" v-if="staff.type == 'Encoder'"><i class="far fa-times"></i></button>
                                                        <select class="form-control" v-model="staff.profile_id" @change="staffChange($event, key)">
                                                            <option :value="0" selected hidden disabled>Select {{((staff.type == 'Leader') ? 'Project' : '') + ' ' + staff.type }}</option>
                                                            <template v-for="units, division in users" :key="division+'_div'">
                                                                <template v-for="dbusers, unit in units" :key="division+'_'+unit+'_unit'">
                                                                    <template v-for="user in dbusers" :key="user.id+'_user'">
                                                                        <option 
                                                                            v-if="userMatchDivision(user, key) && !project.selectedStaffs.includes(getActiveProfileId(user.profiles)) || parseInt(staff.profile_id) == getActiveProfileId(user.profiles)" 
                                                                            :value="getActiveProfileId(user.profiles)">
                                                                            {{user.firstname + ' ' +user.lastname }} {{setTitleShort(user.profiles)}}
                                                                        </option>
                                                                    </template>
                                                                </template>
                                                            </template>
                                                        </select>
                                                        <label for="floatingInput">{{((staff.type == 'Leader') ? 'Project' : '') + ' ' + staff.type }}</label>
                                                    </div>
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
                            <div v-else>
                                <div class="row flex-row-reverse">
                                    <div class="col-sm-3">
                                        <div class="card shadow mb-3">
                                            <div class="card-body">
                                                <div class="form-floating mb-3">
                                                    <select class="form-select" id="DisplayType" v-model="displaytype" @change="displayTypeChange()">
                                                        <option value="division">Division</option>
                                                        <option value="program">Program</option>
                                                    </select>
                                                    <label for="DisplayType">Type</label>
                                                </div>
                                                <template v-if="displaytype == 'division'">
                                                    <div class="form-floating mb-3">
                                                        <select class="form-select" id="Division">
                                                            <option :value="0">Any Division</option>
                                                            <option :value="division.id" v-for="division in divisions" :key="division.id+'_divOption'">{{division.name}}</option>
                                                        </select>
                                                        <label for="Division">Division</label>
                                                    </div>
                                                </template>
                                                <template v-else>
                                                    <div class="form-floating mb-3">
                                                        <select class="form-select" id="Program">
                                                            <option :value="0">Any Program</option>
                                                            <option :value="program.id" v-for="program in programs" :key="program.id+'_divOption'">{{program.title}}</option>
                                                        </select>
                                                        <label for="Program">Program</label>
                                                    </div>
                                                </template>
                                                <div class="d-flex justify-content-end">
                                                    <button class="btn btn-sm btn-primary">Sync <i class="fas fa-sync"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-9">
                                        <div class="d-flex justify-content-between mb-2">
                                            <button class="btn btn-sm btn-outline-secondary" @click="manageprojects = false"><i class="fas fa-arrow-left"></i></button>
                                            <button class="btn btn-sm btn-success" @click="resetForm()" title="Add"><i class="fas fa-plus"></i></button>
                                        </div>
                                        <div class="table-container">
                                            <table class="table table-sm table-bordered table-hover align-middle rounded">
                                                <caption>List of Project Titles</caption>
                                                <thead class="position-sticky bg-white shadow" style="top: -1px;">
                                                    <tr>
                                                        <th>Project Title</th>
                                                        <th style="min-width: 120px">Staff/s</th>
                                                        <th style="min-width: 80px">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <template v-for="first, fkey in projects" :key="fkey+'_first'">
                                                        <tr>
                                                            <td colspan="3" :class="displaytype == 'program' ? 'bg-success text-white' : 'yellow'"><strong>{{fkey}}</strong></td>
                                                        </tr>
                                                        <template v-for="second, skey in first" :key="skey+'_second'">
                                                            <tr v-if="skey !=''">
                                                                <td colspan="3"><div class="ms-2"><strong>{{skey}}</strong></div></td>
                                                            </tr>
                                                            <template v-for="third, tkey in second" :key="tkey+'_third'">
                                                                <tr v-if="tkey != ''">
                                                                    <td colspan="3"><div class="ms-3"><strong>{{tkey}}</strong></div></td>
                                                                </tr>
                                                                <template v-for="project in third" :key="project.id+'_project'">
                                                                    <tr>
                                                                        <td><div class="ms-4">{{project.title}}</div></td>
                                                                        <td>{{project.project_leader}}
                                                                        </td>
                                                                        <td class="text-center">
                                                                            <button @click="editForm(project)" class="btn btn-sm btn-primary me-1" title="Edit"><i class="far fa-pencil-alt"></i></button>
                                                                            <!-- <button class="btn btn-sm btn-danger" title="Delete"><i class="far fa-trash-alt"></i></button> -->
                                                                            <button class="btn btn-sm btn-danger me-1 border border-secondary" data-bs-target="#deletePrompt" data-bs-toggle="modal" title="Delete" @click="openDeleteModal(project.id)"><i class="far fa-trash-alt"></i></button>

                                                                        </td>
                                                                    </tr>
                                                                    <tr v-for="subproject in project.subprojects" :key="subproject.id+'_subproj'">
                                                                        <td colspan="3"><div class="ms-5">{{subproject.title}}</div></td>
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
                </div>

                <div class="modal fade" id="deletePrompt" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Deleting</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body my-3">
                            <div class="text-center">
                                <i class="fa fa-exclamation-triangle fa-5x" style="color: #ED553B"></i>
                                <h5 class="my-4">Are you sure you want to delete this data?</h5>
                            </div>
                            <div class="d-flex justify-content-center mt-2">
                                <button type="button" class="btn rounded-pill min-100 btn-success mx-1 border border-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn rounded-pill min-100 btn-danger mx-1 border border-secondary" data-bs-dismiss="modal" @click="deleteData()">Yes</button>
                            </div>
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
            deleteData_id: 0,
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
        ...mapActions('project', ['fetchProjects', 'saveProject', 'deleteProject']),
        ...mapActions('division', ['fetchDivisions']),
        ...mapActions('user', ['fetchUsers']),
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
                projects: [{
                    title: '', 
                    num: this.setUID(), 
                    status: 'New', 
                    description: '',
                    subprojects: [], 
                    staffs: [{ id: '', profile_id: 0, type: 'Leader' }],
                    selectedStaffs: [] 
                }],
                tempIds: []
            }
            this.subprograms = []
            this.clusters = []
        },
        editForm(project){
            this.resetForm()
            var form = this.form
            form.id = project.id

            form.program_id = project.program_id
            this.progChange()
            form.subprogram_id = (project.subprogram_id) ? project.subprogram_id : 0
            this.subpChange()
            form.cluster_id = (project.cluster_id) ? project.cluster_id : 0

            form.division_id = project.division_id
            this.divChange()
            form.unit_id = (project.unit_id) ? project.unit_id : 0
            this.unitChange()
            form.subunit_id = (project.subunit_id) ? project.subunit_id : 0

            var formproject = form.projects[0]

            formproject.title = project.title
            formproject.description = project.description
            formproject.num = project.num
            formproject.status = project.status

            for(let i = 0; i < project.subprojects.length; i++){
                var subproject = project.subprojects[i]
                formproject.subprojects.push({id: subproject.id, title: subproject.title})
                form.tempIds.push(subproject.id)
            }

            formproject.selectedStaffs = []

            var leader = formproject.staffs[0]
            if(project.leader){
                leader.id = project.leader.id
                leader.profile_id = project.leader.profile_id
                formproject.selectedStaffs.push(leader.profile_id)
                for(let i = 0; i < project.encoders.length; i++){
                    var encoder = project.encoders[i]
                    formproject.staffs.push({
                        id: encoder.id,
                        profile_id: encoder.profile_id,
                        type: 'Encoder'
                    })
                    formproject.selectedStaffs.push(encoder.profile_id)
                }
            }
        },
        openDeleteModal(id){
            console.log("the id prompted: "+id)
            this.deleteData_id = id;
            console.log("deleteUser_id", this.deleteData_id)
            this.modalmode = 'delete'
        },
        deleteData(){
            if(this.deleteData_id != 0){
                console.log("DATA DELETE", this.deleteData_id)
                this.deleteProject(this.deleteData_id).then(res => {
                    var icon = res.errors ? 'error' : 'success'
                    this.toastMsg(icon, res.message)
                })
            }
        },
        submitForm(){
            if(this.formValidated()){
                this.saveProject(this.form).then(res => {
                    var icon = res.errors ? 'error' : 'success'
                    this.toastMsg(icon, res.message)
                    if(!res.errors){
                        this.formshow = false
                        this.displayTypeChange()
                    }
                })
            }
        },
        formValidated(){
            if(this.form.program_id === ''){ this.toastMsg('warning', 'Program Required'); return false }
            if(this.form.subprogram_id === ''){ this.toastMsg('warning', 'Sub-Program Required'); return false }
            if(this.clusters.length > 0 && this.form.cluster_id === ''){ this.toastMsg('warning', 'Cluster Required'); return false }
            if(this.form.division_id === ''){ this.toastMsg('warning', 'Division Required'); return false }
            if(this.form.unit_id === ''){ this.toastMsg('warning', 'Unit Required'); return false }
            if(this.subunits.length > 0 && this.form.subunit_id === ''){ this.toastMsg('warning', 'Sub-Unit Required'); return false }
            for(let i = 0; i < this.form.projects.length; i++){
                if(this.form.projects[i].title == ''){ this.toastMsg('warning', 'Project Title Required'); return false }
                for(let j = 0; j < this.form.projects[i].subprojects.length; j++){
                    if(this.form.projects[i].subprojects[j].title == ''){ this.toastMsg('warning', 'Sub-Project Title Required'); return false }
                }
                for(let j = 0; j < this.form.projects[i].staffs.length; j++){
                    var staff = this.form.projects[i].staffs[j]
                    var stafftype = staff.type == 'Leader' ? 'Project Leader' : 'Encoder'
                    if(staff.profile_id == 0){ this.toastMsg('warning', stafftype+' Required') }
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
                subprojects: [],
                staffs: [{ id: '', profile_id: 0, type: 'Leader' }],
                selectedStaffs: []
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
        addStaff(key){
            var project = this.form.projects[key]
            project.staffs.push({
                id: '',
                profile_id: 0,
                type: 'Encoder'
            })
        },
        removeStaff(key, staff){
            var project = this.form.projects[key]
            if(project.selectedStaffs.includes(staff.profile_id)){
                project.selectedStaffs.remove(staff.profile_id)
            }
            project.staffs.remove(staff)
        },
        staffChange(e, key){
            var id = parseInt(e.target.value)
            var project = this.form.projects[key]
            if(!project.selectedStaffs.includes(id)){
                project.selectedStaffs.push(id)
            }
            for(let i = 0; i < project.selectedStaffs.length; i++){
                var id = project.selectedStaffs[i]
                var staff = project.staffs.find(elem => elem.profile_id == id)
                if(!staff){
                    project.selectedStaffs.remove(id)
                }
            }
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
            this.resetStaffs()
        },
        unitChange(){
            this.form.subunit_id = ''
            var subunits = []
            if(this.form.unit_id != 0){
                var unit = this.units.find(elem => elem.id == this.form.unit_id)
                subunits = unit.subunits
            }
            this.subunits = subunits
            this.resetStaffs()
        },
        subuChange(){
            this.resetStaffs()
        },
        resetStaffs(){
            var projects = this.form.projects
            for(let i = 0; i < projects.length; i++){
                var project = projects[i]
                project.selectedStaffs = []
                var staffs = project.staffs
                for(let j = 0; j < staffs.length; j++){
                    var staff = staffs[j]
                    staff.profile_id = 0
                }
            }
        },
        userMatchDivision(user, key){
            var state = false

            var divId = (this.form.division_id) ? this.form.division_id : 0
            var unitId = (this.form.unit_id)    ? this.form.unit_id     : 0
            var subuId = (this.form.subunit_id) ? this.form.subunit_id  : 0

            var udivId = user.division_id
            var uunitId = (user.unit_id)    ? user.unit_id    : 0
            var usubuId = (user.subunit_id) ? user.subunit_id : 0

            var activeprofile = user.profiles.find(elem => elem.active == true)
            var title = activeprofile.title.name

            var project = this.form.projects[key]

            state = divId == udivId && 
                (unitId == uunitId || uunitId === 0) && 
                (subuId == usubuId || usubuId == 0) && 
                (title.includes('Unit') || title.includes('Leader') || title.includes('Chief')
                || (key == 0 && title.includes('Encoder')))

            return state
        },
        getActiveProfileId(profiles){
            var activeprofile = profiles.find(elem => elem.active == true)
            return activeprofile.id
        },
        setTitleShort(profiles){
            var activeprofile = profiles.find(elem => elem.active == true)
            return '('+activeprofile.title.name+')'
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
        },
        
    },
    computed: {
        ...mapGetters('program', ['getPrograms']),
        programs(){ return this.getPrograms },
        ...mapGetters('project', ['getProjects']),
        division_projects(){ return this.getProjects.division_group },
        program_projects(){ return this.getProjects.program_group },
        ...mapGetters('division', ['getDivisions']),
        divisions(){ return this.getDivisions },
        ...mapGetters('user', ['getUsers']),
        users(){ return this.getUsers },
    },
    created(){
        this.fetchPrograms().then(res => {
            this.fetchProjects().then(res => {
                if(this.divisions.length == 0){
                    this.fetchDivisions()
                }
                this.fetchUsers()
                this.projects = res.program_group
                this.loading = false
            })
        })
    },
    watch: {
        $route: {
            immediate: true,
            handler(to, from) {
                this.childSelected = (this.$route.name != 'Programs')
            }
        },
    },
}
</script>
<style scoped>
h3, h6, h5{
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
    max-height: calc(100vh - 180px);;
    overflow: auto;
}
th{
    text-align: center;
}

.content{
    overflow-x: hidden;
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
.card{
    border-radius: 8px;
}
.card-body{
    min-height: 150px;
    justify-content: left;
    padding: 2%;
    color: whitesmoke;
}
#main-container{
    height: calc(100vh - 135px);
    padding: 0 10px;
    overflow: auto;
    overflow-x: hidden;
}
</style>