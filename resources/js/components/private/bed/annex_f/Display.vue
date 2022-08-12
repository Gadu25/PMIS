<template>
    <div>
        <div class="d-flex justify-content-between mb-2">
            <input type="search" class="form-control form-control-sm w-50" placeholder="Search" v-model="keyword">
            <button class="btn btn-sm btn-outline-secondary shadow-none" @click="filtershow = 'active'"><i class="far fa-filter"></i></button>
        </div>
        <div class="d-flex align-items-center mb-3">
            <div class="form-check form-switch">
                <input style="cursor: pointer" class="form-check-input" type="checkbox" id="print" v-model="printmode">
                <label style="cursor: pointer" class="form-check-label" for="print">Print Mode</label>
            </div>
            <button v-if="printmode" v-print="'#printMe'" class="btn btn-sm btn-outline-secondary ms-3"><i class="far fa-print"></i> Print</button>
        </div>
        <div :class="printmode ? '' : 'table-container'" v-dragscroll  id="printMe">
            <template v-if="printmode">
                <small class="fw-bold float-end">Annex F</small>
                <small>Department of Science and Technology</small><br>
                <small class="fw-bold">SCIENCE EDUCATION INSTITUTE</small><br><br>
                <h6 style="background: yellow;"><small class="fw-bold">Schedule of FY {{parseInt(workshop.year) + 1}} Project Implementation</small></h6>
            </template>
            <table class="table table-sm table-bordered table-hover" :style="'font-size: '+(printmode ? '11.3px' : '16px')+'; width: '+(!printmode ? '2400px' : '')">
                <thead class="align-middle text-center" :class="!printmode ? 'viewmode' : ''">
                    <tr>
                        <th class="py-0" style="width: 8%" rowspan="3">Program Name/Activity</th>
                        <th class="py-0" style="width: 1%" rowspan="3">Total <br>Target <br>(P'000)</th>
                        <th class="py-0" colspan="3">{{workshop.year}}</th>
                        <th class="py-0" colspan="12">{{parseInt(workshop.year) + 1}}</th>
                        <th class="py-0" style="width: 4%" rowspan="3">Total</th>
                        <th class="py-0" style="width: 4%" rowspan="3">Remarks</th>
                    </tr>
                    <tr>
                        <th class="py-0" colspan="3">4th Qtr</th>
                        <th class="py-0" colspan="3">1st Qtr</th>
                        <th class="py-0" colspan="3">2nd Qtr</th>
                        <th class="py-0" colspan="3">3rd Qtr</th>
                        <th class="py-0" colspan="3">4th Qtr</th>
                    </tr>
                    <tr>
                        <th class="py-0" style="width: 5.53%" v-for="col, key in columns" :key="col+key">{{col}}</th>
                    </tr>
                    <tr style="font-size: 10px !important;">
                        <td class="py-0" v-for="num in [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19]" :key="num">{{(num == 18) ? '' : (num == 19) ? '(18)' : '('+num+')'}}</td>
                    </tr>
                </thead>
                <tbody class="align-top">
                    <template v-for="subprograms, program in annexfs" :key="program">
                        <template v-if="filter.programs.includes(program)">
                            <tr style="background: lightgreen"><td class="fw-bold py-0" colspan="19">{{program}}</td></tr>
                            <template v-for="clus, subprogram in subprograms" :key="subprogram">
                                <template v-if="filter.subprograms.includes(subprogram)">
                                    <template v-for="annexfs, cluster in clus" :key="program+'_cluster_'+cluster">
                                        <template v-if="filter.clusters.includes(cluster) || cluster == ''">
                                            <tr style="background: lightblue" v-if="cluster != ''"><td colspan="19" class="fw-bold py-0"><div class="ms-1">{{cluster}}</div></td></tr>
                                            <template v-for="annexf in annexfs" :key="'annexf_'+annexf.id">
                                                <tr>
                                                    <td>{{annexf.title}}</td><td></td>
                                                    <td v-for="col, key in columns" :key="col+'_'+key"><template v-for="activity in annexf.activities" :key="'activity_'+activity.id">
                                                        <div class="border-bottom" :class="printmode ? 'mb-1' : 'mb-3'" v-if="activity.table_key == key">{{activity.description}}</div>
                                                    </template></td>
                                                    <td></td><td>{{annexf.remarks}}</td>
                                                </tr>
                                                <tr class="fw-bold" style="background: rgba(255, 166, 0, 0.15)">
                                                    <td class="py-0 text-center"><small>Fundings</small></td><td></td>
                                                    <td class="py-0 text-end" v-for="col, key in columns" :key="col+'_'+key"><template v-for="fund in annexf.funds" :key="'fund_'+fund.id">
                                                        <span v-if="fund.table_key == key">{{formatAmount(fund.amount)}}</span>
                                                    </template></td>
                                                    <td class="py-0 text-end">{{formatAmount(getTotalAmount(annexf.funds))}}</td><td></td>
                                                </tr>
                                                <template v-for="sub in annexf.subs" :key="'sub_'+sub.id">
                                                    <tr>
                                                        <td><div class="ms-3">{{sub.subproject.title}}</div></td><td></td>
                                                        <td v-for="col, key in columns" :key="'sub_'+col+'_'+key"><template v-for="activity in sub.activities" :key="'subactivity_'+activity.id">
                                                            <div :class="printmode ? 'mb-1' : 'mb-3'" v-if="activity.table_key == key"><span>{{activity.description}}</span> <br></div>
                                                        </template></td>
                                                        <td></td><td>{{sub.remarks}}</td>
                                                    </tr>
                                                    <tr class="fw-bold" style="background: rgba(255, 166, 0, 0.15)">
                                                        <td class="py-0 text-center"><small>Fundings</small></td><td></td>
                                                        <td class="py-0 text-end" v-for="col, key in columns" :key="'sub_'+col+'_'+key"><template v-for="fund in sub.funds" :key="'subfund_'+fund.id">
                                                            <span v-if="fund.table_key == key">{{formatAmount(fund.amount)}}</span>
                                                        </template></td>
                                                        <td class="py-0 text-end">{{formatAmount(getTotalAmount(sub.funds))}}</td><td></td>
                                                    </tr>
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
        <div class="filter-container shadow-lg" :class="filtershow">
            <button class="btn btn-sm btn-danger float-end" @click="filtershow = ''"><i class="fas fa-times"></i></button>
            <h4><i class="far fa-filter"></i> Filter</h4><hr>
            <div class="programs">
                <template v-for="program in programs" :key="program.id+'_program'">
                    <div class="form-check form-switch my-2 pb-1 border-bottom" style="background: lightgreen;">
                        <input style="cursor: pointer" class="form-check-input shadow-none" type="checkbox" :id="program.title" :value="program.title" v-model="filter.programs" @click="filterChange($event, program.id)">
                        <label style="cursor: pointer" class="form-check-label fw-bold w-100" :for="program.title">{{program.title}}</label>
                    </div>
                    <template v-for="subprogram in program.subprograms" :key="subprogram.id+'_subprogram'">
                        <div class="form-check form-switch mb-1 pb-1 ms-3 border-bottom">
                            <input style="cursor: pointer" class="form-check-input shadow-none" type="checkbox" :id="subprogram.title" :value="subprogram.title_short" v-model="filter.subprograms" @click="filterChange($event, program.id, subprogram.id)">
                            <label style="cursor: pointer" class="form-check-label fw-bold w-100" :for="subprogram.title">{{program.id != 2 || subprogram.id != 3 ? subprogram.title_short : subprogram.title}}</label>
                        </div>
                        <template v-for="cluster in subprogram.clusters" :key="cluster.id+'_cluster'">
                            <div class="form-check form-switch mb-1 pb-1 ms-5 border-bottom">
                                <input style="cursor: pointer" class="form-check-input shadow-none" type="checkbox" :id="cluster.title" :value="cluster.title" v-model="filter.clusters" @click="filterChange($event, program.id, subprogram.id, cluster.id)">
                                <label style="cursor: pointer" class="form-check-label w-100" :for="cluster.title">{{cluster.title}}</label>
                            </div>
                        </template>
                    </template>
                </template>
            </div>
        </div>
    </div>
</template>
<script>
import { mapGetters } from 'vuex'
import { dragscroll } from 'vue-dragscroll'
export default {
    name: 'Display',
    directives: {
        dragscroll: dragscroll,
    },
    data(){
        return {
            printmode: false,
            filtershow: '',
            keyword: '',
            columns: [
                'Oct', 'Nov', 'Dec',
                'Jan', 'Feb', 'Mar',
                'Apr', 'May', 'Jun',
                'Jul', 'Aug', 'Sep',
                'Oct', 'Nov', 'Dec',
            ],
            filter: {
                programs: [],
                subprograms: [],
                clusters: []
            }
        }
    },
    methods: {
        formatAmount(amount){
            return (Math.round(amount * 100) / 100).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2 })
        },
        getTotalAmount(funds = []){
            var total = 0
            for(let i = 0; i < funds.length; i++){
                if(funds[i].table_key > 2){
                    var amount = this.strToFloat(funds[i].amount)
                    total = total + Math.abs(amount)
                }
            }
            return total
        },
        strToFloat(num){
            let strNum = num.toString().replace(/\,/g,'')
            return parseFloat(strNum)
        },
        filterChange(e, progId, subpId = 0, clusId = 0){
            var checked = e.target.checked
            var program = this.programs.find(elem => elem.id == progId)
            if(clusId != 0){
                var subprogram = program.subprograms.find(elem => elem.id == subpId)
                var cluster = subprogram.clusters.find(elem => elem.id == clusId)
                if(checked){
                    this.filter.clusters.push(cluster.title)
                    this.filter.subprograms.push(subprogram.title_short)
                    this.filter.programs.push(program.title)
                }
                else{
                    this.filter.clusters.remove(cluster.title)
                    var state = false
                    for(let i = 0; i < subprogram.clusters.length; i++){
                        if(!state){
                            var cluster = subprogram.clusters[i]
                            state = this.filter.clusters.includes(cluster.title)
                        }
                    }
                    if(!state){
                        this.filter.subprograms.remove(subprogram.title_short)
                        for(let i = 0; i < program.subprograms.length; i++){
                            if(!state){
                                var subprogram = program.subprograms[i]
                                state = this.filter.subprograms.includes(subprogram.title_short)
                            }
                        }
                        if(!state){
                            this.filter.programs.remove(program.title)
                        }
                    }
                }
            }
            else if(subpId != 0){
                var subprogram = program.subprograms.find(elem => elem.id == subpId)
                if(checked){
                    if(!this.filter.programs.includes(program.title)){
                        this.filter.programs.push(program.title)
                    }
                    this.filter.subprograms.push(subprogram.title_short)
                    for(let i = 0; i < subprogram.clusters.length; i++){
                        var cluster = subprogram.clusters[i]
                        this.filter.clusters.push(cluster.title)
                    }
                }
                else{
                    this.filter.subprograms.remove(subprogram.title_short)
                    for(let i = 0; i < subprogram.clusters.length; i++){
                        var cluster = subprogram.clusters[i]
                        this.filter.clusters.remove(cluster.title)
                    }

                    var state = false
                    for(let i = 0; i < program.subprograms.length; i++){
                        if(!state){
                            var subprogram = program.subprograms[i]
                            state = this.filter.subprograms.includes(subprogram.title_short)
                        }
                    }
                    if(!state){
                        this.filter.programs.remove(program.title)
                    }
                }
            }
            else{
                if(checked){
                    this.filter.programs.push(program.title)
                    for(let i = 0; i < program.subprograms.length; i++){
                        var subprogram = program.subprograms[i]
                        this.filter.subprograms.push(subprogram.title_short)
                        for(let j = 0; j < subprogram.clusters.length; j++){
                            var cluster = subprogram.clusters[j]
                            this.filter.clusters.push(cluster.title)
                        }
                    }
                }
                else{
                    this.filter.programs.remove(program.title)
                    for(let i = 0; i < program.subprograms.length; i++){
                        var subprogram = program.subprograms[i]
                        this.filter.subprograms.remove(subprogram.title_short)
                        for(let j = 0; j < subprogram.clusters.length; j++){
                            var cluster = subprogram.clusters[j]
                            this.filter.clusters.remove(cluster.title)
                        }
                    }

                }
            }
        },
        // testExcel(){
        //     var uri = 'data:application/vnd.ms-excel;base64,',
        //     template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>',
        //     base64 = function(s) {
        //         return window.btoa(unescape(encodeURIComponent(s)))
        //     },
        //     format = function(s, c) {
        //         return s.replace(/{(\w+)}/g, function(m, p) {
        //         return c[p];
        //         })
        //     }
        //     var toExcel = document.getElementById("printMe").innerHTML;
        //     var ctx = {
        //     worksheet: name || '',
        //     table: toExcel
        //     };
        //     var link = document.createElement("a");
        //     link.download = "export.xlsx";
        //     link.href = uri + base64(format(template, ctx))
        //     link.click();
        // }
    },
    computed: {
        ...mapGetters('annexf', ['getAnnexFs']),
        annexfs(){
            return this.getAnnexFs
        },
        ...mapGetters('workshop', ['getWorkshop']),
        workshop(){ return this.getWorkshop },
        ...mapGetters('program', ['getPrograms']),
        programs(){ return this.getPrograms },
    },
    created(){
        for(let i = 0; i < this.programs.length; i++){
            var program = this.programs[i]
            this.filter.programs.push(program.title)
            for(let j = 0; j < program.subprograms.length; j++){
                var subprogram = program.subprograms[j]
                this.filter.subprograms.push(subprogram.title_short)
                for(let k = 0; k < subprogram.clusters.length; k++){
                    this.filter.clusters.push(subprogram.clusters[k].title)
                }
            }
        }
    }
}
</script>
<style scoped>
.table-container{
    overflow: auto;
    height: 68vh;
    padding: 10px;
}
.viewmode{
    position: sticky;
    top: -11px;
    background: lightgray;
}
.filter-container{
    position: fixed;
    top: -1000px;
    right: 1vw;
    background: white;
    padding: 10px;
    border-radius: 0.25rem;
    width: 380px;
    height: 500px;
    transition: all 0.3s;
}
.filter-container.active{
    top: 20vh;
}
.programs{
    height: 400px;
    overflow: auto;
}
</style>