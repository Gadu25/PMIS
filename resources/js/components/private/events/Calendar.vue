<template>
    <div>
        <button type="button" class="d-none" data-bs-toggle="modal" data-bs-target="#modal" ref="modal">
            Launch demo modal
        </button>
        <FullCalendar :options="calendarOptions"/>
        <!-- Modal -->
        <div class="modal fade" id="modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <h5 class="border-bottom pb-2">{{event.title}}</h5>
                    <p class="mb-0"><strong>Event: </strong>{{event.description}}</p>
                    <strong>Date: </strong>{{formatDate()}}
                    <div class="d-flex justify-content-end pt-2 border-top">
                        <button style="min-width: 100px" type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>

                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
</template>
<script>
import { mapGetters } from 'vuex'
import '@fullcalendar/core/vdom' // solves problem with Vite
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'
export default {
    name: 'Calendar',
    components: {
        FullCalendar // make the <FullCalendar> tag available
    },
    data() {
        return {
            calendarOptions: {
                plugins: [ dayGridPlugin, interactionPlugin ],
                initialView: 'dayGridMonth',
                events: [
                    // { title: 'event 1', start: '2022-02-27', end: '2022-02-29' },
                    // { title: 'event 2', date: '2022-10-02' }
                ],
                validRange: {
                    start: this.year+'-01-01',
                    end: this.year+'-12-31'
                },
                eventClick: this.eventclicked,
            },
            prevColor: '',
            title: '',
            description: '',
            event: {
                title: '',
                description: '',
                start: '',
                end: ''
            }
        }
    },
    methods: {
        eventclicked(e){
            this.event.title = e.event.title
            this.event.start = e.event.startStr
            this.event.end = e.event.endStr
            this.event.description = e.event.extendedProps.description

            this.$refs.modal.click()
        },
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
        formatDate(){
            var start = this.event.start
            var end = this.event.end
            var startArr = start.toString().split('-')
            var startInt = parseInt(startArr[2])
            var endArr   = end.toString().split('-')
            var endInt   = parseInt(endArr[2])

            var month = parseInt(startArr[1])
            month = this.monthName(month)
            var day  = startInt == endArr[2] ?
                startInt : startInt + '-' + endInt
            return month+', '+day+' '+startArr[0]
        },
        getRandomColor() {
            var letters = '0123456789ABCDEF';
            var color = '#';
            for (var i = 0; i < 6; i++) {
                color += letters[Math.floor(Math.random() * 16)];
            }
            this.prevColor = color
            return color;
        },
        formatEndDate(date){
            var array = date.toString().split('-')
            var intdate = parseInt(array[2])+1
            var strdate = intdate > 9 ? intdate : '0'+intdate
            return array[0]+'-'+array[1]+'-'+strdate
        },
        loadEvents(){
            for(let profile of this.profiles){
                var title = profile.title
                for(let milestone of profile.sorted){
                    var desc = milestone.title
                    for(let date of milestone.newdates){
                        this.calendarOptions.events.push({
                            title: title,
                            description: desc,
                            start: date.start,
                            end: this.formatEndDate(date.end),
                            backgroundColor: this.getRandomColor(),
                            borderColor: this.prevColor
                        })
                    }
                }
            }
        }
    },
    created(){
        this.loadEvents()
    },
    computed: {
        ...mapGetters('event', ['getEvents']),
        profiles(){ return this.getEvents }
    },
    props: {
        year: Number
    }
}
</script>