<script setup>
const items = [
    { text: '首页', icon: 'mdi-home', to: '/' },
    { text: '留言', icon: 'mdi-comment-multiple', to: '/Messages' },
];

const subItems = [
    { text: '电脑壁纸', to: '/Picture/Desktop' },
    { text: '轻音图网', to: '/Picture/Space' },
    { text: '轻音表情包', to: '/Picture/Meme' },
    { text: 'AlphaCoders', to: '/Picture/AlphaCoders'  },
]
</script>

<template>
    <div class="AppNav">
        <router-link v-for="item in items" :key="item.text" :to="item.to">
            <v-btn :prepend-icon="item.icon" variant="text">{{ item.text }}</v-btn>
        </router-link>
        <v-menu open-on-hover>
            <template v-slot:activator="{ props }">
                <v-btn v-bind="props" prepend-icon="mdi-panorama-variant-outline" variant="text">
                    全部壁纸
                </v-btn>
            </template>

            <v-list>
                <v-list-item v-for="(item, index) in subItems" :key="index" :value="index">
                    <router-link :to="item.to">
                        <v-list-item-title>{{ item.text }}</v-list-item-title>
                    </router-link>
                </v-list-item>
            </v-list>
        </v-menu>
    </div>
</template>

<style scoped>
.AppNav {
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>