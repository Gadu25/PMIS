<template>
    <div>
        Dashboard
        <pre>
            {{getUser}}
        </pre>
        <button @click="logout">Logout</button>
    </div>
</template>
<script>
import { mapActions, mapGetters } from 'vuex'
export default {
    name: 'Dashboard',
    methods: {
        ...mapActions('user', ['logout'])
    },
    computed: {
        ...mapGetters('user', ['getUser'])
    }
}
</script>
<style scoped>

</style>