<template>
    <div class="px-5 py-4">
        <button @click="moveBack()" class="btn btn-light btn-sm float-start"><i class="fas fa-arrow-left"></i> Back</button>
        <h2 class="text-center"> Projects of {{$route.params.selected}}</h2><hr>
    </div>
</template>
<script>
export default {
    name: 'Projects',
    data(){
        return {

        }
    },
    methods: {

        moveBack(){
            this.$emit('clicked', true)
            this.$router.back()
        }
    }
}
</script>