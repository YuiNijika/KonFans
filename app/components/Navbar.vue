<script setup>
const items = [
    { name: '首页', path: '/' },
    { name: '留言', path: '/Message' },
    { name: '入部', path: '/Member' },
    { name: '壁纸', path: '/Wallpaper' },
]
</script>

<template>
    <div class="navbar bg-base-100 shadow-sm">
        <div class="flex-1">
            <NuxtLink to="/" class="btn btn-ghost text-xl">KonFans</NuxtLink>
        </div>
        <div class="flex-none">
            <ul class="menu menu-horizontal px-1">
                <li v-for="item in items" :key="item.name">
                    <NuxtLink :to="item.path" class="btn btn-ghost">{{ item.name }}</NuxtLink>
                </li>
            </ul>
        </div>
    </div>
</template>