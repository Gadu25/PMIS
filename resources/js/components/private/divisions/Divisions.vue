<template>
    <div class="px-5 py-4" v-if="!loading">
        <h2 class="text-center">Divisions and Units</h2><hr>
        <div class="accordion shadow-lg" id="accordionExample">
            <div class="accordion-item" v-for="division in divisions" :key="division.id+'_div'">
                <h2 class="accordion-header" id="headingOne">
                <button class="accordion-button shadow-none collapsed" type="button" data-bs-toggle="collapse" :data-bs-target="'#'+division.code" expanded="false" aria-controls="collapseOne">
                    <strong>{{division.name}}</strong>
                </button>
                </h2>
                <div :id="division.code" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                <div class="accordion-body">
                    <li v-for="role in division.roles" :key="role.id+'_divrole'">{{role.body}}</li>
                    <div class="ms-2 mt-2" v-for="unit in division.units" :key="unit.id+'_unit'">
                        <h5><strong>{{unit.name}}</strong></h5>
                        <li v-for="role in unit.roles" :key="role.id+'_unitrole'">{{role.body}}</li>
                        <div class="ms-2 mt-2" v-for="subunit in unit.subunits" :key="subunit.id+'_subunit'">
                            <h5>{{subunit.name}}</h5>
                            <li v-for="role in subunit.roles" :key="role.id+'_subunitrole'">{{role.body}}</li>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
    <h1 class="text-center p-5" v-else><i class="fas fa-spinner fa-spin fa-5x"></i></h1>
</template>
<script>
import { mapActions, mapGetters } from 'vuex'
export default {
    name: 'Divisions',
    data(){
        return {
            loading: true
        }
    },
    methods: {
        ...mapActions('division', ['fetchDivisions'])
    },
    computed: {
        ...mapGetters('division', ['getDivisions']),
        divisions(){ return this.getDivisions }
    },
    created(){
        this.fetchDivisions().then(res => {
            this.loading = false
        })
    }
}
</script>
<style scoped>
.accordion{
    max-height: calc(100vh - 165px);
    overflow: auto;
}
/* .accordion-body{ */
    /* max-height: 50vh; */
    /* overflow: auto; */
/* } */
</style>