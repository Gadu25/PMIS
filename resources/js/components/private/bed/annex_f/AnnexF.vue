<template>
    <div class="px-3 py-4">
        <div class="border-bottom mb-2">
            <button v-if="!formshow" class="btn btn-sm btn-light float-start" @click="this.$router.push('/budget-executive-documents')"><i class="fas fa-arrow-left"></i> Back</button>
            <h4 class="text-center">Annex F</h4>
            <small>Planning Workshop <span v-if="!loading">{{workshop.date}}</span><span v-else>Loading date <i class="fas fa-spinner fa-spin"></i></span></small>
        </div>
        <div class="d-flex justify-content-between mb-2 ">
            <div> <!-- div needed for flex space between  -->
                <template v-if="!editmode && inUserRole('annex_f_export') && syncedstatus == 'Submitted'">
                <!-- <template v-if="!editmode && inUserRole('annex_f_export') "> -->
                    <button v-if="!editmode" class="btn btn-sm shadow-none min-100 me-1" :class="printmode ? 'btn-secondary' : 'btn-outline-secondary'" @click="printmode = !printmode"><i v-if="printmode" class="far fa-arrow-left"></i> Export</button>
                    <button class="btn btn-sm btn-outline-secondary me-1" v-print="'#printMe'" v-if="printmode"><i class="far fa-print"></i> Print or Save as PDF</button>
                    <a v-if="!editmode && exportlink != '' && printmode" :href="exportlink" target="_blank" class="btn btn-sm btn-success bg-gradient"><i class="far fa-file-excel"></i> Excel</a>
                    <span v-else><small v-if="printmode"> if export excel missing, resync records</small></span>
                </template>
            </div>
            <button class="btn btn-sm shadow-none" v-if="annexfs.length > 0 && !formshow" @click="editmode = !editmode, printmode = false" :class="!editmode? 'btn-success' : 'btn-primary'">{{editmode ? 'View' : 'Edit'}} mode</button>
        </div>
        <div class="row flex-row-reverse">
            <div class="col-sm-3" v-if="filtershow && !formshow">
                <div class="card shadow border-0 mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <strong>Filters</strong>
                            <button @click="filtershow = false" class="btn btn-sm btn-outline-secondary"><i class="far fa-arrow-right"></i></button>
                        </div>
                        <div class="form-floating mb-3">
                            <select class="form-control" id="Status" v-model="filter.status">
                                <!-- <option value="New">New</option> -->
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
                                    <option value="">All Program</option>
                                    <option :value="program.id" v-for="program in programs" :key="program.id+'_filter-program'">{{program.title}}</option>
                                </select>
                                <label for="Program">Program</label>
                            </div>
                            <div class="form-floating mb-3" v-if="subprograms.length > 0">
                                <select class="form-control" id="Sub-Program" v-model="filter.subprogram_id" @change="changeSubprogram()">
                                    <option value="">All Sub-Program</option>
                                    <option :value="subprogram.id" v-for="subprogram in subprograms" :key="subprogram.id+'_filter-subprogram'">{{subprogram.title}}</option>
                                </select>
                                <label for="Sub-Program">Sub-Program</label>
                            </div>
                            <div class="form-floating mb-3" v-if="clusters.length > 0">
                                <select class="form-control" id="Cluster" v-model="filter.cluster_id">
                                    <option value="">All Cluster</option>
                                    <option :value="cluster.id" v-for="cluster in clusters" :key="cluster.id+'_filter-cluster'">{{cluster.title}}</option>
                                </select>
                                <label for="Cluster">Cluster</label>
                            </div>
                        </template>
                        <template v-if="filter.type == 'Division'">
                            <div class="form-floating mb-3">
                                <select class="form-control" id="Division" v-model="filter.division_id" @change="changeDivision()">
                                    <option value="">All Division</option>
                                    <option :value="division.id" v-for="division in divisions" :key="division.id+'_filter-division'">{{division.name}}</option>
                                </select>
                                <label for="Division">Division</label>
                            </div>
                            <div class="form-floating mb-3" v-if="units.length > 0">
                                <select class="form-control" id="Unit" v-model="filter.unit_id" @change="changeUnit()">
                                    <option value="">All Unit</option>
                                    <option :value="unit.id" v-for="unit in units" :key="unit.id+'_filter-unit'">{{unit.name}}</option>
                                </select>
                                <label for="Unit">Unit</label>
                            </div>
                            <div class="form-floating mb-3" v-if="subunits.length > 0">
                                <select class="form-control" id="Sub-Unit" v-model="filter.subunit_id">
                                    <option value="">All Sub-Unit</option>
                                    <option :value="subunit.id" v-for="subunit in subunits" :key="subunit.id+'_filter-subunit'">{{subunit.name}}</option>
                                </select>
                                <label for="Sub-Unit">Sub-Unit</label>
                            </div>
                        </template>
                        <div class="form-floating mb-3">
                            <select class="form-control" id="Year" v-model="filter.year">
                                <option :value="workshopYear+1">{{workshopYear+1}}</option>
                                <option :value="workshopYear+2">{{workshopYear+2}}</option>
                            </select>
                            <label for="Year">Year</label>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button class="btn btn-sm btn-primary" v-if="inUserRole('annex_f_sync')" @click="syncRecords()"><i class="far fa-sync" :class="syncing ? 'fa-spin' : ''"></i> Sync</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="position-relative" :class="'col-sm-'+(filtershow && !formshow ? 9 : 12) ">
                <button @click="filtershow = true" v-if="!filtershow" class="btn btn-sm btn-secondary show-filter"><i class="far fa-arrow-left"></i> <span>Show Filter</span></button>
                <div class="card border-0 shadow position-relative">
                    <div class="overlay" v-if="syncing">
                        <h1 class="text-white">Syncing Records <i class="far fa-spinner fa-spin"></i></h1>
                    </div>
                    <div class="card-body" :class="editmode ? 'p-0' : ''" :style="printmode ? 'height: calc(100vh - 205px); overflow: auto;' : ''">
                        <template v-if="!editmode">
                            <div class="position-relative" id="printMe">
                                <div class="d-flex justify-content-between"><small>Department of Science and Technology</small> <small class="fw-bold">Annex F</small></div>
                                <h6 class="mb-2 fw-bold"><small>SCIENCE EDUCATION INSITITUTE</small></h6>
                                <h6 class="mb-3 fw-bold" style="background: yellow;"><small>Schedule of FY {{this.syncedyear}} Project Implementation</small></h6>
                                <div class="table-responsive " :class="printmode ? '' : 'display'" v-dragscroll>
                                    <Display :printmode="printmode" :year="syncedyear" />
                                </div>
                                <span v-if="printmode">Annex F {{syncedstatus}} Projects as of {{this.getDateToday()}}</span>
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
                                                <th v-if="!isAdmin">Leader</th>
                                                <th v-if="!isAdmin">Cluster</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template v-for="program in annexfs" :key="program.id+'_program'">
                                                <!-- <tr class="bg-success text-white fw-bold" v-if="program.items.length > 0 || program.show"> -->
                                                <tr class="bg-success text-white fw-bold" v-if="isAdmin">
                                                    <td class="fw-bold" colspan="2">{{program.title}}</td>
                                                </tr>
                                                <FormItem @clicked="childClick" :items="program.items" :saving="saving"/>
                                                <template v-for="subprogram in program.subprograms" :key="subprogram.id+'_subprogram'">
                                                    <!-- <tr v-if="subprogram.items.length > 0 || subprogram.show"> -->
                                                    <tr v-if="isAdmin">
                                                        <td class="fw-bold" colspan="2">{{program.id == 1 ? subprogram.title_short : subprogram.title}}</td>
                                                    </tr>
                                                    <FormItem @clicked="childClick" :items="subprogram.items" :saving="saving"/>
                                                    <template v-for="cluster in subprogram.clusters" :key="cluster.id+'_cluster'">
                                                        <!-- <tr v-if="cluster.items.length > 0"> -->
                                                        <tr v-if="isAdmin">
                                                            <td colspan="2"><div class="fw-bold ms-2">{{cluster.title}}</div></td>
                                                        </tr>
                                                        <FormItem @clicked="childClick" :items="cluster.items" :saving="saving"/>
                                                    </template>
                                                </template>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </template>
                            <div class="p-2" v-else> <!-- Form -->
                                <div class="d-flex justify-content-between mb-3">
                                    <div>
                                        <button :class="saving ? 'disabled' : ''" class="btn btn-sm btn-danger me-1" @click="!saving ? hideForm() : null"><i class="far fa-times"></i> Cancel</button>
                                        <button :class="saving ? 'disabled' : ''" class="btn btn-sm btn-outline-secondary" v-if="histories.length > 0" data-bs-toggle="modal" data-bs-target="#history"><i class="far fa-clipboard-list"></i> Logs</button>
                                    </div>
                                    <span v-if="saving">Saving <i class="fas fa-spinner fa-spin"></i></span>
                                    <div v-if="statusNewDraft(form.status) && isUserProjectLeader(form.leaderId) || isAdmin">
                                        <!-- <button v-if="statusNewDraft(form.status) && isAdmin" :class="saving ? 'disabled' : ''" class="btn btn-sm btn-primary bg-gradient me-1" @click="!saving ? submitForm(form.status) : null"><i class="far fa-save"></i> Admin Save</button> -->
                                        <button v-if="statusNewDraft(form.status) && isAdmin" :class="saving ? 'disabled' : ''" class="btn btn-sm btn-primary bg-gradient me-1" data-bs-toggle="modal" data-bs-target="#comment"><i class="far fa-save"></i> Admin Save</button>
                                        <button v-if="checkUserTitle(form.status)" :class="saving ? 'disabled' : ''" class="btn btn-sm btn-secondary me-1" @click="!saving ? submitForm('Draft') : null"><i class="fas fa-edit"></i> Draft</button>
                                        <button v-if="checkUserTitle(form.status)" :class="saving ? 'disabled' : ''" class="btn btn-sm btn-success" @click="!saving ? submitForm('For Review') : null"><i class="fas fa-search"></i> {{authuser.active_profile.title.name == 'Unit Header' ? 'For Approval' : 'For Review'}}</button>
                                    </div>
                                    <div v-if="!statusNewDraft(form.status) && form.status != 'Submitted'">
                                        <!-- <button v-if="isAdmin" :class="saving ? 'disabled' : ''" class="btn btn-sm btn-primary bg-gradient me-1" @click="!saving ? submitForm(form.status) : null"><i class="far fa-save"></i> Admin Save</button> -->
                                        <button v-if="isAdmin" :class="saving ? 'disabled' : ''" class="btn btn-sm btn-primary bg-gradient me-1" data-bs-toggle="modal" data-bs-target="#comment"><i class="far fa-save"></i> Admin Save</button>
                                        <button v-if="checkUserTitle(form.status)" :class="saving ? 'disabled' : ''" class="btn btn-sm min-100 btn-secondary me-1" data-bs-toggle="modal" data-bs-target="#comment"><i class="fas fa-times"></i> Reject</button>
                                        <button v-if="checkUserTitle(form.status)" :class="saving ? 'disabled' : ''" class="btn btn-sm min-100 btn-success" @click="!saving ? submitForm('approve') : null"><i class="fas fa-check"></i> {{form.status == 'Approved' ? 'Submitted' : 'Approve'}}</button>
                                    </div>
                                </div>
                                <div class="table-responsive form-details pb-2">
                                    <table class="table table-sm table-bordered" style="width: 3000px;">
                                        <TableHead :year="syncedyear" :sticky="true" />
                                        <tbody>
                                            <tr>
                                                <td>{{form.title}}</td>
                                                <td></td>
                                                <td :class="statusNewDraft(form.status) || isAdmin ? 'p-0' : ''" v-for="activity, key in form.activities" :key="key+'month-form-activities'">
                                                    <div class="position-relative" v-for="act, akey in activity" :key="key+'mf-act-desc'+akey">
                                                        <button   v-if="(statusNewDraft(form.status) || isAdmin) && activity.length > 1" @click="removeActivity(key, act)" class="btn btn-outline-danger act-rmv"><i class="far fa-times"></i></button>
                                                        <textarea v-if="statusNewDraft(form.status) || isAdmin" class="form-control act-input" rows="8" v-model="act.description"></textarea>
                                                        <p v-else>{{act.description}}</p>
                                                    </div>
                                                    <button v-if="statusNewDraft(form.status) || isAdmin" @click="addActivity(key)" class="btn btn-sm btn-outline-secondary act-add"><i class="fas fa-plus"></i></button>
                                                </td>
                                                <td></td>
                                                <td style="height: 1px;" :class="statusNewDraft(form.status) || isAdmin ? 'p-0' : ''">
                                                    <textarea v-if="statusNewDraft(form.status) || isAdmin" style="resize: none;" v-model="form.remarks" class="form-control remarks"></textarea>
                                                    <p v-else>{{form.remarks}}</p>
                                                </td>
                                            </tr>
                                            <tr class="funding">
                                                <td class="text-center"><small>Funding</small></td>
                                                <td></td>
                                                <td :class="statusNewDraft(form.status) || isAdmin ? 'p-0' : 'text-end'" v-for="fund, key in form.funds" :key="key+'month-form-fund'">
                                                    <input v-if="statusNewDraft(form.status) || isAdmin" type="text" class="form-control fund-input" @change="numChange(fund.amount, key)" v-model="fund.amount" v-money="money">
                                                    <span  v-else>{{fund.amount}}</span>
                                                </td>
                                                <td class="text-end"> {{formatAmount(getTotalAmount())}} <input type="text" class="d-none" v-money="money" @change="numChange(0, 15)"></td>
                                                <td></td>
                                            </tr>
                                            <template v-for="sub, key in form.subs" :key="key+'_form-sub'">
                                                <tr>
                                                    <td>{{sub.title}}</td>
                                                    <td></td>
                                                    <td :class="statusNewDraft(form.status) || isAdmin ? 'p-0' : ''" v-for="activity, skey in sub.activities" :key="skey+'month-form-activities-sub'">
                                                        <div class="position-relative" v-for="act, akey in activity" :key="key+'mf-act-desc'+akey">
                                                            <button   v-if="(statusNewDraft(form.status) || isAdmin) && activity.length > 1" @click="removeActivity(skey, act, key)" class="btn btn-outline-danger act-rmv"><i class="far fa-times"></i></button>
                                                            <textarea v-if="statusNewDraft(form.status) || isAdmin" class="form-control act-input" rows="8" v-model="act.description"></textarea>
                                                            <p v-else>{{act.description}} {{act.length}}</p>
                                                        </div>
                                                        <button v-if="statusNewDraft(form.status) || isAdmin" @click="addActivity(skey, key)" class="btn btn-sm btn-outline-secondary act-add"><i class="fas fa-plus"></i></button>
                                                    </td>
                                                    <td></td>
                                                    <td style="height: 1px;" :class="statusNewDraft(form.status) || isAdmin ? 'p-0' : ''">
                                                        <textarea v-if="statusNewDraft(form.status) || isAdmin" style="resize: none;" v-model="sub.remarks" class="form-control remarks"></textarea>
                                                        <p v-else>{{form.remarks}}</p>
                                                    </td>
                                                </tr>
                                                <tr class="funding">
                                                    <td class="text-center"><small>Funding</small></td>
                                                    <td></td>
                                                    <td :class="statusNewDraft(form.status) || isAdmin ? 'p-0' : 'text-end'" v-for="fund, fkey in sub.funds" :key="fkey+'month-form-fund-sub'">
                                                        <input v-if="statusNewDraft(form.status) || isAdmin" type="text" class="form-control fund-input" @change="numChange(fund.amount, fkey, key)" v-model="fund.amount" v-money="money">
                                                        <span  v-else>{{fund.amount}}</span>
                                                        <!-- <span  v-else>{{fund.amount != 0 ? formatAmount(fund.amount) : ''}}</span> -->
                                                    </td>
                                                    <td class="text-end"> {{formatAmount(getTotalAmount(sub.funds))}} <input type="text" class="d-none" v-money="money" @change="numChange(0, 15)"></td>
                                                    <td></td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                                <strong>Status: {{form.status}}</strong>
                            </div>
                            <Logs :histories="histories" :title="historyfor"/>
                            <div class="modal fade" id="comment" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-body">
                                            <div class="d-flex mb-2 justify-content-end">
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" ref="Close"></button>
                                            </div>
                                            <div class="form-floating mb-3">
                                                <textarea class="form-control" placeholder="Leave a comment here" v-model="form.comment" id="Comment" style="height: 200px"></textarea>
                                                <label for="Comment">Please add comment <span class="text-danger">*</span></label>
                                            </div>
                                            <div class="d-flex justify-content-end">
                                                <button type="button" class="btn btn-success rounded-pill" @click="isAdmin ? submitForm(form.status) : submitForm('reject')">Submit</button>
                                            </div>
                                        </div>
                                    </div>
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
import moment from 'moment'
import { dragscroll } from 'vue-dragscroll'
import { VMoney } from 'v-money'
import TableHead from './TableHead.vue'
import FormItem from './FormItem.vue'
import Display from './Display.vue'
import Logs from './Logs.vue'
import { mapGetters, mapActions } from 'vuex'
export default {
    name: 'AnnexF',
    directives: {
        dragscroll: dragscroll,
        money: VMoney
    },
    components: {
        Display, TableHead, FormItem, Logs
    },
    data(){
        return {
            editmode:   false,
            printmode:  false,
            syncing:    false,
            saving:     false,
            loading:    true,
            filtershow: true,
            formshow:   false,
            syncedstatus: '',
            syncedyear: 0,
            filter: {
                status:   'Draft', type: 'Program',
                program_id:    '', division_id: '',
                subprogram_id: '', unit_id: '',
                cluster_id:    '', subunit_id: '',
                workshopId: this.$route.params.workshopId,
                year: 0
            },
            subprograms: [], //Empty arrays for filter
            clusters:    [],
            units:       [],
            subunits:    [],
            histories:   [],
            historyfor:  '',
            form: {
                id:       '',
                status:   '',
                title:    '',
                remarks:  '',
                comment:  '',
                leaderId: '',
                activities: [],
                funds:      [],
                subs:       []
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
            exportlink: '',
        }
    },
    methods: {
        ...mapActions('annexf', ['fetchAnnexFs', 'saveAnnexF', 'fetchAnnexF']),
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
        async syncRecords(){
            this.syncing = true
            const promise = await new Promise((resolve, reject) => {
                var time = 0
                setTimeout(async () => {
                    var start = new Date().getTime();
                    const items = await this.fetchAnnexFs(this.filter).then(res => {
                        this.syncing = false
                        this.syncedstatus = this.filter.status
                        this.syncedyear = this.filter.year
                        this.setExportLink(this.filter)
                        return res
                    })
                    var end = new Date().getTime();
                    time = end - start;
                    resolve(items)
                }, time)
            })
            return promise
        },
        setExportLink(options){
            // /api/export/1/annex-e/New/Program/0/0/0/year
            var ids = {
                one:   options.type == 'Program' ? (options.program_id ? options.program_id : 0)    : (options.division_id ? options.division_id : 0),
                two:   options.type == 'Program' ? (options.subprogram_id ? options.program_id : 0) : (options.unit_id     ? options.unit_id : 0),
                three: options.type == 'Program' ? (options.cluster_id ? options.program_id : 0)    : (options.subunit_id  ? options.subunit_id : 0),
            }
            this.exportlink = '/api/export/'+options.workshopId+'/annex-f/'+options.status+'/'+options.type+'/'+ids.one+'/'+ids.two+'/'+ids.three+'/'+this.syncedyear
        },
        // Form
        childClick(item, title, type){
            if(type == 'editform'){
                this.editForm(item, title)
            }
            if(type == 'history'){
                this.histories  = item.histories
                this.historyfor = title
            }
        },
        hideForm(){
            this.formshow = false
            this.histories = []
            this.historyfor = ''
        },
        submitForm(status){
            var form = this.form
            if(status == 'reject' || this.isAdmin){
                if(form.comment == ''){
                    this.toastMsg('warning', 'Please add a comment')
                    return false
                }
            }

            this.saving = true
            form.status = this.setFormStatus(status)
            this.saveAnnexF(form).then(res => {
                var icon = res.errors ? 'error' : 'success'
                if(!res.errors){
                    this.hideForm()
                    this.filter.status = res.status
                    this.syncRecords()
                    this.$refs.Close.click()
                }
                this.saving = false
                this.toastMsg(icon, res.message)
            })
        },
        editForm(item, title){
            this.histories  = item.histories
            this.historyfor = title
            this.formshow = true
            var form = this.form
            form.id       = item.id
            form.status   = item.status
            form.title    = title
            form.leaderId = item.projects[0].leader.profile_id
            form.activities = []
            form.funds      = []
            form.subs       = []

            this.setFormArrays(form)
            this.setFunds(form.funds, item.funds)
            this.setActivities(form.activities, item.activities)
            this.setFormSubitems(form, item.subs)
        },
        setFormStatus(status){
            var formstatus = this.form.status
            if(status == formstatus){
                status = 'same'
            }
            if(status == 'reject'){
                status = 'Draft'
            }
            if(status == 'approve'){
                status = formstatus == 'For Review' ? 'For Approval' : formstatus == 'For Approval' ? 'Approved' : 'Submitted'
            }
            if(status == 'For Review'){
                status = this.authuser.active_profile.title.name == 'Unit Head' ? 'For Approval' : 'For Review'
            }
            return status
        },
        setFormSubitems(form, subs){
            for(let i = 0; i < subs.length; i++){
                var sub = subs[i]
                var temp = {
                    id:         sub.id,
                    title:      sub.subproject.title,
                    remarks:    sub.remarks,
                    activities: [],
                    funds:      []
                }
                this.setFormArrays(temp)
                this.setFunds(temp.funds, sub.funds)
                this.setActivities(temp.activities, sub.activities)

                form.subs.push(temp)
            }
        },
        setFormArrays(form){
            for(let i = 0; i < this.months.length; i++){
                form.activities.push([{id: '', description: ''}])
                form.funds.push({id: '', amount: 0})
            }
        },
        setFunds(form, funds){
            for(let i = 0; i < funds.length; i++){
                var fund = funds[i]
                form[fund.table_key].id = fund.id
                form[fund.table_key].amount = this.formatAmount(fund.amount)
            }
        },
        setActivities(form, activities){
            for(let i = 0; i < activities.length; i++){
                var activity = activities[i]
                if(form[activity.table_key][0].description == ''){
                    form[activity.table_key][0].id          = activity.id
                    form[activity.table_key][0].description = activity.description
                }
                else{
                    form[activity.table_key].push({
                        id:          activity.id,
                        description: activity.description
                    })
                }
            }
        },
        addActivity(key, sub = null){
            if(sub === null){
                this.form.activities[key].push({id: '', description: ''})
            }
            else{
                this.form.subs[sub].activities[key].push({id: '', description: ''})
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
        isUserProjectLeader(id){
            return this.authuser.active_profile.id == id
        },
        // Display
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
            status = (status == '') ? 'Draft' : status
            this.filter.status = status
            this.syncedstatus = status
        },
        checkUserTitle(status){
            var result = false
            var userTitle = this.authuser.active_profile.title.name
            if((status == 'New' || status == 'Draft') && (userTitle == 'Unit Head' || userTitle == 'Project Leader')){
                result = true
            }
            if(status == 'For Review' && userTitle == 'Unit Head'){
                result = true
            }
            if((status == 'For Approval' || status == 'Approved') && userTitle == 'Division Chief'){
                result = true
            }
            return result
        },
        statusNewDraft(status){
            return (status == 'New' || status == 'Draft') && !this.saving
        },
        setItemTitle(projects){
            var project = projects[0]
            var title = project.title
            if(projects.length > 1){
                title = project.subprogram.title
            }
            return title
        },
        getDateToday(){
            return moment().format("LLL");
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
        // Watcher
        checkQueryStr(){
            this.fetchAnnexF(this.$route.query.id).then(res => {
                if(res){
                    this.filter.year = res.year
                    this.filter.status = res.status
                    var title = this.setItemTitle(res.projects)
                    this.syncRecords().then(r => {
                        this.editForm(res, title)
                        this.editmode = true
                    })
                }
            })
        },
        // Roles
        inUserRole(code){
            var role = this.authuser.active_profile.roles.find(elem => elem.code == code)
            return (role)
        },
        // checkUserAccess(progId, subpId = null, clusId = null){
            // var user = this.authuser
            // var uProgId = user.program_id
            // var uSubpId = user.subprogram_id
            // var uClusId = user.cluster_id
            // console.log(user)
            // return true
        // }
    },
    computed: {
        ...mapGetters('annexf', ['getAnnexFs']),
        annexfs(){ return this.getAnnexFs },
        ...mapGetters('workshop', ['getWorkshop']),
        workshop(){ return this.getWorkshop },
        workshopYear(){ return parseInt(this.getWorkshop.year) },
        ...mapGetters('program', ['getPrograms']),
        programs(){ return this.getPrograms },
        ...mapGetters('division', ['getDivisions']),
        divisions(){ return this.getDivisions },
        ...mapGetters('user', ['getAuthUser']),
        authuser(){ return this.getAuthUser },
        isAdmin(){ return this.authuser.active_profile.title.name == 'Superadmin' }
    },
    created(){
        this.fetchWorkshop(this.$route.params.workshopId).then(res => {
            this.filter.year = this.filter.year ? this.filter.year : this.workshopYear+1
            this.syncedyear = this.filter.year
            this.loading = false
        })
        if(this.programs.length == 0){
            this.fetchPrograms()
        }
        if(this.divisions.length == 0){
            this.fetchDivisions()
        }
        if(!this.$route.query.id){
            this.checkStatus()
        }
    },
    watch: {
        $route: {
            immediate: true,
            handler (to, from) {
                if(this.$route.query.id){
                    this.checkQueryStr()
                }
            }
        },
    },
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
    max-height: calc(100vh - 318px);
    padding: 0 10px 10px 0;
}
.table-responsive.form-details{
    height: calc(100vh - 258px);
    padding: 10px;
}
.table-responsive.form-display{
    max-height: calc(100vh - 247px);
    padding: 10px;
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