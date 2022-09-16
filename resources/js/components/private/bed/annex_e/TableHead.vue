<template>
    <thead class="align-middle text-center" :class="!syncing && !printmode ? 'position-sticky top-0 bg-warning' : ''">
        <tr>
            <th style="width: 11.11%" rowspan="3"><span class="text-nowrap">Program / Project</span></th>
            <th style="width: 11.11%" rowspan="3"><span class="text-nowrap">Performance Indicators (PIs)</span></th>
            <th colspan="2"><span class="text-nowrap">Previous Year Acccomplishments</span> <br> CY {{parseInt(workshop.year)}}</th>
            <th rowspan="3">CY {{parseInt(workshop.year) + 1}} <br> <span class="text-nowrap">Physical Targets</span></th>
            <th colspan="4">CY {{parseInt(workshop.year) + 1}} Quarterly Physical Targets</th>
        </tr>
        <tr>
            <th style="width: 11.11%">Actual</th>
            <th style="width: 11.11%">Estimate</th>
            <th style="width: 11.11%" rowspan="2">1st</th>
            <th style="width: 11.11%" rowspan="2">2nd</th>
            <th style="width: 11.11%" rowspan="2">3rd</th>
            <th style="width: 11.11%" rowspan="2">4th</th>
        </tr>
        <tr>
            <th>Jan 1 - Sep 30</th>
            <th>Oct 1 - Dec 30</th>
        </tr>
    </thead>
</template>
<script>
import { mapGetters } from 'vuex'
export default {
    name: 'TableHead',
    props: {
        syncing: Boolean,
        printmode: Boolean
    },
    computed: {
        ...mapGetters('workshop', ['getWorkshop']),
        workshop(){ return this.getWorkshop },
    }
}
</script>