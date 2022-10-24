<template>
    <div class="px-3 py-4">
        <button class="btn btn-sm btn-light float-start" @click="this.$router.push({name: 'Workshops'})"><i class="fas fa-arrow-left"></i> Back</button>
        <h2 class="text-center">National Expenditure Program</h2>
        <small>Planning Workshop  <span v-if="!loading">{{workshop.date}}</span><span v-else>Loading date <i class="fas fa-spinner fa-spin"></i></span> </small><hr>
        <template v-if="!loading">
            <div class="row justify-content-center">
                <div class="col-sm-10">
                    <div class="d-flex justify-content-end mb-3" v-if="inUserRoles('nep_editmode')">
                        <button class="btn btn-sm bg-gradient shadow-none" @click="editmode = !editmode" :class="editmode ? 'btn-outline-primary' : 'btn-success'">{{!editmode ? 'Edit' : 'View'}} mode</button>
                    </div>
                    <div class="table-responsive mb-2 shadow-lg">
                        <table class="table table-sm table-bordered">
                            <thead class="text-center align-middle bg-warning position-sticky top-0">
                                <tr class="">
                                    <th style="width: 1px"></th>
                                    <th :style="'width: ' +  (!editmode ? '50%' : 'calc(50% - 150px) !important')">Programs / Projects / Activities</th>
                                    <th :style="'width: ' +  (!editmode ? '50%' : '30%')">NEP</th>
                                    <th style="width: 140px" v-if="editmode">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="align-middle">
                                <template v-for="division in form.divisions" :key="division.divCode">
                                    <tr style="background: orange">
                                        <td colspan="3"><strong>{{division.divCode}}</strong></td>
                                        <td v-if="editmode" class="p-0" style="height: 1px;">
                                            <button v-if="inUserRoles('nep_add')" class="btn btn-sm btn-success bg-gradient h-100 w-100 border-0 shadow-none rounded-0" tabindex="-1" @click="addProject(division)"><i class="fas fa-plus"></i> Project</button>
                                        </td>
                                    </tr>
                                    <template v-for="project, key in division.projects" :key="key">
                                        <tr :id="editmode ? 'input' : ''" v-if="editmode || project.id">
                                            <td :class="project.isAdded ? 'bg-success' : 'bg-secondary'"></td>
                                            <td>
                                                <select class="form-control" v-model="project.project_id" v-if="editmode && !project.id" @change="projectSelect(division, project.project_id)">
                                                    <option value="" disabled selected hidden>Select Project</option>
                                                    <template v-for="proj in projects" :key="proj.id">
                                                        <option :value="proj.id" v-if="(!used.includes(proj.id) || proj.id == project.project_id) && proj.division_id == division.divId">{{proj.title}}</option>
                                                    </template>
                                                </select>
                                                <div v-else class="px-2">{{project.project_title}}</div>
                                            </td>
                                            <td class="text-end">
                                                <input type="text" class="form-control text-end" v-model="project.amount" v-money="money" @change="amountCheck(project)" v-if="editmode && project.editmode">
                                                <span v-else class="px-2">{{formatAmount(project.amount)}}</span>
                                            </td>
                                            <td v-if="editmode">
                                                <button v-if="inUserRoles('nep_edit')" class="btn btn-sm" tabindex="-1" style="min-height: 36px;" :class="[project.editmode ? 'w-75' : 'w-50', project.id ? 'btn-outline-primary' : 'btn-outline-success']" 
                                                    @click="project.editmode ? submitForm({
                                                        type: 'single', 
                                                        project: project,
                                                        division_id: division.divId
                                                    }) : editForm(project)">
                                                    <span v-if="project.editmode">
                                                        <i class="far fa-lg" :class="project.id ? 'fa-save' : 'fa-plus'"></i> <strong> {{project.id ? 'Save' : 'Add'}}</strong>
                                                    </span>
                                                    <i class="far fa-lg" :class="project.id ? 'fa-pencil-alt' : 'fa-plus'" v-else></i>
                                                </button>
                                                <!-- <button class="btn btn-sm btn-outline-danger" :class="project.editmode ? 'w-25' : 'w-50'" 
                                                    @click="removeProject(division, project)"> -->
                                                <button v-if="inUserRoles('nep_delete')" class="btn btn-sm btn-outline-danger" tabindex="-1" :class="project.editmode ? 'w-25' : 'w-50'" 
                                                    @click="project.editmode && project.id ? removeEdit(project) : removeProject(division, project)">
                                                    <i class="far fa-trash-alt fa-lg" v-if="!project.editmode"></i>
                                                    <i class="far fa-times fa-lg" v-else></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <template v-for="sub, subKey in project.subs" :key="subKey">
                                            <tr :id="editmode ? 'input' : ''" v-if="editmode || sub.id">
                                                <td :class="sub.isAdded ? 'bg-success' : 'bg-secondary'"></td>
                                                <td><div class="mx-3 my-1">{{sub.subprojectTitle}}</div></td>
                                                <td class="text-end">
                                                    <input type="text" class="form-control text-end" v-model="sub.amount" v-money="money" @change="amountCheck(sub)" v-if="editmode && project.editmode">
                                                    <span v-else class="px-2">{{sub.amount != 0 ? formatAmount(sub.amount) : ''}}</span>
                                                </td>
                                                <td v-if="editmode"></td>
                                            </tr>
                                        </template>
                                    </template>
                                </template>
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end" v-if="editmode && editCount > 1">
                        <button v-if="inUserRoles('nep_multisave')" class="btn btn-primary bg-gradient rounded-pill" style="min-width: 120px" @click="submitForm({type: 'multiple'})"><i class="far fa-save"></i> Save all</button>
                    </div>
                </div>
            </div>
        </template>
        <h1 v-else class="text-center p-5"><i class="fas fa-spinner fa-spin fa-5x"></i></h1>
    </div>
</template>
<script>
import { mapActions, mapGetters } from 'vuex'
import { VMoney } from 'v-money'
export default {
    name: 'NationalExpediture',
    directives: {money: VMoney},
    data(){
        return {
            loading: true,
            editmode: false,
            form: {
                savingOptions: {
                    id: '',
                    project_id: '',
                    division_id: '',
                    workshop_id: this.$route.params.workshopId,
                    type: 'single',
                },
                divisions: [
                    {
                        divCode: '',
                        divId: '',
                        projects: [{
                            id: '',
                            project_id: '',
                            amount: 0,
                            workshop_id: this.$route.params.workshopId,
                            isAdded: false,
                            editmode: false
                        }]
                    }
                ],
            },
            editCount: 0,
            used: [],
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
    methods: {
        ...mapActions('workshop', ['fetchWorkshop', 'fetchOptions']),
        ...mapActions('nep', ['fetchNationalExpenditures', 'saveNationalExpenditure', 'deleteNationalExpenditure']),
        newForm(){
            this.editmode = false
        },
        editForm(project){
            project.editmode = !project.editmode
            this.editCount++
        },
        removeEdit(project){
            project.editmode = !project.editmode
            this.editCount--
        },
        submitForm(savingOptions){
            if(this.checkForm(savingOptions)){
                this.form.savingOptions = savingOptions
                this.saveNationalExpenditure(this.form).then(res => {
                    var icon = res.errors ? 'error' : 'success'
                    this.toastMsg(icon, res.message)
                    if(!res.errors){
                        this.setForm()
                    }
                })
            }
        },
        checkForm(savingOptions){
            var type = savingOptions.type
            if(type == 'single'){
                var project = savingOptions.project
                if(project.project_id == ''){ this.toastMsg('warning', 'Project required!'); return false }
                if(parseFloat(project.amount) == 0 ){ this.toastMsg('warning', 'Amount required'); return false }
            }
            if(type == 'multiple'){
                var edits = 0
                for(let division of this.form.divisions){
                    for(let project of division.projects){
                        if(project.editmode){
                            if(project.project_id == ''){ this.toastMsg('warning', 'Project required!'); return false }
                            if(parseFloat(project.amount) == 0 ){ this.toastMsg('warning', 'Amount required'); return false }
                            edits++
                        }
                    }
                }
                if(edits == 0){ return false }
            }
            return true
        },
        addProject(division){
            division.projects.push({
                id: '',
                project_id: '',
                amount: 0,
                workshop_id: this.$route.params.workshopId,
                isAdded: false,
                subprojects: [],
                subs: [],
                editmode: true
            })
            this.editCount++
        },
        removeProject(division, project){
            if(project.project_id){
                if(!project.id){
                    this.used.remove(project.project_id)
                    project.project_id = ''
                    project.amount = 0
                    project.isAdded = false
                    project.editmode = true
                    project.subs = []
                }
                if(project.id){
                    this.swalConfirmCancel('Are you sure', 'This is not irreversible').then(res => {
                        if(res){
                            this.used.remove(project.project_id)
                            this.deleteNationalExpenditure(project.id).then(res => {
                                this.setForm()
                            })
                        }
                    })
                }
            }
            else{
                division.projects.remove(project)
            }
        },
        amountCheck(project){
            if(project.amount.toString().includes('-')){
                project.amount = project.amount.replace('-', '')
            }
        },
        projectSelect(division, projectId){
            if(!this.used.includes(projectId)){
                this.used.push(projectId)
            }
            var project = this.projects.find(elem => elem.id == projectId)
            var subprojects = project.subprojects
            // var division = this.form.divisions.find(elem => elem.divId == division)
            project = division.projects.find(elem => elem.project_id == projectId)
            project.subs = []
            for(let subproject of subprojects){
                project.subs.push({
                    id: '',
                    subprojectTitle: subproject.title,
                    subproject_id: subproject.id,
                    isAdded: false,
                    amount: 0
                })
            }
            this.checkSelectedProject(division)
        },
        checkSelectedProject(division){
            for(let projectId of this.used){
                var project = division.projects.find(elem => elem.project_id == projectId)
                if(!project){
                    this.used.remove(projectId)
                }
            }
        },
        setForm(){
            this.editCount = 0
            this.form.divisions = []
            for(let division of this.divisions){
                var temp = {
                    divCode: division.code,
                    divId: division.id,
                    projects: []
                }
                var projects = []
                for(let item of division.items){
                    var tempItem = {
                        id: item.id,
                        project_id: item.project_id,
                        project_title: item.project.title,
                        amount: item.amount,
                        workshop_id: item.workshop_id,
                        isAdded: item.isAdded,
                        subs: [],
                        editmode: false
                    }
                    for(let subproject of item.project.subprojects){
                        tempItem.subs.push({
                            id: '',
                            subproject_id: subproject.id,
                            subprojectTitle: subproject.title,
                            amount: 0,
                            isAdded: false,
                            editmode: false
                        })
                    }
                    for(let sub of item.subs){
                        var tempSub = tempItem.subs.find(elem => elem.subproject_id == sub.subproject_id)
                        tempSub.id = sub.id
                        tempSub.amount = sub.amount
                        tempSub.isAdded = sub.isAdded
                    }
                    projects.push(tempItem)
                    this.used.push(item.project_id)
                }
                temp.projects = projects
                this.form.divisions.push(temp)
            }
        },
        inUserRoles(code){
            var role = this.userroles.find(elem => elem.code == code)
            return (role)
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
        formatAmount(amount){
            amount = parseFloat(amount.toString().replaceAll(',', ''))
            return (Math.round(amount * 100) / 100).toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 0 })
        },
    },
    created(){
        var workshopId = this.$route.params.workshopId
        this.fetchWorkshop(workshopId).then(res => {
            this.fetchNationalExpenditures(workshopId).then(res => {
                this.fetchOptions({workshopId: workshopId, annex: 'nep'}).then(res => {
                    this.setForm()
                    this.loading = false
                })
            })
        })
    },
    computed: {
        ...mapGetters('workshop', ['getWorkshop', 'getOptions']),
        ...mapGetters('nep', ['getNationalExpenditures']),
        ...mapGetters('user', ['getAuthUser']),
        workshop(){ return this.getWorkshop },
        projects(){ return this.getOptions.projects },
        divisions(){ return this.getNationalExpenditures },
        auth(){ return this.getAuthUser },
        userroles(){ return this.getAuthUser.active_profile.roles }
        
    }
}
</script>
<style scoped>
tr>th{
    height: 60px
}
tr#input>td{
    padding: 0px !important;
    height: 1px;
}
tr#input>td>.form-control{
    border-radius: 0;
    border: 0;
    box-shadow: none;
}
tr#input>td>.form-control:focus{
    background: rgba(173, 216, 230, 0.3);
}
tr#input>td>button{
    border: 0;
    box-shadow: none;
    border-radius: 0;
    height: 100%;
    /* height: 40px */
}
.table-responsive{
    overflow: auto;
    max-height: calc(100vh - 290px)
}
</style>