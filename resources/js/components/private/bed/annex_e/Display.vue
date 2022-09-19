<template>
    <div>
        <h6 class="text-end fw-bold">Annex E</h6>
        <h6 class="text-center mb-3 fw-bold">CY {{workshop.year}} PHYSICAL PLAN </h6>
        <div class="table-responsive" :class="!printmode ? 'display' : ''" :style="!printmode ? 'font-size: 14px;' : 'font-size: 12px;'" v-dragscroll>
            <table class="table table-sm table-bordered" :style="!printmode ? 'width: 1500px' : ''">
                <TableHead :syncing="syncing" :printmode="printmode" />
                <tbody>
                    <template v-for="program in annexes" :key="'program_'+program.id">
                        <template v-if="program.subpwithitems || program.subpwithci || program.commonindicators.length > 0">
                            <tr><td colspan="9" class="bg-success fw-bold text-white">{{program.title}}</td></tr>
                            <OtherItems :commonindicators="program.commonindicators" />
                            <Items :items="program.items" />
                            <template v-for="subprogram in program.subprograms" :key="'subprogram_'+subprogram.id">
                                <template v-if="subprogram.items.length > 0 || subprogram.commonindicators.length > 0 || subprogram.cluswithitems || subprogram.cluswithci">
                                    <tr> <td colspan="9" class="fw-bold">{{subprogram.title}}</td> </tr>
                                    <OtherItems :commonindicators="subprogram.commonindicators" />
                                    <Items :items="subprogram.items" />
                                    <TotalItem v-if="program.id == 1" :item="subprogram.totalitems" />
                                    <template v-for="cluster in subprogram.clusters" :key="'cluster_'+cluster.id">
                                        <template v-if="cluster.items.length > 0 || cluster.commonindicators.length > 0">
                                            <tr> <td colspan="9" style="background: lightgreen;"><div class="ms-2 fw-bold">{{cluster.title}}</div></td> </tr>
                                            <OtherItems :commonindicators="cluster.commonindicators" />
                                            <Items :items="cluster.items" />
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
import Items from './Items.vue'
import OtherItems from './OtherItems.vue'
import TableHead from './TableHead.vue'
import TotalItem from './TotalItem.vue'
export default {
    name: 'Display',
    directives: {
        dragscroll: dragscroll,
    },
    components: {
        Items, OtherItems, TableHead, TotalItem
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
    }
}
</script>
<style scoped>
.table-responsive.display{
    height: 100%; 
    padding: 0 4px 4px 4px; 
}
</style>