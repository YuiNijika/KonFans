<script setup>
import { DownOutlined } from '@ant-design/icons-vue';
const menus = [
    {
        title: '首页',
        to: '/',
    },
    {
        title: '轻音部相册',
        children: [
            {
                title: '表情包',
                to: '/Picture/Meme',
            },
            {
                title: '轻音图网',
                to: '/Picture/Space',
            },
            {
                title: 'AlphaCoders',
                to: '/Picture/AlphaCoders',
            },
            {
                title: '全部图片',
                to: '/Picture/All',
            },
        ]
    },
    {
        title: '留言',
        to: '/Messages',
    }
]
</script>

<template>
    <a-flex justify="space-between" align="center"
        style="box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.08); padding: 6px 30px 6px 30px;">
        <div>
            <a-space size="large">
                <RouterLink to="/">
                    <img src="/assets/logo.png" alt="logo" style="height:50px; margin-right: 20px;">
                </RouterLink>

                <template v-for="menu in menus" :key="menu.title">
                    <RouterLink v-if="!menu.children" :to="menu.to">
                        <a-button type="text">{{ menu.title }}</a-button>
                    </RouterLink>
                    <a-dropdown v-else>
                        <a-button type="text">
                            {{ menu.title }}
                            <DownOutlined />
                        </a-button>
                        <template #overlay>
                            <a-menu>
                                <a-menu-item v-for="child in menu.children" :key="child.title">
                                    <RouterLink :to="child.to">{{ child.title }}</RouterLink>
                                </a-menu-item>
                            </a-menu>
                        </template>
                    </a-dropdown>
                </template>
            </a-space>
        </div>

        <div>
            <a-space size="large">
                <a href="https://space.bilibili.com/435502585" target="_blank">
                    <a-flex vertical justify="space-between" align="center" class="header-hover-element">
                        <svg t="1745741745422" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="1484" width="22" height="22"><path d="M306.005333 117.632L444.330667 256h135.296l138.368-138.325333a42.666667 42.666667 0 1 1 60.373333 60.373333l-78.037333 77.952L789.333333 256A149.333333 149.333333 0 0 1 938.666667 405.333333v341.333334a149.333333 149.333333 0 0 1-149.333334 149.333333h-554.666666A149.333333 149.333333 0 0 1 85.333333 746.666667v-341.333334A149.333333 149.333333 0 0 1 234.666667 256h88.96L245.632 177.962667a42.666667 42.666667 0 0 1 60.373333-60.373334zM789.333333 341.333333h-554.666666a64 64 0 0 0-63.701334 57.856L170.666667 405.333333v341.333334a64 64 0 0 0 57.856 63.701333L234.666667 810.666667h554.666666a64 64 0 0 0 63.701334-57.813334L853.333333 746.666667v-341.333334A64 64 0 0 0 789.333333 341.333333zM341.333333 469.333333a42.666667 42.666667 0 0 1 42.666667 42.666667v85.333333a42.666667 42.666667 0 1 1-85.333333 0v-85.333333a42.666667 42.666667 0 0 1 42.666666-42.666667z m341.333334 0a42.666667 42.666667 0 0 1 42.666666 42.666667v85.333333a42.666667 42.666667 0 1 1-85.333333 0v-85.333333a42.666667 42.666667 0 0 1 42.666667-42.666667z" p-id="1485"></path></svg>
                        <a-typography-text type="secondary">哔哩哔哩</a-typography-text>
                    </a-flex>
                </a>
            </a-space>
        </div>
    </a-flex>
</template>

<style scoped>
</style>