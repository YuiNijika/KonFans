<script setup>
//
</script>

<template>
  <AppBars />
  <v-main>
    <v-container>
      <AppNav />
      <AppAnnouncement />
      <AppLove />
      <v-card style="margin-top: 20px;">
        <router-view />
      </v-card>
    </v-container>
  </v-main>
</template>