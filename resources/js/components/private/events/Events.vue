<template>
    <div class="p-2">
        <div class="text-center mb-2 pb-2 border-bottom">
            <h2>Events Management</h2>
        </div>
        <div class="events-container">
            <div class="events">
                <div class="card border-0 rounded-0 shadow" v-if="!showcalendar">
                    <div class="card-body">
                       <strong>DOST-SEI Indicative Calendar of Activities from {{monthName(filter.start) +' to '+monthName(filter.end)+', '+filter.year}}</strong> 
                        <div class="table-responsive" >
                            <table class="table table-sm table-bordered">
                                <thead class="text-center align-middle position-sticky top-0 bg-warning">
                                    <tr>
                                        <th style="width: 25%;">Project</th>
                                        <th style="width: 40%;">Brief Description</th>
                                        <th style="width: 20%;">Title of Event</th>
                                        <th style="width: 15%;">Schedule of Event</th>
                                    </tr>
                                </thead>
                                <tbody v-if="!syncing">
                                    <template v-for="profile in profiles" :key="profile.id">
                                        <tr>
                                            <td :rowspan="profile.sorted.length+1">{{profile.title}}</td>
                                            <td :rowspan="profile.sorted.length+1"><span id="description">{{profile.project.description}}</span></td>
                                        </tr>
                                        <tr v-for="event in profile.sorted" :key="event.id">
                                            <td>{{event.title}}</td>
                                            <td>
                                                <template v-for="date in event.newdates" :key="date.id">
                                                    <div v-if="date.type == 'Milestone'">
                                                        {{formatDate(date)}}
                                                    </div>
                                                </template>
                                            </td>
                                        </tr>
                                    </template>
                                    <tr v-if="profiles.length == 0">
                                        <td colspan="4" class="text-center p-5">
                                            <h1>No Events found</h1>
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody v-if="syncing">
                                    <tr>
                                        <td colspan="4" class="text-center p-5">
                                            <h1><i class="far fa-sync fa-spin"></i> Syncing</h1>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div v-else>
                    <Calendar :year="this.filter.year" />
                </div>
            </div>
            <div class="controls">
                <div class="form-floating mb-2">
                    <select class="form-select" id="floatingSelect" v-model="filter.start">
                        <option :value="month" v-for="month in 12" :key="'start'+month">{{monthName(month)}}</option>
                    </select>
                    <label for="floatingSelect">Start</label>
                </div>
                <div class="form-floating mb-2">
                    <select class="form-select" id="floatingSelect" v-model="filter.end">
                        <option :value="month" v-for="month in 12" :key="'end'+month">{{monthName(month)}}</option>
                    </select>
                    <label for="floatingSelect">End</label>
                </div>
                <div class="form-floating mb-2">
                    <select class="form-select" id="floatingSelect" v-model="filter.year">
                        <option :value="year+1950" v-for="year in 200" :key="year">{{year+1950}}</option>
                    </select>
                    <label for="floatingSelect">Year</label>
                </div>
                <div class="d-flex justify-content-end mb-2 pb-2 border-bottom">
                    <button class="btn btn-sm btn-primary bg-gradient shadow-none" @click="syncRecords()"><i class="far fa-sync" :class="syncing ? 'fa-spin' : ''"></i> Sync</button>
                </div>
                <div class="form-floating pb-2 border-bottom mb-3">
                    <input type="search" class="form-control" id="floatingInput" placeholder=" ">
                    <label for="floatingInput"><i class="far fa-search"></i> Search</label>
                </div>
                <button @click="showcalendar = !showcalendar" :class="showcalendar ? 'btn-outline-secondary' : 'btn-outline-success'" class="btn btn-sm shadow-none bg-gradient w-100 mb-3"><i class="far fa-lg me-1" :class="showcalendar ? 'fa-calendar-times' : 'fa-calendar-alt'"></i> {{showcalendar? 'Hide' : 'Show'}} Calendar</button>
            </div>
        </div>
    </div>
</template>
<script>
import { mapActions, mapGetters } from 'vuex'
import Calendar from './Calendar.vue'
export default {
    name: 'Events',
    components: { Calendar },
    data(){
        return {
            filter: {
                start: 1,
                end: 12,
                year: ''
            },
            syncing: true,
            showcalendar: false
        }
    },
    methods: {
        ...mapActions('event', ['fetchEvents']),
        monthName(month){
            if(month == 1){  return 'January' }
            if(month == 2){  return 'February' }
            if(month == 3){  return 'March' }
            if(month == 4){  return 'April' }
            if(month == 5){  return 'May' }
            if(month == 6){  return 'June' }
            if(month == 7){  return 'July' }
            if(month == 8){  return 'August' }
            if(month == 9){  return 'September' }
            if(month == 10){ return 'October' }
            if(month == 11){ return 'November' }
            if(month == 12){ return 'December' }
        },
        formatDate(month){
            // var year = this.filter.year
            var startArr = month.start.toString().split('-')
            var startInt = parseInt(startArr[2])
            var endArr   = month.end.toString().split('-')
            var endInt   = parseInt(endArr[2])
            var month = this.monthName(month.month)
            var day  = startInt == endArr[2] ?
                startInt : startInt + '-' + endInt
            return month+' '+day
        },
        syncRecords(){
            var filter = this.filter
            if(filter.start > filter.end){
                this.toastMsg('warning', 'Start month must be before End month')
                return false
            }
            this.syncing = true
            this.fetchEvents(filter).then(res => {
                this.syncing = false
            })
        },
        toastMsg(icon, msg){
            toast.fire({
                icon: icon,
                title: msg
            })
        },
    },
    computed: {
        ...mapGetters('event', ['getEvents']),
        profiles(){ return this.getEvents }
    },
    created(){
        var date = new Date
        this.filter.year = date.getFullYear()
        this.fetchEvents(this.filter).then(res => {
            this.syncing = false
        })
    }
}
</script>
<style scoped>
.events-container{
    display: flex;
}
.events{
    width: calc(100vw - 220px);
    padding: 0 10px;
}
.events>.card{
    height: calc(100vh - 140px);
}
.table-responsive{
    height: calc(100vh - 200px);
    overflow: auto;
}
.controls{
    width: 200px;
    padding: 0 10px;
}

@media only screen and (max-width: 600px) {
    .events-container{
        flex-wrap: wrap-reverse;
    }
    .events{
        width: 100%;
    }
    .table-responsive{
        height: calc(100vh - 220px);
        overflow: auto;
    }
    .controls{
        width: 100%;
        display: flex;
        flex-direction: column;
    }
}
</style>