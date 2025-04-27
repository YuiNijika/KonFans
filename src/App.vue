<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const message = '祝贺《轻音少女》(けいおん! / K-ON!) 16周年快乐! 第一季动画于2009年4月2日开始放送.';

const isMobile = ref(false);

const handleResize = () => {
    isMobile.value = window.innerWidth <= 768;
};

onMounted(() => {
    handleResize();
    window.addEventListener('resize', handleResize);
});

onUnmounted(() => {
    window.removeEventListener('resize', handleResize);
});
</script>

<template>
  <header>
    <AppMenuMobile v-if="isMobile" />
    <AppMenuDesktop v-else />
  </header>
  <main>
    <a-alert :message="message" type="info" class="alert" show-icon />
      <RouterView />
  </main>
</template>

<style scoped>

main {
  width: 85%;
  margin: 0 auto;
  padding: 80px 0 20px 0;
}
header {
  position: fixed;
  z-index: 1000;
  top: 0;
  width: 100%;
  background-color: rgb(255 255 255 / 80%)
}
.alert {
  margin-bottom: 20px;
  background-color: rgb(255 255 255 / 50%);
}
</style>
