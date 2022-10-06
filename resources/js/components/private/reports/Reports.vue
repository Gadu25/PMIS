<template>
    <div class="p-2 " v-if="!childSelected">
        <h2 class="text-center pb-2 mb-3 border-bottom">Reports</h2>
        <div class="row flex-wrap px-2">
            <div class="col-sm-6 px-3 pb-3" v-for="report in reports" :key="report">
                <div class="card border-0 shadow" @click="childSelected = true, $router.push({ name: reportRoute(report)})">
                    <div class="card-body">
                        <h1> {{report}}</h1>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <router-view @clicked="childSelected = false" v-else></router-view>
</template>
<script>
export default {
    name: 'Reports',
    data(){
        return {
            reports: [
                'Annual Reports',
                'BAR',
                'Annex E',
                'Annex F',
                'Monthly Accomplishment Reports',
                'BED',
            ],
            childSelected: false
        }
    },
    methods: {
        reportRoute(report){
            if(report == 'Annual Reports'){
                return 'AnnualReport'
            }
            if(report == 'Annex E'){
                return 'AnnexEReport'
            }
            if(report == 'Annex F'){
                return 'AnnexFReport'
            }
            if(report == 'BAR'){
                return 'QuarterlyReport'
            }
            if(report == 'Monthly Accomplishment Reports'){
                return 'MonthlyReport'
            }
            if(report == 'BED'){
                return 'BedReport'
            }
        }
    },
    watch: {
        $route: {
            immediate: true,
            handler(to, from) {
                this.childSelected = (this.$route.name != 'Reports')
            }
        },
    },
}
</script>
<style scoped>
.card-body{
    min-height: 150px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
}
.card:hover{
    /* transition: 0.3s all ease; */
    /* background: linear-gradient(to bottom right, hsl(215, 100%, 60%) 0%, hsl(215, 100%, 80%) 100%);; */
    transform: scale(1.02);
    cursor: pointer;
}
@media only screen and (max-width: 600px) {
    .card-body{
        min-height: 100px;
    }
}
</style>