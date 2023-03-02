<template>
    <div class="px-3 py-4">
        <h2 class="text-center color-darkBlue">User Management</h2><hr>
        <div class="main-container">
            <div class="card shadow overflow-auto">
                <div class="card-overlay" v-if="syncing">
                    <i class="fas fa-spinner fa-spin fa-5x"></i>
                </div>
                <div class="card-body">
                    <template v-if="!formshow">
                        <div class="d-flex justify-content-between mb-3">
                            <div class="d-flex">
                                <div class="form-floating">
                                    <select class="form-select" id="filterDivision" v-model="filterdivision_id">
                                        <option :value="0">All Division</option>
                                        <option :value="division.id" v-for="division in divisions" :key="'filterdiv_'+division.id">{{division.name}}</option>
                                    </select>
                                    <label for="filterDivision">Division</label>
                                </div>
                                <div class="d-flex align-items-center mx-2">
                                    <button @click="syncData()" class="btn btn-primary" title="Refresh"><i class="fas fa-sync"></i></button>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <button class="btn bg-gradient btn-secondary me-1" data-bs-target="#modal" data-bs-toggle="modal" @click="modalmode = 'roles'"><i class="fas fa-user-shield"></i></button>
                                <button class="btn bg-gradient btn-secondary me-1" data-bs-target="#modal" data-bs-toggle="modal" @click="modalmode = 'logs'"><i class="fas fa-user-clock"></i></button>
                                <button class="btn bg-gradient btn-success" @click="resetForm()" title="Add user"><i class="far fa-user-plus"></i></button>
                            </div>
                        </div>
                        <div class="table-responsive users">
                            <table class="table table-sm table-bordered shadow" style="min-width: 630px">
                                <thead class="position-sticky darkBlue text-white text-center" style="top: -1px">
                                    <tr>
                                        <th style="width: 30%">Name</th>
                                        <th style="width: 30%">Email</th>
                                        <th>Profile/s</th>
                                        <th style="width: 80px !important">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template v-for="division, divname in users" :key="'division_'+divname">
                                        <tr><td class="yellow px-3 py-2" colspan="4"><strong>{{divname}}</strong></td></tr>
                                        <template v-for="unit, unitname in division" :key="'unit_'+unitname">
                                            <tr v-if="unitname != ''"> <td colspan="4"><div class="ms-2 fw-bold">{{unitname}}</div></td> </tr>
                                            <tr class="align-middle" v-for="user in unit" :key="'user_'+user.id">
                                                <td><div class="ms-3">{{user.firstname}} {{user.lastname}}</div></td>
                                                <td>{{user.email}}</td>
                                                <td class="d-flex justify-content-center align-items-center">
                                                    <div class="text-nowrap" :class="profile.active ? 'mint bg-gradient px-2 py-1 my-1 rounded-pill text-center w-100' : ''" v-for="profile in user.profiles" :key="'profile_'+profile.id">
                                                        <strong class="text-white">{{profile.title.name}} {{profile.isOIC ? ', OIC' : ''}}</strong>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <button @click="editForm(user)" class="btn btn-sm btn-primary me-1 border border-secondary" title="Edit user"><i class="far fa-pencil-alt"></i></button>
                                                    <button class="btn btn-sm btn-danger me-1 border border-secondary" data-bs-target="#deletePrompt" data-bs-toggle="modal" title="Delete user" @click="openDeleteModal(user.id)"><i class="far fa-trash-alt"></i></button>
                                                </td>
                                            </tr>
                                        </template>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                        <!-- <strong class="position-absolute bottom-0">List of Users</strong> -->
                    </template>
                    <template v-else>
                        <button tabindex="-1" @click="formshow = false" class="btn btn-sm btn-danger float-end" title="Cancel Edit"><i class="fas fa-times"></i></button>
                        <button tabindex="-1" @click="changepassword = !changepassword; form.password = ''" v-if="form.id" class="btn shadow-none btn-sm me-1 float-end" :class="changepassword ? 'btn-secondary' : 'btn-outline-secondary'"><i class="fas fa-key"></i> {{changepassword ? 'Cancel' : ''}} Change Password</button>
                        <h4><strong>Form</strong></h4><hr>
                        <div class="form-container">
                            <div class="form-group row mb-3">
                                <div :class="'col-sm-'+((subunits.length > 0 ? 4 : units.length > 0 ? 6 : 12))">
                                    <div class="form-floating">
                                        <select class="form-control" id="Division" :disabled="synceddivision_id != 0" @change="divChange()" v-model="form.division_id">
                                            <option value="" selected hidden disabled>Select Division</option>
                                            <template v-for="division in divisions" :key="'division_'+division.id">
                                                <option :value="division.id" v-if="synceddivision_id == 0 || (synceddivision_id != 0 && synceddivision_id == division.id)">{{division.name}}</option>
                                            </template>
                                        </select>
                                        <label for="Division">Division</label>
                                    </div>
                                </div>
                                <div :class="'col-sm-'+((subunits.length > 0 ? 4 : 6))" v-if="units.length > 0">
                                    <div class="form-floating">
                                        <select class="form-control" id="Unit" @change="unitChange()" v-model="form.unit_id">
                                            <option value="" selected hidden disabled>Select Unit</option>
                                            <option :value="unit.id" v-for="unit in units" :key="'unit_'+unit.id">{{unit.name}}</option>
                                            <option :value="0">N/A</option>
                                        </select>
                                        <label for="Unit">Unit</label>
                                    </div>
                                </div>
                                <div class="col-sm-4" v-if="subunits.length > 0">
                                    <div class="form-floating">
                                        <select class="form-control" id="Subunit" v-model="form.subunit_id">
                                            <option value="" selected hidden disabled>Select Sub-Unit</option>
                                            <option :value="subunit.id" v-for="subunit in subunits" :key="'subunit_'+subunit.id">{{subunit.name}}</option>
                                            <option :value="0">N/A</option>
                                        </select>
                                        <label for="Subunit">Sub-Unit</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row mb-3">
                                <div class="col-sm-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="FirstName" placeholder="First Name" v-model="form.firstname">
                                        <label for="FirstName">First Name</label>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="LastName" placeholder="Last Name" v-model="form.lastname">
                                        <label for="LastName">Last Name</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="email" class="form-control" id="Email" placeholder="Email" v-model="form.email">
                                <label for="Email">Email</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="password" class="form-control" id="Password" placeholder="Password" :disabled="form.id != '' && !changepassword" v-model="form.password">
                                <label for="Password">Password</label>
                            </div><hr>
                            <button class="btn btn-sm btn-success float-end" tabindex="-1" @click="addProfile()"><i class="fas fa-plus"></i> Profile</button>
                            <h5 class="text-center">Profile/s</h5><hr>
                            <div class="row">
                                <div class="col-sm-6 mb-3" v-for="profile, key in form.profiles" :key="'profile_'+key">
                                    <div class="card bg-gradient" :class="profile.active ? 'yellow' : 'bg-light'">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-end" v-if="!profile.active">
                                                <button class="btn btn-sm btn-danger position-absolute top-0 end-0" @click="removeProfile(profile)"><i class="far fa-user-times"></i></button>
                                            </div>
                                            <div class="d-flex mb-1">
                                                <strong>#{{parseInt(key)+1}}</strong>
                                                <div class="form-check form-switch mx-3">
                                                    <input tabindex="-1" style="cursor: pointer;" v-model="profile.active" class="form-check-input shadow-none" type="checkbox" :id="'active_'+key" @change="activeStatusChange(key)">
                                                    <label style="cursor: pointer;" class="form-check-label" :for="'active_'+key">Active Profile</label>
                                                </div>
                                            </div>
                                            <div class="form-floating mb-2">
                                                <select class="form-control" id="Title" v-model="profile.title_id">
                                                    <option value="">Select Title</option>
                                                    <template v-for="title in titles" :key="'title_'+title.id">
                                                        <option v-if="title.name != 'Superadmin' || (title.name == 'Superadmin' && authuser.active_profile.title.name == 'Superadmin')" :value="title.id">{{title.name}}</option>
                                                    </template>
                                                </select>
                                                <label for="Title">Title</label>
                                            </div>
                                            <div class="form-floating mb-2">
                                                <select class="form-control" id="AccessLevel" v-model="profile.access_level">
                                                    <option value="User">User</option>
                                                    <option value="Admin">Admin</option>
                                                </select>
                                                <label for="AccessLevel">Access Level</label>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2">
                                                <button class="btn btn-sm btn-primary me-2" @click="modalmode = 'user', form.selectedKey = key" data-bs-target="#modal" data-bs-toggle="modal"><i class="far fa-cogs"></i> Roles & permissions</button>
                                                <div class="form-check form-switch">
                                                    <input style="cursor: pointer;" v-model="profile.isOIC" class="form-check-input shadow-none" type="checkbox" :id="'isoic_'+key">
                                                    <label style="cursor: pointer;" class="form-check-label" :for="'isoic_'+key">Officer In-Charge</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button class="btn rounded-pill" :class="form.id ? 'btn-primary' : 'btn-success'" style="min-width: 100px" @click="submitForm()">{{form.id ? 'Save Changes' : 'Submit'}}</button>
                        </div>
                    </template>
                </div>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="modal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">User {{modalmode == 'logs' ? 'Logs' : 'Roles'}}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <Logs v-if="modalmode == 'logs'" />
                        <Roles v-if="modalmode == 'roles'" />
                        <!-- User Roles -->
                        <div v-if="modalmode == 'user'">
                            <div class="mb-2">
                                <button v-if="!copyroles" @click="toggleCopy()" class="btn btn-sm btn-secondary mb-1">
                                    <div id="tooltip" class="px-2">
                                        <i class="far fa-question-circle"></i>
                                        <span id="tooltiptext">Copy roles from existing accounts</span>
                                    </div>
                                    Copy Roles
                                </button>
                                <template v-else>
                                    <div class="form-floating mb-2">
                                        <select class="form-control" id="CopyUser" v-model="copyuser">
                                            <template v-for="division, div in users" :key="div">
                                                <optgroup v-for="unit, unitname in division" :key="unitname" :label="(unitname != '' ? unitname : div)">
                                                    <option :value="user" v-for="user in unit" :key="user.id">{{setUserOption(user)}}</option>
                                                </optgroup>
                                            </template>
                                        </select>
                                        <label for="CopyUser">Copy Roles From</label>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        <button class="btn btn-sm btn-secondary me-1" @click="copyroles = !copyroles, copyuser = []">Cancel</button>
                                        <button class="btn btn-sm btn-success" @click="copyUser()">Copy</button>
                                    </div>
                                </template>
                            </div>
                            <div class="roles-container">
                                <template v-for="item in sidebaritems" :key="item.id+'-sidebaritem'">
                                    <div v-if="item.roles.length > 0 && form.selectedKey !== ''">
                                        <h6 class="text-center">{{item.name}}</h6>
                                        <div class="row border-bottom mb-2">
                                            <div class="col-sm-6" v-for="role in item.roles" :key="item.id+'-sidebarrole-'+role.id">
                                                <div class="form-check form-switch">
                                                    <input :value="role.id" class="form-check-input" type="checkbox" :id="role.code" v-model="form.profiles[form.selectedKey].roles">
                                                    <div class="form-check-label d-flex justify-content-between">
                                                        <label :for="role.code">{{role.title}}</label>
                                                        <div id="tooltip">
                                                            <i class="far fa-question-circle " :id="role.id"></i>
                                                            <span id="tooltiptext">{{role.description}}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            <div class="d-flex justify-content-end mt-2">
                                <button type="button" class="btn rounded-pill min-100 btn-primary" data-bs-dismiss="modal">Save</button>
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
                        <h5 class="modal-title" id="exampleModalLabel">Deleting User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body my-3">
                        <div class="text-center">
                            <i class="fa fa-exclamation-triangle fa-5x" style="color: #ED553B"></i>
                            <h5 class="my-4">Are you sure you want to delete this user?</h5>
                        </div>
                        <div class="d-flex justify-content-center mt-2">
                            <button type="button" class="btn rounded-pill min-100 btn-success mx-1 border border-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn rounded-pill min-100 btn-danger mx-1 border border-secondary" data-bs-dismiss="modal" @click="deleteU()">Yes</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>
<script>
import Roles from './Roles.vue'
import Logs from './Logs.vue'
import { mapActions, mapGetters } from 'vuex'
export default {
    name: 'Users',
    components: {
        Roles, Logs
    },
    data(){
        return {
            filterdivision_id: 0,
            synceddivision_id: 0,
            deleteUser_id: 0,
            syncing: false,
            formshow: false,
            copyroles: false,
            units: [],
            subunits: [],
            changepassword: false,
            form: {
                id: '',
                firstname: '',
                lastname: '',
                email: '',
                password: '',
                division_id: '',
                unit_id: '',
                subunit_id: '',
                profiles: [{
                    id: '',
                    title_id: '',
                    active: true,
                    isOIC: false,
                    access_level: 'User',
                    roles: []
                }],
                isSynced: false,
                selectedKey: ''
            },
            modalmode: 'logs',
            copyuser: []
        }
    },
    methods: {
        ...mapActions('user', ['fetchUsers', 'fetchTitles', 'saveUser', 'deleteUser', 'fetchUsersByDivision']),
        ...mapActions('division', ['fetchDivisions']),
        ...mapActions('roles', ['fetchSidebarItems']),
        syncData(){
            this.syncing = true
            if(this.filterdivision_id == 0){
                this.fetchUsers().then(res => {
                    this.syncing = false
                    this.synceddivision_id = 0
                })
            }
            else{
                this.fetchUsersByDivision(this.filterdivision_id).then(res => {
                    this.syncing = false
                    this.synceddivision_id = this.filterdivision_id
                })
            }
        },
        resetForm(){
            this.formshow = true
            this.units = []
            this.subunits = []
            this.form.id = ''
            this.form.firstname = ''
            this.form.lastname = ''
            this.form.email = ''
            this.form.password = ''
            this.form.division_id = (this.synceddivision_id != 0) ? this.synceddivision_id : ''
            this.divChange()
            this.form.unit_id = ''
            this.form.subunit_id = ''
            this.form.profiles = [{
                    id: '',
                    title_id: '',
                    active: true,
                    isOIC: false,
                    access_level: 'User',
                    roles: []
                }]
            this.form.isSynced = (this.synceddivision_id != 0)
            this.form.selectedKey = ''
        },
        editForm(user){
            this.resetForm()
            this.form.id = user.id
            this.form.firstname = user.firstname
            this.form.lastname = user.lastname
            this.form.email = user.email
            this.form.division_id = user.division_id
            this.divChange()
            this.form.unit_id = (user.unit_id) ? user.unit_id : 0
            this.unitChange()
            this.form.subunit_id = (user.subunit_id) ? user.subunit_id : 0
            this.form.profiles = []
            for(let i = 0; i < user.profiles.length; i++){
                var profile = user.profiles[i]
                var roles = []
                for(let j = 0; j < profile.roles.length; j++){
                    roles.push(profile.roles[j].id)
                }
                var temp = {
                    id: profile.id,
                    title_id: profile.title_id,
                    active: profile.active,
                    isOIC: profile.isOIC,
                    access_level: profile.access_level,
                    roles: roles
                }
                this.form.profiles.push(temp)
            }
        },
        activeStatusChange(key){
            for(let i = 0; i < this.form.profiles.length; i++){
                this.form.profiles[i].active = (i == key)
            }
        },
        addProfile(){
            var profile = {
                id: '',
                title: '',
                active: false,
                isOIC: false,
                access_level: 'User',
                roles: []
            }
            this.form.profiles.push(profile)
        },
        removeProfile(profile){
            this.form.selectedKey = ''
            this.form.profiles.remove(profile)
        },
        submitForm(){
            if(this.formValidated()){
                this.saveUser(this.form).then(res => {
                    var icon = res.errors ? 'error' : 'success'
                    this.toastMsg(icon, res.message)
                    if(!res.errors){
                        this.formshow = false
                    }
                })
            }
        },
        openDeleteModal(id){
            console.log("the id prompted: "+id)
            this.deleteUser_id = id;
            console.log("deleteUser_id", this.deleteUser_id)
            this.modalmode = 'delete'
        },
        deleteU(){
            if(this.deleteUser_id != 0){
                console.log("USER DELETE", this.deleteUser_id)
                this.deleteUser(this.deleteUser_id).then(res => {
                    var icon = res.errors ? 'error' : 'success'
                    this.toastMsg(icon, res.message)
                })
            }
        },
        formValidated(){
            if(this.form.division_id == ''){ this.toastMsg('warning', 'Division Required'); return false }
            if(this.form.firstname == ''){ this.toastMsg('warning', 'First Name Required'); return false }
            if(this.form.lastname == ''){ this.toastMsg('warning', 'Last Name Required'); return false }
            if(this.form.email == ''){ this.toastMsg('warning', 'Email Required'); return false }
            if(this.validateEmail(this.form.email) === null){ this.toastMsg('warning', 'Invalid Email Address'); return false }
            if(!this.form.id){ // New
                if(this.form.password == ''){ this.toastMsg('warning', 'Password Required'); return false }
                if(this.form.password.length < 7){ this.toastMsg('warning', 'Password too short. Minimum of (8) eight characters'); return false }
            }
            if(this.changepassword){ // Update
                if(this.form.password == ''){ this.toastMsg('warning', 'Password Required'); return false }
                if(this.form.password.length < 7){ this.toastMsg('warning', 'Password too short. Minimum of (8) eight characters'); return false }
            }
            var unithead = this.titles.find(elem => elem.name.includes('Unit Head'))
            for(let i = 0; i < this.form.profiles.length; i++){
                var profile = this.form.profiles[i]
                if(profile.title_id == ''){ this.toastMsg('warning', 'Title Required'); return false }
                if(profile.title_id == unithead.id && (this.form.unit_id == '' || this.form.unit_id == 0)){
                    this.toastMsg('warning', 'Unit Head must have a selected Unit'); return false
                }
            }
            return true
        },
        validateEmail(email){
            return email.toLowerCase().match(/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/)
        },
        divChange(){
            this.units = []
            this.subunits = []
            this.form.unit_id = ''
            this.form.subunit_id = ''
            if(this.form.division_id > 0){
                var division = this.divisions.find(elem => elem.id == this.form.division_id)
                this.units = division.units
            }
        },
        unitChange(){
            this.subunits = []
            this.form.subunit_id = ''
            if(this.form.unit_id > 0){
                var unit = this.units.find(elem => elem.id == this.form.unit_id)
                this.subunits = unit.subunits
            }
        },
        setUserOption(user){
            var name = ''
            name = user.firstname+' '+user.lastname +' ('+user.active_profile.title.name+')'
            return name
        },
        toggleCopy(){
            this.copyroles = !this.copyroles
        },
        copyUser(){
            var form = this.form
            this.form.profiles[form.selectedKey].roles = []
            var roles = this.copyuser.active_profile.roles
            for(let i = 0; i < roles.length; i++){
                var role = roles[i]
                this.form.profiles[form.selectedKey].roles.push(role.id)
            }
            // console.log("after for loop", this.form.profiles[form.selectedKey])
        },
        // Toast
        toastMsg(icon, msg){
            toast.fire({
                icon: icon,
                title: msg
            })
        },
    },
    created(){
        if(this.divisions.length == 0){
            this.fetchDivisions()
        }
        if(this.titles.length == 0){
            this.fetchTitles()
        }
        this.syncing = true
        this.fetchUsers().then(res => {
            this.syncing = false
        })
        if(this.sidebaritems.length == 0){
            this.fetchSidebarItems()
        }
    },
    computed: {
        ...mapGetters('user', ['getUsers', 'getAuthUser', 'getTitles']),
        users(){ return this.getUsers },
        authuser(){ return this.getAuthUser },
        titles(){ return this.getTitles },
        ...mapGetters('division', ['getDivisions']),
        divisions(){ return this.getDivisions },
        ...mapGetters('roles', ['getSidebarItems']),
        sidebaritems(){ return this.getSidebarItems }
    }
}
</script>
<style scoped>
.card-overlay{
    position: absolute;
    z-index: 999;
    top: 0;
    left: 0;
    background: rgba(0,0,0,0.5);
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}
.table-responsive.users{
    height: calc(100vh - 275px);
    border-radius: 8px;
}
.main-container{
    height: calc(100vh - 165px);
    overflow: auto;
}
.form-container{
    height: calc(100vh - 310px);
    overflow-x: hidden !important;
    padding: 0.5em;
    margin-bottom: 0.5em;
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
#tooltip {
  position: relative;
  display: inline-block;
}
#tooltip #tooltiptext {
  visibility: hidden;
  width: 150px;
  background-color: rgba(0,0,0,0.8);
  color: #fff;
  text-align: center;
  padding: 8px;
  border-radius: 6px;
  display: none;
 
  /* Position the tooltip text - see examples below! */
  position: absolute;
  z-index: 1;
  left: -160px;
  top: -20px; 
}

/* Show the tooltip text when you mouse over the tooltip container */
#tooltip:hover #tooltiptext {
  visibility: visible;
  display: block;
}
.roles-container{
    overflow: auto;
    padding: 8px;
    overflow-x: hidden;
    max-height: calc(100vh - 320px);
}
</style>