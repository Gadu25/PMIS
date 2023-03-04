<template>
    <div class="modal fade" id="history" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{title}} Logs</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <button @click="tab = 'history'"  :class="tab == 'history'  ? 'btn-secondary' : 'btn-outline-secondary'" class="btn btn-sm shadow-none me-1"><i class="fas fa-history"></i> History</button>
                    <button @click="tab = 'timeline'" :class="tab == 'timeline' ? 'btn-secondary' : 'btn-outline-secondary'" class="btn btn-sm shadow-none"><i class="fas fa-stream"></i> Timeline</button>
                    <div class="log-container">
                        <div class="histories" v-if="tab == 'history'">
                            <div class="history" v-for="history in histories" :key="history.id+'_history'">
                                <div class="body" v-html="history.subject"></div>
                                <span v-if="history.comment">Comment: {{history.comment}}</span>
                                <div class="footer">
                                    <strong><small>By: {{history.profile.user.firstname}} {{history.profile.user.lastname}}</small></strong>
                                    <small>{{ moment( history.created_at ).fromNow() }}</small>
                                    <!-- <span>{{ history.created_at }}</span>
                                    <span>2023-03-3 11:00:00</span> -->
                                    <!-- <small>{{history.created_at}}</small> -->
                                </div>
                                <hr>
                            </div>
                        </div>
                        <div class="timelines" v-if="tab == 'timeline'">
                            <div class="history">
                                <span>Published at: {{ moment(projectCreated).format('MMMM Do YYYY, h:mm:ss a') }}</span>
                                <br>
                            </div>
                            <div class="history" v-for="(val,index) of histories"  :key="val.id+'_history'">
                                <div v-if="index != 0 && val.subject != histories[index-1].subject">
                                    <div class="body" v-html="val.subject"></div>
                                    <span>end date: {{ moment(projectCreated).format('MMMM Do YYYY, h:mm:ss a') }} </span>
                                    <hr>
                                </div>
                                
                                <!-- <span>val {{ val }}</span>
                                <br>
                                <span>same as {{ histories[index-1] }}</span> -->
                                <!-- <div v-if="index != 0">
                                    <v-if="va"
                                </div> -->
                                <!-- <br>
                                <span>index {{ index }}</span>
                                <br> -->
                                <!-- <span>status: {{ val.subject == histories[index] }}</span> -->
                                <!-- <span>created at: {{ val.created_at }}</span>
                                <br>
                                <span>same as {{ histories[index != 0 ? (index-1) : (index)].created_at }}</span> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import moment from 'moment';

export default {
    name: 'Logs',
    data(){
        return {
            moment: moment,
            tab: 'history',
            recent: ""
        }
    },
    props: {
        histories: Object,
        title: String,
        projectCreated: String
    },
    created: function(){
        this.myFunctionOnLoad()
    },
    methods:{
        myFunctionOnLoad(){
            // console.log("ang project id ay: ", this.projectId)
        }
    }
}
</script>
<style scoped>
.log-container{
    /* max-height: calc(100vh - 500px); */
    height: 500px;
    overflow: auto;
    padding: 0.5em;
    margin-top: 0.5em;
}
/* History */
.histories{
    border: 1px solid rgba(0,0,0,0.25);
    border-radius: 0.25em;
}
.history{
    padding: 0.5em;

}
.history>.footer{
    display: flex;
    justify-content: space-between;
}
/* Timeline */
.timelines{
    padding: 1em;
    border: 1px solid rgba(0,0,0,0.25);
    border-radius: 0.25em;
}
</style>