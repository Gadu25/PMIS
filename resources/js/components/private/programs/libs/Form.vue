<template>
    <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
        <button class="btn btn-sm btn-secondary"  style="min-width: 100px" @click="childClick()"><i class="fas fa-times"></i> Cancel</button>
        <strong>{{editmode ? 'Update' : 'New'}} Profile</strong>
        <button class="btn btn-sm" :class="editmode ? 'btn-primary' : 'btn-success'" @click="submitForm()" style="min-width: 100px">{{editmode ? 'Save Changes' : 'Submit'}}</button>
    </div>
    <div id="form-container">
        <div class="text-center mb-2"><strong>General Info</strong></div>
        <div class="row">
            <!-- General Information -->
            <div class="col-sm-8">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="floatingInput" placeholder=" " v-model="form.title">
                    <label for="floatingInput">Title</label>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-floating mb-3">
                    <select class="form-select" id="floatingSelect" v-model="form.status">
                        <option value="" selected hidden disabled>Select one</option>
                        <option value="New">New</option>
                        <option value="Ongoing">Ongoing</option>
                        <option value="Completed">Completed</option>
                        <option value="Terminated">Terminated</option>
                    </select>
                    <label for="floatingSelect">Status</label>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-floating position-relative mb-3">
                    <button class="btn btn-sm btn-outline-secondary border-0 position-absolute end-0" v-if="form.comp.includes('Other')" @click="form.comp = 'Mandate'"><i class="fas fa-times"></i></button>
                    <select class="form-select" id="floatingSelect" v-model="form.comp" v-if="!form.comp.includes('Other')">
                        <option value="" selected hidden disabled>Select one</option>
                        <option value="Disaster and Risk Reduction and Management (DRRM)">Disaster and Risk Reduction and Management (DRRM)</option>
                        <option value="Gender and Development (GAD)">Gender and Development (GAD)</option>
                        <option value="Indigenous People">Indigenous People</option>
                        <option value="Mandate">Mandate</option>
                        <option value="Person With Disabilities (PWD)">Person With Disabilities (PWD)</option>
                        <option value="Senior Citizen">Senior Citizen</option>
                        <option value="Other - ">Other</option>
                    </select>
                    <input type="text" v-else class="form-control" v-model="form.comp">
                    <label for="floatingSelect">Compliance with Law</label>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="floatingInput" v-model="form.leader" disabled>
                    <label for="floatingInput">Project Leader</label>
                </div>
            </div>
            <p>Implementation Period</p>
            <div class="col-sm-4">
                <div class="form-floating mb-3">
                    <select @change="checkMonthSelected()" class="form-select" id="floatingSelect" v-model="form.start">
                        <option selected hidden disabled>Select one</option>
                        <option :value="month" v-for="month in 12" :key="month">{{monthName(month)}}</option>
                    </select>
                    <label for="floatingSelect">Start</label>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-floating mb-3">
                    <select @change="checkMonthSelected()" class="form-select" id="floatingSelect" v-model="form.end">
                        <option selected hidden disabled>Select one</option>
                        <option :value="month" v-for="month in 12" :key="month">{{monthName(month)}}</option>
                    </select>
                    <label for="floatingSelect">End</label>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-floating mb-3">
                    <select class="form-select" id="floatingSelect" v-model="form.year">
                        <option selected hidden disabled>Select one</option>
                        <option :value="year+1950" v-for="year in 200" :key="year">{{year+1950}}</option>
                    </select>
                    <label for="floatingSelect">Year</label>
                </div>
            </div>
            <p>Proponents  <button class="btn btn-sm btn-outline-success border-0 shadow-none mb-1" tabindex="-1" @click="addProponent()"><i class="fas fa-plus"></i></button></p>
            <div class="col-sm-4" v-for="proponent, key in form.proponents" :key="key">
                <div class="form-floating mb-3 position-relative">
                    <button class="btn btn-sm btn-outline-danger border-0 end-0 position-absolute shadow-none" tabindex="-1" v-if="form.proponents.length > 1" @click="removeProponent(proponent)"><i class="fas fa-times"></i></button>
                    <input type="text" class="form-control" id="floatingInput" placeholder=" " v-model="proponent.name">
                    <label for="floatingInput">Proponent {{key+1}}</label>
                </div>
            </div><hr>
            <!-- Proposal Content -->
            <div class="text-center mb-2"><strong>Proposal</strong> <button class="btn btn-outline-success btn-sm border-0 shadow-none mb-1" tabindex="-1" data-bs-toggle="modal" data-bs-target="#modal" @click="newContent()"><i class="fas fa-plus"></i></button></div>
            <div class="col-sm-12" v-for="content, key in form.proposalcontent" :key="key">
                <div class="d-flex justify-content-between mb-1">
                    <div class="content-title fw-bold">{{content.title}}</div>
                    <div class="content-controls" v-if="!content.required">
                        <button tabindex="-1" style="width: 32px" class="btn btn-sm btn-primary me-1" data-bs-toggle="modal" data-bs-target="#modal" @click="editContent(content, key)"><i class="far fa-pencil-alt"></i></button>
                        <button tabindex="-1" style="width: 32px" class="btn btn-sm btn-danger" @click="removeContent(content)"><i class="far fa-trash-alt"></i></button>
                    </div>
                </div>
                <div class="border mb-3">
                    <ckeditor :editor="editor" v-model="content.text" :config="editorConfig"  style="min-height: 120px;" placeholder=" " id="floatingTextarea"></ckeditor>
                </div>
            </div><hr>
            <!-- Line-Item Budget -->
            <template v-if="!editmode">
                <div class="col-sm-3">
                    <div class="form-floating mb-3">
                        <select class="form-select" id="floatingSelect" v-model="form.source">
                            <option selected hidden disabled>Select one</option>
                            <option value="2A1">2A1</option>
                            <option value="2A1-AC">2A1-AC</option>
                            <option value="2A2">2A2</option>
                        </select>
                        <label for="floatingSelect">Source of Funds</label>
                    </div>
                    <div class="form-check" v-for="budgettype in budgettypes" :key="budgettype">
                        <input class="form-check-input" type="checkbox" @change="resetBudget" v-model="form.selectedbudget" :value="budgettype" :id="budgettype">
                        <label class="form-check-label" :for="budgettype">
                            {{budgettype}}
                        </label>
                    </div>
                </div>
                <div class="col-sm-9">
                    <div class="text-center mb-2"><strong>Line-Item Budget (LIB)</strong></div>
                    <div class="budget-items-container">
                        <template v-for="budget in form.budgets" :key="budget.name">
                            <div class="border-bottom mb-2 pb-2" v-if="form.selectedbudget.includes(budget.name)">
                                <p><strong>{{budget.name}}</strong> <small tabindex="-1" id="addItem" @click="addBudgetItem(budget.items)"><i class="fas fa-plus"></i> Item</small></p>
                                <div class="ms-4"><strong>Items</strong><strong class="float-end">Amount</strong></div>
                                <div class="input-group position-relative ms-3" v-for="item, key in budget.items" :key="key">
                                    <button tabindex="-1" @click="removeBudgetItem(budget.items, item)" class="btn btn-sm btn-outline-danger border-0 shadow-none position-absolute rounded" style="left: -1.5em"><i class="fas fa-times"></i></button>
                                    <input id="budget-input" v-model="item.name" type="text" class="form-control rounded-0 border-0 shadow-none w-50" placeholder="Item Name">
                                    <span class="input-group-text bg-white border-0">₱</span>
                                    <input id="budget-input" v-model="item.amount" v-money="money" @change="checkAmount(item.amount, key, budget.name)" type="text" class="form-control rounded-0 border-0 shadow-none text-end" placeholder="P 0.00">
                                </div>
                                <div class="input-group ms-3">
                                    <input type="text" disabled class="form-control bg-white border-0 shadow-none rounded-0 w-50 fw-bold" value="Sub-Total">
                                    <span class="input-group-text bg-white border-0 border-top">₱</span>
                                    <input type="text" disabled class="form-control bg-white border-0 shadow-none rounded-0 fw-bold text-end border-top" :value="getSubTotal(budget.items)">
                                </div>
                            </div>
                        </template>
                        <div class="input-group ms-3" v-if="form.selectedbudget.length > 0">
                            <input type="text" disabled class="form-control bg-white border-0 shadow-none rounded-0 w-50 fw-bold" value="Grand Total">
                            <span class="input-group-text bg-white border-0">₱</span>
                            <input type="text" disabled class="form-control bg-white border-0 shadow-none rounded-0 fw-bold text-end" :value="getGrandTotal()">
                        </div>
                    </div>
                </div><hr>
            </template>
            <!-- Schedule of Activities -->
            <div class="text-center mb-2"><strong>Schedule of Activities</strong> </div>
            <div class="activity-types mb-2">
                <label class="me-2">Activity Type:</label>
                <div class="form-check form-check-inline" v-for="activitytype in activitytypes" :key="activitytype">
                    <input class="form-check-input shadow-none" type="radio" :name="activitytype" :id="activitytype" :value="activitytype" v-model="form.activitytype" :style="form.activitytype == 'Milestone' && activitytype == 'Milestone' ? 'background-color: #32CD32; border: #32CD32' : ''">
                    <label class="form-check-label" :for="activitytype">{{activitytype}}</label>
                </div>
            </div>
            <div class="table-responsive">
                <button class="d-none" data-bs-toggle="modal" data-bs-target="#modal" ref="showModal">shumudal</button>
                <table class="table table-sm table-bordered">
                    <thead class="text-center">
                        <tr>
                            <th style="width: 30%">Activities</th>
                            <template v-for="month in 12" :key="month+'act'">
                                <th v-if="monthInImplementation(month)">{{monthName(month).substring(0, 3)}}</th>
                            </template>
                            <th class="p-0" tabindex="-1" style="width: 32px;"><button tabindex="-1" @click="addActivity()" class="btn btn-outline-success rounded-0 border-0 shadow-none"><i class="fas fa-plus"></i></button></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="activity, key in form.activities" :key="key+'_formactivity'">
                            <td class="p-0">
                                <input id="budget-input" type="text" v-model="activity.title" class="form-control border-0 rounded-0 shadow-none" placeholder="Activity Title...">
                            </td>
                            <template v-for="act, akey in activity.months" :key="act.month+'act'">
                                <td v-if="monthInImplementation(act.month)" style="cursor: pointer;" class="text-center" @click="setActivity(key, akey, act)"> <i  v-if="act.type != ''" :style="'color:' + (act.type == 'Regular' ? '#0d6efd' : '#32CD32')" class="fas fa-circle"></i> </td>
                            </template>
                            <td class="p-0" tabindex="-1" style="width: 32px;"><button tabindex="-1" @click="removeActivity(activity)" class="btn btn-outline-danger rounded-0 border-0 shadow-none w-100"><i class="fas fa-times"></i></button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="modal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" v-if="tab == 'content'">
                <div class="modal-header">
                    <h5 class="modal-title">{{editproposalcontent ? 'Update' : 'New'}} Content</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" ref="Close"></button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="editproposalcontent ? saveContent() : addContent()">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="floatingInput" placeholder=" " v-model="newcontenttitle">
                            <label for="floatingInput">Content Title</label>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button class="btn btn-sm btn-success rounded-pill px-4" type="submit">{{editproposalcontent ? 'Save' : 'Add'}}</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-content" v-if="tab == 'activity'">
                <div class="modal-header">
                    <h5 class="modal-title">Set Milestone Activity</h5>
                    <button type="button" class="btn-close d-none" data-bs-dismiss="modal" ref="CloseMilestone"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <div class="col-sm-6">
                            <div class="form-floating mb-3">
                                <input type="date" class="form-control" :min="minDate" :max="maxDate" v-model="form.monthact.start" id="start" placeholder="start">
                                <label for="start">Start</label>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-floating">
                                <input type="date" class="form-control" :min="minDate" :max="maxDate" v-model="form.monthact.end" id="end" placeholder="end">
                                <label for="end">End</label>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button @click="saveMilestone(false)" class="btn btn-sm btn-secondary rounded-pill px-4 me-1" data-bs-dismiss="modal">Cancel</button>
                        <button @click="saveMilestone(true)" class="btn btn-sm btn-success rounded-pill px-4">Save</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>
<script>
import { mapActions, mapGetters } from 'vuex'
import InlineEditor from '@ckeditor/ckeditor5-build-inline';
export default {
    name: 'Form',
    emits: ['clicked'],
    setup({emit}){},
    data(){
        return {
            tab: 'content',
            form: {
                id: '',
                project_id: '',
                title: '',
                status: '',
                comp: 'Mandate',
                leader: '',
                start: 1,
                end: 12,
                year: 0,
                proponents: [{id: '', name: ''}],
                proposalcontent: [
                    {title: 'Background / Rationale', text: '', file: '', required: true},
                    {title: 'General Objective',      text: '', file: '', required: true}
                ],
                source: '',
                selectedbudget: ['Personal Services', 'Maintenance and Other Operating Expenses (MOOE)'],
                budgets: [
                    {name: 'Personal Services', items: []},
                    {name: 'Maintenance and Other Operating Expenses (MOOE)', items: []},
                    {name: 'Capital Outlay', items: []}
                ],
                activitytype: 'Regular',
                activities: [],
                monthact: {
                    start: '',
                    end: ''
                }
            },
            budgettypes: ['Personal Services', 'Maintenance and Other Operating Expenses (MOOE)', 'Capital Outlay'],
            editproposalcontent: false,
            newcontenttitle: '',
            newcontentkey: '',
            money: {
                decimal: '.',
                thousands: ',',
                prefix: '',
                suffix: '',
                precision: 2,
                masked: false /* doesn't work with directive */
            },
            editor: InlineEditor,
            editorData: '<p>Content of the editor.</p>',
            editorConfig: {
                // The configuration of the editor.
            },
            activitytypes: ['Regular', 'Milestone'],
            minDate: '',
            maxDate: '',
            tempactivitykey: '',
            tempactkey: ''
        }
    },
    methods: {
        ...mapActions('project', ['saveProfile']),
        fillForm(){
            if(this.editmode){
                var profile = this.profile
                this.form.id = profile.id
                this.form.project_id = profile.project_id
                this.form.title = profile.title
                this.form.status = profile.status
                this.form.comp = profile.compliance_with_law
                this.form.leader = profile.project_leader
                this.form.start = profile.start
                this.form.end = profile.end
                this.form.year = profile.year
                this.form.proponents = []
                for(let proponent of profile.proponents){
                    this.form.proponents.push({
                        id: proponent.id,
                        name: proponent.name
                    })
                }
                this.form.proposalcontent = []
                for(let content of profile.proposals){
                    this.form.proposalcontent.push({
                        id: content.id,
                        title: content.title,
                        text: content.text,
                        required: (content.title.includes('Background') || content.title.includes('General'))
                    })
                }
                this.form.activities = []
                for(let activity of profile.activities){
                    var act = {
                        id: activity.id,
                        title: activity.title,
                        months: []
                    }
                    this.setActivityMonths(act, activity.months)
                    this.form.activities.push(act)
                }
            }
            else{
                var date = new Date
                this.form.year = date.getFullYear()

                var leader = this.project.leader.profile.user
                this.form.leader = leader.firstname+' '+leader.lastname

                this.form.title = this.project.title
                this.form.project_id = this.project.id
            }
        },
        childClick(){
            this.$emit('clicked')
        },
        monthName(month){
            if(month == 1){  return 'January' }
            if(month == 2){  return 'February' }
            if(month == 3){  return 'March' }
            if(month == 4){  return 'April' }
            if(month == 5){  return 'May' }
            if(month == 6){  return 'June' }
            if(month == 7){  return 'July' }
            if(month == 8){  return 'August' }
            if(month == 9){  return 'September' }
            if(month == 10){ return 'October' }
            if(month == 11){ return 'November' }
            if(month == 12){ return 'December' }
        },
        addProponent(){
            this.form.proponents.push({id: '', name: ''})
        },
        removeProponent(proponent){
            this.form.proponents.remove(proponent)
        },
        newContent(){
            this.editproposalcontent = false
            this.newcontenttitle = ''
            this.newcontentkey = ''
            this.tab = 'content'
        },
        editContent(content, key){
            this.editproposalcontent = true
            this.newcontenttitle = content.title
            this.newcontentkey = key
        },
        addContent(){
            if(this.checkContentTitle()){
                this.form.proposalcontent.push({
                    id: '',
                    title: this.newcontenttitle,
                    text: '',
                    file: '',
                    required: false
                })
                this.$refs.Close.click()
            }
        },
        saveContent(){
            if(this.checkContentTitle()){
                var content = this.form.proposalcontent[this.newcontentkey]
                content.title = this.newcontenttitle
                this.$refs.Close.click()
            }
        },
        removeContent(content){
            this.form.proposalcontent.remove(content)
        },
        checkContentTitle(){
            if(this.newcontenttitle == ''){
                this.toastMsg('warning', 'Content Title required')
                return false
            }
            return true
        },
        addBudgetItem(items){
            items.push({name: '', amount: 0})
        },
        removeBudgetItem(items, item){
            items.remove(item)
        },
        resetBudget($event){
            if(!$event.target.checked){
                var budget = this.form.budgets.find(elem => elem.name == $event.target.value)
                budget.items = []
            }
        },
        checkAmount(amount, key, type){
            if(amount.toString().includes('-')){
                // this.toastMsg('warning', 'Avoid negative')
                var budget = this.form.budgets.find(elem => elem.name == type)
                var item = budget.items[key]
                item.amount = item.amount.replace(/\-/g,'')
            }
        },
        getSubTotal(items){
            var total = 0.00
            for(let i of items){
                total = total + this.strToFloat(i.amount)
            }
            return this.formatNumber(total)
        },
        getGrandTotal(){
            var total = 0
            for(let budget of this.form.budgets){
                for(let item of budget.items){
                    total = total + this.strToFloat(item.amount)
                }
            }

            return this.formatNumber(total)
        },
        strToFloat(num){
            let strNum = num.toString().replace(/\,/g,'')
            return parseFloat(strNum)
        },
        formatNumber(num){
            return (Math.round(num * 100) / 100).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2 })
        },
        monthInImplementation(month){
            var start = this.form.start
            var end = this.form.end
            return (month >= start && month <= end)
        },
        checkMonthSelected(){
            if(this.form.end < this.form.start){
                this.toastMsg('warning', 'Start month must be before the End Month')
                this.form.start = 1; this.form.end = 12;
            }
            if(this.form.activities.length > 0){
                this.swalConfirmCancel('Changing Implementation Period will reset Activities', 'Continue?').then(res => {
                    if(res == 'confirm'){
                        for(let i = 0; i < this.form.activities.length; i++){
                            var activity = this.form.activities[i]
                            activity.months = []
                            this.setActivityMonths(activity)
                        }
                    }
                })
            }
        },
        saveMilestone(proceed){
            var akey = this.tempactkey
            var key = this.tempactivitykey
            var activity = this.form.activities[key].months[akey]

            var start = this.form.monthact.start
            var end = this.form.monthact.end
            if(proceed){
                if(start == ''){ this.toastMsg('warning', 'Start date required'); return false }
                if(end == '')  { this.toastMsg('warning', 'End date required');   return false }
                if(start > end){ this.toastMsg('warning', 'Start date must be on or before the End date'); return false }
            }

            activity.type = proceed ? 'Milestone' : ''
            activity.start = proceed ? start : ''
            activity.end = proceed ? end : ''

            this.$refs.CloseMilestone.click()
        },
        setActivity(key, akey, act){
            var type = this.form.activitytype
            act.type = act.type != type ? type : ''
            act.start = ''
            act.end = ''
            if(act.type == 'Milestone'){
                this.tab = 'activity'
                var year = this.form.year

                this.form.monthact.start = ''
                this.form.monthact.end = ''

                var date = new Date(year, act.month, 0);
                var last = date.toString().split(' ')

                var month = act.month < 10 ? '0'+act.month : act.month
                this.minDate = year+'-'+month+'-01'
                this.maxDate = year+'-'+month+'-'+last[2]

                this.tempactivitykey = key
                this.tempactkey = akey


                this.$refs.showModal.click()
            }
        },
        addActivity(){
            var activity = {
                id: '',
                title: '',
                months: []
            }
            this.setActivityMonths(activity)

            this.form.activities.push(activity)
        },
        removeActivity(activity){
            this.form.activities.remove(activity)
        },
        setActivityMonths(activity, months = []){
            var start = this.form.start
            var end = this.form.end
            for(let i = start; i <= end; i++){
                var dbmonth = months.find(elem => elem.month == i)
                var checker = (dbmonth)
                var month = {
                    id: checker ? dbmonth.id : '',
                    month: i,
                    start: checker ? dbmonth.start : '',
                    end: checker ? dbmonth.end : '',
                    type: checker ? dbmonth.type : ''
                }
                activity.months.push(month)
            }
        },
        validateForm(){
            var form = this.form
            if(form.title == '') { this.toastMsg('warning', 'Title required'); return false }
            if(form.status == ''){ this.toastMsg('warning', 'Status required'); return false }
            if(form.comp == '')  { this.toastMsg('warning', 'Compliance with Law required'); return false }
            for(let proponent of form.proponents){
                if(proponent.name == ''){ this.toastMsg('warning', 'Proponent required'); return false}
            }
            for(let content of form.proposalcontent){
                if(content.text == ''){ this.toastMsg('warning', content.title+' empty'); return false }
            }
            if(!this.editmode){
                if(form.source == ''){ this.toastMsg('warning', 'Source of Funds required'); return false }
                if(form.selectedbudget.length == 0){ this.toastMsg('warning', 'Please add budget for LIB'); return false }
                for(let budget of form.budgets){
                    if(form.selectedbudget.includes(budget.name)){
                        if(budget.items.length == 0){ this.toastMsg('warning', budget.name+', empty items'); return false }
                        for(let item of budget.items){
                            if(item.name == ''){ this.toastMsg('warning', budget.name+', item name required'); return false }
                            if(item.amount == '0.00'){ this.toastMsg('warning', budget.name+', item amount required'); return false }
                        }
                    }
                }
            }
            var months = []
            for(let i = form.start; i <= form.end; i++){
                months.push(i)
            }
            var monthchecker = []
            for(let activity of form.activities){
                if(activity.title == ''){ this.toastMsg('warning', 'Activity Title required'); return false }
                for(let month of activity.months){
                    if(month.type != '' && !monthchecker.includes(month.month)){
                        monthchecker.push(month.month)
                    }
                }
            }
            if(months.length != monthchecker.length){
                this.toastMsg('warning', 'All months must have at least one (1) activity');
                return false;
            }
            return true
        },
        submitForm(){
            if(this.validateForm()){
                this.saveProfile(this.form).then(res => {
                    var icon = res.errors ? 'error' : 'success'
                    this.toastMsg(icon, res.message)
                    if(!res.errors){
                        this.childClick()
                    }
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
            const res = await swal.fire({
                title: title,
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Continue!',
                reverseButtons: true
                }).then((result) => {
                if (result.isConfirmed) {
                    return 'confirm'
                }
                else{
                    return 'cancel'
                }
            })
            return res
        },
    },
    computed: {
        ...mapGetters('project', ['getProject']),
        project(){ return this.getProject }
    },
    created(){
        this.fillForm()
    },
    props: {
        editmode: Boolean,
        profile: Object
    }
}
</script>
<style scoped>
#form-container{
    padding: 0 1.5em 15em 1.5em;
    height: calc(100vh - 175px);
    overflow: auto;
}
#addItem{
    padding: 4px 6px;
    float: right;
}
#addItem:hover{
    cursor: pointer;
    background: rgba(0,0,0,0.25);
    border-radius: 0.25em;
}
#budget-input:focus{
    background: rgba(173, 216, 230, 0.5);
}
.budget-items-container{
    min-height: 30vh;
}
</style>