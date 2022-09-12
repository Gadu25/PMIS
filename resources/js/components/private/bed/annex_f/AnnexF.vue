<template>
    <div class="px-3 py-4">
        <div class="border-bottom mb-2">
            <button class="btn btn-sm btn-light float-start" @click="this.$router.back()"><i class="fas fa-arrow-left"></i> Back</button>
            <h4 class="text-center">Annex F</h4>
            <small>Planning Workshop <span v-if="!loading">{{workshop.date}}</span><span v-else>Loading date <i class="fas fa-spinner fa-spin"></i></span></small>
        </div>
        <div class="d-flex justify-content-between mb-2 ">
            <div>
                <template v-if="!editmode">
                    <button class="btn btn-sm shadow-none min-100 me-2" :class="!printmode ? 'btn-secondary' : 'btn-success'" @click="printmode = !printmode">{{!printmode ? 'Print' : 'Display'}} View</button>
                    <button class="btn btn-sm btn-outline-secondary" v-if="printmode"><i class="far fa-print"></i> Print</button>
                </template>
            </div>
            <button class="btn btn-sm shadow-none" v-if="annexfs.length > 0 && !formshow" @click="editmode = !editmode, printmode = false" :class="!editmode? 'btn-success' : 'btn-primary'">{{editmode ? 'View' : 'Edit'}} mode</button>
        </div>
        <div class="row flex-row-reverse">
            <div class="col-sm-3" v-if="filtershow && !formshow">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="d-flex justify-content-end mb-2">
                            <button @click="filtershow = false" class="btn btn-sm btn-outline-secondary"><i class="far fa-arrow-right"></i></button>
                        </div>
                        <div class="form-floating mb-3">
                            <select class="form-control" id="Status" v-model="filter.status">
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
                            <select class="form-control" id="displayType" v-model="filter.type" @change="changeFilterType()">
                                <option value="Program">Program</option>
                                <option value="Division">Division</option>
                            </select>
                            <label for="displayType">Type</label>
                        </div>
                        <template v-if="filter.type == 'Program'">
                            <div class="form-floating mb-3">
                                <select class="form-control" id="Program" v-model="filter.program_id" @change="changeProgram()">
                                    <option value="">Any Program</option>
                                    <option :value="program.id" v-for="program in programs" :key="program.id+'_filter-program'">{{program.title}}</option>
                                </select>
                                <label for="Program">Program</label>
                            </div>
                            <div class="form-floating mb-3" v-if="subprograms.length > 0">
                                <select class="form-control" id="Sub-Program" v-model="filter.subprogram_id" @change="changeSubprogram()">
                                    <option value="">Any Sub-Program</option>
                                    <option :value="subprogram.id" v-for="subprogram in subprograms" :key="subprogram.id+'_filter-subprogram'">{{subprogram.title}}</option>
                                </select>
                                <label for="Sub-Program">Sub-Program</label>
                            </div>
                            <div class="form-floating mb-3" v-if="clusters.length > 0">
                                <select class="form-control" id="Cluster" v-model="filter.cluster_id">
                                    <option value="">Any Cluster</option>
                                    <option :value="cluster.id" v-for="cluster in clusters" :key="cluster.id+'_filter-cluster'">{{cluster.title}}</option>
                                </select>
                                <label for="Cluster">Cluster</label>
                            </div>
                        </template>
                        <template v-if="filter.type == 'Division'">
                            <div class="form-floating mb-3">
                                <select class="form-control" id="Division" v-model="filter.division_id" @change="changeDivision()">
                                    <option value="">Any Division</option>
                                    <option :value="division.id" v-for="division in divisions" :key="division.id+'_filter-division'">{{division.name}}</option>
                                </select>
                                <label for="Division">Division</label>
                            </div>
                            <div class="form-floating mb-3" v-if="units.length > 0">
                                <select class="form-control" id="Unit" v-model="filter.unit_id" @change="changeUnit()">
                                    <option value="">Any Unit</option>
                                    <option :value="unit.id" v-for="unit in units" :key="unit.id+'_filter-unit'">{{unit.name}}</option>
                                </select>
                                <label for="Unit">Unit</label>
                            </div>
                            <div class="form-floating mb-3" v-if="subunits.length > 0">
                                <select class="form-control" id="Sub-Unit" v-model="filter.subunit_id">
                                    <option value="">Any Sub-Unit</option>
                                    <option :value="subunit.id" v-for="subunit in subunits" :key="subunit.id+'_filter-subunit'">{{subunit.name}}</option>
                                </select>
                                <label for="Sub-Unit">Sub-Unit</label>
                            </div>
                        </template>
                        <div class="d-flex justify-content-end">
                            <button class="btn btn-sm btn-primary" @click="syncRecords()"><i class="far fa-sync" :class="syncing ? 'fa-spin' : ''"></i> Sync</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="position-relative" :class="'col-sm-'+(filtershow && !formshow ? 9 : 12) ">
                <button @click="filtershow = true" v-if="!filtershow" class="btn btn-sm btn-secondary show-filter"><i class="far fa-arrow-left"></i> <span>Show Filter</span></button>
                <div class="card shadow position-relative">
                    <div class="overlay" v-if="syncing">
                        <h1 class="text-white">Syncing Records <i class="far fa-spinner fa-spin"></i></h1>
                    </div>
                    <div class="card-body" :class="editmode ? 'p-0' : ''">
                        <template v-if="!editmode">
                            <div class="d-flex justify-content-between"><small>Department of Science and Technology</small> <small class="fw-bold">Annex F</small></div>
                            <h6 class="mb-2 fw-bold"><small>SCIENCE EDUCATION INSITITUTE</small></h6>
                            <h6 class="mb-3 fw-bold" style="background: yellow;"><small>Schedule of FY {{parseInt(workshop.year)+1}} Project Implementation</small></h6>
                            <div class="table-responsive display">
                                <EmptyTable :workshopYear="workshop.year" />
                            </div>
                        </template>
                        <template v-else>
                            <template v-if="!formshow">
                                <div class="p-2">
                                    <h6><i class="fas" :class="setStatusIcon(syncedstatus)"></i> {{syncedstatus}} Annex F Items</h6>
                                </div>
                                <div class="table-responsive form-display">
                                    <table class="table table-sm table-bordered table-hover">
                                        <thead class="text-center">
                                            <tr>
                                                <th>Project Name/Activity</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template v-for="program in annexfs" :key="program.id+'_program'">
                                                <tr class="bg-success text-white fw-bold" v-if="program.items.length > 0 || program.show">
                                                    <td class="fw-bold" colspan="2">{{program.title}}</td>
                                                </tr>
                                                <template v-for="item in program.items" :key="item.id+'_program-item'">
                                                    <tr>
                                                        <td><div class="ms-3"><i class="fas" :class="setStatusIcon(item.status)"></i> {{setItemTitle(item.projects)}}</div></td>
                                                        <td class="text-center">
                                                            <button class="btn btn-sm btn-outline-secondary mb-1" @click="editForm(item, setItemTitle(item.projects))"><i class="far fa-pencil-alt"></i> Details</button>    
                                                            <button class="btn btn-sm btn-danger mb-1 ms-1"><i class="far fa-trash-alt"></i></button>
                                                        </td>
                                                    </tr>
                                                </template>
                                                <template v-for="subprogram in program.subprograms" :key="subprogram.id+'_subprogram'">
                                                    <tr v-if="subprogram.items.length > 0 || subprogram.show">
                                                        <td class="fw-bold" colspan="2">{{program.id == 1 ? subprogram.title_short : subprogram.title}}</td>
                                                    </tr>
                                                    <template v-for="item in subprogram.items" :key="item.id+'_subprogram-item'">
                                                        <tr>
                                                            <td><div class="ms-3"><i class="fas" :class="setStatusIcon(item.status)"></i> {{setItemTitle(item.projects)}}</div></td>
                                                            <td class="text-center">
                                                                <button class="btn btn-sm btn-outline-secondary mb-1" @click="editForm(item, setItemTitle(item.projects))"><i class="far fa-pencil-alt"></i> Details</button>    
                                                                <button class="btn btn-sm btn-danger mb-1 ms-1"><i class="far fa-trash-alt"></i></button>
                                                            </td>
                                                        </tr>
                                                    </template>
                                                    <template v-for="cluster in subprogram.clusters" :key="cluster.id+'_cluster'">
                                                        <tr v-if="cluster.items.length > 0">
                                                            <td colspan="2"><div class="fw-bold ms-2">{{cluster.title}}</div></td>
                                                        </tr>
                                                        <template v-for="item in cluster.items" :key="item.id+'_cluster-item'">
                                                            <tr>
                                                                <td><div class="ms-3"><i class="fas" :class="setStatusIcon(item.status)"></i> {{setItemTitle(item.projects)}}</div></td>
                                                                <td class="text-center">
                                                                    <button class="btn btn-sm btn-outline-secondary mb-1" @click="editForm(item, setItemTitle(item.projects))"><i class="far fa-pencil-alt"></i> Details</button>    
                                                                    <button class="btn btn-sm btn-danger mb-1 ms-1"><i class="far fa-trash-alt"></i></button>
                                                                </td>
                                                            </tr>
                                                            <tr v-for="sub in item.subs" :key="sub.id+'_cluster-sub-item'">
                                                                <td colspan="2"><div class="fs-14 fst-italic ms-4">{{sub.subproject.title}}</div></td>
                                                            </tr>
                                                        </template>
                                                    </template>
                                                </template>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </template>
                            <div class="p-2" v-else>
                                <div class="d-flex justify-content-between mb-3">
                                    <button class="btn btn-sm btn-danger" @click="formshow = false"><i class="far fa-times"></i> Cancel</button>
                                    <div>
                                        <button class="btn btn-sm btn-secondary me-1"><i class="fas fa-edit"></i> Save as Draft</button>
                                        <button class="btn btn-sm btn-success"><i class="fas fa-search"></i> Submit "For Review"</button>
                                    </div>
                                </div>
                                <div class="table-responsive form-details pb-2" v-dragscroll>
                                    <table class="table table-sm table-bordered" style="width: 2500px">
                                        <thead class="align-middle text-center">
                                            <tr>
                                                <th rowspan="3"><span class="text-nowrap">Project Name/Activity</span></th>
                                                <th rowspan="3">Total <br> Target <br> (P'000)</th>
                                                <th colspan="3">{{workshop.year}}</th>
                                                <th colspan="12">{{parseInt(workshop.year)+1}}</th>
                                                <th rowspan="3" style="min-width: 150px;">Total</th>
                                                <th rowspan="3">Remarks</th>
                                            </tr>
                                            <tr>
                                                <th v-for="num in 5" :key="num+'_quarter'" :colspan="3"><span class="text-nowrap">{{setQtr(num)}} Qtr</span></th>
                                            </tr>
                                            <tr>
                                                <th v-for="month, key in months" :key="key+'month'">{{month}}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>{{form.title}}</td>
                                                <td></td>
                                                <td class="p-0" v-for="activity, key in form.activities" :key="key+'month-form-activities'">
                                                    <div class="position-relative" v-for="act, akey in activity" :key="key+'mf-act-desc'+akey">
                                                        <button @click="removeActivity(key, act)" class="btn btn-outline-danger act-rmv"><i class="far fa-times"></i></button>
                                                        <textarea class="form-control act-input" rows="8" v-model="act.description"></textarea>
                                                    </div>
                                                    <button @click="addActivity(key)" class="btn btn-sm btn-outline-secondary act-add"><i class="fas fa-plus"></i></button>
                                                </td>
                                                <td></td>
                                                <td style="height: 1px; padding: 0;">
                                                    <textarea style="resize: none;" v-model="form.remarks" class="form-control remarks"></textarea>
                                                </td>
                                            </tr>
                                            <tr class="funding">
                                                <td class="text-center"><small>Funding</small></td>
                                                <td></td>
                                                <td class="p-0" v-for="fund, key in form.funds" :key="key+'month-form-fund'">
                                                    <input type="text" class="form-control fund-input" @change="numChange(fund.amount, key)" v-model="fund.amount" v-money="money">
                                                </td>
                                                <td class="text-end"> {{formatAmount(getTotalAmount())}} <input type="text" class="d-none" v-money="money" @change="numChange(0, 15)"></td>
                                                <td></td>
                                            </tr>
                                            <template v-for="sub, key in form.subs" :key="key+'_form-sub'">
                                                <tr>
                                                    <td>{{sub.title}}</td>
                                                    <td></td>
                                                    <td class="p-0" v-for="activity, skey in sub.activities" :key="skey+'month-form-activities-sub'">
                                                        <div class="position-relative" v-for="act, akey in activity" :key="key+'mf-act-desc'+akey">
                                                            <button @click="removeActivity(skey, act, key)" class="btn btn-outline-danger act-rmv"><i class="far fa-times"></i></button>
                                                            <textarea class="form-control act-input" rows="8" v-model="act.description"></textarea>
                                                        </div>
                                                        <button @click="addActivity(skey, key)" class="btn btn-sm btn-outline-secondary act-add"><i class="fas fa-plus"></i></button>
                                                    </td>
                                                    <td></td>
                                                    <td style="height: 1px; padding: 0;">
                                                        <textarea style="resize: none;" v-model="sub.remarks" class="form-control remarks"></textarea>
                                                    </td>
                                                </tr>
                                                <tr class="funding">
                                                    <td class="text-center"><small>Funding</small></td>
                                                    <td></td>
                                                    <td class="p-0" v-for="fund, fkey in sub.funds" :key="fkey+'month-form-fund-sub'">
                                                        <input type="text" class="form-control fund-input" @change="numChange(fund.amount, fkey, key)" v-model="fund.amount" v-money="money">
                                                    </td>
                                                    <td class="text-end"> {{formatAmount(getTotalAmount(sub.funds))}} <input type="text" class="d-none" v-money="money" @change="numChange(0, 15)"></td>
                                                    <td></td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { dragscroll } from 'vue-dragscroll'
import { VMoney } from 'v-money'
import EmptyTable from './EmptyTable.vue'
import { mapGetters, mapActions } from 'vuex'
export default {
    name: 'AnnexF',
    directives: {
        dragscroll: dragscroll,
        money: VMoney
    },
    components: {
        EmptyTable
    },
    data(){
        return {
            editmode:   false,
            printmode:  false,
            syncing:    false,
            loading:    true,
            filtershow: true,
            formshow:   false,
            syncedstatus: '',
            filter: {
                status:     'New', type: 'Program',
                program_id:    '', division_id: '',
                subprogram_id: '', unit_id: '',
                cluster_id:    '', subunit_id: '',
                workshopId: this.$route.params.workshopId
            },
            subprograms: [], //Empty arrays for filter
            clusters:    [],
            units:       [],
            subunits:    [],
            form: {
                id: '',
                status: '',
                title: '',
                remarks: '',
                activities: [],
                funds: [],
                subs: []
            },
            months: [
                'Oct', 'Nov', 'Dec',
                'Jan', 'Feb', 'Mar',
                'Apr', 'May', 'Jun',
                'Jul', 'Aug', 'Sep',
                'Oct', 'Nov', 'Dec',
            ],
            money: {
                decimal:   '.',
                thousands: ',',
                prefix:    '',
                suffix:    '',
                precision: 2,
                masked:    false /* doesn't work with directive */
            },
        }
    },
    methods: {
        ...mapActions('annexf', ['fetchAnnexFs']),
        ...mapActions('workshop', ['fetchWorkshop']),
        ...mapActions('program', ['fetchPrograms']),
        ...mapActions('division', ['fetchDivisions']),
        // Filter Controls
        changeFilterType(){
            if(this.filter.type == 'Program'){
                this.filter.division_id   = ''
                this.filter.unit_id       = ''
                this.filter.subunit_id    = ''
                this.units           = []
                this.subunits        = []
            }
            else{
                this.filter.program_id    = ''
                this.filter.subprogram_id = ''
                this.filter.cluster_id    = ''
                this.subprograms     = []
                this.clusters        = []
            }
        },
        changeProgram(){
            var progId                = this.filter.program_id
            var program               = this.programs.find(elem => elem.id == progId)
            this.subprograms          = progId ? program.subprograms : []
            this.clusters             = []
            this.filter.subprogram_id = ''
            this.filter.cluster_id    = ''
        },
        changeSubprogram(){
            var subpId             = this.filter.subprogram_id
            var subprogram         = this.subprograms.find(elem => elem.id == subpId)
            this.clusters          = subpId ? subprogram.clusters : []
            this.filter.cluster_id = ''
        },
        changeDivision(){
            var divId              = this.filter.division_id
            var division           = this.divisions.find(elem => elem.id == divId)
            this.units             = divId ? division.units : []
            this.subunits          = []
            this.filter.unit_id    = ''
            this.filter.subunit_id = ''
        },
        changeUnit(){
            var unitId             = this.filter.unit_id
            var unit               = this.units.find(elem => elem.id == unitId)
            this.subunits          = unitId ? unit.subunits : []
            this.filter.subunit_id = ''
        },
        syncRecords(){
            this.syncing = true
            this.fetchAnnexFs(this.filter).then(res => {
                this.syncing = false
                this.syncedstatus = this.filter.status
            })
        },
        // Form
        editForm(item, title){
            this.formshow = true
            var form = this.form
            form.id     = item.id
            form.status = item.status
            form.title  = title
            form.activities = []
            form.funds      = []
            form.subs       = []

            var months = this.months
            for(let i = 0; i < months.length; i++){
                form.activities.push([{id: '', description: ''}])
                form.funds.push({id: '', amount: 0})
            }

            for(let i = 0; i < item.subs.length; i++){
                var sub = item.subs[i]
                var temp = {
                    id:         sub.id,
                    title:      sub.subproject.title,
                    remarks:    sub.remarks,
                    activities: [],
                    funds:      []
                }
                
                for(let i = 0; i < months.length; i++){
                    temp.activities.push([{id: '', description: ''}])
                    temp.funds.push({id: '', amount: 0})
                }

                form.subs.push(temp)
            }
        },
        addActivity(key, sub = null){
            if(sub === null){
                this.form.activities[key].push([{id: '', description: ''}])
            }
            else{
                this.form.subs[sub].activities[key].push([{id: '', description: ''}])
            }
        },
        removeActivity(key, activity, sub = null){
            if(sub === null){
                if(this.form.activities[key].length > 1){
                    this.form.activities[key].remove(activity)
                }
            }
            else{
                if(this.form.subs[sub].activities[key].length > 1){
                    this.form.subs[sub].activities[key].remove(activity)
                }
            }
        },
        // Display
        setItemTitle(projects){
            var project = projects[0]
            var title = project.title
            if(projects.length > 1){
                title = project.subprogram.title
            }
            return title
        },
        setStatusIcon(status){
            return status == 'New' ? 'fa-sparkles text-warning' : 
            status == 'Draft' ? 'fa-edit' :
            status == 'For Review' ? 'fa-search' : 
            status == 'For Approval' ? 'fa-clipboard-list-check' : 
            status == 'Approved' ? 'fa-clipboard-check text-success' : 'fa-badge-check text-info'
        },
        checkStatus(){
            var status = ''
            for(let i = 0; i < this.annexfs.length; i++){
                var prog = this.annexfs[i]
                for(let j = 0; j < prog.items.length; j++){
                    if(status == ''){
                        var item = prog.items[j]
                        status = item.status
                        break
                    }
                }
                for(let j = 0; j < prog.subprograms.length; j++){
                    var subp = prog.subprograms[j]
                    for(let k = 0; k < subp.items.length; k++){
                        var item = subp.items[k]
                        status = item.status
                        break
                    }
                    for(let k = 0; k < subp.clusters.length; k++){
                        var clus = subp.clusters[k]
                        for(let l = 0; l < clus.items.length; l++){
                            var item = clus.items[l]
                            status = item.status
                            break
                        }
                    }
                }
            }
            status = (status == '') ? 'New' : status
            this.filter.status = status
            this.syncedstatus = status
        },
        // Form Display
        setQtr(num){
            return num == 2 ? '1st' : num == 3 ? '2nd' : num == 4 ? '3rd' : '4th'
        },
        // Formats
        numChange: _.debounce(function(e, key, sub = null){
            if(key != 15){
                if(e.includes('-')){
                    this.toastMsg('warning', 'Please avoid using Negative Number')
                    var amount = sub !== null ? this.form.subs[sub].funds[key].amount : this.form.funds[key].amount
                    amount = amount.replace(/\-/g,'')
                    if(sub !== null){
                        this.form.subs[sub].funds[key].amount = amount
                    }
                    else{
                        this.form.funds[key].amount = amount
                    }
                }
            }
        }),
        getTotalAmount(funds = []){
            var total = 0
            var fundArray = funds.length > 0 ? funds : this.form.funds
            for(let i = 0; i < fundArray.length; i++){
                if(i > 2){
                    var amount = this.strToFloat(fundArray[i].amount)
                    total = total + Math.abs(amount)
                }
            }
            return total
        },
        strToFloat(num){
            let strNum = num.toString().replace(/\,/g,'')
            return parseFloat(strNum)
        },
        formatAmount(amount){
            return (Math.round(amount * 100) / 100).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2 })
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
        ...mapGetters('annexf', ['getAnnexFs']),
        annexfs(){ return this.getAnnexFs },
        ...mapGetters('workshop', ['getWorkshop']),
        workshop(){ return this.getWorkshop },
        ...mapGetters('program', ['getPrograms']),
        programs(){ return this.getPrograms },
        ...mapGetters('division', ['getDivisions']),
        divisions(){ return this.getDivisions },
    },
    created(){
        this.fetchWorkshop(this.$route.params.workshopId).then(res => {
            this.loading = false
        })
        if(this.programs.length == 0){
            this.fetchPrograms()
        }
        if(this.divisions.length == 0){
            this.fetchDivisions()
        }
        this.checkStatus()
    }
}
</script>
<style>
.fs-14{
    font-size: 14px;
}
.min-100{
    min-width: 100px;
}
.show-filter{
    position: absolute;
    right: 0;
    z-index: 100;
}
.show-filter:hover>span{
    display: inline-block;
}
.show-filter>span{
    display: none;
}
.table-responsive>table{
    margin: 0;
}
.table-responsive.display{
    max-height: calc(100vh - 320px);
    padding: 0 10px 10px 0;
}
.table-responsive.form-details,
.table-responsive.form-display{
    max-height: calc(100vh - 248px);
    padding: 10px 10px 10px 10px;
}

.overlay{
    position: absolute;
    height: 100%;
    width: 100%;
    background: rgba(0,0,0,0.6);
    z-index: 101;
    display: flex;
    justify-content: center;
    align-items: center;
}
tr.funding{
    background: rgba(255, 166, 0, 0.15);
    font-weight: bold;
}
.fund-input{
    background: transparent;
    border-radius: 0;
    border: 0;
    box-shadow: none !important;
    text-align: right;
}
.fund-input:focus{
    background: rgba(0,0,0,0.05);
}
.act-input{
    border: 0;
    border-radius: 0;
    box-shadow: none !important;
    font-size: 12px;
    resize: none;
    border-bottom: 1px solid rgba(0,0,0,0.15);
}
.act-rmv{
    position: absolute;
    right: 0;
    border: 0;
    border-radius: 0;
    box-shadow: none !important;
    padding: 0;
    margin: 0;
    width: 26px;
}
.act-add{
    border: 0;
    border-radius: 0;
    width: 100%;
    box-shadow: none !important;
    border-bottom: 1px solid rgba(0,0,0,0.15);
}
.remarks{
    height: 100%;
    box-shadow: none !important;
    border: 0;
    border-radius: 0;
}
</style>