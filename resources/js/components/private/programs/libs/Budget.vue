<template>
    <div class="mt-2" v-for="budgettype in lib.types" :key="budgettype.id">
        <div id="title"> {{budgettype.name}} </div>
        <div class="budget" id="title">
            <div class="item">Item</div>
            <div class="amount">Amount</div>
        </div>
        <div class="budget" v-for="item in budgettype.items" :key="item.id">
            <div class="item">{{item.name}}</div>
            <div id="peso-sign"></div>
            <div class="amount">{{formatNumber(item.amount)}}</div>
        </div>
        <div class="budget" id="title">
            <div class="item">Sub-Total</div>
            <div id="peso-sign"></div>
            <div class="amount" id="sub">{{getSubTotal(budgettype.items)}}</div>
        </div>
    </div>
    <div class="budget" id="title">
        <div class="item">Grand Total</div>
        <div id="peso-sign"></div>
        <div class="amount">{{getGrandTotal()}}</div>
    </div>
</template>
<script>
export default {
    name: 'Budget',
    data(){
        return {
            lib: []
        }
    },
    methods: {
        formatNumber(num){
            return (Math.round(num * 100) / 100).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2 })
        },
        getSubTotal(items){
            var total = 0
            for(let item of items){
                total = total + parseFloat(item.amount)
            }
            return this.formatNumber(total)
        },
        getGrandTotal(){
            var total = 0
            for(let budgettype of this.lib.types){
                for(let item of budgettype.items){
                    total = total + parseFloat(item.amount)
                }
            }
            return this.formatNumber(total)
        }
    },
    props: {
        libs: Object
    },
    created(){
        var key = this.libs.length - 1
        this.lib = this.libs[key]
    }
}
</script>
<style scoped>
#title{
    font-weight: bold;
    text-transform: uppercase;
}
#peso-sign::before{
    content: '₱';
}
#sub{
    border-top: 2px solid black;
}
.budget{
    display: flex;
}
.budget>.item{
    width: 70%;
    margin-left: 1em;
    padding: 0 15px;
}
.budget>.amount{
    width: 30%;
    text-align: right;
}
</style>