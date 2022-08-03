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
                <div class="form-group row mb-2">
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
                <div class="form-group row mb-2">
                    <div class="col-sm-4 mb-3" v-for="proj, pkey in form.projects" :key="pkey+'_project'">
                        <div class="card shadow">
                            <div class="card-body">
                                <div class="form-floating mb-1">
                                    <select class="form-control" :id="'Project'+pkey" v-model="proj.project_id">
                                        <option value="" selected hidden disabled>Select Project</option>
                                        <template v-for="project in options.projects" :key="project.id+'_projectOption'">
                                        <option :value="project.id"  v-if="showProject(project)">{{project.title}}</option>
                                        </template>
                                    </select>
                                    <label :for="'Project'+pkey">Project {{pkey+1}} </label>
                                </div>
                                <div class="col" v-for="col, key in columns" :key="col">
                                    <div class="form-floating mb-1">
                                        <input type="text" class="form-control text-end" :id="col+key" :placeholder="col+key" v-model="proj[col]">
                                        <label :for="col+key">{{parseInt(workshop.year) + parseInt(key)}}</label>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button class="btn btn-sm btn-danger" @click="removeProject(proj)"><i class="far fa-trash-alt"></i> Remove</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end">
                <button style="min-width: 100px;" class="btn rounded-pill btn-outline-secondary me-1" @click="formshow = false">Cancel</button>
                <button style="min-width: 100px;" class="btn rounded-pill btn-success">Submit</button>
            </div>
        </div>
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
            form: {},
            subprograms: [],
            clusters: [],
            units: [],
            subunits: [],
            columns: ['col1', 'col2', 'col3', 'col4', 'col5', 'col6', 'col7']
        }
    },
    methods: {
        ...mapActions('workshop', ['fetchOptions']),
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
                source: '',
                header: '',
                projects: [
                    { project_id: '', col1: 0, col2: 0, col3: 0, col4: 0, col5: 0, col6: 0, col7: 0 }
                ]
            }
            this.subprograms = []
            this.clusters = []
            this.units = []
            this.subunits = []
        },

        // Form Behavior
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
            return (JSON.stringify(tpis) == JSON.stringify(tsis))
        },
        addProject(){
            this.form.projects.push({ project_id: '', col1: 0, col2: 0, col3: 0, col4: 0, col5: 0, col6: 0, col7: 0 })
        },
        removeProject(project){
            if(this.form.projects.length > 1){
                this.form.projects.remove(project)
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
    },
    computed: {
        ...mapGetters('workshop', ['getOptions', 'getWorkshop']),
        options(){ return this.getOptions },
        workshop(){ return this.getWorkshop }
    },
    created(){
        if(this.options.length == 0){
            this.fetchOptions()
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