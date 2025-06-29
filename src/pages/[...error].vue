<script setup>
const QQGroupLink = 'http://qm.qq.com/cgi-bin/qm/qr?_wv=1027&k=9hcJluy09C1Ymx3EIARyijgWjFw-7W3h&authKey=PhvK%2FkWbp3paILIzLv7xHoR5YR8iejaPP5LLE0OxXelRbk2mprgfRL6yNFpzUmYD&noverify=0&group_code=574888080'
</script>

<template>
    <a-empty image="/assets/蓝白碗.png" :image-style="{
        height: '240px',
    }">
        <template #description>
            <span>
                页面走丢咯~<br>
                QQ同好群: <a :href="QQGroupLink" target="_blank">574888080</a>
            </span>
        </template>
        <RouterLink to="/">
            <a-button type="primary">返回首页</a-button>
        </RouterLink>
    </a-empty>
</template>