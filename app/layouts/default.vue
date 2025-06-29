<script setup>
const route = useRoute()
</script>
<template>
    <Navbar />
    <main class="min-h-screen bg-base-200 p-10">
        <slot />
    </main>
    <AppFooter />
</template>