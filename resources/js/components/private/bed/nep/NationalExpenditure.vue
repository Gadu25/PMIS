<template>
    <div class="px-3 py-4">
        <button class="btn btn-sm btn-light float-start" @click="this.$router.push({name: 'Workshops'})"><i class="fas fa-arrow-left"></i> Back</button>
        <h2 class="text-center">National Expenditure Program</h2>
        <small>Planning Workshop  <span v-if="!loading">{{workshop.date}}</span><span v-else>Loading date <i class="fas fa-spinner fa-spin"></i></span> </small><hr>
        <template v-if="!loading">
            <div class="row justify-content-center">
                <div class="col-sm-10">
                    <div class="d-flex justify-content-end mb-3">
                        <button class="btn btn-sm bg-gradient shadow-none" @click="editmode = !editmode" :class="editmode ? 'btn-outline-primary' : 'btn-success'">{{!editmode ? 'Edit' : 'View'}} mode</button>
                        <!-- <button class="btn btn-sm btn-success bg-gradient shadow-none" @click="addProject()"><i class="fas fa-plus"></i> Project</button> -->
                    </div>
                    <div class="table-responsive mb-2 shadow-lg">
                        <table class="table table-sm table-bordered">
                            <thead class="text-center align-middle bg-warning position-sticky top-0">
                                <tr class="">
                                    <th style="width: 1px"></th>
                                    <th :style="'width: ' +  (!editmode ? '50%' : 'calc(50% - 150px) !important')">Programs / Projects / Activities</th>
                                    <th :style="'width: ' +  (!editmode ? '50%' : 'calc(50% - 150px) !important')">NEP</th>
                                    <th style="width: 140px" v-if="editmode">
                                        Actions
                                        <!-- <button class="btn btn-sm btn-success bg-gradient h-100 w-100 border-0 shadow-none rounded-0" @click="addProject()"><i class="fas fa-plus"></i> Project</button> -->
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <template v-for="division in form.divisions" :key="division.divCode">
                                    <tr style="background: orange">
                                        <td colspan="3"><strong>{{division.divCode}}</strong></td>
                                        <td v-if="editmode" class="p-0" style="height: 1px;">
                                            <button class="btn btn-sm btn-success bg-gradient h-100 w-100 border-0 shadow-none rounded-0" @click="addProject(division)"><i class="fas fa-plus"></i> Project</button>
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
                                                <span v-else>project title</span>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control text-end" v-model="project.amount" v-money="money" @change="amountCheck(project)" v-if="editmode">
                                                <span v-else> nep</span>
                                            </td>
                                            <td v-if="editmode">
                                                <button class="btn btn-sm" :class="[project.editmode ? 'w-75' : 'w-50', project.id ? 'btn-outline-primary' : 'btn-outline-success']" 
                                                    @click="project.editmode ? submitForm({
                                                        type: 'single', 
                                                        project: project,
                                                        division_id: division.divId
                                                    }) : project.editmode = !project.editmode">
                                                    <span v-if="project.editmode">
                                                        <i class="far fa-lg" :class="project.id ? 'fa-save' : 'fa-plus'"></i> <strong> {{project.id ? 'Save' : 'Add'}}</strong>
                                                    </span>
                                                    <i class="far fa-lg" :class="project.id ? 'fa-pencil-alt' : 'fa-plus'" v-else></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" :class="project.editmode ? 'w-25' : 'w-50'" 
                                                    @click="project.editmode ? project.editmode = !project.editmode : removeProject(division, project)">
                                                    <i class="far fa-trash-alt fa-lg" v-if="!project.editmode"></i>
                                                    <i class="far fa-times fa-lg" v-else></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </template>
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end" v-if="editmode">
                        <button class="btn btn-primary bg-gradient rounded-pill" style="min-width: 120px" @click="submitForm({type: 'multiple'})"><i class="far fa-save"></i> Save all</button>
                    </div>
                </div>
            </div>
            <!-- Modal -->
            <!-- <div class="modal fade" id="modal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{editmode ? 'Edit' : 'New'}} Project/s</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="d-flex justify-content-end mb-3">
                                <button class="btn btn-sm btn-success bg-gradient" @click="addProject()"><i class="fas fa-plus"></i> Project</button>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary">Save changes</button>
                        </div>
                    </div>
                </div>
            </div> -->
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
        ...mapActions('nep', ['fetchNationalExpenditures', 'saveNationalExpenditure']),
        newForm(){
            this.editmode = false
            // this.form.projects = [{
            //     id: '',
            //     project_id: '',
            //     amount: 0,
            //     workshop_id: this.$route.params.workshopId,
            //     isAdded: false,
            //     editmode: false
            // }]
        },
        editForm(){

        },
        submitForm(savingOptions){
            // console.log(savingOptions)
            if(this.checkForm(savingOptions)){
                this.form.savingOptions = savingOptions
                this.saveNationalExpenditure(this.form).then(res => {
                    // console.log(res)
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
                editmode: false
            })
        },
        removeProject(division, project){
            if(project.project_id){
                if(!project.id){
                    this.used.remove(project.project_id)
                    project.project_id = ''
                    project.amount = 0
                    project.isAdded = false
                    project.editmode = false
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
            // divisions: [
            //     {
            //         divCode: '',
            //         divId: '',
            //         projects: [{
            //             id: '',
            //             project_id: '',
            //             amount: 0,
            //             workshop_id: this.$route.params.workshopId,
            //             isAdded: false,
            //             editmode: false
            //         }]
            //     }
            // ],
            this.form.divisions = []
            for(let division of this.divisions){
                this.form.divisions.push({
                    divCode: division.code,
                    divId: division.id,
                    projects: []
                })
            }
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
        workshop(){ return this.getWorkshop },
        projects(){ return this.getOptions.projects },
        divisions(){ return this.getOptions.divisions }
        
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