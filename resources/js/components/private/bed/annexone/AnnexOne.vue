<template>
    <div class="px-3 py-4">
        <button class="btn btn-sm btn-light float-start" @click="this.$router.push('/budget-executive-documents')"><i class="fas fa-arrow-left"></i> Back</button>
        <h2 class="text-center">Annex 1</h2>
        <!-- <small>Planning Workshop <span v-if="!loading">{{workshop.date}}</span><span v-else>Loading date <i class="fas fa-spinner fa-spin"></i></span></small> --><hr>
        <template v-if="!loading">
            <div class="d-flex justify-content-end mb-2">
                <button v-if="!formshow" class="btn btn-sm bg-gradient shadow-none" @click="editmode = !editmode" :class="editmode ? 'btn-primary' : 'btn-success'">{{!editmode ? 'Edit' : 'View'}} mode</button>
                <!-- <div class="form-check form-switch">
                    <input style="cursor: pointer" class="form-check-input" type="checkbox" id="editmode" v-model="editmode">
                    <label style="cursor: pointer" class="form-check-label" for="editmode">Edit Mode</label>
                </div> -->
            </div>
            <!-- <Form v-if="editmode" /> -->
            <Display @clicked="childClicked()" :editmode="editmode" />
        </template>
        <h1 v-else class="text-center p-5"><i class="fas fa-spinner fa-spin fa-5x"></i></h1>
    </div>
</template>
<script>
import Display from './new/Display.vue'
// import Form from './new/Form.vue'
import { mapGetters, mapActions } from 'vuex'
export default {
    name: 'AnnexOne',
    // components: { Display, Form },
    components: { Display },
    data(){
        return {
            editmode: false,
            loading: true,
            formshow: false
        }
    },
    methods: {
        ...mapActions('workshop', ['fetchWorkshop']),
        ...mapActions('annexone', ['fetchAnnexOnes']),
        childClicked(){
            this.formshow = !this.formshow
        }
    },
    computed: {
        ...mapGetters('workshop', ['getWorkshop']),
        workshop(){ return this.getWorkshop }
    },
    created(){
        this.fetchAnnexOnes(this.$route.params.workshopId).then(res => {
            this.fetchWorkshop(this.$route.params.workshopId).then(res => {
                this.loading = false
            })
        })
    }
}
</script>