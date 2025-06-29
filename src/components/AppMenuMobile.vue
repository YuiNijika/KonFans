<script setup>
import { ref, watch } from 'vue';
import { useRoute } from 'vue-router';
import { MenuOutlined } from '@ant-design/icons-vue';
import { Menu } from 'ant-design-vue';

const open = ref(false);
const route = useRoute();

const afterOpenChange = bool => {
    console.log('open', bool);
};

const showDrawer = () => {
    open.value = true;
};

const menus = [
    {
        title: '首页',
        to: '/',
    },
    {
        title: '留言',
        to: '/Messages',
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
];

const closeDrawer = () => {
    open.value = false;
};

// 监听路由变化，关闭对话框
watch(
    () => route.path,
    () => {
        closeDrawer();
    }
);
</script>

<template>
    <a-flex justify="space-between" align="center" style="box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.08); padding: 6px 30px 6px 30px;">
        <div>
            <a-space size="large">
                <MenuOutlined @click="showDrawer" />
            </a-space>
            <a-drawer v-model:open="open" class="custom-class" root-class-name="root-class-name" :root-style="{ color: 'blue' }" style="color: red" title="KonFans" placement="left" @after-open-change="afterOpenChange">
                <a-menu mode="inline" style="width: 100%;">
                    <template v-for="menu in menus" :key="menu.title">
                        <a-menu-item v-if="!menu.children">
                            <RouterLink :to="menu.to" class="menu-link">
                                {{ menu.title }}
                            </RouterLink>
                        </a-menu-item>
                        <a-sub-menu v-else :title="menu.title">
                            <a-menu-item v-for="child in menu.children" :key="child.title">
                                <RouterLink :to="child.to" class="menu-link">
                                    {{ child.title }}
                                </RouterLink>
                            </a-menu-item>
                        </a-sub-menu>
                    </template>
                </a-menu>
            </a-drawer>
        </div>

        <div style="margin: 0 auto;">
            <RouterLink to="/">
                <img src="/assets/logo.png" alt="logo" style="height:50px;">
            </RouterLink>
        </div>
    </a-flex>
</template>

<style scoped>
.menu-link {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100%;
    color: inherit;
    text-decoration: none;
}
</style>