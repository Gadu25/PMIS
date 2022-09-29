<template>
    <div v-if="!formshow">
        <button class="btn btn-sm btn-outline-secondary float-start" @click="childClick()"><i class="fas fa-arrow-left"></i></button>
        <button class="btn btn-sm btn-success bg-gradient float-end" @click="formshow = true, editmode = false"><i class="fas fa-plus"></i></button>
        <h5 class="text-center pb-3 mb-2 border-bottom ">Line-Item Budgets and Project Proposals</h5>
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