<template>
    <div class="p-2 profile-container" v-if="!loading">
        <template v-if="!formshow && !libformshow">
            <div class="mb-2 pb-2 border-bottom">
                <button class="btn btn-sm btn-outline-secondary float-start" @click="childClick"><i class="fas fa-arrow-left"></i></button>
                <h4 class="text-center">{{profile.year}} Profile</h4>
            </div>
            <div class="content-container">
                <div class="content" :class="exportmode ? 'print' : ''">
                    <!-- <div class="d-flex justify-content-end mb-2">
                        <button class="btn btn-sm btn-primary"><i class="far fa-pencil-alt"></i></button>
                    </div> -->
                    <div class="card rounded-0 border-0 " :class="!exportmode ? 'shadow' : ''" id="printMe">
                        <div class="card-body">
                            <h5 class="text-center text-uppercase">
                                {{tab == 'lib' ? 'Line-Item Budget' : 'Project Proposal'}} <br>
                                <small v-if="tab == 'lib'">(LIB)</small>
                            </h5>

                            <div class="common-details" :class="tab">
                                <div class="item" v-if="tab == 'lib'">
                                    <div class="title">Project No.: </div>
                                    <div class="body"></div>
                                </div>
                                <div class="item">
                                    <div class="title">Project Title: </div>
                                    <div class="body">{{profile.title}}</div>
                                </div>
                                <div class="item">
                                    <div class="title">Proponents: </div>
                                    <div class="body">
                                        <div v-for="proponent in profile.proponents" :key="proponent.id">
                                            {{proponent.name}}
                                        </div>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="title">Project Duration: </div>
                                    <div class="body">{{monthName(profile.start) + ' to ' + monthName(profile.end) + ', ' +profile.year }}</div>
                                </div>
                                <div class="item">
                                    <div class="title">Proposed Budget: </div>
                                    <div class="body">{{getProposedBudget()}}</div>
                                </div>
                            </div>
                            <template v-if="tab == 'proposal'">
                                <template v-for="content in profile.proposals" :key="content.id">
                                    <span>{{setTexts(content)}}</span>
                                    <div class="proposal-content"  v-if="content.title != 'Budgetary Requirements' || content.title != 'Gantt Chart'">
                                        <strong class="text-uppercase">{{content.title}}</strong><br>
                                        <span v-html="content.text"></span>
                                    </div>
                                </template>
                                <strong class="text-uppercase">Budgetary Requirements: </strong>
                                <span v-html="budgetaryrequirements"></span>
                            </template>
                            <Budget :libs="profile.libs" />
                            <div class="py-3" v-if="tab == 'lib'">
                                <p class="m-0">Chargeable Against {{getSOF()}}</p>
                                <p>Certified Funds Available:</p>
                            </div>
                            <div id="pagebreak"></div>
                            <div class="mt-4" v-if="tab == 'proposal'">
                                <strong class="text-uppercase">Schedule of Activities</strong>
                                <span v-html="ganttchart"></span>
                                <div class="table-responsive my-3">
                                    <table class="table table-sm table-bordered">
                                        <thead class="text-center">
                                            <tr>
                                                <th>Activities</th>
                                                <template v-for="month in 12" :key="month">
                                                    <th v-if="monthInImplementation(month)">{{monthName(month).substring(0, 3)}}</th>
                                                </template>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="activity in profile.activities" :key="activity.id">
                                                <td>{{activity.title}}</td>
                                                <template v-for="month in 12" :key="month">
                                                    <td v-if="monthInImplementation(month)" :class="setActivityCell(month, activity.months)"></td>
                                                </template>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="side-btns">
                    <strong>Profile Tabs</strong><br>
                    <button @click="tab = 'proposal'" :class="tab == 'proposal' ? 'btn-secondary' : 'btn-outline-secondary'" class="bg-gradient btn border-0 shadow mb-2">Proposal</button>
                    <button @click="tab = 'lib'"      :class="tab == 'lib'      ? 'btn-secondary' : 'btn-outline-secondary'" class="bg-gradient btn border-0 shadow mb-2">Line-Item Budget</button>
                    <strong>Controls</strong>
                    <template v-if="tab == 'lib'">
                        <button @click="libformshow = true, editmode = true" class="btn border-0 shadow mb-2" :class="isDraft() ? 'btn-primary' : 'btn-secondary'"><i class="far" :class="isDraft() ? 'fa-pencil-alt' : 'fa-eye'"></i> Line-Item</button>
                        <button @click="libformshow = true, editmode = false" class="btn btn-success border-0 shadow mb-2" v-if="latestLib.status == 'Approved'"><i class="far fa-plus"></i> Realignment</button>
                        <button data-bs-toggle="modal" data-bs-target="#modal" class="btn btn-warning border-0 shadow mb-2"><i class="far fa-history"></i> Logs</button>
                    </template>
                    <template v-else>
                        <button @click="formshow = true" class="btn btn-primary border-0 shadow mb-2"><i class="far fa-pencil-alt"></i> Proposal</button>
                    </template>
                    <button class="btn border-0 shadow mb-2" @click="exportmode = !exportmode" :class="exportmode ? 'btn-secondary' : 'btn-outline-secondary'">{{exportmode ? 'Exit' : ''}} Export</button>
                    <template v-if="exportmode">
                        <button class="btn btn-outline-secondary border-0 shadow mb-2" v-print="'#printMe'"><i class="far fa-print"></i> Print / <i class="fas fa-download"></i> PDF</button>
                        <button class="btn btn-success border-0 shadow mb-2"><i class="fas fa-file-excel"></i> Excel</button>
                    </template><br>
                    <strong>Gen Info</strong><br>
                    <div class="card shadow border-0 ms-3 mb-2">
                        <div class="card-body text-start"> 
                            <p><small><strong>Profile Status:</strong></small> <span class="badge" :class="profile.status == 'New' ? 'bg-primary' :
                                                                profile.status == 'Continuing' ? 'bg-warning' :
                                                                profile.status == 'Completed' ? 'bg-success' :
                                                                profile.status == 'Terminated' ? 'bg-danger' : 'bg-secondary'">{{profile.status}}</span> </p>
                           <small> <strong>Compliance with Law:</strong> <li  v-for="law in profile.laws" :key="law.id">{{law.name}}</li></small>
                        </div>
                    </div> 
                </div>
            </div>
        </template>
        <Form :editmode="editmode" :profile="profile" @clicked="formshow = false" v-if="formshow" />
        <LibForm :editmode="editmode" :libs="profile.libs" @clicked="libformshow = false" v-if="libformshow" />
        <!-- Modal -->
        <div class="modal fade" id="modal" tabindex="-1">
            <div class="modal-dialog modal-dailog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{profile.title}} Logs</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body overflow-auto" style="max-height: calc(100vh - 300px)">
                        <div class="border-bottom pb-2 mb-3" v-for="lib, key in profile.libs" :key="lib.id">
                            <strong>
                                {{key == 0 ? (lib.status == 'Approved' ? 'As Approved' : 'LIB Status: ' + lib.status) : key + getOrdinalSuffix(key) + ' Amendment'}} 
                                <span v-if="lib.date_approved">({{lib.date_approved}})</span>
                            </strong>
                            <div class=" p-2 mb-3 rounded shadow" v-for="history in lib.histories" :key="history.id">
                                <span v-html="history.subject"></span>
                                <span v-if="history.comment != null">Comment: {{history.comment}}</span><hr>
                                <div class="d-flex justify-content-between">
                                    <small><strong>By: {{history.profile.user.firstname}} {{history.profile.user.lastname}}</strong></small>
                                    <small>{{history.created_at}}</small>
                                </div>
                                <!-- {{history}} -->
                            </div>
                            <div v-if="lib.histories.length == 0" class="text-center p-5">
                                No Logs found
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div v-else class="p-5 m-5 text-center">
        <h1 class="mb-3">Loading Project Profile </h1>
        <i class="fas fa-spinner fa-spin fa-5x"></i>
    </div>
</template>
<script>
import moment from 'moment'
import LibForm from './LibForm.vue'
import Form from './Form.vue'
import Budget from './Budget.vue'
import { mapActions, mapGetters } from 'vuex'
export default {
    name: 'Profile',
    emits: ['clicked'],
    setup({emit}){},
    components: {Budget, Form, LibForm},
    data(){
        return {
            tab: 'proposal',
            loading: true,
            formshow: false,
            libformshow: false,
            editmode: true,
            exportmode: false,
            latestLib: [],
            budgetaryrequirements: '',
            ganttchart: ''
        }
    },
    methods: {
        ...mapActions('project', ['fetchProfile']),
        childClick(){
            this.$emit('clicked')
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
        monthInImplementation(month){
            var start = this.profile.start
            var end = this.profile.end
            return (month >= start && month <= end)
        },
        setActivityCell(month, months){
            var act = months.find(elem => elem.month == month)
            if(!act){
                return ''
            }
            return act.type
        },
        getProposedBudget(){
            var total = 0
            var libs = this.profile.libs
            var key = libs.length - 1
            var lib = libs[key]
            for(let type of lib.types){
                for(let item of type.items){
                    total = total + parseFloat(item.amount)
                }
            }
            return '??? '+this.formatNumber(total)
        },
        getSOF(){
            var lib = this.profile.libs[0]
            return lib.source_of_funds
            console.log(lib)
            // console.log(this.profile.libs)
        },
        setLatest(){
            var length = this.profile.libs.length
            var key = length - 1
            this.latestLib = this.profile.libs[key]
        },
        isDraft(){
            return this.latestLib.status == 'Draft'
        },
        setTexts(content){
            if(content.title == 'Budgetary Requirements'){
                this.budgetaryrequirements = content.text
            } 
            if(content.title == 'Gantt Chart'){
                this.ganttchart = content.text
            }
        },
        getOrdinalSuffix(num){
            var array = ("" + num).split("").reverse(); // E.g. 123 = array("3","2","1")
    
            if (array[1] != "1") { // Number is not in the teens
                switch (array[0]) {
                    case "1": return "st";
                    case "2": return "nd";
                    case "3": return "rd";
                }
            }
            
            return "th";
        },
        formatNumber(num){
            return (Math.round(num * 100) / 100).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2 })
        },
    },
    props: {
        profileId: Number
    },
    computed: {
        ...mapGetters('project', ['getProfile']),
        profile(){ return this.getProfile }
    },
    created(){
        this.fetchProfile(this.profileId).then(res => {
            this.setLatest()
            this.loading = false
        })
    }
}
</script>
<style scoped>
.profile-container{
    height: calc(100vh - 105px);
}
.content-container{
    max-height: calc(100vh - 175px);
    overflow: auto;
    padding: 10px;
    display: flex;
    /* flex-direction: row; */
}
.content{
    width: calc(100% - 100px);
}
.content>.card{
    height: calc(100vh - 190px);
    overflow: auto;
}
.content.print>.card{
    min-height: 100vh !important;
    overflow: hidden;
}
.side-btns{
    width: 200px;
    text-align: center;
}

.side-btns>.card,
.side-btns>button{
    width: 150px;
}

.side-btns>button:focus,
.side-btns>button:hover{
    transform: scale(1.05);
}

.common-details.lib{
    padding-bottom: 6px;
    border-bottom: 3px solid black;
}
.common-details>.item{
    display: flex;
    margin-bottom: 0.75em;
}
.common-details>.item>.title{
    width: 30%;
    font-weight: bold;
    text-transform: uppercase;
}
.common-details>.item>.body{
    width: 70%;
}
td.Regular{
    background:#0d6efd;
}
td.Milestone{
    background: #32CD32
}
#pagebreak{
    page-break-after: always;
}
@media only screen and (max-width: 600px) {
    .content-container{
        flex-wrap: wrap-reverse;
    }
    .content{
        width: 100%;
    }
    .content>.card{
        height: calc(100vh - 290px)
    }
    .side-btns{
        width: 100%;
        display: flex;
        flex-direction: column;
    }
    .side-btns>.card,
    .side-btns>button{
        width: 100%;
    }
    .common-details>.item>.title,
    .common-details>.item>.body{
        width: 50%;
        margin-bottom: 6px;
    }
}
</style>