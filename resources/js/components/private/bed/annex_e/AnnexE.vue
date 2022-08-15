<template>
    <div class="px-3 py-4">
        <button class="btn btn-sm btn-light float-start" @click="this.$router.back()"><i class="fas fa-arrow-left"></i> Back</button>
        <h2 class="text-center">Annex E</h2>
        <small>Planning Workshop <span v-if="!loading">{{workshop.date}}</span><span v-else>Loading date <i class="fas fa-spinner fa-spin"></i></span></small><hr>
        <template v-if="!loading">
            <div class="row flex-row-reverse">
                <div class="col-sm-3">
                    <div class="card shadow">
                        <div class="card-body">
                            <div class="form-floating mb-3">
                                <select class="form-control" id="displayType" v-model="displaytype" @change="displaytypeChange()">
                                    <option value="Program">Program</option>
                                    <option value="Division">Division</option>
                                </select>
                                <label for="displayType">Display Type</label>
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
                    <!-- <div class="my-3">
                        <button @click="displaystatus = 'Submitted'"    :class="displaystatus == 'Submitted'    ? 'btn-success'   : 'btn-outline-success'"   class="btn btn-sm shadow-none rounded-0">Submitted</button>
                        <button @click="displaystatus = 'For Approval'" :class="displaystatus == 'For Approval' ? 'btn-primary'   : 'btn-outline-primary'"   class="btn btn-sm shadow-none rounded-0">For Approval</button>
                        <button @click="displaystatus = 'For Review'"   :class="displaystatus == 'For Review'   ? 'btn-info'      : 'btn-outline-info'"   class="btn btn-sm shadow-none rounded-0">For Review</button>
                        <button @click="displaystatus = 'Drafts'"       :class="displaystatus == 'Drafts'       ? 'btn-secondary' : 'btn-outline-secondary'" class="btn btn-sm shadow-none rounded-0" style="width: 80px">Drafts</button>
                    </div> -->
                    <div class="card shadow overflow-auto" style="height: 76vh">
                        <div class="card-body">
                            <h6 class="text-end">Annex E</h6>
                            <h6 class="text-center">CY {{workshop.year}} PHYSICAL PLAN </h6>
                            <!-- <pre>{{annexes}}</pre> -->
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <h1 v-else class="text-center p-5"><i class="fas fa-spinner fa-spin fa-5x"></i></h1>
    </div>
</template>
<script>
import Display from './Display.vue'
import Form from './Form.vue'
import { mapActions, mapGetters } from 'vuex'
export default {
    name: 'AnnexE',
    components: { Display, Form },
    data(){
        return {
            displaytype: 'Program',
            // displaystatus: 'Submitted',
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
            //  old
            editmode: false,
            program_id: 1
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
            // if(type == 'Program'){
            //     if(this.disProg.program_id == 0){ this.toastMsg('warning', 'Select a Program'); this.syncing = false; return false }
            // }
            // else{
            //     if(this.disDiv.division_id == 0){ this.toastMsg('warning', 'Select a Divison'); this.syncing = false; return false }
            // }

            var options = (type == 'Program') ? this.disProg : this.disDiv
            options.type = type
            options.workshopId = this.$route.params.workshopId
            this.fetchAnnexEs(options).then(res => {
                this.syncing = false
            })
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