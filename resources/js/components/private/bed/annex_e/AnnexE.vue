<template>
    <div class="px-3 py-4">
        <button class="btn btn-sm btn-light float-start" @click="this.$router.back()"><i class="fas fa-arrow-left"></i> Back</button>
        <h2 class="text-center">Annex E</h2>
        <small>Planning Workshop <span v-if="!loading">{{workshop.date}}</span><span v-else>Loading date <i class="fas fa-spinner fa-spin"></i></span></small><hr>
        <template v-if="!loading">
            <div class="row flex-row-reverse" v-if="!formshow && !detailshow">
                <div class="col-sm-3">
                    <div class="d-flex justify-content-end mb-3">
                        <button style="width: 120px" class="btn shadow-none" :class="!editmode ? 'btn-success' : 'btn-primary'" @click="editmode = !editmode">{{editmode ? 'View' : 'Edit'}} Mode</button>
                    </div>
                    <div class="card shadow mb-3">
                        <div class="card-body">
                            <div class="form-floating mb-3">
                                <select class="form-control" id="Status" v-model="displaystatus">
                                    <option value="New">New</option>
                                    <option value="Draft">Draft</option>
                                    <option value="For Review">For Review</option>
                                    <option value="For Approval">For Approval</option>
                                    <option value="Approved">Approved</option>
                                    <option value="Submitted">Submitted</option>
                                </select>
                                <label for="Status">Status</label>
                            </div>
                            <div class="form-floating mb-3">
                                <select class="form-control" id="displayType" v-model="displaytype" @change="displaytypeChange()">
                                    <option value="Program">Program</option>
                                    <option value="Division">Division</option>
                                </select>
                                <label for="displayType">Type</label>
                            </div>
                            <template v-if="displaytype == 'Program'">
                                <div class="form-floating mb-3">
                                    <select class="form-control" id="Program" v-model="disProg.program_id" @change="progChange()">
                                        <option :value="0">Any Program</option>
                                        <option :value="program.id" v-for="program in programs" :key="'program_'+program.id">{{program.title}}</option>
                                    </select>
                                    <label for="Program">Program</label>
                                </div>
                                <div class="form-floating mb-3" v-if="subprograms.length > 0">
                                    <select class="form-control" id="SubProgram" v-model="disProg.subprogram_id" @change="subpChange()">
                                        <option :value="0">Any Sub-Program</option>
                                        <option :value="subprogram.id" v-for="subprogram in subprograms" :key="'subprogram_'+subprogram.id">{{(disProg.program_id != 2) ? subprogram.title_short : subprogram.title}}</option>
                                    </select>
                                    <label for="SubProgram">Sub-Program</label>
                                </div>
                                <div class="form-floating mb-3" v-if="clusters.length > 0">
                                    <select class="form-control" id="Cluster" v-model="disProg.cluster_id">
                                        <option :value="0">Any Cluster</option>
                                        <option :value="cluster.id" v-for="cluster in clusters" :key="'cluster_'+cluster.id">{{cluster.title}}</option>
                                    </select>
                                    <label for="Cluster">Cluster</label>
                                </div>
                            </template>
                            <template v-if="displaytype == 'Division'">
                                <div class="form-floating mb-3">
                                    <select class="form-control" id="Division" v-model="disDiv.division_id" @change="divChange()">
                                        <option :value="0">Any Division</option>
                                        <option :value="division.id" v-for="division in divisions" :key="'division_'+division.id">{{division.name}}</option>
                                    </select>
                                    <label for="Division">Division</label>
                                </div>
                                <div class="form-floating mb-3" v-if="units.length > 0">
                                    <select class="form-control" id="Unit" v-model="disDiv.unit_id" @change="unitChange()">
                                        <option :value="0">Any Unit</option>
                                        <option :value="unit.id" v-for="unit in units" :key="'unit_'+unit.id">{{unit.name}}</option>
                                    </select>
                                    <label for="Unit">Unit</label>
                                </div>
                                <div class="form-floating mb-3" v-if="subunits.length > 0">
                                    <select class="form-control" id="SubUnit" v-model="disDiv.subunit_id">
                                        <option :value="0">Any Sub-Unit</option>
                                        <option :value="subunit.id" v-for="subunit in subunits" :key="'subunit_'+subunit.id">{{subunit.name}}</option>
                                    </select>
                                    <label for="SubUnit">Sub-Unit</label>
                                </div>
                            </template>
                            <div class="d-flex justify-content-end">
                                <button class="btn btn-sm btn-primary shadow-none" @click="syncRecords()">{{syncing ? 'Syncing...' : 'Sync'}} <i class="far fa-sync-alt" :class="syncing ? 'fa-spin' : ''"></i></button>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="col-sm-9 mb-3">
                    <div class="card shadow overflow-auto" style="height: 76vh" v-if="!editmode">
                        <div class="card-body">
                            <h6 class="text-end fw-bold">Annex E</h6>
                            <h6 class="text-center mb-3 fw-bold">CY {{workshop.year}} PHYSICAL PLAN </h6>
                            <EmptyTable />

                            <span class="position-absolute bottom-0"><small>Empty Data Set</small></span>
                        </div>
                    </div>
                    <div v-else>
                        <div class="d-flex justify-content-end mb-2">
                            <button class="btn btn-sm btn-success me-1">Batch Status Change</button>
                            <button class="btn btn-sm btn-success"><i class="fas fa-plus"></i> New</button>
                        </div>
                        <div class="table-responsive" style="height: 70vh">
                            <table class="table table-sm table-bordered table-hover">
                                <thead class="text-center">
                                    <tr>
                                        <th style="width: 40%">Program/Project</th>
                                        <th style="width: 40%">Performance Indicators (PIs)</th>
                                        <th style="width: 20%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template v-if="!syncing">
                                        <template v-for="items, header in annexes" :key="'annexe_'+header">
                                            <tr><th class="bg-success text-white" colspan="3">{{header}}</th></tr>
                                            <template v-for="item in items" :key="'annexe_'+item.id">
                                                <tr>
                                                    <td><div class="ms-2 fw-bold">{{item.project.title}}</div></td>
                                                    <td class="p-0" style="height: 1px;">
                                                        <table class="table table-sm h-100 m-0">
                                                            <tr v-for="indicator, key in item.indicators" :key="'indicator_'+indicator.id">
                                                                <td :class="(item.indicators.length - 1) != key ? 'border-bottom' : ''">{{indicator.description}}</td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                    <td class="text-center" :rowspan="item.subs.length + 1">
                                                        <button style="width: 48px"  class="shadow-none btn btn-sm btn-primary me-1 mb-1"><i class="far fa-pencil-alt"></i></button>
                                                        <button style="width: 48px"  class="shadow-none btn btn-sm btn-danger me-1 mb-1"><i class="far fa-trash-alt"></i></button><br>
                                                        <button style="width: 100px" class="shadow-none btn btn-sm btn-outline-secondary me-1 mb-1"><i class="far fa-pencil-alt"></i> Indicators</button><br>
                                                        <button style="width: 100px" class="shadow-none btn btn-sm btn-outline-secondary me-1 mb-1" @click="editForm(item, 'details')"><i class="far fa-pencil-alt"></i> Details</button>
                                                    </td>
                                                </tr>
                                                <tr v-for="sub in item.subs" :key="'sub_'+sub.id">
                                                    <td><div class="ms-4">{{(sub.subproject_id !== null) ? sub.subproject.title : (sub.temp_title == 'ms' ? 'MS' : sub.temp_title == 'phd' ? 'PhD' : sub.temp_title)}}</div></td>
                                                    
                                                    <td class="p-0" style="height: 1px;">
                                                        <table class="table table-sm h-100 m-0">
                                                            <tr v-for="indicator, key in sub.indicators" :key="'indicator_'+indicator.id">
                                                                <td :class="(sub.indicators.length - 1) != key ? 'border-bottom' : ''"><div class="ms-2">{{indicator.description}}</div></td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </template>
                                        </template>
                                    </template>
                                    <tr v-else><td colspan="3" class="text-center p-4"><h3>Syncing <i class="fas fa-sync fa-spin"></i> </h3></td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="detailshow">
                <div class="d-flex justify-content-between mb-3">
                    <button class="btn btn-sm btn-danger" @click="detailshow = false"><i class="fas fa-times"></i> Cancel</button>
                    <button class="btn btn-sm btn-primary" @click="detailshow = false"><i class="fas fa-save"></i> Save changes</button>
                </div>
                <!-- <table>
                    <tr>
                        <th>Company</th>
                        <th>Contact</th>
                        <th style="width: 101px">Country</th>
                    </tr>
                    <tr>
                        <td rowspan="3">test</td>
                        <td colspan="2" class="p-0"><table>
                            <tr>
                                <td>test</td>    
                                <td>test</td>    
                            </tr>
                            <tr>
                                <td>test</td>    
                                <td style="width: 100px">{{workshop.year}}</td>    
                            </tr>
                        </table></td>
                    </tr>
                </table> -->
                <table>
                    <tr>
                        <th>Company</th>
                        <th>Contact</th>
                        <th>{{workshop.year}}</th>
                    </tr>
                    <tr>
                        <td rowspan="2">test</td>
                        <td>test 1</td>
                        <td>test 1</td>
                    </tr>
                    <tr>
                        <td>test 1</td>
                        <td>test 1</td>
                    </tr>
                </table>
                                <!-- eslint-disable-next-line -->
            </div>
        </template>
        <h1 v-else class="text-center p-5"><i class="fas fa-spinner fa-spin fa-5x"></i></h1>
    </div>
</template>
<script>
import EmptyTable from './EmptyTable.vue'
import Display from './Display.vue'
import Form from './Form.vue'
import { mapActions, mapGetters } from 'vuex'
export default {
    name: 'AnnexE',
    components: { EmptyTable, Display, Form },
    data(){
        return {
            displaytype: 'Program',
            displaystatus: 'New',
            disProg: {
                program_id: 0,
                subprogram_id: 0,
                cluster_id: 0
            },
            subprograms: [],
            clusters: [],
            disDiv: {
                division_id: 0,
                unit_id: 0,
                subunit_id: 0
            },
            units: [],
            subunits: [],
            syncing: false,
            loading: true,
            editmode: false,
            
            // form
            formshow: false,
            detailshow: false,
            form: {
                id: '',
                project_id: '',
                project_title: '',
                indicators: []
            },
            // indicator columns
            indcols: ['description', 'actual', 'estimate', 'physical_targets', 'first', 'second', 'third', 'fourth']
        }
    },
    methods: {
        ...mapActions('workshop', ['fetchWorkshop']),
        ...mapActions('annexe', ['fetchAnnexEs']),
        ...mapActions('program', ['fetchPrograms']),
        ...mapActions('division', ['fetchDivisions']),
        displaytypeChange(){
            this.disProg.program_id = 0
            this.disProg.subprogram_id = 0
            this.disProg.cluster_id = 0
            this.subprograms = []
            this.clusters = []

            this.disDiv.division_id = 0
            this.disDiv.unit_id = 0
            this.disDiv.subunit_id = 0
            this.units = []
            this.subunits = []
        },
        progChange(){
            this.disProg.subprogram_id = 0
            this.disProg.cluster_id = 0
            this.subprograms = []
            this.clusters = []

            var progId = this.disProg.program_id
            if(progId != 0){
                var program = this.programs.find(elem => elem.id == progId)
                this.subprograms = program.subprograms
            }
        },
        subpChange(){
            this.disProg.cluster_id = 0
            this.clusters = []
            var subpId = this.disProg.subprogram_id
            if(subpId != 0){
                var subprogram = this.subprograms.find(elem => elem.id == subpId)
                this.clusters = subprogram.clusters
            }
        },
        divChange(){
            this.disDiv.unit_id = 0
            this.disDiv.subunit_id = 0
            this.units = []
            this.subunits = []

            var divId = this.disDiv.division_id
            if(divId != 0){
                var division = this.divisions.find(elem => elem.id == divId)
                this.units = division.units
            }
        },
        unitChange(){
            this.disDiv.subunit_id = 0
            this.subunits = []
            var unitId = this.disDiv.unit_id
            if(unitId != 0){
                var unit = this.units.find(elem => elem.id == unitId)
                this.subunits = unit.subunits
            }
        },
        syncRecords(){
            this.syncing = true
            var type = this.displaytype

            var options = (type == 'Program') ? this.disProg : this.disDiv
            options.type = type
            options.workshopId = this.$route.params.workshopId
            options.status     = this.displaystatus
            this.fetchAnnexEs(options).then(res => {
                this.syncing = false
            })
        },

        // Form
        editForm(item, type){
            this.form.id = item.id
            this.form.project_id = item.project_id
            this.form.project_title = item.project.title
            this.form.indicators = []

            for(let i = 0; i < item.indicators.length; i++){
                var indicator = item.indicators[i]
                var tempIndicator = {
                    id: indicator.id,
                    description: indicator.description,
                    actual:           (indicator.details === null) ? 0 : indicator.details.actual,
                    estimate:         (indicator.details === null) ? 0 : indicator.details.estimate,
                    physical_targets: (indicator.details === null) ? 0 : indicator.details.physical_targets,
                    first:            (indicator.details === null) ? 0 : indicator.details.first,
                    second:           (indicator.details === null) ? 0 : indicator.details.second,
                    third:            (indicator.details === null) ? 0 : indicator.details.third,
                    fourth:           (indicator.details === null) ? 0 : indicator.details.fourth,
                }
                this.form.indicators.push(tempIndicator)
            }

            this.detailshow = (type == 'details')
        },

        testfunction(){
            return 2020
        },
        // Toast
        toastMsg(icon, msg){
            toast.fire({
                icon: icon,
                title: msg
            })
        },
    },
    computed: {
        ...mapGetters('workshop', ['getWorkshop']),
        workshop(){ return this.getWorkshop },
        ...mapGetters('program', ['getPrograms']),
        programs(){ return this.getPrograms },
        ...mapGetters('division', ['getDivisions']),
        divisions(){ return this.getDivisions },
        ...mapGetters('annexe', ['getAnnexEs']),
        annexes(){ return this.getAnnexEs }
    },
    created(){
        this.fetchWorkshop(this.$route.params.workshopId).then(res => {
            // this.fetchAnnexEs(this.$route.params.workshopId).then(res => {
                this.loading = false
            // })
        })
        if(this.programs.length == 0){
            this.fetchPrograms()
        }
        if(this.divisions.length == 0){
            this.fetchDivisions()
        }
    }
}
</script>
<style scoped>
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td, th {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;
}
</style>