<script setup>
useSeoMeta({
  title: 'KonFans(轻音小窝)',
  titleTemplate: '%s - ふわふわ時間、16年目!',
  description: 'KonFans(轻音小窝)是《轻音少女》(K-ON!)非官方中文粉丝站点，与けいおん爱好者一起重温放学后茶会的感动!',
})
definePageMeta({
  layout: 'custom'
})
</script>

<template>
  <div role="alert" class="alert alert-info alert-soft">
    <span>欢迎来到KonFans(轻音小窝), QQ群: 574888080</span>
  </div>
  <div class="hero bg-base-200 min-h-screen">
    <div class="hero-content flex-col lg:flex-row-reverse">
      <div class="w-full max-w-[90vw] md:max-w-sm">
        <img src="~/assets/images/logo.png" class="w-full h-auto rounded-lg">
      </div>
      <div>
        <h1 class="text-5xl font-bold">KonFans</h1>
        <p class="py-6">
          KonFans(轻音小窝)是《轻音少女》(K-ON!)非官方中文粉丝站点，与けいおん爱好者一起重温放学后茶会的感动!
        </p>
        <NuxtLink to="/Wallpaper" class="btn btn-info m-2">轻音壁纸</NuxtLink>
        <NuxtLink to="/Member" class="btn btn-accent">加入轻音部</NuxtLink>
      </div>
    </div>
  </div>
</template>