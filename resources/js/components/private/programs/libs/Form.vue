<template>
    <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
        <button class="btn btn-sm btn-danger"  style="min-width: 100px" @click="childClick()"><i class="fas fa-times"></i> Cancel</button>
        <strong>{{editmode ? 'Update' : 'New'}} Profile</strong>
        <button class="btn btn-sm btn-success" style="min-width: 100px">Submit</button>
    </div>
    <div id="form-container">
        <div class="text-center mb-2"><strong>General Info</strong></div>
        <div class="row">
            <div class="col-sm-8">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="floatingInput" placeholder=" " v-model="form.title">
                    <label for="floatingInput">Title</label>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-floating mb-3">
                    <select class="form-select" id="floatingSelect" v-model="form.status">
                        <option selected hidden disabled>Select one</option>
                        <option value="New">New</option>
                        <option value="Ongoing">Ongoing</option>
                        <option value="Completed">Completed</option>
                        <option value="Terminated">Terminated</option>
                    </select>
                    <label for="floatingSelect">Status</label>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-floating mb-3">
                    <select class="form-select" id="floatingSelect" v-model="form.comp">
                        <option selected hidden disabled>Select one</option>
                        <option value="Disaster and Risk Reduction and Management (DRRM)">Disaster and Risk Reduction and Management (DRRM)</option>
                        <option value="Gender and Development (GAD)">Gender and Development (GAD)</option>
                        <option value="Indigenous People">Indigenous People</option>
                        <option value="Mandate">Mandate</option>
                        <option value="Person With Disabilities (PWD)">Person With Disabilities (PWD)</option>
                        <option value="Senior Citizen">Senior Citizen</option>
                        <option value="Other">Other</option>
                    </select>
                    <label for="floatingSelect">Compliance with Law</label>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-floating mb-3">
                    <select class="form-select" id="floatingSelect" v-model="form.leader">
                        <option selected hidden disabled>Select one</option>
                        
                    </select>
                    <label for="floatingSelect">Project Leader</label>
                </div>
            </div>
            <p>Proponents  <button class="btn btn-sm btn-outline-success border-0 shadow-none mb-1" tabindex="-1" @click="addProponent()"><i class="fas fa-plus"></i></button></p>
            <div class="col-sm-4" v-for="proponent, key in form.proponents" :key="key">
                <div class="form-floating mb-3 position-relative">
                    <button class="btn btn-sm btn-outline-danger border-0 end-0 position-absolute shadow-none" tabindex="-1" v-if="form.proponents.length > 1" @click="removeProponent(proponent)"><i class="fas fa-times"></i></button>
                    <input type="text" class="form-control" id="floatingInput" placeholder=" " v-model="proponent.name">
                    <label for="floatingInput">Proponent {{key+1}}</label>
                </div>
            </div>
            <p>Implementation Period</p>
            <div class="col-sm-4">
                <div class="form-floating mb-3">
                    <select class="form-select" id="floatingSelect" v-model="form.start">
                        <option selected hidden disabled>Select one</option>
                        <option :value="month" v-for="month in 12" :key="month">{{monthName(month)}}</option>
                    </select>
                    <label for="floatingSelect">Start</label>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-floating mb-3">
                    <select class="form-select" id="floatingSelect" v-model="form.end">
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
            </div><hr>
            <div class="text-center mb-2"><strong>Proposal</strong> <button class="btn btn-outline-success btn-sm border-0 shadow-none mb-1" tabindex="-1" data-bs-toggle="modal" data-bs-target="#proposalcontent" @click="newContent()"><i class="fas fa-plus"></i></button></div>
            <div class="col-sm-12 d-flex" v-for="content, key in form.proposalcontent" :key="key">
                <div class="form-floating mb-3" style="width: 90%">
                    <textarea class="form-control" style="height: 120px" placeholder=" " id="floatingTextarea"></textarea>
                    <label for="floatingTextarea">{{content.title}}</label>
                </div>
                <div class="text-center" style="width: 10%;">
                    <button tabindex="-1" style="width: 32px" class="btn btn-sm btn-outline-secondary my-1 shadow-none"><i class="fas fa-paperclip"></i></button><br>
                    <template v-if="!content.required">
                        <button tabindex="-1" style="width: 32px" class="btn btn-sm btn-primary mb-1" data-bs-toggle="modal" data-bs-target="#proposalcontent" @click="editContent(content, key)"><i class="far fa-pencil-alt"></i></button><br>
                        <button tabindex="-1" style="width: 32px" class="btn btn-sm btn-danger" @click="removeContent(content)"><i class="far fa-trash-alt"></i></button>
                    </template>
                </div>
            </div><hr>
            <div class="col-sm-2">
                <div class="form-floating mb-3">
                    <select class="form-select" id="floatingSelect">
                        <option selected hidden disabled>Select one</option>
                        <option value="2A1">2A1</option>
                        <option value="2A1-AC">2A1-AC</option>
                        <option value="2A2">2A2</option>
                    </select>
                    <label for="floatingSelect">Source of Funds</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" @change="resetBudget" v-model="form.selectedbudget" value="Personal Services" id="PS">
                    <label class="form-check-label" for="PS">
                        Personal Services
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" @change="resetBudget" v-model="form.selectedbudget" value="Maintenance and Other Operating Expenses (MOOE)" id="MOOE">
                    <label class="form-check-label" for="MOOE">
                        Maintenance and Other Operating Expenses (MOOE)
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" @change="resetBudget" v-model="form.selectedbudget" value="Capital Outlay" id="CO">
                    <label class="form-check-label" for="CO">
                        Capital Outlay
                    </label>
                </div>
            </div>
            <div class="col-sm-10">
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
                                <input id="budget-input" v-model="item.amount" v-money="money" type="text" class="form-control rounded-0 border-0 shadow-none text-end" placeholder="P 0.00">
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
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="proposalcontent" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
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
        </div>
    </div>
</template>
<script>
export default {
    name: 'Form',
    emits: ['clicked'],
    setup({emit}){},
    data(){
        return {
            form: {
                id: '',
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
                selectedbudget: ['Personal Services', 'Maintenance and Other Operating Expenses (MOOE)'],
                budgets: [
                    {name: 'Personal Services', items: []},
                    {name: 'Maintenance and Other Operating Expenses (MOOE)', items: []},
                    {name: 'Capital Outlay', items: []}
                ]
            },
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
        }
    },
    methods: {
        fillForm(){

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
        },
        editContent(content, key){
            this.editproposalcontent = true
            this.newcontenttitle = content.title
            this.newcontentkey = key
        },
        addContent(){
            if(this.checkContentTitle()){
                this.form.proposalcontent.push({
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
            items.push({title: '', amount: 0})
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
        toastMsg(icon, msg){
            toast.fire({
                icon: icon,
                title: msg
            })
        },
        strToFloat(num){
            let strNum = num.toString().replace(/\,/g,'')
            return parseFloat(strNum)
        },
        formatNumber(num){
            return (Math.round(num * 100) / 100).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2 })
        },
    },
    created(){
        this.fillForm()
        var date = new Date
        this.form.year = date.getFullYear()
    },
    props: {
        editmode: Boolean
    }
}
</script>
<style scoped>
#form-container{
    padding: 0 1.5em 15em 1.5em;
    /* padding-right: 1.5em;
    padding-left: 1.5em; */
    height: calc(100vh - 155px);
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