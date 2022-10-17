<template>
    <thead class="text-center align-middle " :class="!forPrint ? 'position-sticky top-0 transparint shadow-lg' : ''">
        <tr>
            <th rowspan="4" style="width: 15% !important;">Programs / Projects / Activities</th>
            <th colspan="13">Budgetary Requirements</th>
        </tr>
        <tr>
            <th rowspan="2">FY {{workshopYear}}</th>
            <th rowspan="2" colspan="3">FY {{workshopYear+1}}</th>
            <th colspan="2">{{workshopYear+2}}</th>
            <th colspan="7">Forward Estimates</th>
        </tr>
        <tr>
            <th colspan="2">Proposal to DBM</th>
            <th v-for="num in 4" :key="num" :colspan="num == 4 ? '1' : '2'">{{workshopYear+num+2}}</th>
        </tr>
        <tr>
            <th>GAA</th>
            <th>Proposed</th>
            <th>NEP</th>
            <template v-for="num in 5" :key="num+'_'">
                <th>{{revise}}</th>
                <th>{{num < 5 ? submitted : '   '}}</th>
            </template>
        </tr>
    </thead>
</template>
<script>
import { mapGetters } from 'vuex'
export default {
    name: 'TableHead',
    data(){
        return {
            revise: 'Revised Proposed Programs and Projects with Corresponding Budget (if any)',
            submitted: 'Submitted During Previous Annual Planning Workshop'
        }
    },
    computed: {
        ...mapGetters('workshop', ['getWorkshop']),
        workshopYear(){ return parseInt(this.getWorkshop.year) }
    },
    props: {
        forPrint: Boolean
    }
}
</script>
<style scoped>
th {
    width: 6.538% !important;
    /* box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; */
}
.transparint{
    background: rgba(255, 255, 255, 0.9);
}
</style>