<template>
    <div class="px-2 py-2">
        <div class="form-floating mb-2 position-relative">
            <select class="form-control form-control-sm" id="program" style="width: 275px">
                <option value="" disabled hidden selected>Select Program</option>
                <option :value="program.id" v-for="program in programs" :key="program.id+'_program'">{{program.title}}</option>
            </select>
            <label for="program">Program</label>
            <button class="btn btn-sm btn-primary shadow-none position-absolute" style="top: 15px; left: 280px"><i class="far fa-sync-alt"></i></button>
        </div>
    </div>
</template>
<script>
import { mapActions, mapGetters } from 'vuex'
export default {
    name: 'Display',
    data(){
        return {

        }
    },
    methods: {

    },
    computed: {
        ...mapGetters('program', ['getPrograms']),
        programs(){ return this.getPrograms }
    }
}
</script>