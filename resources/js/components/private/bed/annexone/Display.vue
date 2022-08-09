<template>
    <div class="px-2 py-3">
        <div class="d-flex justify-content-between mb-3">
            <div class="w-50"><input type="search" class="form-control form-control-sm" placeholder="Search..." v-model="keyword"></div>
            <button class="btn btn-sm btn-outline-secondary" v-if="!loading" @click="filtershow = 'show'"><i class="far fa-filter"></i></button>
            <button class="btn btn-sm btn-outline-secondary" v-else><i class="far fa-spinner fa-spin"></i></button>
        </div>
        <div class="overflow-auto" style="max-height: 65vh;" v-if="!loading">
            <table class="table table-sm table-bordered">
                <thead class="position-sticky top-0 bg-secondary text-white">
                    <tr>
                        <th style="width: 30%">Programs/ Projects/ Activities</th>
                        <th v-for="col, key in columns" :key="col+'_th'">{{setYear(this.workshop.year, key)}}</th>
                    </tr>
                </thead>
                <tbody>
                    <template v-for="div, divKey in annexones" :key="divKey">
                        <template v-if="filters.includes(divKey)">
                            <tr class="fw-bold" style="background: orange">
                                <td>{{divKey}}</td>
                                <td class="text-end" v-for="col, key in columns" :key="divKey+col">{{setDivFunds(div, divKey, setYear(workshop.year, key))}}</td>
                            </tr>
                            <template v-for="source, sourceKey in div" :key="sourceKey">
                                <tr class="fw-bold" v-if="divKey != 'STSD'" style="background: yellow">
                                    <td class="text-center" :style="divKey != 'STSD' ? 'color: red' : ''"><Highlighter highlightClassName="bg-warning bg-gradient fst-italic px-2 rounded" :searchWords="[keyword]" :textToHighlight="sourceKey"/></td>
                                    <td class="text-end" v-for="col, key in columns" :key="sourceKey+col">{{setSourceFunds(source, setYear(workshop.year, key))}}</td>
                                </tr>
                                <template v-for="head, headKey in source" :key="headKey">
                                    <tr class="fw-bold">
                                        <td><Highlighter highlightClassName="bg-warning bg-gradient fst-italic px-2 rounded" :searchWords="[keyword]" :textToHighlight="headKey"/></td>
                                        <td class="text-end" v-for="col, key in columns" :key="headKey+col">{{ !headKey.includes('Unit') ? setHeaderFunds(head, setYear(workshop.year, key)) : ''}}</td>
                                    </tr>
                                    <template v-for="item in head" :key="item.id+'_item'">
                                        <tr>
                                            <td><div class="ms-3"><Highlighter highlightClassName="bg-warning bg-gradient fst-italic px-2 rounded" :searchWords="[keyword]" :textToHighlight="item.project.title"/></div></td>
                                            <td class="text-end" v-for="col, key in columns" :key="item.project.title+col">{{setFund(item.funds, setYear(workshop.year, key))}}</td>
                                        </tr>
                                        <tr v-for="sub in item.subs" :key="sub.id+'_sub'">
                                            <td><div class="ms-4"><Highlighter highlightClassName="bg-warning bg-gradient fst-italic px-2 rounded" :searchWords="[keyword]" :textToHighlight="sub.subproject.title"/></div></td>
                                            <td class="text-end" v-for="col, key in columns" :key="sub.subproject.title+col">{{setFund(sub.funds, setYear(workshop.year, key))}}</td>
                                        </tr>
                                    </template>
                                    <tr class="fw-bold" style="background: lightgreen;" v-if="headKey.includes('Unit')">
                                        <td class="text-center">Sub-Total</td>
                                        <td class="text-end" v-for="col, key in columns" :key="headKey+col">{{setHeaderFunds(head, setYear(workshop.year, key))}}</td>
                                    </tr>
                                </template>
                                <tr class="fw-bold" v-if="divKey == 'STSD'" style="background: lightblue">
                                    <td class="py-3 text-center" :style="divKey != 'STSD' ? 'color: red' : ''">Total <Highlighter highlightClassName="bg-warning bg-gradient fst-italic px-2 rounded" :searchWords="[keyword]" :textToHighlight="sourceKey"/></td>
                                    <td class="py-3 text-end" v-for="col, key in columns" :key="sourceKey+col">{{setSourceFunds(source, setYear(workshop.year, key))}}</td>
                                </tr>
                                <tr class="fw-bold" v-if="divKey == 'STSD'" style="background: lightgreen">
                                    <td class="py-3 text-center">Less AC</td>
                                    <td class="py-3 text-end" v-for="col, key in columns" :key="sourceKey+col">{{setACFunds(setYear(workshop.year, key))}}</td>
                                </tr>
                            </template>
                        </template>
                    </template>
                </tbody>
            </table>
        </div>
        <h1 class="text-center p-5" v-else><i class="fas fa-spinner fa-spin"></i></h1>
        <div class="filter-container shadow-lg rounded overflow-auto" :class="filtershow">
            <button class="btn btn-danger btn-sm float-end" @click="filtershow = ''"><i class="fas fa-times"></i></button>
            <h4><strong>Filters</strong></h4><hr>
            <div class="form-check form-switch mb-1 pb-1 border-bottom" v-for="division in divisions" :key="division.id+'_division'">
                <input style="cursor: pointer" class="form-check-input shadow-none" type="checkbox" :id="division.name" :value="division.code" v-model="filters">
                <label style="cursor: pointer" class="form-check-label fw-bold text-center w-100" :for="division.name">{{division.name}}</label>
            </div>
        </div>
    </div>
</template>
<script>
import { mapGetters, mapActions } from 'vuex'
import Highlighter from 'vue-highlight-words'
export default {
    name: 'Display',
    components: { Highlighter },
    data(){
        return {
            columns: ['col1', 'col2', 'col3', 'col4', 'col5', 'col6', 'col7'],
            filters: [],
            loading: true,
            filtershow: '',
            keyword: ''
        }
    },
    methods: {
        ...mapActions('division', ['fetchDivisions']),
        setYear(year, key){
            return parseInt(year) + parseInt(key)
        },
        setFund(funds, year){
            var fund = funds.find(elem => elem.year == year)
            if(fund){
                return this.formatAmount(fund.amount)
            }
        },
        setHeaderFunds(items, year){
            var result = 0
            for(let i = 0; i < items.length; i++){
                var fund = items[i].funds.find(elem => elem.year == year)
                if(fund){
                    result = result + parseFloat(fund.amount)
                }
            }
            return this.formatAmount(result)
        },
        setSourceFunds(headers, year){
            var result = 0
            var keys = Object.keys(headers)
            for(let i = 0; i < keys.length; i++){
                var items = headers[keys[i]]
                for(let j = 0; j < items.length; j++){
                    var fund = items[j].funds.find(elem => elem.year == year)
                    if(fund){
                        result = result + parseFloat(fund.amount)
                    }
                }
            }
            return this.formatAmount(result)
        },
        setACFunds(year){
            var result = 0
            var keys = Object.keys(this.annexones)
            for(let i = 0; i < keys.length; i++){
                var divs = this.annexones[keys[i]]
                var divkeys = Object.keys(divs)
                for(let j = 0; j < divkeys.length; j++){
                    var sourcekey = divkeys[j]
                    if(sourcekey.includes('AC')){
                        var headers = divs[sourcekey]
                        var headerkey = Object.keys(headers)
                        for(let k = 0; k < headerkey.length; k++){
                            var items = headers[headerkey[k]]
                            for(let l = 0; l < items.length; l++){
                                var fund = items[l].funds.find(elem => elem.year == year)
                                if(fund){
                                    result = result + parseFloat(fund.amount)
                                }
                            }
                        }
                    }
                }
            }
            return this.formatAmount(result)
        },
        setDivFunds(sources, division, year){
            var result = 0
            var keys = Object.keys(sources)
            for(let i = 0; i < keys.length; i++){
                var headers = sources[keys[i]]
                var headerkeys = Object.keys(headers)
                for(let j = 0; j < headerkeys.length; j++){
                    var items = headers[headerkeys[j]]
                    for(let k = 0; k < items.length; k++){
                        var fund = items[k].funds.find(elem => elem.year == year)
                        if(fund){
                            result = result + parseFloat(fund.amount)
                        }
                    }
                }
            }
            if(division == 'STSD'){
                var acamount = this.setACFunds(year)
                result = result - parseFloat(acamount.replace(/,/g, ''))
            }

            return this.formatAmount(result)
        },
        formatAmount(amount){
            return (Math.round(amount * 100) / 100).toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 0 })
        },
        displaySearch(keyword){
            var display = this.getAnnexOnes
            if(keyword){
                var filteredData = {}
                var divcodes = Object.keys(display)
                for(let i = 0; i < divcodes.length; i++){
                    var divcode = divcodes[i]
                    var sources = display[divcode]
                    var sourcekeys = Object.keys(sources)
                    var tempSources = {}
                    for(let j =0; j < sourcekeys.length; j++){
                        var state = false
                        var source = sourcekeys[j]
                        var headers = sources[source]
                        var headerkeys = Object.keys(headers)
                        if(!state){
                            state = source.toLowerCase().includes(keyword)
                        }
                        if(state){
                            tempSources[source] = sources[source]
                        }
                        if(!state){
                            var tempHeaders = {}
                            for(let k = 0; k < headerkeys.length; k++){
                                var state = false
                                var header = headerkeys[k]
                                var items = headers[header]
                                if(!state){
                                    state = header.toLowerCase().includes(keyword)
                                }
                                if(state){
                                    tempHeaders[header] = headers[header]
                                }
                                if(!state){
                                    var tempItems = []
                                    for(let l = 0; l < items.length; l++){
                                        var state = false
                                        var item = items[l]
                                        if(!state){
                                            state = item.project.title.toLowerCase().includes(keyword)
                                        }
                                        if(!state){
                                            for(let m = 0; m < item.subs.length; m++){
                                                var sub = item.subs[m]
                                                if(!state){
                                                    state = sub.subproject.title.toLowerCase().includes(keyword)
                                                }
                                            }
                                        }
                                        if(state){
                                            tempItems.push(item)
                                        }
                                    }
                                    if(tempItems.length > 0){
                                        tempHeaders[header] = tempItems
                                    }
                                }
                            }
                            if(Object.keys(tempHeaders).length > 0){
                                tempSources[source] = tempHeaders
                            }
                        }
                    }
                    if(Object.keys(tempSources).length > 0){
                        filteredData[divcode] = tempSources
                    }
                }
                return filteredData
            }
            else{
                return display
            }
        },
        fillFilters(){
            this.filters = []
            for(let i = 0; i < this.divisions.length; i++){
                this.filters.push(this.divisions[i].code)
            }
            this.loading = false
        }
    },
    computed: {
        ...mapGetters('workshop', ['getWorkshop']),
        workshop(){ return this.getWorkshop },
        ...mapGetters('annexone', ['getAnnexOnes']),
        annexones(){ 
            var keyword = this.keyword.toLowerCase()
            return this.displaySearch(keyword)
        },
        ...mapGetters('division', ['getDivisions']),
        divisions(){ return this.getDivisions }
    },
    created(){
        if(this.divisions.length == 0){
            this.loading = true
            this.fetchDivisions().then(res => {
                this.fillFilters()
            })
        }
        else{
            this.fillFilters()
        }
    }
}
</script>
<style scoped>
th{
    text-align: center;
}
.filter-container{
    position: fixed;
    right: -350px;
    top: 20vh;
    width: 300px;
    height: 400px;
    background: rgba(255, 255, 255, 0.9);
    padding: 1rem;
    transition: all 0.3s;
}
.filter-container.show{
    right: 5vh;
}
</style>