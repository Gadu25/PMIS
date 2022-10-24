<template>
    <thead class="align-middle text-center" :class="sticky ? 'position-sticky top-0 transparint shadow' : ''">
        <tr>
            <th style="width: 8%" rowspan="3"><span class="text-nowrap">Project Name/Activity</span></th>
            <th style="width: 1%" rowspan="3">Total <br> Target <br> (P'000)</th>
            <th colspan="3">{{year-1}}</th>
            <th colspan="12">{{year}}</th>
            <th style="width: 6%" rowspan="3">Total</th>
            <th style="width: 5%" rowspan="3">Remarks</th>
        </tr>
        <tr>
            <th v-for="num in 5" :key="num+'_quarter'" :colspan="3"><span class="text-nowrap">{{setQtr(num)}} Qtr</span></th>
        </tr>
        <tr>
            <th style="width: 5.33%" v-for="month, key in months" :key="key+'month'">{{month}}</th>
        </tr>
    </thead>
</template>
<script>
import { mapGetters } from 'vuex'
export default {
    name: 'TableHead',
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
    methods: {
        setQtr(num){
            return num == 2 ? '1st' : num == 3 ? '2nd' : num == 4 ? '3rd' : '4th'
        },
    },
    computed: {
        ...mapGetters('workshop', ['getWorkshop']),
        workshop(){ return this.getWorkshop },
    },
    props: {
        sticky: Boolean,
        year: Number
    }
}
</script>
<style scoped>
.transparint{
    background: rgba(255, 255, 255, 0.9);
}
</style>