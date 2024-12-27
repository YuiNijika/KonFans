<script setup>
document.title = 'OpenAPI';
</script>
<template>
  <div class="mdui-card-primary-title">
    OpenAPI
  </div>
  <div class="mdui-card-primary-subtitle">
    轻音部の小窝开放API
  </div>
  <div class="mdui-divider"></div>
  <div class="mdui-card-content">
    <div class="mdui-typo">
      <h2>APIUrl</h2>
      <pre><code>https://api.x-x.work/get/Wallpaper/KON</code></pre>
      <h3>参数</h3>
      <table><thead><tr><th>返回格式</th> <th>调用参数</th> <th>调用说明</th></tr></thead> <tbody><tr><td>Json</td> <td>All</td> <td>返回全部</td></tr> <tr><td>Json</td> <td>PC</td> <td>返回电脑壁纸</td></tr> <tr><td>Json</td> <td>Mobile</td> <td>返回手机壁纸</td></tr> <tr><td>Json</td> <td>Space</td> <td>返回轻音图网</td></tr> <tr><td>Json</td> <td>Meme</td> <td>返回表情包</td></tr></tbody></table>
      <h3>返回格式</h3>
      <pre><code>
        [
          {
              "File": "111713.jpg",
              "Url": "https://ss.bscstorage.com/wpteam/Wallpaper/KON/111713.jpg"
          },
          {
              "File": "113388.jpg",
              "Url": "https://ss.bscstorage.com/wpteam/Wallpaper/KON/113388.jpg"
          },
        ]
      </code></pre>
    </div>
  </div>
</template>