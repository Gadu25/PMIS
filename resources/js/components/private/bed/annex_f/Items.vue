<template>
    <template v-for="item in items" :key="'cluster-item'+item.id">
        <tr>
            <td>{{setItemTitle(item.projects)}}</td>
            <td></td>
            <td v-for="num, key in 15" :key="item.id+'cluster-item-activities'+key">
                <template v-for="activity in item.activities" :key="activity.id+'cluster-item-activity'">
                    <li v-if="key == activity.table_key">{{activity.description}}</li>
                </template>
            </td>
            <td></td>
            <td>{{item.remarks}}</td>
        </tr>
        <tr class="funding">
            <td class="text">Funding</td>
            <td></td>
            <td class="amount" v-for="num, key in 15" :key="item.id+'cluster-item-funds-'+key">
                <span v-for="fund in item.funds" :key="fund.id+'cluster-item-fund'">{{fund.table_key == key ? formatAmount(fund.amount) : ''}}</span>
            </td>
            <td class="amount">{{getTotalAmount(item.funds)}}</td>
            <td></td>
        </tr>
        <template v-for="sub in item.subs" :key="sub.id+'cluster-sub'">
            <tr>
                <td><div class="ms-3">{{sub.subproject.title}}</div></td>
                <td></td>
                <td v-for="num, key in 15" :key="sub.id+'cluster-sub-activities'+key">
                    <template v-for="activity in sub.activities" :key="activity.id+'cluster-item-activity'">
                        <li v-if="key == activity.table_key">{{activity.description}}</li>
                    </template>
                </td>
                <td></td>
                <td>{{sub.remarks}}</td>
            </tr>
            <tr class="funding">
                <td class="text">Funding</td>
                <td></td>
                <td class="amount" v-for="num, key in 15" :key="sub.id+'cluster-sub-funds-'+key">
                    <span v-for="fund in sub.funds" :key="fund.id+'cluster-sub-fund'">{{fund.table_key == key ? formatAmount(fund.amount) : ''}}</span>
                </td>
                <td class="amount">{{getTotalAmount(sub.funds)}}</td>
                <td></td>
            </tr>
        </template>
    </template>
</template>
<script>
export default {
    name: 'Items',
    methods: {
        setItemTitle(projects){
            var project = projects[0]
            var title = project.title
            if(projects.length > 1){
                title = project.subprogram.title
            }
            return title
        },
        getTotalAmount(funds){
            var total = 0
            for(let i = 0; i < funds.length; i++){
                if(i > 2){
                    var amount = this.strToFloat(fundArray[i].amount)
                    total = total + Math.abs(amount)
                }
            }
            return total != 0 ? this.formatAmount(total) : ''
        },
        strToFloat(num){
            let strNum = num.toString().replace(/\,/g,'')
            return parseFloat(strNum)
        },
        formatAmount(amount){
            return (Math.round(amount * 100) / 100).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2 })
        },
    },
    props: {
        items: Object
    }
}
</script>