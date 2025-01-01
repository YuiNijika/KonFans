<script setup>
import { useRouter } from 'vue-router';
import { ref } from 'vue';

const router = useRouter();
const debugMessage = ref('');

const menuItems = ref([
  { icon: 'home', text: '首页', route: '/' },
  { icon: 'rss_feed', text: '博客', route: '/Blog' },
  { icon: 'camera', text: '哔哩哔哩', url: 'https://space.bilibili.com/435502585' },
  { subheader: '壁纸' },
  { icon: 'child_care', text: '表情包', route: '/Meme' },
  { icon: 'extension', text: '全部壁纸', route: '/Wallpaper' },
  { icon: 'extension', text: '电脑壁纸', route: '/WallpaperPC' },
  { icon: 'extension', text: '手机壁纸', route: '/WallpaperMobile' },
  { icon: 'extension', text: '轻音图网壁纸', route: '/WallpaperSpace' },
  { subheader: '其他' },
  { icon: 'beenhere', text: 'OpenAPI', route: '/OpenAPI' },
  { icon: 'sms', text: '前往留言', url: 'https://messages.miomoe.cn/' },
  { icon: 'videocam', text: '观看轻音全集', url: 'https://messages.miomoe.cn/Video' }
]);

const navigate = (item) => {
  if (item.route) {
    debugMessage.value = `${item.text} clicked`;
    router.push(item.route);
  } else if (item.url) {
    window.open(item.url, '_blank');
  }
};
</script>

<template>
  <div id="Sidebar">
    <div id="drawer" class="mdui-drawer mdui-card" style="border-radius: 0px;">
      <img src="/logo.png" class="mdui-img-fluid">
      <div class="mdui-divider"></div>
      <ul class="mdui-list">
        <template v-for="(item, index) in menuItems" :key="index">
          <li v-if="item.subheader" class="mdui-subheader">{{ item.subheader }}</li>
          <a v-else-if="item.url" :href="item.url" target="_blank">
            <li class="mdui-list-item mdui-ripple">
              <i class="mdui-list-item-icon mdui-icon material-icons">{{ item.icon }}</i>
              <div class="mdui-list-item-content">{{ item.text }}</div>
            </li>
          </a>
          <a v-else @click="navigate(item)">
            <li class="mdui-list-item mdui-ripple">
              <i class="mdui-list-item-icon mdui-icon material-icons">{{ item.icon }}</i>
              <div class="mdui-list-item-content">{{ item.text }}</div>
            </li>
          </a>
        </template>
      </ul>
    </div>
  </div>
</template>
