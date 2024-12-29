<script setup>
import { ref, onMounted, nextTick } from 'vue';
import axios from 'axios';
import Isotope from 'isotope-layout';

const wallpapers = ref([]);
let iso = null;

async function fetchWallpapers() {
  try {
    const response = await axios.get('https://api.x-x.work/get/Wallpaper/KON?Meme');
    wallpapers.value = response.data;
  } catch (error) {
    console.error('Error fetching wallpapers:', error);
  }
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

document.title = '轻音少女表情包';
</script>

<template>
  <div class="Wallpaper" ref="grid">
    <div v-for="(wallpaper, index) in wallpapers" :key="index" class="Wallpaper-item">
      <a href="javascript:;">
        <img v-lazy="wallpaper.Url" :alt="wallpaper.File" @load="reLayoutIsotope"/>
      </a>
    </div>
  </div>
</template>
