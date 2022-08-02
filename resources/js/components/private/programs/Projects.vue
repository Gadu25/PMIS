<template>
    <div class="px-5 py-4">
        <router-link :to="{ name: 'Programs' }" class="btn btn-light btn-sm float-start"><i class="fas fa-arrow-left"></i></router-link>
        <h2 class="text-center"> Projects of {{$route.params.selected}}</h2><hr>
    </div>
</template>
<script>
export default {
    name: 'Projects'
}
</script>