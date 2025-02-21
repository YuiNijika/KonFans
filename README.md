# KON!

> 这是我写的第一个 Vue3 项目的重构版，技术较差大佬勿喷！

欢迎 fork 提交代码大家一起努力罢！
> 特别感谢[@小宏XeLa](https://github.com/xiaohong2022)

QQ交流群`574888080`  
哔哩哔哩[@Tomoriゞ](https://space.bilibili.com/435502585)

## 运行&构建

- npm i
- npm i -D vuetify vite-plugin-vuetify
- npm i @mdi/font
- npm run dev
- npm run build

### OpenAPI

```html
https://api-v2.x-x.work/web/kon/wallpaper
```
### 参数
| 返回格式 | 调用参数  |  调用说明 |
| ------------ | ------------ | ------------ |
|  Json |  All | 返回全部  |
| Json  | PC  |  返回电脑壁纸 |
|  Json | Mobile  | 返回手机壁纸  |
|  Json |  Space |  返回轻音图网 |
|  Json |  Meme | 返回表情包  |
| Json | AlphaCoders | 返回AlphaCoders的壁纸 |

#### 数量控制

| 调用参数  | 调用说明  |
| ------------ | ------------ |
|  number |  数量 |
|  page |  页码 |

## 示例
```html
https://api-v2.x-x.work/web/kon/wallpaper?All&number=5&page=1
```
### 返回信息
```json
{
    "data": [
        {
            "File": "111713.jpg",
            "Url": "https://ss.bscstorage.com/wpteam/Wallpaper/KON/111713.jpg"
        },
        {
            "File": "113388.jpg",
            "Url": "https://ss.bscstorage.com/wpteam/Wallpaper/KON/113388.jpg"
        },
        {
            "File": "120611.jpg",
            "Url": "https://ss.bscstorage.com/wpteam/Wallpaper/KON/120611.jpg"
        },
        {
            "File": "120613.jpg",
            "Url": "https://ss.bscstorage.com/wpteam/Wallpaper/KON/120613.jpg"
        },
        {
            "File": "127889.jpg",
            "Url": "https://ss.bscstorage.com/wpteam/Wallpaper/KON/127889.jpg"
        }
    ],
    "meta": {
        "current_page": 1,
        "per_page": 5,
        "total_items": 5,
        "total_pages": 1
    }
}
```