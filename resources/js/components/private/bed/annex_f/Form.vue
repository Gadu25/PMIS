<template>
    <div class="px-2 my-2">
        <template v-if="formshow">
            <h4>Form</h4>
            <strong>Program</strong>
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
            <strong>Division</strong>
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
            <button class="btn btn-sm btn-success float-end" @click="addProject()"><i class="fas fa-plus"></i> Project</button>
            <strong>Projects</strong>
            <div class="d-flex flex-wrap mt-3">
                <div class="col-sm-6 px-2 my-2" v-for="proj, key in form.projects" :key="'project_'+key">
                    <div class="card shadow">
                        <div class="card-body">
                            <button class="btn btn-xs btn-danger float-end" @click="removeProject(proj)"><i class="far fa-trash-alt"></i> Remove</button>
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
        <template v-else>   
            <div class="d-flex justify-content-end">
                <button class="btn btn-sm btn-success" @click="resetForm()"><i class="fas fa-plus"></i></button>
            </div>
        </template>
    </div>
</template>
<script>
import { mapActions, mapGetters } from 'vuex'
export default {
    name: 'Form',
    data(){
        return {
            loading: true,
            formshow: false,
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
                    subprojects: []
                }],
                selectedProjects: []
            },
            subprograms: [],
            clusters: [],
            units: [],
            subunits: []
        }
    },
    methods: {
        ...mapActions('workshop', ['fetchOptions']),
        ...mapActions('annexf', ['fetchAnnexFs', 'saveAnnexF', 'deleteAnnexF']),
        resetForm(){
            this.formshow = true
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
                    state: false
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
        // ...mapGetters('division', ['getDivisions']),
        // divisions(){ return this.getDivisions },
    },
    created(){
        this.fetchOptions({workshopId: this.$route.params.workshopId, annex: 'f'}).then(res => {
            this.loading = false
        })
    }
}
</script>