<script setup>

</script>

<template>
    <v-app-bar scroll-behavior="hide" app>
        
        <v-app-bar-title>
            <router-link to="/">KonFans</router-link>
        </v-app-bar-title>

        <template v-slot:append>
            <a href="https://github.com/ShuShuicu/KonFans" target="_blank">
                <v-tooltip activator="parent" location="start">
                    点个Star叭!</v-tooltip>
                <v-btn icon="mdi-star"></v-btn>
            </a>
        </template>
    </v-app-bar>
</template>