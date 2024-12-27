<script setup>
import {
  useRouter
} from 'vue-router';
import {
  ref
} from 'vue';

const router = useRouter();
const debugMessage = ref('');

const GoIndex = () => {
  debugMessage.value = 'GoIndex clicked';
  router.push('/');
};

const GoWallpaper = () => {
  debugMessage.value = 'GoWallpaper clicked';
  router.push('/Wallpaper');
};

const GoWallpaperPC = () => {
  debugMessage.value = 'WallpaperPC clicked';
  router.push('/WallpaperPC');
};

const GoWallpaperMobile = () => {
  debugMessage.value = 'WallpaperMobile clicked';
  router.push('/WallpaperMobile');
};

const GoWallpaperSpace = () => {
  debugMessage.value = 'WallpaperSpace clicked';
  router.push('/WallpaperSpace');
};

const GoMeme = () => {
  debugMessage.value = 'Meme clicked';
  router.push('/Meme');
};

const GoOpenAPI = () => {
  debugMessage.value = 'GoOpenAPI clicked';
  router.push('/OpenAPI');
}
</script>

<template>
  <div id="Sidebar">
    <div id="drawer" class="mdui-drawer mdui-card" style="border-radius: 0px;">
      <img src="/logo.png" class="mdui-img-fluid">
      <div class="mdui-divider"></div>
      <ul class="mdui-list">
        <a @click="GoIndex">
          <li class="mdui-list-item mdui-ripple">
            <i class="mdui-list-item-icon mdui-icon material-icons">home</i>
            <div class="mdui-list-item-content">首页</div>
          </li>
        </a>
        <a href="https://space.bilibili.com/435502585" target="_blank">
          <li class="mdui-list-item mdui-ripple">
            <i class="mdui-list-item-icon mdui-icon material-icons">camera</i>
            <div class="mdui-list-item-content">哔哩哔哩</div>
          </li>
        </a>
        <li class="mdui-subheader">壁纸</li>
        <a @click="GoMeme">
          <li class="mdui-list-item mdui-ripple">
            <i class="mdui-list-item-icon mdui-icon material-icons">child_care</i>
            <div class="mdui-list-item-content">表情包</div>
          </li>
        </a>
        <a @click="GoWallpaper">
          <li class="mdui-list-item mdui-ripple">
            <i class="mdui-list-item-icon mdui-icon material-icons">extension</i>
            <div class="mdui-list-item-content">全部壁纸</div>
          </li>
        </a>
        <a @click="GoWallpaperPC">
          <li class="mdui-list-item mdui-ripple">
            <i class="mdui-list-item-icon mdui-icon material-icons">extension</i>
            <div class="mdui-list-item-content">电脑壁纸</div>
          </li>
        </a>
        <a @click="GoWallpaperMobile">
          <li class="mdui-list-item mdui-ripple">
            <i class="mdui-list-item-icon mdui-icon material-icons">extension</i>
            <div class="mdui-list-item-content">手机壁纸</div>
          </li>
        </a>
        <a @click="GoWallpaperSpace">
          <li class="mdui-list-item mdui-ripple">
            <i class="mdui-list-item-icon mdui-icon material-icons">extension</i>
            <div class="mdui-list-item-content">轻音图网壁纸</div>
          </li>
        </a>
        <li class="mdui-subheader">其他</li>
        <a @click="GoOpenAPI">
          <li class="mdui-list-item mdui-ripple">
            <i class="mdui-list-item-icon mdui-icon material-icons">beenhere</i>
            <div class="mdui-list-item-content">OpenAPI</div>
          </li>
        </a>
        <a href="https://messages.miomoe.cn/" target="_blank">
          <li class="mdui-list-item mdui-ripple">
            <i class="mdui-list-item-icon mdui-icon material-icons">sms</i>
            <div class="mdui-list-item-content">前往留言</div>
          </li>
        </a>
        <a href="https://messages.miomoe.cn/Video" target="_blank">
          <li class="mdui-list-item mdui-ripple">
            <i class="mdui-list-item-icon mdui-icon material-icons">videocam</i>
            <div class="mdui-list-item-content">观看轻音全集</div>
          </li>
        </a>
    </ul>
  </div>
  </div>
</template>