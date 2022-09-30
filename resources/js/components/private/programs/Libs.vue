<template>
    <div v-if="!formshow">
        <button class="btn btn-sm btn-outline-secondary float-start" @click="childClick()"><i class="fas fa-arrow-left"></i></button>
        <button class="btn btn-sm btn-success bg-gradient float-end" @click="formshow = true, editmode = false"><i class="fas fa-plus"></i></button>
        <h5 class="text-center pb-3 mb-2 border-bottom ">Line-Item Budgets and Project Proposals</h5>
        <div class="row flex-wrap p-2">
            <div class="col-sm-4 mb-3" v-for="year in 5" :key="year">
                <div class="card shadow border-0" style="cursor: pointer;">
                    <div class="card-body text-center">
                        <i id="close" class="far fa-folder fa-5x"></i>
                        <i id="open" class="far fa-folder-open fa-5x"></i>
                        <br>
                        <strong>{{year+2019}}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <Form :editmode="editmode" @clicked="formshow = false" v-else />
</template>
<script>
import Form from './libs/Form.vue'
export default {
    name: 'Libs',
    emits: ['clicked'],
    setup({emit}){},
    components: {
        Form
    },
    data(){
        return {
            formshow: false,
            editmode: false
        }
    },
    methods: {
        childClick(){
            this.$emit('clicked')
        },
    }
}
</script>
<style scoped>
.card:hover>div>i#close{
    display: none;
}
.card>div>i#open{

    display: none;
}
.card:hover>div>i#open{
    display: inline;
}
</style>