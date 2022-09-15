<template>
    <template v-for="ci in commonindicators" :key="'other_'+ci.id">
        <tr class="text-primary fw-bold">
            <td colspan="2">{{ci.description}}</td>
            <template v-for="detail in details" :key="ci.id+'_details_'+detail">
                <td class="text-end">{{setIndicatorDetail(ci.details, detail, ci.description)}}</td>
            </template>
        </tr>
        <tr class="text-primary" v-for="sub in ci.subindicators" :key="'ci_sub_'+sub.id">
            <td colspan="2"><div class="ms-2">{{sub.description}}</div></td>
            <template v-for="detail in details" :key="sub.id+'_details_'+detail">
                <td class="text-end">{{setIndicatorDetail(sub.details, detail, ci.description)}}</td>
            </template>
        </tr>
    </template>
</template>
<script>
export default {
    name: 'OtherItems',
    data(){
        return {
            details: ['actual', 'estimate', 'physical_targets', 'first', 'second', 'third', 'fourth'],
        }
    },
    methods: {
        setIndicatorDetail(details, col, desc = ''){
            if(details){
                var suffix = desc.toLowerCase().includes('percentage') ? '%' : ''
                var num = this.strToFloat(details[col])
                return num > 0 ? this.formatNumber(num)+suffix : ''
            }
        },
        strToFloat(num){
            let strNum = num.toString().replace(/\,/g,'')
            return parseFloat(strNum)
        },
        formatNumber(num){
            return (Math.round(num * 100) / 100).toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 0 })
        },
    },
    props: {
        commonindicators: Object
    }
}
</script>