<script setup>
import {ref, onMounted, nextTick} from 'vue';
import axios from 'axios';
import Isotope from 'isotope-layout';

const wallpapers = ref([]);
const displayedWallpapers = ref([]);
let iso = null;
const limit = 20; // 每页加载的数量

async function fetchWallpapers() {
  try {
    const response = await axios.get('https://api.x-x.work/get/Wallpaper/KON?Mobile');
    wallpapers.value = response.data;
    updateDisplayedWallpapers();
  } catch (error) {
    console.error('Error fetching wallpapers:', error);
  }
}

function updateDisplayedWallpapers() {
  const randomWallpapers = shuffle(wallpapers.value).slice(0, limit);
  displayedWallpapers.value = randomWallpapers;

  // 确保 Isotope 能够检测到新添加的元素
  nextTick(() => {
    if (iso) {
      iso.layout();
    }
  });
}

function shuffle(array) {
  for (let i = array.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [array[i], array[j]] = [array[j], array[i]];
  }
  return array;
}

function reLayoutIsotope() {
  if (iso) {
    iso.layout();
  }
}

onMounted(async () => {
  await fetchWallpapers();
  await nextTick(); // 确保 DOM 更新完成

  const grid = document.querySelector('.Wallpaper');
  if (grid) {
    console.log('Initializing Isotope...');
    iso = new Isotope(grid, {
      itemSelector: '.Wallpaper-item',
      layoutMode: 'masonry'
    });
    console.log('Isotope initialized:', iso);
  }

  window.ViewImage && ViewImage.init('.Wallpaper img');
});

document.title = '轻音少女手机壁纸';

// 加载更多内容的函数
function loadMore() {
  updateDisplayedWallpapers();
}
</script>

<template>
  <div class="Wallpaper" ref="grid">
    <div v-for="(wallpaper, index) in displayedWallpapers" :key="index" class="Wallpaper-item">
      <a href="javascript:;">
        <img v-lazy="wallpaper.Url" :alt="wallpaper.File" @load="reLayoutIsotope"/>
      </a>
    </div>
  </div>
  <div class="mdui-valign">
    <button class="mdui-center mdui-btn mdui-btn-raised mdui-ripple mdui-color-theme-accent" @click="loadMore">换一换
    </button>
  </div>
</template>

