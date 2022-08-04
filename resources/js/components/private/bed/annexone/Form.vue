<template>
    <div class="px-3 py-2">
        <div v-if="formshow">
            
            <div class="mb-2"><h4>Form</h4></div>
            <div class="form-container">
                <div class="form-grou row mb-2">
                    <strong class="mb-2">General Info</strong>
                    <div class="col-sm-3 mb-2">
                        <div class="form-floating">
                            <select class="form-select" id="Source" v-model="form.source">
                                <option value="" selected hidden disabled>Select Source of Funds</option>
                                <option value="2A1">2A1</option>
                                <option value="2A1-AC">2A1-AC</option>
                                <option value="2A2">2A2</option>
                            </select>
                            <label for="Source">Source of Funds</label>
                        </div>
                    </div>
                    <div class="col-sm-9">
                        <div class="form-floating">
                            <select class="form-select" id="Source" v-model="form.header">
                                <option value="" selected hidden disabled>Select Header Type</option>
                                <option value="None">None</option>
                                <option value="Subprogram">Subprogram</option>
                                <option value="Unit">Unit</option>
                            </select>
                            <label for="Source">Header Type</label>
                        </div>
                    </div>
                </div>
                <div class="form-group row mb-2">
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
                            <select class="form-select" id="Cluster" v-model="form.cluster_id">
                                <option value="" selected hidden disabled>Select Cluster</option>
                                <option :value="cluster.id" v-for="cluster in clusters" :key="cluster.id+'_clusOption'">{{cluster.title}}</option>
                                <option :value="0">N/A</option>
                                
                            </select>
                            <label for="Cluster">Cluster</label>
                        </div>
                    </div>
                </div>
                <div class="form-group row mb-2" v-if="user.active_profile.title == 'Superadmin Profile'">
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
                            <select class="form-select" id="Subunit" v-model="form.subunit_id">
                                <option value="" selected hidden disabled>Select Sub-Unit</option>
                                <option :value="subunit.id" v-for="subunit in subunits" :key="subunit.id+'_subuOption'">{{subunit.name}}</option>
                                <option :value="0">N/A</option>
                            </select>
                            <label for="Subunit">Sub-Unit</label>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <strong>Projects</strong>
                    <button class="btn btn-sm btn-success" @click="addProject()"><i class="fas fa-plus"></i> Project</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-hover" style="min-width: 1000px">
                        <thead>
                            <tr>
                                <th style="width: 300px">Project Title</th>
                                <th class="text-center" v-for="col, key in columns" :key="col+'_head'">{{parseInt(workshop.year)+parseInt(key)}}</th>
                                <th style="width: 20px"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template v-for="proj, pkey in form.projects" :key="pkey+'_projectrow'">
                                <tr>
                                    <td class="p-0">
                                        <select class="form-control form-control-sm rounded-0 border-0 shadow-none" v-model="proj.project_id" @change="projectChange(proj.project_id)" tabindex="-1">
                                            <option value="" disabled selected hidden>Select Project</option>
                                            <template v-for="project in options.projects" :key="project.id+'_projectOption'">
                                                <option :value="project.id"  v-if="showProject(project) || project.id == proj.project_id">{{project.title}}</option>
                                            </template>
                                        </select>
                                    </td>
                                    <td class="p-0" v-for="col in columns" :key="pkey+'-'+col">
                                        <input type="text" class="form-control form-control-sm rounded-0 border-0 shadow-none text-end" v-model.lazy="proj[col]" v-money="money">
                                    </td>
                                    <td class="p-0 d-flex"><button tabindex="-1" class="btn btn-sm btn-danger rounded-0 w-100 shadow-none" @click="removeProject(proj)"><i class="fas fa-times"></i></button></td>
                                </tr>
                                <tr v-for="subproject in proj.subprojects" :key="subproject.id+'_subprojectrow'">
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" v-model="subproject.state" :id="'subprojectcheck'+subproject.subproject_id" tabindex="-1">
                                            <label class="form-check-label w-100" :for="'subprojectcheck'+subproject.subproject_id">
                                                <small>{{subproject.title}}</small>
                                            </label>
                                        </div>
                                    </td>
                                    <td class="p-0" style="height: 1px;" v-for="col in columns" :key="pkey+'-'+col">
                                        <input v-if="subproject.state" type="text" class="form-control form-control-sm rounded-0 border-0 shadow-none text-end h-100" v-model.lazy="subproject[col]" v-money="money">
                                    </td><td></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="d-flex justify-content-end">
                <button style="min-width: 100px;" class="btn rounded-pill btn-outline-secondary me-1" @click="formshow = false">Cancel</button>
                <button style="min-width: 100px;" class="btn rounded-pill btn-success" @click="submitForm()">Submit</button>
            </div>
        </div>
        <template v-else>
            <div class="d-flex justify-content-end" v-if="!loading">
                <button class="btn btn-sm btn-success" @click="resetForm()"><i class="fas fa-plus"></i></button>
            </div>
            <h1 class="text-center p-5" v-else><i class="fas fa-spinner fa-spin fa-5x"></i></h1>
        </template>
    </div>
</template>
<script>
import { mapActions, mapGetters } from 'vuex'
import { VMoney } from 'v-money'
export default {
    name: 'Form',
    data(){
        return {
            loading: false,
            formshow: false,
            form: {},
            subprograms: [],
            clusters: [],
            units: [],
            subunits: [],
            columns: ['col1', 'col2', 'col3', 'col4', 'col5', 'col6', 'col7'],
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
    directives: {money: VMoney},
    methods: {
        ...mapActions('workshop', ['fetchOptions']),
        ...mapActions('annexone', ['saveAnnexOne']),
        resetForm(){
            this.formshow = true
            this.form = {
                id: '',
                workshop_id: this.$route.params.workshopId,
                workshop_year: parseInt(this.workshop.year),
                program_id: '',
                subprogram_id: '',
                cluster_id: '',
                division_id: '',
                unit_id: '',
                subunit_id: '',
                source: '',
                header: '',
                projects: [{ 
                    project_id: '', 
                    subprojects: [], 
                    col1: 0, col2: 0, col3: 0, col4: 0, col5: 0, col6: 0, col7: 0 
                }],
                selectedProjects: []
            }
            this.subprograms = []
            this.clusters = []

            this.form.division_id = this.user.division_id
            this.divChange()
            this.form.unit_id = (this.user.unit_id) ? this.user.unit_id : 0
            this.unitChange()
            this.form.subunit_id = (this.user.subunit_id) ? this.user.subunit_id: 0
        },
        submitForm(){
            if(this.formValidated()){
                this.saveAnnexOne(this.form).then(res => {
                    console.log(res)
                    var icon = res.errors ? 'error' : 'success'
                    this.toastMsg(icon, res.message)
                    this.formshow = false
                })
            }
        },
        formValidated(){
            if(this.form.source == ''){ this.toastMsg('warning', 'Please select a Source of Funds'); return false }
            if(this.form.header == ''){ this.toastMsg('warning', 'Please select a Header Type'); return false }
            for(let i = 0; i < this.form.projects.length; i++){
                if(this.form.projects[i].project_id == ''){ this.toastMsg('warning', 'Empty Project detected!'); return false }
            }
            return true
        },

        // Form Behavior
        projectChange(id){
            var project = this.options.projects.find(elem => elem.id == id)
            var formproject = this.form.projects.find(elem => elem.project_id == id)
            formproject.subprojects = []
            for(let i = 0; i < project.subprojects.length; i++){
                var subproject = project.subprojects[i]
                formproject.subprojects.push({
                    id: '',
                    subproject_id: subproject.id,
                    title: subproject.title,
                    state: false,
                    col1: 0, col2: 0, col3: 0, col4: 0, col5: 0, col6: 0, col7: 0 
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

            return ((JSON.stringify(tpis) == JSON.stringify(tsis)) && !this.form.selectedProjects.includes(project.id))
        },
        addProject(){
            this.form.projects.push({ project_id: '', subprojects: [], col1: 0, col2: 0, col3: 0, col4: 0, col5: 0, col6: 0, col7: 0 })
        },
        removeProject(project){
            if(this.form.projects.length > 1){
                this.form.projects.remove(project)
                this.form.selectedProjects.remove(project.project_id)
            }
            else{
                this.form.projects[0] = { project_id: '', subprojects: [], col1: 0, col2: 0, col3: 0, col4: 0, col5: 0, col6: 0, col7: 0 }
                this.form.selectedProjects.remove(project.project_id)
            }
        },
        progChange(){
            this.form.subprogram_id = ''
            this.form.cluster_id = ''
            var program = this.options.programs.find(elem => elem.id == this.form.program_id)
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
            var division = this.options.divisions.find(elem => elem.id == this.form.division_id)
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

        // Others
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
        ...mapGetters('annexone', ['getAnnexOnes']),
        annexones(){ return this.getAnnexOnes }
    },
    created(){
        if(this.options.length == 0){
            this.loading = true
            this.fetchOptions().then(res => {
                this.loading = false
            })
        }
    }
}
</script>
<style scoped>
.form-container{
    max-height: 62vh;
    overflow: auto;
    overflow-x: hidden;
    margin-bottom: 15px;
    padding: 1rem;
}
</style>