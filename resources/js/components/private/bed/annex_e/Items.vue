<template>
    <template v-for="item in items" :key="'item'+item.id">
        <tr v-if="getRowspan(item.indicators) > 1 || item.status == 'Draft'"> <td :rowspan="getRowspan(item.indicators)" :colspan="getRowspan(item.indicators) == 1 ? 9 : 1">{{item.project.title}}</td> </tr>
        <template v-for="indicator in item.indicators" :key="'indicator_'+indicator.id">
            <tr v-if="indicator.details">
                <td>{{indicator.description}}</td>
                <template v-for="detail in details" :key="indicator.id+'_details_'+detail">
                    <td class="text-end">{{setIndicatorDetail(indicator.details, detail)}}</td>
                </template>
            </tr>
        </template>
        <template v-for="sub in item.subs" :key="'sub_'+sub.id">
            <tr v-if="getRowspan(sub.indicators) > 1 || item.status == 'Draft'">
                <td :rowspan="getRowspan(sub.indicators)" :colspan="getRowspan(item.indicators) == 1 ? 9 : 1"><div class="ms-2">{{!sub.temp_title ? sub.subproject.title : (sub.temp_title == 'ms' ? 'MS' : (sub.temp_title == 'phd') ? 'PhD' : sub.temp_title)}}</div></td>
            </tr>
            <template v-for="indicator in sub.indicators" :key="'indicator_'+indicator.id">
                <tr v-if="indicator.details">
                    <td>{{indicator.description}}</td>
                    <template v-for="detail in details" :key="indicator.id+'_details_'+detail">
                        <td class="text-end">{{setIndicatorDetail(indicator.details, detail)}}</td>
                    </template>
                </tr>
            </template>
        </template>
    </template>
</template>
<script>
export default {
    name: 'Items',
    data(){
        return {
            details: ['actual', 'estimate', 'physical_targets', 'first', 'second', 'third', 'fourth'],
        }
    },
    methods: {
        getRowspan(indicators){
            var length = 1
            for(let i = 0; i < indicators.length; i++){
                if(indicators[i].details){
                    length++
                }
            }
            return length
        },
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
        items: Object
    }
}
</script>