<template>
    <div>
        <h6 class="text-end fw-bold">Annex E</h6>
        <h6 class="text-center mb-3 fw-bold">CY {{workshop.year}} PHYSICAL PLAN </h6>
        <div class="table-responsive" :style="!printmode ? 'height: 62vh' : ''">
            <table class="table table-sm table-bordered">
                <thead class="align-middle text-center" :class="!syncing && !printmode ? 'position-sticky top-0 bg-secondary text-white' : ''">
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
                    <template v-for="program in annexes" :key="'program_'+program.id">
                        <template v-if="program.subpwithitems">
                            <tr>
                                <td colspan="9" class="bg-success fw-bold text-white">{{program.title}}</td>
                            </tr>
                            <template v-for="item in program.items" :key="'cluster-item'+item.id">
                                <tr v-if="getRowspan(item.indicators) > 1 || item.status == 'New'"> <td :rowspan="getRowspan(item.indicators)">{{item.project.title}}</td> </tr>
                                <template v-for="indicator in item.indicators" :key="'indicator_'+indicator.id">
                                    <tr v-if="indicator.details">
                                        <td>{{indicator.description}}</td>
                                        <template v-for="detail in details" :key="indicator.id+'_details_'+detail">
                                            <td>{{indicator.details[detail]}}</td>
                                        </template>
                                    </tr>
                                </template>
                                <template v-for="sub in item.subs" :key="'sub_'+sub.id">
                                    <tr v-if="getRowspan(sub.indicators) > 1 || item.status == 'New'">
                                        <td :rowspan="getRowspan(sub.indicators)"><div class="ms-2">{{!sub.temp_title ? sub.subproject.title : (sub.temp_title == 'ms' ? 'MS' : (sub.temp_title == 'phd') ? 'PhD' : sub.temp_title)}}</div></td>
                                    </tr>
                                    <template v-for="indicator in sub.indicators" :key="'indicator_'+indicator.id">
                                        <tr v-if="indicator.details">
                                            <td>{{indicator.description}}</td>
                                            <template v-for="detail in details" :key="indicator.id+'_details_'+detail">
                                                <td>{{indicator.details[detail]}}</td>
                                            </template>
                                        </tr>
                                    </template>
                                </template>
                            </template>
                            <template v-for="subprogram in program.subprograms" :key="'subprogram_'+subprogram.id">
                                <template v-if="subprogram.items.length > 0 || subprogram.cluswithitems">
                                    <tr>
                                        <td colspan="9" class="fw-bold">{{subprogram.title}}</td>
                                    </tr>
                                    <template v-for="item in subprogram.items" :key="'cluster-item'+item.id">
                                        <tr v-if="getRowspan(item.indicators) > 1 || item.status == 'New'"> <td :rowspan="getRowspan(item.indicators)">{{item.project.title}}</td> </tr>
                                        <template v-for="indicator in item.indicators" :key="'indicator_'+indicator.id">
                                            <tr v-if="indicator.details">
                                                <td>{{indicator.description}}</td>
                                                <template v-for="detail in details" :key="indicator.id+'_details_'+detail">
                                                    <td>{{indicator.details[detail]}}</td>
                                                </template>
                                            </tr>
                                        </template>
                                        <template v-for="sub in item.subs" :key="'sub_'+sub.id">
                                            <tr v-if="getRowspan(sub.indicators) > 1 || item.status == 'New'">
                                                <td :rowspan="getRowspan(sub.indicators)"><div class="ms-2">{{!sub.temp_title ? sub.subproject.title : (sub.temp_title == 'ms' ? 'MS' : (sub.temp_title == 'phd') ? 'PhD' : sub.temp_title)}}</div></td>
                                            </tr>
                                            <template v-for="indicator in sub.indicators" :key="'indicator_'+indicator.id">
                                                <tr v-if="indicator.details">
                                                    <td>{{indicator.description}}</td>
                                                    <template v-for="detail in details" :key="indicator.id+'_details_'+detail">
                                                        <td>{{indicator.details[detail]}}</td>
                                                    </template>
                                                </tr>
                                            </template>
                                        </template>
                                    </template>
                                    <template v-for="cluster in subprogram.clusters" :key="'cluster_'+cluster.id">
                                        <template v-if="cluster.items.length > 0">
                                            <tr> <td colspan="9" style="background: lightgreen;"><div class="ms-2 fw-bold">{{cluster.title}}</div></td> </tr>
                                            <template v-for="item in cluster.items" :key="'cluster-item'+item.id">
                                                <tr v-if="getRowspan(item.indicators) > 1 || item.status == 'New'"> <td :rowspan="getRowspan(item.indicators)">{{item.project.title}}</td> </tr>
                                                <template v-for="indicator in item.indicators" :key="'indicator_'+indicator.id">
                                                    <tr v-if="indicator.details">
                                                        <td>{{indicator.description}}</td>
                                                        <template v-for="detail in details" :key="indicator.id+'_details_'+detail">
                                                            <td>{{indicator.details[detail]}}</td>
                                                        </template>
                                                    </tr>
                                                </template>
                                                <template v-for="sub in item.subs" :key="'sub_'+sub.id">
                                                    <tr v-if="getRowspan(sub.indicators) > 1 || item.status == 'New'">
                                                        <td :rowspan="getRowspan(sub.indicators)"><div class="ms-2">{{!sub.temp_title ? sub.subproject.title : (sub.temp_title == 'ms' ? 'MS' : (sub.temp_title == 'phd') ? 'PhD' : sub.temp_title)}}</div></td>
                                                    </tr>
                                                    <template v-for="indicator in sub.indicators" :key="'indicator_'+indicator.id">
                                                        <tr v-if="indicator.details">
                                                            <td>{{indicator.description}}</td>
                                                            <template v-for="detail in details" :key="indicator.id+'_details_'+detail">
                                                                <td>{{indicator.details[detail]}}</td>
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
        </div>
        <span class="position-absolute bottom-0" v-if="annexes.length > 0"><small>Annex E {{displaysyncstatus}} Projects </small></span>
    </div>
</template>
<script>
import { mapGetters } from 'vuex'
export default {
    name: 'Display',
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
        }
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
    }
}
</script>
<style scoped>
</style>