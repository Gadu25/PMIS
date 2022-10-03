<template>
    <div class="p-2">
        <div class="d-flex justify-content-between border-bottom mb-2 pb-2">
            <button class="btn btn-sm btn-danger" @click="childClick">Cancel</button>
            <div>
                <button style="min-width: 80px;" class="btn btn-sm btn-secondary me-1">Draft</button>
                <button style="min-width: 80px;" class="btn btn-sm btn-success">For Review</button>
            </div>
        </div>
        <div class="row">
            
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
                    <template v-for="budgettype in form.types" :key="budgettype.name">
                        <div v-if="form.selectedbudget.includes(budgettype.name)">
                            <p><strong>{{budgettype.name}}</strong> <small tabindex="-1" id="addItem" @click="addBudgetItem(budgettype.items)"><i class="fas fa-plus"></i> Item</small> </p>
                            <div class="budget" id="title">
                                <div class="title">Items</div>
                                <div class="amount">Amount</div>
                            </div>
                            <div class="budget position-relative" v-for="item, key in budgettype.items" :key="key">
                                <div class="title">
                                    <button v-if="item.name == ''" tabindex="-1" @click="removeBudgetItem(budgettype.items, item)" class="btn btn-sm btn-outline-danger border-0 shadow-none position-absolute rounded" style="left: -0.5em"><i class="fas fa-times"></i></button>
                                    <textarea placeholder="Item name..." :style="setHeight(item.name)" id="input" class="form-control border-0 shadow-none rounded-0" v-model="item.name"></textarea>
                                </div>
                                <div id="peso-sign"></div>
                                <div class="amount">
                                    <div class="input-group">
                                        <input v-for="amount, akey in item.amounts" :disabled="akey < (form.libcount - 1)" :key="item.id" v-model="amount.amount" v-money="money" type="text" id="input" class="form-control bg-white border-0 shadow-none rounded-0 text-end">
                                    </div>
                                </div>
                            </div>
                            <div class="budget" id="title">
                                <div class="title"> Sub-Total </div>
                                <div id="peso-sign"></div>
                                <div class="amount">
                                    <div class="input-group">
                                        <template v-for="num in form.libcount" :key="num">
                                            <input :value="setSubTotal(budgettype, num)" disabled type="text" id="input" class="form-control border-0 shadow-none rounded-0 text-end bg-white fw-bold">
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                    <div class="budget" id="title">
                        <div class="title"> Grand Total </div>
                        <div id="peso-sign"></div>
                        <div class="amount">
                            <div class="input-group">
                                <template v-for="num in form.libcount" :key="num">
                                    <input :value="setGrandTotal(num)" disabled type="text" id="input" class="form-control border-0 shadow-none rounded-0 text-end bg-white fw-bold">
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'LibForm',
    emits: ['clicked'],
    setup({emit}){},
    data(){
        return {
            form: {
                ids: [],
                selectedbudget: [],
                source: '',
                types: [
                    {
                        ids: [],
                        name: 'Personal Services',
                        items: [{
                            id: '',
                            name: '',
                            amounts: [{
                                amount: 0,
                                libcount: 0
                            }]
                        }]
                    },
                    {
                        ids: [],
                        name: 'Maintenance and Other Operating Expenses (MOOE)',
                        items: [{
                            id: '',
                            name: '',
                            amounts: [{
                                amount: 0,
                                libcount: 0
                            }]
                        }]
                    },
                    {
                        ids: [],
                        name: 'Capital Outlay',
                        items: [{
                            id: '',
                            name: '',
                            amounts: [{
                                amount: 0,
                                libcount: 0
                            }]
                        }]
                    },
                ],
                libcount: 0
            },
            budgettypes: ['Personal Services', 'Maintenance and Other Operating Expenses (MOOE)', 'Capital Outlay'],
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
        addBudgetItem(items){
            var temp = {
                id: '',
                name: '',
                amounts: []
            }
            for(let i = 0; i < this.form.libcount; i++){
                temp.amounts.push({
                    amount: 0,
                    libcount: i
                })
            }
            items.push(temp)
        },
        removeBudgetItem(items, item){
            items.remove(item)
        },
        resetBudget($event){
            if(!$event.target.checked && this.form.libcount <= 1){
                // var type = this.form.types.find(elem => elem.name == $event.target.value)
                // type.items = [{
                //             id: '',
                //             name: '',
                //             amounts: [{
                //                 amount: 0,
                //                 libcount: 0
                //             }]
                //         }]
            }
        },
        processForm(libs){
            var form = this.form
            var ctr = 0
            for(let lib of libs){
                form.source = lib.source_of_funds
                form.ids.push(lib.id)
                ctr = ctr+1
                for(let budgettype of lib.types){
                    var type = form.types.find(elem => elem.name == budgettype.name)
                    for(let item of budgettype.items){
                        var formitem = type.items.find(elem => elem.name == item.name)
                        if(formitem){
                            formitem.id = item.id
                            formitem.amounts.push({
                                amount: this.formatNumber(item.amount),
                                libcount: ctr
                            })
                        }
                        else{
                            var temp = {
                                id: item.id,
                                name: item.name,
                                amounts: []
                            }
                            temp.amounts.push({
                                amount: this.formatNumber(item.amount),
                                libcount: ctr
                            })
                            type.items.push(temp)
                        }
                    }

                    if(!form.selectedbudget.includes(budgettype.name)){
                        form.selectedbudget.push(budgettype.name)
                    }
                }
            }
            
            form.libcount = ctr
            this.cleanForm()
        },
        cleanForm(){
            for(let type of this.form.types){
                if(type.items.length > 1){
                    var item = type.items.find(elem => elem.id == '')
                    type.items.remove(item)
                }
            }
        },
        childClick(){
            this.$emit('clicked')
        },
        setHeight(name){
            return name.length > 100 ? 'height: 100px' : name.length > 50 ? 'height: 70px' : 'height: 1px'
        },
        setSubTotal(budget, num){
            var total = 0
            for(let item of budget.items){
                for(let amount of item.amounts){
                    if(amount.libcount == num){
                        total = total + this.strToFloat(amount.amount)
                    }
                }
            }
            return this.formatNumber(total)
        },
        setGrandTotal(num){
            var total = 0
            for(let budgettype of this.form.types){
                if(this.form.selectedbudget.includes(budgettype.name)){
                    for(let item of budgettype.items){
                        for(let amount of item.amounts){
                            if(amount.libcount == num){
                                total = total + this.strToFloat(amount.amount)
                            }
                        }
                    }
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
    },
    props: {
        editmode: Boolean,
        libs: Array
    },
    created(){
        this.processForm(this.libs)
    }
}
</script>
<style scoped>
#title{
    font-weight: bold;
    text-transform: uppercase;
}
.budget{
    display: flex;
}
.budget>.title{
    width: 50%;
    padding: 0 10px 0 20px;
}
.budget>.title>textarea{
    height: 10px;
}
.budget>.amount{
    width: 50%;
    text-align: right;
}
#input:focus{
    background: rgba(173, 216, 230, 0.5) !important;
}
#peso-sign::before{
    content: '₱';
}
textarea{
    resize: none;
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
</style>