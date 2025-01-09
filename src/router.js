import { createRouter, createWebHistory } from 'vue-router';
import Home from './components/views/Home.vue';
import Meme from './components/views/Meme.vue';
import Wallpaper from './components/views/Wallpaper.vue';
import WallpaperPC from './components/views/WallpaperPC.vue';
import WallpaperMobile from './components/views/WallpaperMobile.vue';
import WallpaperSpace from './components/views/WallpaperSpace.vue';
import OpenAPI from './components/views/OpenAPI.vue';
import NotFound from './components/views/NotFound.vue';

const routes = [
    {
        path: '/',
        name: 'Home',
        component: Home
    },
    {
        path: '/Meme',
        name: 'Meme',
        component: Meme
    },
    {
        path: '/Wallpaper',
        name: 'Wallpaper',
        component: Wallpaper
    },
    {
        path: '/Wallpaper/Computer',
        name: 'WallpaperPC',
        component: WallpaperPC
    },
    {
        path: '/Wallpaper/Mobile',
        name: 'WallpaperMobile',
        component: WallpaperMobile
    },
    {
        path: '/Wallpaper/Space',
        name: 'WallpaperSpace',
        component: WallpaperSpace
    },
    {
        path: '/OpenAPI',
        name: 'OpenAPI',
        component: OpenAPI
    },
    {
        path: '/:catchAll(.*)', // 捕获所有路径
        name: 'NotFound',
        component: NotFound
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;