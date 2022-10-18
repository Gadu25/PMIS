<template>
    <div class="row flex-wrap-reverse">
        <div class="position-relative" :class="toggle ? 'col-sm-9' : 'col-sm-12'">
            <button v-if="!toggle && !formshow" @click="toggle = !toggle" style="z-index: 99" class="btn btn-sm btn-secondary bg-gradient position-absolute end-0"><i class="fas fa-arrow-left"></i></button>
            <div v-if="!editmode" class="card border-0 shadow rounded-0">
                <div class="card-body">
                    <div :style="'font-size:' + fontsize" class="d-flex justify-content-end fw-bold">Annex 1</div>
                    <div class="text-center mb-3">
                        <h6 :style="'font-size:' + fontsize" class="mb-0 fw-bold">SEI Annual Planning Workshop</h6>
                        <h6 :style="'font-size:' + fontsize" class="fw-bold">{{workshop.date}}</h6>
                    </div>
                    <div class="table-responsive" id="main-container" v-dragscroll>
                        <table class="table table-sm table-bordered" :style="'font-size:' + fontsize">
                            <TableHead :forPrint="exportmode" />
                            <tbody class="align-middle">
                                <template v-for="sources, div in annexones" :key="div">
                                    <tr class="fw-bold" style="background: orange;">
                                        <td>{{div}}</td>
                                        <td class="text-end" v-for="num in 13" :key="num">
                                            {{ setDivisionFund(num, sources, div) }}
                                        </td>
                                    </tr>
                                    <template v-for="headers, source in sources" :key="source">
                                        <tr v-if="div != 'STSD'" class="fw-bold text-danger" style="background: yellow;">
                                            <td class="text-center">{{source}}</td>
                                            <td class="text-end" v-for="num in 13" :key="num">
                                                {{ setSourceFund(num, headers) }}
                                            </td>
                                        </tr>
                                        <template v-for="items, header in headers" :key="header">
                                            <tr class="fw-bold">
                                                <td>{{header}}</td>
                                                <td class="text-end" v-for="num in 13" :key="num">
                                                    {{ isSubprogram(items) ? setHeaderFund(num, items) : '' }}
                                                </td>
                                            </tr>
                                            <template v-for="item in items" :key="item.id">
                                                <tr>
                                                    <td><div class="ms-1">{{item.project.title}}</div></td>
                                                    <td class="text-end" v-for="num in 13" :key="num">
                                                        {{ setFund(num, item.funds) }}
                                                    </td>
                                                </tr>
                                                <template v-for="subitem in item.subs" :key="subitem">
                                                    <tr>
                                                        <td><div class="ms-3">{{subitem.subproject.title}}</div></td>
                                                        <td class="text-end" v-for="num in 13" :key="num">
                                                            {{ setFund(num, subitem.funds) }}
                                                        </td>
                                                    </tr>
                                                </template>
                                            </template>
                                            <template v-if="!isSubprogram(items)">
                                                <tr style="background: green;" class="fw-bold">
                                                    <td class="text-center">Sub-Total</td>
                                                    <td class="text-end" v-for="num in 13" :key="num">
                                                        {{ setHeaderFund(num, items) }}
                                                    </td>
                                                </tr>
                                            </template>
                                        </template>
                                        <template v-if="div == 'STSD'">
                                            <tr style="background: #7aa9cf;" class="fw-bold">
                                                <td class="stsdtotals">Total 2A1</td>
                                                <td class="text-end" v-for="num in 13" :key="num">
                                                    {{ setSourceFund(num, headers) }}
                                                </td>
                                            </tr>
                                            <tr style="background: #b4c867;" class="fw-bold">
                                                <td class="stsdtotals">Less AC</td>
                                                <td class="text-end" v-for="num in 13" :key="num">
                                                    {{ setBySource(num, 'AC') }}
                                                </td>
                                            </tr>
                                            <tr style="background: #7aa9cf;" class="fw-bold">
                                                <td class="stsdtotals">Total for 2A2</td>
                                                <td class="text-end" v-for="num in 13" :key="num">
                                                    {{ setBySource(num, '2A2') }}
                                                </td>
                                            </tr>
                                        </template>
                                    </template>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <Form @clicked="childClicked()" v-else />
        </div>
        <div class="col-sm-3" v-if="toggle && !formshow">
            <div class="card border-0 shadow">
                <div class="card-body">
                    <button @click="toggle = !toggle" class="btn btn-sm btn-secondary bg-gradient float-end"><i class="fas fa-arrow-right"></i></button>
                    <h4>Filters</h4>
                    <div class="form-floating mb-2">
                        <select class="form-select" id="floatingSelect" aria-label="Floating label select example">
                            <option value="0" selected>All Division</option>
                            <option :value="division.id" v-for="division in divisions" :key="division.id">{{division.name}}</option>
                        </select>
                        <label for="floatingSelect">Division</label>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button class="btn btn-sm btn-primary bg-gradient"><i class="fas fa-sync"></i> Sync</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { mapActions, mapGetters } from 'vuex'
import { dragscroll } from 'vue-dragscroll'
import Form from './Form.vue'
import TableHead from './TableHead.vue'
export default {
    name: 'Display',
    emits: ['clicked'],
    setup({emit}){},
    directives: {
        dragscroll: dragscroll,
    },
    components: { Form, TableHead },
    data(){
        return {
            fontsize: '12px',
            toggle: true,
            prev: false,
            formshow: false,
            exportmode: false
        }
    },
    methods: {
        ...mapActions('division', ['fetchDivisions']),
        childClicked(){
            this.formshow = !this.formshow
            this.prev = this.formshow ? this.toggle : this.prev
            this.toggle = this.formshow ? false : this.prev
            this.childClick()
        },
        childClick(){
            this.$emit('clicked')
        },
        isSubprogram(items){
            for(let item of items){
                return item.header_type == 'Subprogram'
            }
        },
        setFund(num, funds){
            var amount = 0
            if(num == 1){
                // GAA 1
                var fund = funds.find(elem => elem.type == 'GAA')
                amount = fund ? fund.amount : 0
            }
            if(num == 2 || num == 5 || num == 7 || num == 9 || num == 11){
                // Proposed 2 5 7 9 11
                var fund = funds.find(elem => elem.type == 'Proposed')
                amount = fund ? fund.amount : 0
            }
            if(num == 3){
                // NEP 3
                var fund = funds.find(elem => elem.type == 'NEP')
                amount = fund ? fund.amount : 0
            }
            if(num == 4 || num == 6 || num == 8 || num == 10 || num == 12){
                // Revised 4 6 8 10 12
                var fund = funds.find(elem => elem.type == 'Revised')
                amount = fund ? fund.amount : 0
            }
            if(num == 13){
                // Last 13
                var fund = funds.find(elem => elem.type == 'Last')
                amount = fund ? fund.amount : 0
            }
            return amount > 0 ? this.formatAmount(amount) : ''
        },
        setHeaderFund(num, items){
            var amount = 0
            for(let item of items){
                var fund = this.setFund(num, item.funds)
                if(fund != ''){
                    amount = amount + this.strToFloat(fund)
                }
            }
            return amount > 0 ? this.formatAmount(amount) : ''
        },
        setSourceFund(num, headers){
            var amount = 0
            for(let header in headers){
                var items = headers[header]
                var fund = this.setHeaderFund(num, items)
                if(fund != ''){
                    amount = amount + this.strToFloat(fund)
                }
            }
            
            return amount > 0 ? this.formatAmount(amount) : ''
        },
        setBySource(num, src){
            var amount = 0
            var annexones = this.annexones
            for(let div in annexones){
                var sources = annexones[div]
                for(let source in sources){
                    if(source.includes(src)){
                        var header = sources[source]
                        for(let head in header){
                            var items = header[head]
                            for(let item of items){
                                var fund = this.setFund(num, item.funds)
                                if(fund != ''){
                                    amount = amount + this.strToFloat(fund)
                                }
                            }
                        }
                    }
                }
            }
            return amount > 0 ? this.formatAmount(amount) : ''
        },
        setDivisionFund(num, data, division){
            var amount = 0
            for(let source in data){
                for(let header in data[source]){
                    for(let item of data[source][header]){
                        var fund = this.setFund(num, item.funds)
                        if(fund != ''){
                            amount = amount + this.strToFloat(fund)
                        }
                    }
                }
            }
            amount = division == 'STSD' ? amount - this.strToFloat(this.setBySource(num, 'AC')) : amount
            return amount > 0 ? this.formatAmount(amount) : ''
        },
        formatAmount(amount){
            amount = parseFloat(amount.toString().replaceAll(',', ''))
            return (Math.round(amount * 100) / 100).toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 0 })
        },
        strToFloat(num){
            let strNum = num.toString().replace(/\,/g,'')
            return Math.abs(parseFloat(strNum))
        },
    },
    computed: {
        ...mapGetters('workshop', ['getWorkshop']),
        ...mapGetters('division', ['getDivisions']),
        ...mapGetters('annexone', ['getAnnexOnes']),
        workshop(){ return this.getWorkshop },
        divisions(){ return this.getDivisions },
        annexones(){ return this.getAnnexOnes },
    },
    created(){
        if(this.divisions.length == 0){
            this.fetchDivisions()
        }
    },
    props: {
        editmode: Boolean
    },
}
</script>
<style scoped>
#main-container{
    height: calc(100vh - 300px);
    overflow: auto;
}
#main-container>table{
    min-width: 1500px;
}
.stsdtotals{
    height: 60px;
    text-align: center;
}
</style>