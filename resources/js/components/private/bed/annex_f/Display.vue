<template>
    
    <table class="table table-sm table-bordered" :style="!printmode ? 'width: 2000px' : '' ">
        <TableHead :sticky="!printmode" :year="year" />
        <tbody v-if="annexfs.length == 0">
            <tr v-for="row in 10" :key="row+'row'">
                <td v-for="col in 19" :key="row+'row_'+col+'col'"><span class="text-white">-empty-</span></td>
            </tr>
        </tbody>
        <tbody v-else>
            <template v-for="program in annexfs" :key="'program-'+program.id">
                <tr v-if="program.items.length > 0 || program.show"> <td colspan="19" class="bg-success fw-bold text-white">{{program.title}}</td> </tr>
                <Items v-if="program.items.length > 0" :items="program.items" />
                <template v-for="subprogram in program.subprograms" :key="'subprogram-'+subprogram.id">
                    <tr v-if="subprogram.items.length > 0 || subprogram.show"> <td colspan="19" class="fw-bold">{{subprogram.title}}</td> </tr>
                    <Items v-if="subprogram.items.length > 0" :items="subprogram.items" />
                    <template v-for="cluster in subprogram.clusters" :key="'cluster-'+cluster.id">
                        <tr v-if="cluster.items.length > 0"> <td colspan="19" class="bg-info fw-bold"><span class="ms-2">{{cluster.title}}</span></td> </tr>
                        <Items v-if="cluster.items.length > 0" :items="cluster.items" />
                    </template>
                </template>
            </template>
        </tbody>
    </table>
</template>
<script>
import Items from './Items.vue'
import TableHead from './TableHead.vue'
import { mapGetters } from 'vuex'
export default {
    name: 'Display',
    components: { Items, TableHead },
    computed: {
        ...mapGetters('annexf', ['getAnnexFs']),
        annexfs(){ return this.getAnnexFs },
    },
    props: {
        printmode: Boolean,
        year: Number
    }
}
</script>
<style scoped>
table{
    font-size: 12px;
}
.funding{
    background: rgba(255, 166, 0, 0.15);;
}
.funding>.text{
    text-align: center;
    font-weight: bold;
}
.funding>.amount{
    text-align: right;
}
</style>