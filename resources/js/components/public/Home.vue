<template>
    <div class="home-container">
        <div class="card shadow">
            <div class="card-body">
                <div class="d-flex flex-wrap sm-flex-col">
                    <div class="p-2">
                        <div class="text-center"><img :src="'/images/Logo.png'" alt="Logo" width="200"></div>
                    </div>
                    <div class="p-2">
                        <h3 class="mb-3 text-start">Welcome to</h3>
                        <h1 class="text-start"><strong>DOST-SEI Project Management Information System</strong></h1>
                        <div class="d-flex justify-content-end mt-5">
                            <router-link class="btn btn-primary rounded-pill" style="min-width: 200px" :to="{name: 'Login'}">Sign In</router-link>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'Home',
}
</script>
<style scoped>
.home-container{
    background: linear-gradient(to bottom right, hsl(215, 100%, 60%) 0%, hsl(215, 100%, 80%) 100%);
    height: 100vh;
    padding: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>