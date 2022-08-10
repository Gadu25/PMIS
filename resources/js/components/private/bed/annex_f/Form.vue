<template>
    <div class="px-2 my-2">
        <template v-if="formshow">
            <h4>Form</h4>
            <strong v-if="!form.id">Program</strong>
            <div class="form-group row mb-2" v-if="!form.id">
                <div :class="'mb-2 col-sm-'+ ((subprograms.length > 0 && clusters.length > 0) ? 4 : (subprograms.length > 0) ? 6 : 12 )">
                    <div class="form-floating">
                        <select class="form-select" id="Program" v-model="form.program_id" @change="progChange()">
                            <option value="" selected hidden disabled>Select Program</option>
                            <option :value="program.id" v-for="program in options.programs" :key="program.id+'_progOption'">{{program.title}}</option>
                        </select>
                        <label for="Program">Program</label>
                    </div>
                </div>
                <div :class="'mb-2 col-sm-' + (clusters.length > 0 ? 4 : 6)" v-if="subprograms.length > 0">
                    <div class="form-floating">
                        <select class="form-select" id="Subprogram" v-model="form.subprogram_id" @change="subpChange()">
                            <option value="" selected hidden disabled>Select Sub-Program</option>
                            <option :value="subprogram.id" v-for="subprogram in subprograms" :key="subprogram.id+'_subpOption'">{{subprogram.title}}</option>
                            <option :value="0" >N/A</option>
                        </select>
                        <label for="Subprogram">Sub-Program</label>
                    </div>
                </div>
                <div class="mb-2 col-sm-4" v-if="clusters.length > 0">
                    <div class="form-floating">
                        <select class="form-select" id="Cluster" v-model="form.cluster_id" @change="resetProject()">
                            <option value="" selected hidden disabled>Select Cluster</option>
                            <option :value="cluster.id" v-for="cluster in clusters" :key="cluster.id+'_clusOption'">{{cluster.title}}</option>
                            <option :value="0">N/A</option>
                        </select>
                        <label for="Cluster">Cluster</label>
                    </div>
                </div>
            </div>
            <strong v-if="!form.id">Division</strong>
            <div class="form-group row mb-2" v-if="user.active_profile.title == 'Superadmin Profile' && !form.id">
                <div :class="'mb-2 col-sm-'+ ((units.length > 0 && subunits.length > 0) ? 4 : (units.length > 0) ? 6 : 12 )">
                    <div class="form-floating">
                        <select class="form-select" id="Division" v-model="form.division_id" @change="divChange()">
                            <option value="" selected hidden disabled>Select Division</option>
                            <option :value="division.id" v-for="division in options.divisions" :key="division.id+'_divOption'">{{division.name}}</option>
                        </select>
                        <label for="Division">Division</label>
                    </div>
                </div>
                <div :class="'mb-2 col-sm-' + (subunits.length > 0 ? 4 : 6)" v-if="units.length > 0">
                    <div class="form-floating">
                        <select class="form-select" id="Unit" v-model="form.unit_id" @change="unitChange()">
                            <option value="" selected hidden disabled>Select Unit</option>
                            <option :value="unit.id" v-for="unit in units" :key="unit.id+'_unitOption'">{{unit.name}}</option>
                            <option :value="0">N/A</option>
                        </select>
                        <label for="Unit">Unit</label>
                    </div>
                </div>
                <div class="col-sm-4" v-if="subunits.length > 0">
                    <div class="form-floating">
                        <select class="form-select" id="Subunit" v-model="form.subunit_id" @change="resetProject()">
                            <option value="" selected hidden disabled>Select Sub-Unit</option>
                            <option :value="subunit.id" v-for="subunit in subunits" :key="subunit.id+'_subuOption'">{{subunit.name}}</option>
                            <option :value="0">N/A</option>
                        </select>
                        <label for="Subunit">Sub-Unit</label>
                    </div>
                </div>
            </div>
            <button class="btn btn-sm btn-success float-end" v-if="!form.id" @click="addProject()"><i class="fas fa-plus"></i> Project</button>
            <strong>{{!form.id ? 'Projects' : 'Project'}}</strong>
            <div class="d-flex flex-wrap mt-3">
                <div class="col-sm-6 px-2 my-2" v-for="proj, key in form.projects" :key="'project_'+key">
                    <div class="card shadow">
                        <div class="card-body">
                            <button class="btn btn-xs btn-danger float-end" v-if="!form.id" @click="removeProject(proj)"><i class="far fa-trash-alt"></i> Remove</button>
                            <div class="form-check form-switch">
                                <input style="cursor: pointer" class="form-check-input" type="checkbox" :id="'multiple'+key" v-model="proj.multiple">
                                <label style="cursor: pointer" class="form-check-label" :for="'multiple'+key">Multiple</label>
                            </div>
                            <template v-if="!proj.multiple">
                                <div class="form-floating my-2">
                                    <select class="form-select" id="Project" v-model="proj.project_id" @change="projectChange(proj.project_id)">
                                        <option value="" selected hidden disabled>Select Project</option>
                                        <template v-for="project in options.projects" :key="project.id+'_projOption'">
                                            <option :value="project.id" v-if="(showProject(project) && !this.form.selectedProjects.includes(project.id)) || project.id == proj.project_id">{{project.title}}</option>
                                        </template>
                                        <template v-for="project in options.used_projects" :key="project.id+'_projOptionUsed'">
                                            <option :value="project.id" v-if="(showProject(project) && project.id == proj.project_id) || proj.project_ids.includes(project.id)">{{project.title}}</option>
                                        </template>
                                    </select>
                                    <label for="Project">Project</label>
                                </div>
                                
                                <div class="form-check" v-for="subproject in proj.subprojects" :key="subproject.id+'_subproject'">
                                    <input class="form-check-input" type="checkbox" v-model="subproject.state" :id="'subprojectcheck'+subproject.subproject_id" tabindex="-1">
                                    <label class="form-check-label w-100" :for="'subprojectcheck'+subproject.subproject_id">
                                        <small>{{subproject.title}}</small>
                                    </label>
                                </div>
                            </template>
                            <template v-else>
                                <template v-for="project in options.projects" :key="project.id+'_projOption'">
                                    <div class="form-check my-2" v-if="(showProject(project) && !this.form.selectedProjects.includes(project.id)) || proj.project_ids.includes(project.id)">
                                        <input class="form-check-input" type="checkbox" :value="project.id" :id="'projCheck_'+project.id" v-model="proj.project_ids" @click="checkboxProject($event, key, project.id)">
                                        <label class="form-check-label" :for="'projCheck_'+project.id">
                                            {{project.title}}
                                        </label>
                                    </div>
                                </template>
                                <template v-for="project in options.used_projects" :key="project.id+'_projOption'">
                                    <div class="form-check my-2" v-if="showProject(project) && (proj.project_ids.includes(project.id) || proj.project_id == project.id)">
                                        <input class="form-check-input" type="checkbox" :value="project.id" :id="'projCheck_'+project.id" v-model="proj.project_ids" @click="checkboxProject($event, key, project.id)">
                                        <label class="form-check-label" :for="'projCheck_'+project.id">
                                            {{project.title}}
                                        </label>
                                    </div>
                                </template>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end">
                <button style="min-width: 100px;" class="btn rounded-pill btn-outline-secondary me-1" @click="formshow = false" tabindex="-1">Cancel</button>
                <button style="min-width: 100px;" class="btn rounded-pill" :class="form.id ? 'btn-primary' : 'btn-success'" @click="submitForm()">{{form.id ? 'Save Changes' :'Submit'}}</button>
            </div>
        </template>
        <template v-if="detailshow">
            <div class="d-flex justify-content-between mb-2">
                <button @click="detailshow = false" class="btn btn-sm btn-danger"><i class="fas fa-times"></i> Cancel</button>
                <button class="btn btn-sm btn-primary"><i class="far fa-save"></i> Save Changes</button>
            </div>
            <div class="overflow-auto" v-dragscroll>
                <table class="table table-sm table-bordered" style="width: 3500px;">
                    <thead class="align-middle">
                        <tr>
                            <th style="width: 8%" rowspan="3">Program Name/Activity</th>
                            <th style="width: 1%" rowspan="3">Total <br>Target <br>(P'000)</th>
                            <th colspan="3">{{workshop.year}}</th>
                            <th colspan="12">{{parseInt(workshop.year) + 1}}</th>
                            <th style="width: 4%" rowspan="3">Total</th>
                            <th style="width: 4%" rowspan="3">Remarks</th>
                        </tr>
                        <tr>
                            <th colspan="3">4th Qtr</th>
                            <th colspan="3">1st Qtr</th>
                            <th colspan="3">2nd Qtr</th>
                            <th colspan="3">3rd Qtr</th>
                            <th colspan="3">4th Qtr</th>
                        </tr>
                        <tr>
                            <th style="width: 5.53%" v-for="col, key in columns" :key="col+key">{{col}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{form.title}}</td><td></td>
                            <td class="p-0" v-for="activity, key in form.activities" :key="'activity_'+key">
                                <div class="position-relative" v-for="act, akey in activity" :key="'act_'+akey">
                                    <button @click="removeActivity(key, act)" class="btn btn-xs btn-danger position-absolute rounded-0 end-0"><i class="fas fa-times"></i></button>
                                    <textarea class="form-control rounded-0 shadow-none h-100" rows="6" v-model="act.description"></textarea>
                                </div>
                                <button @click="addActivity(key)" class="btn btn-sm btn-outline-secondary shadow-none rounded-0 w-100 border-0"><i class="fas fa-plus"></i></button>
                            </td><td></td>
                            <td class="p-0" style="height: 1px"><textarea class="form-control rounded-0 shadow-none h-100" v-model="form.remarks"></textarea></td>
                        </tr>
                        <tr style="background: rgba(255, 166, 0, 0.15)">
                            <td class="text-center fw-bold"><small>Fundings</small></td><td></td>
                            <td class="p-0" style="height: 1px" v-for="fund, fkey in form.funds" :key="'fund_'+fkey">
                                <input type="text" class="form-control rounded-0 h-100 shadow-none text-end" style="background: transparent" v-model.lazy="fund.amount" v-money="money">
                            </td>
                            <td class="text-end" style="padding: 6px 12px">{{formatAmount(getTotalAmount())}}</td><td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </template>
        <template v-if="!formshow && !detailshow">   
            <div class="d-flex justify-content-end mb-2">
                <button class="btn btn-sm btn-success" @click="resetForm()"><i class="fas fa-plus"></i></button>
            </div>
            <div class="row">
                <div class="col-sm-3 mb-3">
                    <div class="card shadow mx-3">
                        <div class="card-body">
                            <h4>Filters</h4>
                        </div>
                    </div>
                </div>
                <div class="col-sm-9">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-hover">
                            <thead>
                                <tr>
                                <th>Project Name/Activity</th>
                                <th style="width: 87px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template v-for="items, header in annexfs" :key="header">
                                    <tr><td class="fw-bold">{{header}}</td><th></th></tr>
                                    <template v-for="annexf in items" :key="'annexf_'+annexf.id">
                                        <tr>
                                            <td>{{annexf.title}}</td>
                                            <td>
                                                <button style="width: 37px" class="btn btn-sm btn-primary mb-1 me-1" @click="editForm(annexf, 'form')"><i class="far fa-pencil-alt"></i></button>
                                                <button style="width: 37px" class="btn btn-sm btn-danger mb-1"><i class="far fa-trash-alt"></i></button><br>
                                                <button class="btn btn-sm btn-outline-secondary" @click="editForm(annexf, 'detail')"><i class="far fa-pencil-alt"></i> Details</button>
                                            </td>
                                        </tr>
                                        <tr v-for="sub in annexf.subs" :key="'annexfsub_'+sub.id">
                                            <td colspan="2"><div class="ms-3">{{sub.subproject.title}}</div></td>
                                        </tr>
                                    </template>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>
<script>
import { dragscroll } from 'vue-dragscroll'
import { VMoney } from 'v-money'
import { mapActions, mapGetters } from 'vuex'
export default {
    name: 'Form',
    directives: {
        dragscroll: dragscroll,
        money: VMoney
    },
    data(){
        return {
            loading: true,
            formshow: false,
            detailshow: false,
            form: {
                id: '',
                program_id: '',
                subprogram_id: '',
                cluster_id: '',
                division_id: '',
                unit_id: '',
                subunit_id: '',
                workshop_id: this.$route.params.workshopId,
                projects: [{
                    multiple: false,
                    project_id: '',
                    project_ids: [],
                    subprojects: [],
                }],
                selectedProjects: [],
                remarks: '',
                title: '',
                activities: [],
                funds: [],
                total: 0
            },
            subprograms: [],
            clusters: [],
            units: [],
            subunits: [],
            columns: [
                'Oct', 'Nov', 'Dec',
                'Jan', 'Feb', 'Mar',
                'Apr', 'May', 'Jun',
                'Jul', 'Aug', 'Sep',
                'Oct', 'Nov', 'Dec',
            ],
            tempAmounts: [],
            tempSubitemAmounts: [],
            money: {
                decimal: '.',
                thousands: ',',
                prefix: '',
                suffix: '',
                precision: 2,
                masked: false /* doesn't work with directive */
            },
        }
    },
    methods: {
        ...mapActions('workshop', ['fetchOptions']),
        ...mapActions('annexf', ['fetchAnnexFs', 'saveAnnexF', 'deleteAnnexF']),
        resetForm(){
            this.formshow = true
            this.form.id = ''
            this.form.program_id = ''
            this.form.subprogram_id = ''
            this.form.cluster_id = ''
            this.form.division_id = ''
            this.form.unit_id = ''
            this.form.subunit_id = ''
            this.form.projects = [{
                    multiple: false,
                    project_id: '',
                    project_ids: [],
                    subprojects: []
                }]
            this.form.selectedProjects = []
            this.subprograms = []
            this.clusters = []
            this.units = []
            this.subunits = []
        },
        editForm(annexf, type){
            this.form.selectedProjects = []
            this.form.id = annexf.id
            var project = annexf.projects[0]
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
    
            this.form.projects = []
            var length = annexf.projects.length
            this.form.projects.push({
                multiple: (length > 1),
                project_id: '',
                project_ids: [],
                subprojects: [],
            })

            this.form.remarks = annexf.remarks
            this.form.title = annexf.title
            this.form.activities = []
            this.form.funds = []

            for(let i = 0; i < this.columns.length; i++){
                this.form.activities.push([{id: '', description: ''}])
                this.form.funds.push({id: '', amount: 0})
            }

            for(let i = 0; i < length; i++){
                project = annexf.projects[i]
                if(length > 1){ this.form.projects[0].project_ids.push(project.id) }
                else{ this.form.projects[0].project_id = project.id }
            }
            if(length == 1){
                this.projectChange(project.id)
                for(let i = 0; i < annexf.subs.length; i++){
                    var subproject = this.form.projects[0].subprojects.find(elem => elem.subproject_id == annexf.subs[i].subproject_id)
                    subproject.state = true
                    subproject.id = annexf.subs[i].id
                }
            }
            
            this.formshow = (type == 'form')
            this.detailshow = (type == 'detail')
        },
        submitForm(){
            if(this.formValidated()){
                this.saveAnnexF(this.form).then(res => {
                    var icon = res.errors ? 'error' : 'success'
                    this.toastMsg(icon, res.message)
                    if(!res.errors){
                        this.formshow = false
                    }
                })
            }
        },
        formValidated(){
            if(this.form.program_id == ''){ this.toastMsg('warning', 'Program Required'); return false }
            if(this.form.division_id == ''){ this.toastMsg('warning', 'Division Required'); return false }
            for(let i = 0; i < this.form.projects.length; i++){
                var project = this.form.projects[i]
                if(project.multiple){
                    if(project.project_ids.length < 2){ this.toastMsg('warning', 'Choose at least (2) two projects'); return false }
                }
                else{
                    if(project.project_id == ''){ this.toastMsg('warning', 'Project Required'); return false }
                }
            }
            return true
        },
         // Form Behavior
        projectChange(id){
            var project = (this.form.id) ? this.options.used_projects.find(elem => elem.id == id) : this.options.projects.find(elem => elem.id == id)
            var formproject = this.form.projects.find(elem => elem.project_id == id)
            formproject.subprojects = []
            for(let i = 0; i < project.subprojects.length; i++){
                var subproject = project.subprojects[i]
                formproject.subprojects.push({
                    id: '',
                    subproject_id: subproject.id,
                    title: subproject.title,
                    state: false,
                    activities: [],
                    funds: []
                })
            }

            if(!this.form.selectedProjects.includes(id)){
                this.form.selectedProjects.push(id)
            }
            for(let i = 0; i < this.form.selectedProjects.length; i++){
                var id = this.form.selectedProjects[i]
                var testproject = this.form.projects.find(elem => elem.project_id == id)
                if(!testproject){
                    this.form.selectedProjects.remove(id)
                }
            }
        },
        showProject(project){
            // tpis = Tempory Project Ids
            var tpis = {
                progId: project.program_id,
                divId:  project.division_id,
                subpId: (project.subprogram_id) ? project.subprogram_id : 0,
                clusId: (project.cluster_id)    ? project.cluster_id    : 0,
                unitId: (project.unit_id)       ? project.unit_id       : 0,
                subuId: (project.subunit_id)    ? project.subunit_id    : 0
            }
            // tsis = Temporary Selected Ids
            var tsis = {
                progId: (this.form.program_id)    ? this.form.program_id    : 0,
                divId:  (this.form.division_id)   ? this.form.division_id   : 0,
                subpId: (this.form.subprogram_id) ? this.form.subprogram_id : 0,
                clusId: (this.form.cluster_id)    ? this.form.cluster_id    : 0,
                unitId: (this.form.unit_id)       ? this.form.unit_id       : 0,
                subuId: (this.form.subunit_id)    ? this.form.subunit_id    : 0
            }
            
            tsis.subpId = (tpis.subpId != 0) ? tsis.subpId : 0
            tsis.clusId = (tpis.clusId != 0) ? tsis.clusId : 0
            tsis.unitId = (tpis.unitId != 0) ? tsis.unitId : 0
            tsis.subuId = (tpis.subuId != 0) ? tsis.subuId : 0

            return ((JSON.stringify(tpis) == JSON.stringify(tsis)))
        },
        checkboxProject(e, key, id){
            var checked = e.target.checked
            if(checked){
                this.form.selectedProjects.push(id)
                this.form.projects[key].project_ids.push(id)
            }
            else{
                this.form.selectedProjects.remove(id)
                this.form.projects[key].project_ids.remove(id)
            }
        },
        addProject(){
            this.form.projects.push({
                    multiple: false,
                    project_id: '',
                    project_ids: [],
                    subs: []
                })
        },
        removeProject(project){
            if(this.form.projects.length > 1){
                this.form.projects.remove(project)
                this.form.selectedProjects.remove(project.project_id)
            }
            else{
                this.form.projects[0] = {
                    multiple: false,
                    project_id: '',
                    project_ids: [],
                    subs: []
                }
                if(project.multiple){
                    for(let i = 0; i < project.project_ids.length; i++){
                        this.form.selectedProjects.remove(project.project_ids[i])
                    }
                }
                else{
                    this.form.selectedProjects.remove(project.project_id)
                }
            }
        },
        progChange(){
            this.form.subprogram_id = ''
            this.form.cluster_id = ''
            var program = this.options.programs.find(elem => elem.id == this.form.program_id)
            this.subprograms = program.subprograms
            this.clusters = []
            this.resetProject()
        },
        subpChange(){
            this.form.cluster_id = ''
            var clusters = []
            if(this.form.subprogram_id != 0){
                var subprogram = this.subprograms.find(elem => elem.id == this.form.subprogram_id)
                clusters = subprogram.clusters
            }
            this.clusters = clusters
            this.resetProject()
        },
        divChange(){
            this.form.unit_id = ''
            this.form.subunit_id = ''
            var division = this.options.divisions.find(elem => elem.id == this.form.division_id)
            this.units = division.units
            this.subunits = []
            this.resetProject()
        },
        unitChange(){
            this.form.subunit_id = ''
            var subunits = []
            if(this.form.unit_id != 0){
                var unit = this.units.find(elem => elem.id == this.form.unit_id)
                subunits = unit.subunits
            }
            this.subunits = subunits
            this.resetProject()
        },
        resetProject(){
            this.form.projects = [{
                    multiple: false,
                    project_id: '',
                    project_ids: [],
                    subs: []
                }]
        },
        // Form Details Behavior
        addActivity(key){
            this.form.activities[key].push({id: '', description: ''})
        },
        removeActivity(key, act){
            if(this.form.activities[key].length > 1){
                this.form.activities[key].remove(act)
            }
        },
        formatAmount(amount){
            return (Math.round(amount * 100) / 100).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2 })
        },
        getTotalAmount(){
            var total = 0
            for(let i = 0; i < this.form.funds.length; i++){
                if(i > 2){
                    var amount = this.strToFloat(this.form.funds[i].amount)
                    total = total + Math.abs(amount)
                }
            }
            return total
        },
        strToFloat(num){
            let strNum = num.toString().replace(/\,/g,'')
            return parseFloat(strNum)
        },
        // Toast
        toastMsg(icon, msg){
            toast.fire({
                icon: icon,
                title: msg
            })
        }
    },
    computed: {
        ...mapGetters('user', ['getAuthUser']),
        user(){ return this.getAuthUser },
        ...mapGetters('workshop', ['getOptions', 'getWorkshop']),
        options(){ return this.getOptions },
        workshop(){ return this.getWorkshop },
        ...mapGetters('annexf', ['getAnnexFs']),
        annexfs(){ return this.getAnnexFs }
    },
    created(){
        this.fetchOptions({workshopId: this.$route.params.workshopId, annex: 'f'}).then(res => {
            this.loading = false
        })
    }
}
</script>
<style scoped>
th{
    text-align: center;
}
</style>