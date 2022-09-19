<template>
    <tr>
        <td class="text-center" :rowspan="item.indicators.length+1">{{item.title}}</td>
    </tr>
    <tr v-for="indicator in item.indicators" :key="indicator.description+'-'">
        <td>{{indicator.description}}</td>
        <td class="text-end" v-for="col in details" :key="col+'-'+'-'+indicator.description">
            {{indicator.details[col] != 0 ? formatNumber(indicator.details[col]) : ''}}
        </td>
    </tr>
    <template v-for="sub in item.subs" :key="sub.temp_title+'-sub-'">
        <tr>
            <td :rowspan="sub.indicators.length+1"><div class="ms-2">{{!sub.temp_title ? sub.subproject.title : (sub.temp_title == 'ms' ? 'MS' : (sub.temp_title == 'phd') ? 'PhD' : sub.temp_title)}}</div></td>
        </tr>
        <tr v-for="indicator in sub.indicators" :key="indicator.description+'-'">
            <td>{{indicator.description}}</td>
            <td class="text-end" v-for="col in details" :key="col+'-'+'-'+indicator.description">
                {{indicator.details[col] != 0 ? formatNumber(indicator.details[col]) : ''}}
            </td>
        </tr>
    </template>
</template>
<script>
    export default {
        name: 'TotalItem',
        data(){
            return {
                details: ['actual', 'estimate', 'physical_targets', 'first', 'second', 'third', 'fourth'],
            }
        },
        methods: {

            formatNumber(num){
            return (Math.round(num * 100) / 100).toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 0 })
        },
        },
        props: {
            item: Object
        }
    }
</script>
<style scoped>
tr>td{
    font-weight: bold;
}
</style>