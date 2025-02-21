<template>
  <v-navigation-drawer v-model="drawer" :location="$vuetify.display.mobile ? 'left' : undefined">
    <v-list>
      <v-list-item prepend-avatar="https://q.qlogo.cn/g?b=qq&nk=1778273540&s=100" subtitle="Tomori@miomoe.cn"
        title="鼠子ゞ"></v-list-item>
    </v-list>

    <v-divider></v-divider>
    <v-list density="compact" nav>
      <router-link v-for="(item, index) in navItems" :key="index" :to="item.to">
        <v-list-item :prepend-icon="item.icon" :title="item.title" :value="item.value" />
      </router-link>
      <v-divider></v-divider>
        <router-link to="/Love">
          <v-list-item prepend-icon="mdi-account-group" title="同人作品" value="love" />
        </router-link>
      <v-divider></v-divider>
      <a href="https://blog.miomoe.cn/article/33" target="_blank">
        <v-list-item prepend-icon="mdi-api" title="OpenAPI" value="api" />
      </a>
      <a href="https://space.bilibili.com/435502585" target="_blank">
        <v-list-item prepend-icon="mdi-account" title="哔哩哔哩" value="bilibili" />
      </a>
      <a href="https://cloud.miomoe.cn/s/BODFo" target="_blank">
        <v-list-item prepend-icon="mdi-video" title="观看全集" value="playerKon" />
      </a>
      <a href="https://messages.miomoe.cn/" target="_blank">
        <v-list-item prepend-icon="mdi-comment-multiple" title="前往留言" value="comments" />
      </a>
    </v-list>
    <template v-slot:append>
      <div class="pa-2">
        <a href="https://qm.qq.com/q/LsfyB5MEa" target="_blank" rel="external nofollow noopener">
          <v-btn block>
            交流群 <v-tooltip activator="parent" location="top">群号：574888080</v-tooltip>
          </v-btn>
        </a>
      </div>
      <div class="pa-2">
        <a href="https://beian.miit.gov.cn/" target="_blank" rel="external nofollow noopener">
          <v-btn block>
            鲁ICP备2023052135号-3
          </v-btn>
        </a>
      </div>
    </template>
  </v-navigation-drawer>

  <v-app-bar scroll-behavior="hide" app>
    <template v-slot:prepend>
      <v-app-bar-nav-icon @click.stop="drawer = !drawer"></v-app-bar-nav-icon>
    </template>

    <v-app-bar-title>
      <router-link to="/">轻音部の小窝</router-link>
    </v-app-bar-title>

    <template v-slot:append>
      <a href="https://github.com/ShuShuicu/KON-WebSite-V2" target="_blank"><v-btn icon="mdi-star"></v-btn></a>
      <v-tooltip activator="parent" location="start">点个Star叭!</v-tooltip>
    </template>
  </v-app-bar>
</template>

<script>
import { ro } from 'vuetify/locale';

export default {
  data() {
    return {
      drawer: null,
      navItems: [
        { icon: 'mdi-home', title: '首页', value: 'home', to: '/' },
        { icon: 'mdi-laptop', title: '电脑壁纸', value: 'pcWallpaper', to: '/Desktop' },
        { icon: 'mdi-cellphone', title: '手机壁纸', value: 'mobileWallpaper', to: '/Mobile' },
        { icon: 'mdi-panorama-variant-outline', title: '轻音图网', value: 'spaceWallpaper', to: '/Space' },
        { icon: 'mdi-panorama-variant', title: '轻音表情包', value: 'meme', to: '/Meme' },
        { icon: 'mdi-heart-flash', title: 'AlphaCoders', value: 'alphacoders', to: '/AlphaCoders' }
      ],
    }
  },
}
</script>
