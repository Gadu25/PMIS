<template>
    <h6 class="text-end fw-bold">Annex E</h6>
    <h6 class="text-center mb-3 fw-bold">CY {{workshop.year}} PHYSICAL PLAN </h6>
    <table class="table table-sm table-bordered">
        <thead class="align-middle text-center bg-warning">
            <tr>
                <th rowspan="3">Program / Project</th>
                <th rowspan="3">Performance Indicators (PIs)</th>
                <th colspan="2">Previous Year Acccomplishments <br> CY {{parseInt(workshop.year)}}</th>
                <th rowspan="3">CY {{parseInt(workshop.year) + 1}} <br> Physical Targets</th>
                <th colspan="4">CY {{parseInt(workshop.year) + 1}} Quarterly Physical Targets</th>
            </tr>
            <tr>
                <th>Actual</th>
                <th>Estimate</th>
                <th rowspan="2">1st</th>
                <th rowspan="2">2nd</th>
                <th rowspan="2">3rd</th>
                <th rowspan="2">4th</th>
            </tr>
            <tr>
                <th>Jan 1 - Sep 30</th>
                <th>Oct 1 - Dec 30</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="row in rows" :key="row">
                <td class="p-3" v-for="col in cols" :key="col"></td>
            </tr>
        </tbody>
    </table>
    <span class="position-absolute bottom-0"><small>Empty Data Set</small></span>
</template>
<script>
import { mapGetters } from 'vuex'
export default {
    name: 'EmptyTable',
    data(){
        return {
            cols: [1,2,3,4,5,6,7,8,9],
            rows: [1,2,3,4,5,6,7,8,9,10,11,12]
        }
    },
    computed: {
        ...mapGetters('workshop', ['getWorkshop']),
        workshop(){ return this.getWorkshop }
    },
}
</script>