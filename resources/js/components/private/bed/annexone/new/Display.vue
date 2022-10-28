<template>
    <div class="row flex-wrap-reverse">
        <div class="position-relative" :class="toggle ? 'col-sm-9' : 'col-sm-12'">
            <button v-if="!toggle && !formshow" @click="toggle = !toggle" style="z-index: 99" class="btn btn-sm btn-secondary bg-gradient position-absolute end-0"><i class="fas fa-arrow-left"></i></button>
            <div v-if="!editmode" class="card border-0 shadow rounded-0">
                <div id="main-container">
                    <div class="card-body" v-if="!syncing" id="printMe">
                        <div :style="'font-size:' + fontsize" class="d-flex justify-content-end fw-bold">Annex 1</div>
                        <div class="text-center mb-3">
                            <h6 :style="'font-size:' + fontsize" class="mb-0 fw-bold">SEI Annual Planning Workshop</h6>
                            <h6 :style="'font-size:' + fontsize" class="fw-bold">{{workshop.date}}</h6>
                        </div>
                        <div :class="!exportmode ? 'table-responsive' : '   '" :id="!exportmode ? 'table-container' : ''" v-dragscroll>
                            <table class="table table-sm table-bordered" :style="'font-size:' + fontsize">
                                <TableHead :forPrint="exportmode" />
                                <tbody class="align-middle">
                                    <template v-for="sources, div in annexones" :key="div">
                                        <tr class="fw-bold" style="background: orange;">
                                            <td>{{div}}</td>
                                            <td class="text-end" v-for="num in 13" :key="num">
                                                {{ setDivisionFund(num, sources, div) }}
                                            </td>
                                        </tr>
                                        <template v-for="headers, source in sources" :key="source">
                                            <tr v-if="div != 'STSD'" class="fw-bold text-danger" style="background: yellow;">
                                                <td class="text-center">{{source}}</td>
                                                <td class="text-end" v-for="num in 13" :key="num">
                                                    {{ setSourceFund(num, headers) }}
                                                </td>
                                            </tr>
                                            <template v-for="items, header in headers" :key="header">
                                                <tr class="fw-bold" v-if="header != 'None'">
                                                    <td>{{header}}</td>
                                                    <td class="text-end" v-for="num in 13" :key="num">
                                                        {{ isSubprogram(items) ? setHeaderFund(num, items) : '' }}
                                                    </td>
                                                </tr>
                                                <template v-for="item in items" :key="item.id">
                                                    <tr>
                                                        <td><div class="ms-1">{{item.project.title}}</div></td>
                                                        <td class="text-end" v-for="num in 13" :key="num">
                                                            {{ setFund(num, item.funds, item.subs) }}
                                                        </td>
                                                    </tr>
                                                    <template v-for="subitem in item.subs" :key="subitem">
                                                        <tr>
                                                            <td><div class="ms-3">{{subitem.subproject.title}}</div></td>
                                                            <td class="text-end" v-for="num in 13" :key="num">
                                                                {{ setFund(num, subitem.funds) }}
                                                            </td>
                                                        </tr>
                                                    </template>
                                                </template>
                                                <template v-if="!isSubprogram(items)">
                                                    <tr style="background: green;" class="fw-bold">
                                                        <td class="text-center">Sub-Total</td>
                                                        <td class="text-end" v-for="num in 13" :key="num">
                                                            {{ setHeaderFund(num, items) }}
                                                        </td>
                                                    </tr>
                                                </template>
                                            </template>
                                            <template v-if="div == 'STSD' && division_id == 0">
                                                <tr style="background: #7aa9cf;" class="fw-bold">
                                                    <td class="stsdtotals">Total 2A1</td>
                                                    <td class="text-end" v-for="num in 13" :key="num">
                                                        {{ setSourceFund(num, headers) }}
                                                    </td>
                                                </tr>
                                                <tr style="background: #b4c867;" class="fw-bold">
                                                    <td class="stsdtotals">Less AC</td>
                                                    <td class="text-end" v-for="num in 13" :key="num">
                                                        {{ setBySource(num, 'AC') }}
                                                    </td>
                                                </tr>
                                                <tr style="background: #7aa9cf;" class="fw-bold">
                                                    <td class="stsdtotals">Total for 2A2</td>
                                                    <td class="text-end" v-for="num in 13" :key="num">
                                                        {{ setBySource(num, '2A2') }}
                                                    </td>
                                                </tr>
                                            </template>
                                        </template>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-body p-5 text-center" v-else>
                        <h1>Data Syncing <i class="far fa-sync fa-spin"></i></h1>
                    </div>
                </div>
            </div>
            <Form :syncing="syncing" @clicked="childClicked()" v-else />
        </div>
        <div class="col-sm-3" v-if="toggle && !formshow">
            <div class="card border-0 shadow mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <strong>Filters</strong>
                        <button @click="toggle = !toggle" class="btn btn-sm btn-outline-secondary bg-gradient"><i class="fas fa-arrow-right"></i></button>
                    </div>
                    <div class="form-floating mb-2">
                        <select class="form-select" id="Division" v-model="division_id">
                            <option :value="0" selected>All Division</option>
                            <option :value="division.id" v-for="division in divisions" :key="division.id">{{division.name}}</option>
                        </select>
                        <label for="Division">Division</label>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button @click="syncRecords()" class="btn btn-sm btn-primary bg-gradient"><i class="fas fa-sync"></i> Sync</button>
                    </div>
                </div>
            </div>
            <template v-if="!editmode">
                <div class="card border-0 shadow mb-3">
                    <div class="card-body">
                        <span class="btn" v-if="!exportmode" @click="exportmode = true">Export Annex 1</span>
                        
                        <template v-if="exportmode">
                            <p class="btn" @click="exportmode = false"><strong>Exit Export Mode</strong></p>
                            <button class="btn btn-sm w-100 btn-outline-secondary mb-2" v-print="'#printMe'"><i class="far fa-print"></i> Print / Save as PDF</button><br>
                            <a class="btn btn-sm w-100 btn-success bg-gradient" href="/api/export/1/0" target="_blank"><i class="far fa-file-excel"></i> Download as Excel</a>
                        </template>
                    </div>
                </div>
                <div class="d-flex justify-content-end">
                    <button v-if="inUserRoles('annex_one_publish_projects')" class="btn btn-sm btn-warning bg-gradient" data-bs-target="#modal" data-bs-toggle="modal">Publish Projects</button>
                </div>
            </template>
        </div>
        <div class="modal fade" id="modal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Publish Projects</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" ref="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- <template v-if="workshop.published"> -->
                            <!-- Temporary disabled other functionalities for other Programs, this is to focus on the only Program 1 RA 10612 and RA 7687/ 2067. This can be further improved -->
                            <!-- <button class="btn btn-sm btn-success float-end"><i class="fas fa-plus"></i></button> -->
                            <p class="fw-bold">Annex F Special Case <i class="far fa-question-circle"></i></p><hr>
                            <div class="mb-2 border-bottom" v-for="specialcase, key in form.cases" :key="key">
                                <div class="form-floating mb-3">
                                    <select class="form-select" id="Programs" v-model="specialcase.program_id" @change="changeProgram(key)">
                                        <option value="" disabled hidden selected>Select Program</option>
                                        <template v-for="program in programs" :key="program.id">
                                            <option :value="program.id" v-if="program.id == 1">{{program.title}}</option>
                                        </template>
                                    </select>
                                    <label for="Programs">Programs</label>
                                </div>
                                <div class="form-group row" v-if="specialcase.subprograms.length > 0">
                                    <!-- <div :class="specialcase.clusters.length == 0 ? 'col-sm-12' : 'col-sm-6'"> -->
                                    <div class="col-sm-12">
                                        <div class="form-floating mb-3">
                                            <select class="form-select" id="SubPrograms" v-model="specialcase.subprogram_id" @change="changeSubprogram(key)">
                                                <option value="" disabled hidden selected>Select Sub-Program</option>
                                                <option :value="subprogram.id" v-for="subprogram in specialcase.subprograms" :key="subprogram.id">{{specialcase.program_id == 1 ? subprogram.title_short : subprogram.title}}</option>
                                            </select>
                                            <label for="SubPrograms">Sub-Programs</label>
                                        </div>
                                    </div>
                                    <!-- <div class="col-sm-6" v-if="specialcase.clusters.length > 0">
                                        <div class="form-floating mb-3">
                                            <select class="form-select" id="Clusters" v-model="specialcase.cluster_id" @change="changeCluster(key)">
                                                <option value="" disabled hidden selected>Select Clusters</option>
                                                <option :value="cluster.id" v-for="cluster in specialcase.clusters" :key="cluster.id">{{cluster.title}}</option>
                                            </select>
                                            <label for="Clusters">Clusters</label>
                                        </div>
                                    </div> -->
                                </div>
                                <div>
                                    <div class="form-check" v-for="project in specialcase.projects" :key="project.id">
                                        <input class="form-check-input" type="checkbox" :value="project.id" :id="project.id+'project'+key" v-model="specialcase.projectIds">
                                        <label class="form-check-label" :for="project.id+'project'+key">
                                            {{project.title}}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        <!-- </template> -->
                        <!-- <div v-else class="text-center p-5">
                            <h3>Projects already published</h3>
                        </div> -->
                    </div>
                    <!-- <div class="modal-footer" v-if="workshop.published"> -->
                    <div class="modal-footer">
                        <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> -->
                        <button type="button" class="btn btn-success bg-gradient" @click="publish()">Publish Projects</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { mapActions, mapGetters } from 'vuex'
import { dragscroll } from 'vue-dragscroll'
import Form from './Form.vue'
import TableHead from './TableHead.vue'
export default {
    name: 'Display',
    emits: ['clicked'],
    setup({emit}){},
    directives: {
        dragscroll: dragscroll,
    },
    components: { Form, TableHead },
    data(){
        return {
            fontsize: '12px',
            toggle: true,
            prev: false,
            formshow: false,
            exportmode: false,
            form: {
                workshop_id: this.$route.params.workshopId,
                cases: [{
                    program_id: '',
                    subprogram_id: 2,
                    cluster_id: '',
                    projects: [],
                    projectIds: [],
                    subprograms: [],
                    clusters: [],
                }]
            },
            division_id: 0,
            syncing: false
        }
    },
    methods: {
        ...mapActions('division', ['fetchDivisions']),
        ...mapActions('program', ['fetchPrograms']),
        ...mapActions('annexone', ['publishProjects', 'fetchFilteredAnnexOne']),
        syncRecords(){
            this.syncing = true
            var filter = {
                workshop_id: this.$route.params.workshopId,
                division_id: this.division_id
            }
            this.fetchFilteredAnnexOne(filter).then(res => {
                this.syncing = false
            })
        },
        publish(){
            this.swalConfirmCancel('Are you sure?', 'This action is irreversible and can be done only one time per workshop').then(result=>{
                if(result){
                    this.publishProjects(this.form).then(res => {
                        var icon = res.errors ? 'error' : 'success'
                        this.toastMsg(icon, res.message)
                        if(!res.errors){
                            this.$refs.Close.click()
                        }
                    })
                }
            })
        },
        changeProgram(key){
            var form = this.form.cases[key]
            var program = this.programs.find(elem => elem.id == form.program_id)
            form.subprogram_id = ''
            form.cluster_id = ''
            form.subprograms = program.subprograms
            form.clusters = []
            form.projectIds = []
            form.projects = program.projects
        },
        changeSubprogram(key){
            var form = this.form.cases[key]
            var subprogram = form.subprograms.find(elem => elem.id == form.subprogram_id)
            form.cluster_id = ''
            form.clusters = subprogram ? subprogram.clusters : []
            form.projectIds = []
            form.projects = subprogram ? subprogram.projects : []
        },
        changeCluster(key){
            var form = this.form.cases[key]
            var cluster = form.clusters.find(elem => elem.id == form.cluster_id)
            form.projectIds = []
            form.projects = cluster ? cluster.projects : []
        },
        childClicked(){
            this.formshow = !this.formshow
            this.prev = this.formshow ? this.toggle : this.prev
            this.toggle = this.formshow ? false : this.prev
            this.childClick()
            this.syncRecords()
        },
        childClick(){
            this.$emit('clicked')
        },
        isSubprogram(items){
            for(let item of items){
                return item.header_type == 'Subprogram'
            }
        },
        subAmount(subs, type, year){
            var amount = 0
            for(let sub of subs){
                var fund = sub.funds.find(elem => elem.type == type && elem.year == year)
                amount = fund ? amount + this.strToFloat(fund.amount) : amount
            }
            return amount
        },
        setFund(num, funds, subs = []){
            var year = parseInt(this.workshop.year)
            var workshopYear = year + (num == 1 ? 0 : 
                    num > 1   && num < 5 ? 1 :
                    num == 5  || num == 6 ? 2 : 
                    num == 7  || num == 8 ? 3 : 
                    num == 9  || num == 10 ? 4 : 
                    num == 11 || num == 12 ? 5 : 6)
            var amount = 0
            if(num == 1){
                // GAA 1
                var fund = funds.find(elem => elem.type == 'GAA')
                var orig = fund ? fund.amount : 0
                amount = this.subAmount(subs, 'GAA', workshopYear)
                amount = subs.length > 0 ? amount > 0 ? amount : orig : orig
            }
            if(num == 2 || num == 5 || num == 7 || num == 9 || num == 11){
                // Proposed 2 5 7 9 11
                var fund = funds.find(elem => elem.type == 'Proposed' && elem.year == workshopYear)
                var orig = fund ? fund.amount : 0
                amount = this.subAmount(subs, 'Proposed', workshopYear)
                amount = subs.length > 0 ? amount : orig
            }
            if(num == 3){
                // NEP 3
                var fund = funds.find(elem => elem.type == 'NEP')
                var orig = fund ? fund.amount : 0
                amount = this.subAmount(subs, 'NEP', workshopYear)
                amount = subs.length > 0 ? amount > 0 ? amount : orig : orig
            }
            if(num == 4 || num == 6 || num == 8 || num == 10 || num == 12){
                // Revised 4 6 8 10 12
                var fund = funds.find(elem => elem.type == 'Revised' && elem.year == workshopYear)
                var orig = fund ? fund.amount : 0
                amount = this.subAmount(subs, 'Revised', workshopYear)
                amount = subs.length > 0 ? amount : orig
            }
            if(num == 13){
                // Last 13
                var fund = funds.find(elem => elem.type == 'Last')
                var orig = fund ? fund.amount : 0
                amount = this.subAmount(subs, 'Last', workshopYear)
                amount = subs.length > 0 ? amount : orig
            }
            return amount > 0 ? this.formatAmount(amount) : ''
        },
        setHeaderFund(num, items){
            var amount = 0
            for(let item of items){
                var fund = this.setFund(num, item.funds, item.subs)
                if(fund != ''){
                    amount = amount + this.strToFloat(fund)
                }
            }
            return amount > 0 ? this.formatAmount(amount) : ''
        },
        setSourceFund(num, headers){
            var amount = 0
            for(let header in headers){
                var items = headers[header]
                var fund = this.setHeaderFund(num, items)
                if(fund != ''){
                    amount = amount + this.strToFloat(fund)
                }
            }
            
            return amount > 0 ? this.formatAmount(amount) : ''
        },
        setBySource(num, src){
            var amount = 0
            var annexones = this.annexones
            for(let div in annexones){
                var sources = annexones[div]
                for(let source in sources){
                    if(source.includes(src)){
                        var header = sources[source]
                        for(let head in header){
                            var items = header[head]
                            for(let item of items){
                                var fund = this.setFund(num, item.funds, item.subs)
                                if(fund != ''){
                                    amount = amount + this.strToFloat(fund)
                                }
                            }
                        }
                    }
                }
            }
            return amount > 0 ? this.formatAmount(amount) : ''
        },
        setDivisionFund(num, data, division){
            var amount = 0
            for(let source in data){
                for(let header in data[source]){
                    for(let item of data[source][header]){
                        var fund = this.setFund(num, item.funds, item.subs)
                        if(fund != ''){
                            amount = amount + this.strToFloat(fund)
                        }
                    }
                }
            }
            amount = division == 'STSD' ? amount - (this.setBySource(num, 'AC') ? this.strToFloat(this.setBySource(num, 'AC')) : 0) : amount
            return amount > 0 ? this.formatAmount(amount) : ''
        },
        formatAmount(amount){
            amount = parseFloat(amount.toString().replaceAll(',', ''))
            return (Math.round(amount * 100) / 100).toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 0 })
        },
        strToFloat(num){
            let strNum = num.toString().replace(/\,/g,'')
            return Math.abs(parseFloat(strNum))
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
    },
    computed: {
        ...mapGetters('workshop', ['getWorkshop']),
        ...mapGetters('division', ['getDivisions']),
        ...mapGetters('program', ['getPrograms']),
        ...mapGetters('annexone', ['getAnnexOnes']),
        ...mapGetters('user', ['getAuthUser']),
        workshop(){ return this.getWorkshop },
        divisions(){ return this.getDivisions },
        programs(){ return this.getPrograms },
        annexones(){ return this.getAnnexOnes },
        userroles(){ return this.getAuthUser.active_profile.roles }
    },
    created(){
        if(this.divisions.length == 0){
            this.fetchDivisions()
        }
        if(this.programs.length == 0){
            this.fetchPrograms()
        }
    },
    props: {
        editmode: Boolean
    },
}
</script>
<style scoped>
#main-container{
    height: calc(100vh - 205px);
    overflow: auto;
}
#table-container{
    height: calc(100vh - 300px);
    overflow: auto;
}
#table-container>table{
    min-width: 1500px;
}
.stsdtotals{
    height: 60px;
    text-align: center;
}
</style>