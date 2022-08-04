<template>
    <div class="px-5 py-4">
        <router-link :to="{ name: 'Workshops' }" class="btn btn-sm btn-light float-start"><i class="fas fa-arrow-left"></i></router-link>
        <h2 class="text-center">Annex One</h2>
        <small>Planning Workshop <span v-if="!loading">{{workshop.date}}</span><span v-else>Loading date <i class="fas fa-spinner fa-spin"></i></span></small><hr>
        <template v-if="!loading">
            <div class="d-flex justify-content-end">
                <div class="form-check form-switch">
                    <input style="cursor: pointer" class="form-check-input" type="checkbox" id="editmode" v-model="editmode">
                    <label style="cursor: pointer" class="form-check-label" for="editmode">Edit Mode</label>
                </div>
            </div>
            <Form v-if="editmode" />
            <Display v-else />
        </template>
        <h1 v-else class="text-center p-5"><i class="fas fa-spinner fa-spin fa-5x"></i></h1>
    </div>
</template>
<script>
import Display from './Display.vue'
import Form from './Form.vue'
import { mapGetters, mapActions } from 'vuex'
export default {
    name: 'AnnexOne',
    components: { Display, Form },
    data(){
        return {
            editmode: false,
            loading: true
        }
    },
    methods: {
        ...mapActions('workshop', ['fetchWorkshop']),
        ...mapActions('annexone', ['fetchAnnexOnes'])
    },
    computed: {
        ...mapGetters('workshop', ['getWorkshop']),
        workshop(){ return this.getWorkshop }
    },
    created(){
        this.fetchWorkshop(this.$route.params.workshopId).then(res => {
            this.fetchAnnexOnes(this.$route.params.workshopId).then(res => {
                this.loading = false
            })
        })
    }
}
</script>