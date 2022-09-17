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
                                <div>Comment: {{history.comment}}</div>
                                <div class="footer">
                                    <strong><small>By: {{history.profile.user.firstname}} {{history.profile.user.lastname}}</small></strong>
                                    <small>{{history.created_at}}</small>
                                </div>
                            </div>
                        </div>
                        <div class="timelines" v-if="tab == 'timeline'">
                            Coming soon...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'Logs',
    data(){
        return {
            tab: 'history'
        }
    },
    props: {
        histories: Object,
        title: String
    }
}
</script>
<style scoped>
.log-container{
    max-height: calc(100vh - 500px);
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
    border-bottom: 1px solid rgba(0,0,0,0.25);

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