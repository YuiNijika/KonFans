<script setup>
import Sidebar from './components/Sidebar.vue'
</script>

<template>
  <header>
    <div class="mdui-appbar mdui-appbar-scroll-hide mdui-headroom">
      <div class="mdui-toolbar mdui-card mdui-shadow-0" style="border-radius: 0;">
        <a href="javascript:;" class="mdui-btn mdui-btn-icon" mdui-drawer="{target: '#drawer'}">
          <i class="mdui-icon material-icons">menu</i>
        </a>
        <a href="javascript:;" class="mdui-typo-headline mdui-hidden-xs">轻音部の小窝</a>
        <a href="javascript:;" class="mdui-typo-title mdui-col-xs-6">轻音少女粉丝网站</a>
        <div class="mdui-toolbar-spacer"></div>
        <a href="https://space.bilibili.com/435502585" target="_blank" class="mdui-btn mdui-btn-icon">
          <i class="mdui-icon material-icons">camera</i>
        </a>
      </div>
    </div>
  </header>
  <main id="content" style="min-height: calc(76vh - 100px);">
    <Sidebar />
    <div class="mdui-appbar-with-toolbar mdui-container">
      <div class="mdui-card mdui-hoverable mdui-m-b-2">
        <div class="mdui-card-primary">
          <router-view></router-view>
        </div>
      </div>
    </div>
  </main>
  <footer style="margin-top: auto;">
    <div class="mdui-valign"><img src="https://www.miomoe.cn/usr/themes/KON/Assets/images/end.png" class="mdui-center mdui-img-fluid" style="opacity: 0.85;"></div>
    <div class="mdui-card" style="border-radius: 0px;">
      <div class="mdui-container">
        <div class="mdui-row mdui-p-y-4">
          <div class="mdui-col-xs-4 mdui-col-md-3 mdui-col-offset-md-1">
            <div class="mdui-float-left">
              如有任何疑问请联系邮箱或B站<br><a href="https://space.bilibili.com/435502585" target="_blank">@Tomoriゞ</a></div>
          </div>
          <div class="mdui-col-xs-4 mdui-col-md-4">
            <div class="mdui-text-center">
              <div>© 2024 <a href="https://www.miomoe.cn/"><b>轻音部の小窝</b></a> <br><a href="https://space.bilibili.com/435502585" target="_blank">B站</a>丨<a href="https://houbunsha.co.jp/" target="_blank">芳文社</a></div>
            </div>
          </div>
          <div class="mdui-col-xs-4 mdui-col-md-3">
            <div class="mdui-float-right"><a href="https://beian.miit.gov.cn/" target="_blank" rel="external nofollow noopener">鲁ICP备2023052135号-3</a> <br>联系邮箱: Tomori@miomoe.cn
            </div>
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>
、