<template>
    <div class="row flex-wrap-reverse">
        <div class="position-relative" :class="toggle ? 'col-sm-9' : 'col-sm-12'">
            <button v-if="!toggle" @click="toggle = !toggle" style="z-index: 99" class="btn btn-sm btn-secondary bg-gradient position-absolute end-0"><i class="fas fa-arrow-left"></i></button>
            <div v-if="!editmode" class="card border-0 shadow rounded-0">
                <div class="card-body">
                    <div :style="'font-size:' + fontsize" class="d-flex justify-content-end fw-bold">Annex 1</div>
                    <div class="text-center mb-3">
                        <h6 :style="'font-size:' + fontsize" class="mb-0 fw-bold">SEI Annual Planning Workshop</h6>
                        <h6 :style="'font-size:' + fontsize" class="fw-bold">{{workshop.date}}</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered" :style="'font-size:' + fontsize">
                            <TableHead />
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
import Form from './Form.vue'
import TableHead from './TableHead.vue'
export default {
    name: 'Display',
    emits: ['clicked'],
    setup({emit}){},
    components: { Form, TableHead },
    data(){
        return {
            fontsize: '12px',
            toggle: true,
            formshow: false,
        }
    },
    methods: {
        ...mapActions('division', ['fetchDivisions']),
        childClicked(){
            this.formshow = !this.formshow
            this.childClick()
        },
        childClick(){
            this.$emit('clicked')
        },
    },
    computed: {
        ...mapGetters('workshop', ['getWorkshop']),
        ...mapGetters('division', ['getDivisions']),
        workshop(){ return this.getWorkshop },
        divisions(){ return this.getDivisions },
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