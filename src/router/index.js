// src/router/index.js
import { createRouter, createWebHistory } from 'vue-router';
import Home from '../components/views/Home.vue';
import Meme from '../components/views/Meme.vue';
import Wallpaper from '../components/views/Wallpaper.vue';
import WallpaperPC from '../components/views/WallpaperPC.vue';
import WallpaperMobile from '../components/views/WallpaperMobile.vue';
import WallpaperSpace from '../components/views/WallpaperSpace.vue';
import OpenAPI from '../components/views/OpenAPI.vue';
import NotFound from '../components/views/NotFound.vue';

const routes = [
    {
        path: '/',
        name: 'Home',
        component: Home,
        meta: {
            title: '首页',
            meta: [
                {
                    name: 'keywords',
                    content: '首页, 关键词1, 关键词2'
                },
                {
                    name: 'description',
                    content: '这是首页的描述'
                }
            ]
        }
    },
    {
        path: '/Meme',
        name: 'Meme',
        component: Meme,
        meta: {
            title: '表情包',
            meta: [
                {
                    name: 'keywords',
                    content: '表情包, 关键词1, 关键词2'
                },
                {
                    name: 'description',
                    content: '这是表情包页面的描述'
                }
            ]
        }
    },
    {
        path: '/Wallpaper',
        name: 'Wallpaper',
        component: Wallpaper,
        meta: {
            title: '壁纸',
            meta: [
                {
                    name: 'keywords',
                    content: '壁纸, 关键词1, 关键词2'
                },
                {
                    name: 'description',
                    content: '这是壁纸页面的描述'
                }
            ]
        }
    },
    {
        path: '/WallpaperPC',
        name: 'WallpaperPC',
        component: WallpaperPC,
        meta: {
            title: 'PC壁纸',
            meta: [
                {
                    name: 'keywords',
                    content: 'PC壁纸, 关键词1, 关键词2'
                },
                {
                    name: 'description',
                    content: '这是PC壁纸页面的描述'
                }
            ]
        }
    },
    {
        path: '/WallpaperMobile',
        name: 'WallpaperMobile',
        component: WallpaperMobile,
        meta: {
            title: '手机壁纸',
            meta: [
                {
                    name: 'keywords',
                    content: '手机壁纸, 关键词1, 关键词2'
                },
                {
                    name: 'description',
                    content: '这是手机壁纸页面的描述'
                }
            ]
        }
    },
    {
        path: '/WallpaperSpace',
        name: 'WallpaperSpace',
        component: WallpaperSpace,
        meta: {
            title: '太空壁纸',
            meta: [
                {
                    name: 'keywords',
                    content: '太空壁纸, 关键词1, 关键词2'
                },
                {
                    name: 'description',
                    content: '这是太空壁纸页面的描述'
                }
            ]
        }
    },
    {
        path: '/OpenAPI',
        name: 'OpenAPI',
        component: OpenAPI,
        meta: {
            title: '开放API',
            meta: [
                {
                    name: 'keywords',
                    content: '开放API, 关键词1, 关键词2'
                },
                {
                    name: 'description',
                    content: '这是开放API页面的描述'
                }
            ]
        }
    },
    {
        path: '/:catchAll(.*)', // 捕获所有路径
        name: 'NotFound',
        component: NotFound,
        meta: {
            title: '404 - 页面未找到',
            meta: [
                {
                    name: 'keywords',
                    content: '404, 页面未找到'
                },
                {
                    name: 'description',
                    content: '您访问的页面不存在'
                }
            ]
        }
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;
