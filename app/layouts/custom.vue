<script setup>
const route = useRoute()
</script>
<template>
    <main class="mockup-browser border border-base-300 w-full min-h-screen">
        <div class="mockup-browser-toolbar">
            <div class="input">https://www.miomoe.cn{{ route.path }}</div>
        </div>
        <div>
            <slot />
        </div>
    </main>
    <AppFooter />
</template>