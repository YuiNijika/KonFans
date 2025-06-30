<script setup>
useSeoMeta({
  title: '出错啦!',
  titleTemplate: '%s - KonFans(轻音小窝)',
  description: '出错啦!',
  keywords: '出错啦!',
})
definePageMeta({
  layout: 'custom'
})
</script>

<template>
<div class="hero bg-base-200 min-h-screen">
  <div class="hero-content text-center">
    <div class="max-w-md">
      <h1 class="text-5xl font-bold">error</h1>
      <p class="py-6">
        这里没有<span class="text-primary"> 鹿仙贝 </span>! 返回首页寻找罢~
      </p>
      <NuxtLink to="/" class="btn btn-primary">Go to Home</NuxtLink>
    </div>
  </div>
</div>
</template>