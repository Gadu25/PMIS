<template>
    <div v-if="!formshow">
        <div class="d-flex justify-content-end mb-2" v-if="inUserRoles('annex_one_add')">
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
                <tbody v-if="!syncing">
                    <template v-for="sources, div in annexones" :key="div">
                        <template v-if="userDiv(div) || isAdmin">
                            <tr class="fw-bold" style="background: orange">
                                <td colspan="2" v-if="isAdmin">{{div}}</td>
                            </tr>
                            <template v-for="headers, source in sources" :key="source">
                                <tr v-if="div != 'STSD'">
                                    <td colspan="2" style="background: yellow;" class="text-danger fw-bold text-center" v-if="isAdmin || isChief">{{source}}</td>
                                </tr>
                                <template v-for="annexones, header in headers" :key="header">
                                    <tr class="fw-bold" v-if="header != 'None'">
                                        <td colspan="2" v-if="isAdmin || isChief">{{header}}</td>
                                    </tr>
                                    <template v-for="annexone in annexones" :key="annexone.id">
                                        <template v-if="userMatchProject(annexone.project) || isAdmin || isChief || isProjectHead(annexone.project)">
                                            <tr>
                                                <td><div class="ms-1">{{annexone.project.title}}</div></td>
                                                <td class="text-center">
                                                    <button v-if="inUserRoles('annex_one_edit') || isAdmin || isChief || isProjectHead(annexone.project)" 
                                                        style="width: 110px;" class="btn btn-sm shadow-none mb-1" 
                                                        :class="(isChief || (isProjectHead(annexone.project)) && !userMatchProject(annexone.project)) ? 'btn-outline-primary' : 'btn-primary'" 
                                                        @click="editFormTable(annexone)">{{(isChief || (isProjectHead(annexone.project)) && !userMatchProject(annexone.project)) ? 'View' : 'Edit'}} Table test</button><br>
                                                    <template v-if="userMatchProject(annexone.project) || isAdmin">
                                                        <button v-if="inUserRoles('annex_one_edit')" 
                                                            style="width: 110px;" 
                                                            class="btn btn-sm shadow-none btn-primary mb-1" 
                                                            data-bs-target="#modal" data-bs-toggle="modal"
                                                            @click="editForm(annexone)">Edit Info</button><br>
                                                        <button v-if="inUserRoles('annex_one_delete')" 
                                                            style="width: 110px;" 
                                                            class="btn btn-sm shadow-none btn-danger" 
                                                            @click="removeAnnexOne(annexone)">Delete Project</button>
                                                    </template>
                                                </td>
                                            </tr>
                                            <tr v-for="sub in annexone.subs" :key="sub.id">
                                                <td colspan="2"><div class="ms-2">{{sub.subproject.title}}</div></td>
                                            </tr>
                                        </template>
                                    </template>
                                </template>
                            </template>
                        </template>
                    </template>
                </tbody>
                <tbody v-else>
                    <tr><td colspan="2" class="text-center p-5"><h3>Data Syncing <i class="fas fa-sync fa-spin"></i></h3></td></tr>
                </tbody>
            </table>
        </div>
    </div>
    <div v-else>
        <div class="d-flex justify-content-between mb-2">
            <button class="btn btn-sm btn-outline-secondary" @click="hideForm()"><i class="fas fa-times"></i> Close</button>
            <button class="btn btn-sm btn-primary" v-if="isAdmin || userMatchProject(form2.project)" @click="submitForm2()"><i class="fas fa-save"></i> Save changes</button>
        </div>
        <div class="table-responsive" v-dragscroll>
            <table class="table table-sm table-bordered" style="font-size: 12px; min-width: 1500px">
                <TableHead :forPrint="true" />
                <tbody class="align-middle ">
                    <tr>
                        <td>{{form2.title}}</td>
                        <td v-for="fund, fkey in form2.funds" :key="fkey" class="text-end" :style="(isForInput(fund.type, form2.subs) && userMatchProject(form2.project)) || isAdmin ? 'padding: 0; height: 1px' : ''">
                            <input type="text" id="fund" v-bind:class="[hasAmountError && amountErrors.includes(fund.amount) ? 'form-control h-100 text-end border border-danger shadow-none rounded-0' : 'form-control h-100 text-end border-0 shadow-none rounded-0']" v-if="(isForInput(fund.type, form2.subs) && userMatchProject(form2.project)) || (isAdmin && form2.subs.length == 0)" v-model="fund.amount" v-money="money">
                            <span class="me-2" v-else>{{getTotal(fkey, form2.subs, fund.amount) == 0 ? '' : formatAmount(fund.amount = getTotal(fkey, form2.subs, fund.amount))}}</span>
                        </td>
                    </tr>
                    <tr v-for="sub in form2.subs" :key="sub.id">
                        <td><div class="ms-2">{{sub.title}}</div></td>
                        <td v-for="fund, fkey in sub.funds" :key="fkey" class="text-end" :style="(isForInput(fund.type) && userMatchProject(form2.project)) || isAdmin ? 'padding: 0; height: 1px' : ''">
                            <input type="text" id="fund" class="form-control h-100 text-end border-0 shadow-none rounded-0" v-if="(isForInput(fund.type) && userMatchProject(form2.project)) || isAdmin" v-model="fund.amount" v-money="money">
                            <span v-else>{{fund.amount == 0 ? '' : formatAmount(fund.amount)}}</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal fade" id="modal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <!-- {{formpart <= 1 ? '1. Project Filters' : '2. Annex 1 Details'}} -->
                        {{editmode ? 'Edit' : 'New'}} Annex 1 Project/s
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" ref="Close"></button>
                </div>
                <div class="modal-body">
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
                                <!-- <i class="far fa-question-circle fa-lg"></i> -->
                                <!-- <span id="tooltiptext">Projects shown on select options are based on selected filters during the first part of the form. Check your selected project filters or contact your System Administrator for further clarifications.</span> -->
                            </div>
                            <button class="btn btn-sm btn-success bg-gradient" v-if="!editmode" @click="addProject()"><i class="fas fa-plus"></i> Project</button>
                        </div>
                        <div class="overflow-auto" style="max-height: 50vh;">
                            <div class="position-relative" v-for="fp, key in form.projects" :key="key">
                                <div class="form-floating mb-3">
                                    <button class="btn btn-sm btn-outline-danger border-0 shadow-none position-absolute end-0" v-if="form.projects.length > 1" @click="removeProject(fp)"><i class="fas fa-times"></i></button>
                                    <select class="form-select" id="floatingSelect" @change="changeProject(fp)" v-model="fp.project_id">
                                        <option value="" hidden disabled selected>Select Project</option>
                                        <template v-for="project in projects" :key="project.id">
                                            <option :value="project.id" v-if="(userMatchProject(project) || isAdmin) && !used.includes(project.id) || project.id == fp.project_id">{{project.title}}</option>
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
                            <div v-if="editmode">
                                <p><strong>Project: </strong>{{form.project.title}}</p>
                                <div class="d-flex flex-wrap px-2" v-if="form.project.subprojects.length > 0">
                                    <div class="w-100"><strong>Subprojects</strong></div>
                                    <div class="form-check w-50" v-for="subproject in form.project.subprojects" :key="subproject.id">
                                        <input class="form-check-input" type="checkbox" :value="subproject.id" :id="subproject.title" v-model="form.project.subprojectIds">
                                        <label class="form-check-label" :for="subproject.title">
                                            {{subproject.title}}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <!-- </template> -->
                </div>
                <div class="modal-footer">
                    <button class="btn bg-gradient rounded-pill" :class="editmode ? 'btn-primary' : 'btn-success'" style="min-width: 100px" @click="submitForm()">{{editmode ? 'Save changes' : 'Submit'}}</button>
                    <!-- <button class="btn bg-gradient shadow-none btn-outline-primary rounded-circle" id="circle-btn" @click="formpart = 1">1</button> -->
                    <!-- <button class="btn bg-gradient shadow-none rounded-circle" id="circle-btn" @click="nextPart()" :class="formpart <= 1 ? 'btn-outline-secondary' : 'btn-outline-primary'">2</button> -->
                    <!-- <button class="btn bg-gradient shadow-none rounded-pill" :class="formpart <= 1 ? 'btn-secondary' : 'btn-primary'" style="min-width: 100px" @click="formpart <= 1 ? nextPart() : submitForm()">{{formpart <= 1 ? 'Next' : 'Submit'}}</button> -->
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { mapActions, mapGetters } from 'vuex'
import { dragscroll } from 'vue-dragscroll'
import { VMoney } from 'v-money'
import TableHead from './TableHead.vue'
import { handleError } from 'vue'
export default {
    name: 'Form',
    emits: ['clicked'],
    setup({emit}){},
    directives: {
        dragscroll: dragscroll,
        money: VMoney
    },
    components: {
        TableHead
    },
    data(){
        return {
            editmode: false,
            formshow: false,
            hasAmountError: false,
            amountErrors: [],
            form: {
                id: '',
                projects: [],
                project: {
                    id: '',
                    title: '',
                    subs: [],
                    subprojects: [],
                    subprojectIds: []
                },
                source: '',
                headerType: '',
                workshop_id: this.$route.params.workshopId
            },
            used: [],
            processing: false,
            form2:{
                id: '',
                title: '',
                funds: [
                    {id: '', type: 'GAA',      amount: 0, year: 0},
                    {id: '', type: 'Proposed', amount: 0, year: 0},
                    {id: '', type: 'NEP',      amount: 0, year: 0},
                    {id: '', type: 'Revised',  amount: 0, year: 0},
                    {id: '', type: 'Proposed', amount: 0, year: 0},
                    {id: '', type: 'Revised',  amount: 0, year: 0},
                    {id: '', type: 'Proposed', amount: 0, year: 0},
                    {id: '', type: 'Revised',  amount: 0, year: 0},
                    {id: '', type: 'Proposed', amount: 0, year: 0},
                    {id: '', type: 'Revised',  amount: 0, year: 0},
                    {id: '', type: 'Proposed', amount: 0, year: 0},
                    {id: '', type: 'Revised',  amount: 0, year: 0},
                    {id: '', type: 'Last',     amount: 0, year: 0},
                ],
                subs: [],
                project: {}
            },
            money: {
                decimal: '.',
                thousands: ',',
                prefix: '',
                suffix: '',
                precision: 0,
                masked: false /* doesn't work with directive */
            },
        }
    },
    watch: {
        form2: {
            handler(newValue){
                var hasError = []
                newValue.funds.forEach(element => {
                    if(element.amount.length > 11){
                        this.toastMsg("error", "Invalid amount")
                        hasError.push(element.amount)
                    }
                })
                if(hasError.length > 0){
                    this.hasAmountError = true
                    this.amountErrors = hasError
                }else{
                    this.hasAmountError = false
                }
            },
            deep: true,
            immediate: true
        }
    },
    methods: {
        ...mapActions('workshop', ['fetchOptions']),
        ...mapActions('annexone', ['fetchAnnexOnes', 'saveAnnexOne', 'updateAnnexOne', 'deleteAnnexOne']),
        removeAnnexOne(annexone){
            this.swalConfirmCancel('Are you sure?', 'You will not be able to retrieve this data').then(res => {
                if(res){
                    this.deleteAnnexOne(annexone.id).then(res => {
                        var icon = res.errors ? 'error' : 'success'
                        this.toastMsg(icon, res.message)
                        if(!res.errors){
                            this.used.remove(annexone.project_id)
                        }
                    })
                }
            })
        },
        newForm(){
            this.editmode = false
            this.form.projects = []
            this.form.id = ''
            this.form.source = ''
            this.form.headerType = ''
            this.form.project = {}
            // this.used = []
            this.addProject()
        },
        editForm(annexone){
            this.editmode = true
            this.form.id = annexone.id
            this.form.source = annexone.source_of_funds
            this.form.headerType = annexone.header_type
            this.form.projects = []
            
            var project = annexone.project
            this.form.project.id = project.id
            this.form.project.title = project.title
            this.form.project.subprojects = project.subprojects
            this.form.project.subprojectIds = []
            this.form.project.subs = annexone.subs
            for(let sub of annexone.subs){
                this.form.project.subprojectIds.push(sub.subproject_id)
            }
        },
        editFormTable(annexone){
            this.formshow = true
            this.form2.id = annexone.id
            this.form2.project = annexone.project
            this.form2.title = annexone.project.title
            this.form2.funds = [
                {id: '', type: 'GAA',      amount: 0, year: 0},
                {id: '', type: 'Proposed', amount: 0, year: 0},
                {id: '', type: 'NEP',      amount: 0, year: 0},
                {id: '', type: 'Revised',  amount: 0, year: 0},
                {id: '', type: 'Proposed', amount: 0, year: 0},
                {id: '', type: 'Revised',  amount: 0, year: 0},
                {id: '', type: 'Proposed', amount: 0, year: 0},
                {id: '', type: 'Revised',  amount: 0, year: 0},
                {id: '', type: 'Proposed', amount: 0, year: 0},
                {id: '', type: 'Revised',  amount: 0, year: 0},
                {id: '', type: 'Proposed', amount: 0, year: 0},
                {id: '', type: 'Revised',  amount: 0, year: 0},
                {id: '', type: 'Last',     amount: 0, year: 0},
            ]
            this.form2.subs = []
            this.setFormFunds(this.form2.funds, annexone.funds)
            for(let sub of annexone.subs){
                var temp = {
                    id: sub.id,
                    title: sub.subproject.title,
                    funds: [
                        {id: '', type: 'GAA',      amount: 0, year: 0},
                        {id: '', type: 'Proposed', amount: 0, year: 0},
                        {id: '', type: 'NEP',      amount: 0, year: 0},
                        {id: '', type: 'Revised',  amount: 0, year: 0},
                        {id: '', type: 'Proposed', amount: 0, year: 0},
                        {id: '', type: 'Revised',  amount: 0, year: 0},
                        {id: '', type: 'Proposed', amount: 0, year: 0},
                        {id: '', type: 'Revised',  amount: 0, year: 0},
                        {id: '', type: 'Proposed', amount: 0, year: 0},
                        {id: '', type: 'Revised',  amount: 0, year: 0},
                        {id: '', type: 'Proposed', amount: 0, year: 0},
                        {id: '', type: 'Revised',  amount: 0, year: 0},
                        {id: '', type: 'Last',     amount: 0, year: 0},
                    ]
                }
                this.setFormFunds(temp.funds, sub.funds)
                this.form2.subs.push(temp)
            }
            this.childClick()
        },
        setFormFunds(funds, dbfunds){
            var ctr = 1
            for(let fund of funds){
                fund.year = this.workshopYear + 
                    (ctr == 1 ? 0 : 
                    ctr > 1 && ctr < 5 ? 1 :
                    ctr == 5 || ctr == 6 ? 2 : 
                    ctr == 7 || ctr == 8 ? 3 : 
                    ctr == 9 || ctr == 10 ? 4 : 
                    ctr == 11 || ctr == 12 ? 5 : 6)
                ctr++
                var dbfund = dbfunds.find(elem => elem.type == fund.type && elem.year == fund.year)
                if(dbfund){
                    fund.id = dbfund.id
                    fund.amount = dbfund.amount
                }
            }
        },
        isForInput(type, subs = []){
            return (type == 'Revised' || type == 'Last') && subs.length == 0
        },
        getTotal(key, subs, fund){
            var amount = 0
            for(let sub of subs){
                amount = amount + this.strToFloat(sub.funds[key].amount)
            }
            return subs.length > 0 ? amount : fund
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
            if(!this.editmode){
                await this.formValidated(this.form)
                    .then(res => {
                        this.toastMsg('info', 'Saving...')
                        this.saveAnnexOne(this.form).then(res => {
                            var icon = res.errors ? 'error' : 'success'
                            this.toastMsg(icon, res.message)
                            this.processing = false
                            if(!res.errors){
                                this.$refs.Close.click()
                                this.childClick()
                            }
                        })
                    })
                    .catch((err) => {
                        var icon = err == 'Cancelled' ? 'info' : 'warning'
                        var msg  = err == 'Cancelled' ? 'Form submission cancelled' : err
                        this.toastMsg(icon, msg)
                        this.processing = false
                    })
            }
            if(this.editmode){
                this.saveAnnexOne(this.form).then(res => {
                    var icon = res.errors ? 'error' : 'success'
                    this.toastMsg(icon, res.message)
                    this.processing = false
                    if(!res.errors){
                        this.$refs.Close.click()
                        this.childClick()
                    }
                })
            }
        },
        submitForm2(){
            var hasError = []
            this.form2.funds.forEach(element => {
                if(element.amount.length > 11){
                    this.toastMsg("error", "Invalid amount")
                    hasError.push(element.amount)
                }
            })
            if(hasError.length > 0){
                this.toastMsg('error', 'Invalid amount')
            }else{
                this.updateAnnexOne(this.form2).then(res => {
                    var icon = res.errors ? 'error' : 'success'
                    this.toastMsg(icon, res.message)
                    if(!res.errors){
                        this.hideForm()
                    }
                })
            }
            
        },
        hideForm(){
            this.formshow = false
            this.childClick()
        },
        childClick(){
            this.$emit('clicked')
        },
        // nextPart(){
        //     if(this.form.division_id == ''){ this.toastMsg('warning', 'Please select a Division'); return false }
        //     if(this.form.program_id == ''){ this.toastMsg('warning', 'Please select a Program'); return false }
        //     this.formpart++
        // },
        // setColumn(type){
        //     var array  = type == 'program' || type == 'subprogram' ? this.subprograms : this.units
        //     var array2 = type == 'program' || type == 'subprogram' ? this.clusters    : this.subunits
        //     if(type == 'program' || type == 'division'){
        //         return array.length == 0 ? 'col-sm-12' : array2.length == 0 ? 'col-sm-6' : 'col-sm-4'
        //     }
        //     if(type == 'subprogram' || type == 'unit'){
        //         return array2.length == 0 ? 'col-sm-6' : 'col-sm-4'
        //     }
        // },
        userMatchProject(project){
            var leaderId = project.leader.profile_id
            var encoderIds = []
            for(let enconder of project.encoders){
                encoderIds.push(enconder.profile_id)
            }
            var profileId = this.user.active_profile.id
            var state = leaderId == profileId
            if(!state){
                state = encoderIds.includes(profileId)
            }
            for(let div in this.annexones){
                for(let source in this.annexones[div]){
                    for(let header in this.annexones[div][source]){
                        for(let item of this.annexones[div][source][header]){
                            if(project.id == item.project_id && !this.used.includes(project.id)){
                                this.used.push(project.id)
                            }
                        }
                    }
                }
            }
            return state
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
            var projects = this.projects
            var projectId = formproject.project_id
            var project = projects.find(elem => elem.id == projectId)
            formproject.projectTitle = project.title
            formproject.subprojects = project.subprojects
            if(!this.used.includes(projectId)){
                this.used.push(projectId)
            }
            this.checkSelectedProject()
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
        inUserRoles(code){
            var role = this.userroles.find(elem => elem.code == code)
            return (role)
        },
        formatAmount(amount){
            amount = parseFloat(amount.toString().replaceAll(',', ''))
            return (Math.round(amount * 100) / 100).toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 0 })
        },
        strToFloat(num){
            let strNum = num.toString().replace(/\,/g,'')
            return Math.abs(parseFloat(strNum))
        },
        userDiv(division){
            return this.user.division.code == division
        },
        // isLeader(project){
        //     var state = false
        //     var profileId = this.user.active_profile.id
        //     state = project.leader.profile_id == profileId
        //     for(let encoder of project.encoders){
        //         if(!state){
        //             state = encoder.profile_id == profileId
        //         }
        //     }
        //     return state
        // },
        isProjectHead(project){
            var unitId = this.user.unit_id
            return project.unit_id == unitId && this.user.active_profile.title.name == 'Unit Head'
        }
    },
    computed:{
        ...mapGetters('annexone', ['getAnnexOnes']),
        ...mapGetters('user', ['getAuthUser']),
        ...mapGetters('workshop', ['getWorkshop', 'getOptions']),
        // divisions(){ return this.getOptions.divisions },
        // programs(){ return this.getOptions.programs },
        projects(){ return this.getOptions.projects },
        // usedprojects(){ return this.getOptions.used_projects },
        annexones(){ return this.getAnnexOnes },
        user(){ return this.getAuthUser },
        userroles(){ return this.user.active_profile.roles },
        userdivision(){ return this.user.division.code == division },
        isAdmin(){ return this.user.active_profile.title.name == 'Superadmin'},
        isChief(){ return this.user.active_profile.title.name == 'Division Chief'},
        workshopYear(){ return parseInt(this.getWorkshop.year) }
    },
    props: {
        syncing: Boolean
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

.form-control#fund{
    font-size: 12px;
    background: rgba(0, 0, 0, 0.03);
}
.form-control#fund:focus{
    background: rgba(173, 216, 230, 0.3);
}

.table-responsive{
    height: calc(100vh - 212px);
}
</style>