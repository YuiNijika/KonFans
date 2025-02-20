<template>
  <v-app class="fill-height">
      <Drawer />
      <v-main>
        <v-container>
          <Announcement />
          <router-view />
        </v-container>
      </v-main>
  </v-app>
</template>

<script lang="ts" setup>
//
</script>
