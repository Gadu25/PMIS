<template>
    <div class="px-3 py-4" v-if="!childSelected">
        <h2 class="text-center color-darkBlue">Reports</h2><hr>
        <div class="row flex-wrap px-2">
            <!-- <div class="col-sm-6 px-3 pb-3" v-for="report in reports" :key="report">
                <div class="card border-0 shadow" @click="childSelected = true, $router.push({ name: reportRoute(report)})">
                    <div class="card-body">
                        <h2 class="text-left"> {{report}}</h2>
                    </div>
                </div>
            </div> -->
            <div class="col-md-6 px-3 pb-3">
                <div class="card blue shadow" @click="childSelected = true, $router.push({ name: reportRoute('Annual Reports')})">
                    <div class="card-body">
                        <i class="far fa-file-chart-pie fa-4x me-3"></i>
                        <h3 class="text-left">Annual Reports</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-6 px-3 pb-3">
                <div class="card darkBlue shadow" @click="childSelected = true, $router.push({ name: reportRoute('BAR')})">
                    <div class="card-body">
                        <i class="far fa-chart-bar fa-4x me-3"></i>    
                        <h3 class="text-left">BAR</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-6 px-3 pb-3">
                <div class="card mint shadow" @click="childSelected = true, $router.push({ name: reportRoute('Annex E')})">
                    <div class="card-body">
                        <i class="far fa-chart-area fa-4x me-3"></i>    
                        <h3 class="text-left">Annex E</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-6 px-3 pb-3">
                <div class="card yellow shadow" @click="childSelected = true, $router.push({ name: reportRoute('Annex F')})">
                    <div class="card-body">
                        <i class="far fa-chart-area fa-4x me-3"></i>    
                        <h3 class="text-left">Annex F</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-6 px-3 pb-3">
                <div class="card red shadow" @click="childSelected = true, $router.push({ name: reportRoute('Monthly Accomplishment Reports')})">
                    <div class="card-body">
                        <i class="far fa-calendar fa-4x me-3"></i>   
                        <h3 class="text-left">Monthly Accomplishment Reports</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-6 px-3 pb-3">
                <div class="card bg-secondary shadow" @click="childSelected = true, $router.push({ name: reportRoute('BED')})">
                    <div class="card-body">
                        <i class="far fa-coins fa-4x me-3"></i>  
                        <h3 class="text-left">BED</h3>
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
.card{
    border-radius: 8px;
}
.card-body{
    min-height: 150px;
    display: flex;
    align-items: center;
    justify-content: left;
    text-align: center;
    padding: 5%;
    color: whitesmoke;
}
.card:hover{
    /* transition: 0.3s all ease; */
    /* background: linear-gradient(to bottom right, hsl(215, 100%, 60%) 0%, hsl(215, 100%, 80%) 100%);; */
    transform: scale(1.02);
    cursor: pointer;
}
.darkBlue{
    background-color: #173F5F;
}
.blue{
    background-color:#20639B; 
}
.mint{
    background-color: #3CAEA3;
}
.yellow{
    background-color: #F6D55C;
}
.red{
    background-color: #ED553B;
}
.color-darkBlue{
    color: #173F5F
}
.color-blue{
    color: #20639B
}
.color-mint{
    color: #3CAEA3
}
.color-yellow{
    color: #F6D55C
}
.color-red{
    color: #ED553B
}
@media only screen and (max-width: 600px) {
    .card-body{
        min-height: 100px;
    }
}
</style>