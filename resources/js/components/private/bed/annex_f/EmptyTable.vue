<template>
    <table class="table table-sm table-bordered">
        <thead class="align-middle text-center">
            <tr>
                <th rowspan="3"><span class="text-nowrap">Project Name/Activity</span></th>
                <th rowspan="3">Total <br> Target <br> (P'000)</th>
                <th colspan="3">{{workshopYear}}</th>
                <th colspan="12">{{parseInt(workshopYear)+1}}</th>
                <th rowspan="3">Total</th>
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
            <tr v-for="row in 15" :key="row+'row'">
                <td v-for="col in 19" :key="row+'row_'+col+'col'"><span class="text-white">-empty-</span></td>
            </tr>
        </tbody>
    </table>
</template>
<script>
export default {
    name: 'EmptyTable',
    data(){
        return {
            months: [
                'Oct', 'Nov', 'Dec',
                'Jan', 'Feb', 'Mar',
                'Apr', 'May', 'Jun',
                'Jul', 'Aug', 'Sep',
                'Oct', 'Nov', 'Dec',
            ]
        }
    },
    methods:{
        setQtr(num){
            return num == 2 ? '1st' : num == 3 ? '2nd' : num == 4 ? '3rd' : '4th'
        }
    },
    props: {
        workshopYear: String
    }
}
</script>
<style scoped>
table{
    font-size: 12px;
}
</style>