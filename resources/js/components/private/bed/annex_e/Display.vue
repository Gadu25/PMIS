<template>
    <div>
        <h6 class="text-end fw-bold">Annex E</h6>
        <h6 class="text-center mb-3 fw-bold">CY {{workshop.year}} PHYSICAL PLAN </h6>
        <div class="table-responsive" :style="!printmode ? 'height: 60vh; padding: 4px;' : 'font-size: 12px;'" v-dragscroll>
            <table class="table table-sm table-bordered">
                <thead class="align-middle text-center" :class="!syncing && !printmode ? 'sticky-header' : ''">
                    <tr>
                        <th rowspan="3"><span class="text-nowrap">Program / Project</span></th>
                        <th rowspan="3"><span class="text-nowrap">Performance Indicators (PIs)</span></th>
                        <th colspan="2"><span class="text-nowrap">Previous Year Acccomplishments</span> <br> CY {{parseInt(workshop.year)}}</th>
                        <th rowspan="3">CY {{parseInt(workshop.year) + 1}} <br> <span class="text-nowrap">Physical Targets</span></th>
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
                    <template v-for="program in annexes" :key="'program_'+program.id">
                        <template v-if="program.subpwithitems || program.subpwithci || program.commonindicators.length > 0">
                            <tr><td colspan="9" class="bg-success fw-bold text-white">{{program.title}}</td></tr>
                            <template v-for="ci in program.commonindicators" :key="'other_'+ci.id">
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
                            <template v-for="item in program.items" :key="'program-item'+item.id">
                                <tr v-if="getRowspan(item.indicators) > 1 || item.status == 'New'"> <td :rowspan="getRowspan(item.indicators)" :colspan="getRowspan(item.indicators) == 1 ? 9 : 1">{{item.project.title}}</td> </tr>
                                <template v-for="indicator in item.indicators" :key="'indicator_'+indicator.id">
                                    <tr v-if="indicator.details">
                                        <td>{{indicator.description}}</td>
                                        <template v-for="detail in details" :key="indicator.id+'_details_'+detail">
                                            <td>{{setIndicatorDetail(indicator.details, detail)}}</td>
                                        </template>
                                    </tr>
                                </template>
                                <template v-for="sub in item.subs" :key="'sub_'+sub.id">
                                    <tr v-if="getRowspan(sub.indicators) > 1 || item.status == 'New'">
                                        <td :rowspan="getRowspan(sub.indicators)" :colspan="getRowspan(item.indicators) == 1 ? 9 : 1"><div class="ms-2">{{!sub.temp_title ? sub.subproject.title : (sub.temp_title == 'ms' ? 'MS' : (sub.temp_title == 'phd') ? 'PhD' : sub.temp_title)}}</div></td>
                                    </tr>
                                    <template v-for="indicator in sub.indicators" :key="'indicator_'+indicator.id">
                                        <tr v-if="indicator.details">
                                            <td>{{indicator.description}}</td>
                                            <template v-for="detail in details" :key="indicator.id+'_details_'+detail">
                                                <td>{{setIndicatorDetail(indicator.details, detail)}}</td>
                                            </template>
                                        </tr>
                                    </template>
                                </template>
                            </template>
                            <template v-for="subprogram in program.subprograms" :key="'subprogram_'+subprogram.id">
                                <template v-if="subprogram.items.length > 0 || subprogram.commonindicators.length > 0 || subprogram.cluswithitems || subprogram.cluswithci">
                                    <tr> <td colspan="9" class="fw-bold">{{subprogram.title}}</td> </tr>
                                    <template v-for="ci in subprogram.commonindicators" :key="'other_'+ci.id">
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
                                    <template v-for="item in subprogram.items" :key="'subprogram-item'+item.id">
                                        <tr v-if="getRowspan(item.indicators) > 1 || item.status == 'New'"> <td :rowspan="getRowspan(item.indicators)" :colspan="getRowspan(item.indicators) == 1 ? 9 : 1">{{item.project.title}}</td> </tr>
                                        <template v-for="indicator in item.indicators" :key="'indicator_'+indicator.id">
                                            <tr v-if="indicator.details">
                                                <td>{{indicator.description}}</td>
                                                <template v-for="detail in details" :key="indicator.id+'_details_'+detail">
                                                    <td class="text-end">{{setIndicatorDetail(indicator.details, detail)}}</td>
                                                </template>
                                            </tr>
                                        </template>
                                        <template v-for="sub in item.subs" :key="'sub_'+sub.id">
                                            <tr v-if="getRowspan(sub.indicators) > 1 || item.status == 'New'">
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
                                    <template v-for="cluster in subprogram.clusters" :key="'cluster_'+cluster.id">
                                        <template v-if="cluster.items.length > 0 || cluster.commonindicators.length > 0">
                                            <tr> <td colspan="9" style="background: lightgreen;"><div class="ms-2 fw-bold">{{cluster.title}}</div></td> </tr>
                                            <template v-for="ci in cluster.commonindicators" :key="'other_'+ci.id">
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
                                            <template v-for="item in cluster.items" :key="'cluster-item'+item.id">
                                                <tr v-if="getRowspan(item.indicators) > 1 || item.status == 'New'"> <td :rowspan="getRowspan(item.indicators)" :colspan="getRowspan(item.indicators) == 1 ? 9 : 1">{{item.project.title}}</td> </tr>
                                                <template v-for="indicator in item.indicators" :key="'indicator_'+indicator.id">
                                                    <tr v-if="indicator.details">
                                                        <td>{{indicator.description}}</td>
                                                        <template v-for="detail in details" :key="indicator.id+'_details_'+detail">
                                                            <td class="text-end">{{setIndicatorDetail(indicator.details, detail)}}</td>
                                                        </template>
                                                    </tr>
                                                </template>
                                                <template v-for="sub in item.subs" :key="'sub_'+sub.id">
                                                    <tr v-if="getRowspan(sub.indicators) > 1 || item.status == 'New'">
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
                                    </template>
                                </template>
                            </template>
                        </template>
                    </template>
                </tbody>
            </table>
            <span :class="!printmode? 'position-absolute bottom-0' : ''" v-if="annexes.length > 0">Annex E {{displaysyncstatus}} Projects <span v-if="printmode">as of {{this.getDateToday()}}</span></span>
        </div>
    </div>
</template>
<script>
import moment from 'moment'
import { mapGetters } from 'vuex'
import { dragscroll } from 'vue-dragscroll'
export default {
    name: 'Display',
    directives: {
        dragscroll: dragscroll,
    },
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
        getDateToday(){
            return moment().format("LLL");
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
    computed: {
        ...mapGetters('workshop', ['getWorkshop']),
        workshop(){ return this.getWorkshop },
        ...mapGetters('annexe', ['getAnnexEs']),
        annexes(){ return this.getAnnexEs }
    },
    props: {
        displaysyncstatus: String,
        syncing: Boolean,
        printmode: Boolean
    },
    created(){
        
    }
}
</script>
<style scoped>
.sticky-header{
    position: sticky;
    top: -3px;
    background: white;
}
.sticky-header>tr>th{
    
    outline: 1px solid rgba(0,0,0,0.25);
}
</style>