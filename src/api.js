export const getWallpapers = async (category, page, pageSize) => {
    try {
        const { data } = await axios.get(
            `https://api-v2.x-x.work/web/kon/wallpaper`,
            {
                params: {
                    // 修改参数传递方式
                    [category]: '', // 空值参数
                    number: pageSize,
                    page: page
                },
                paramsSerializer: params => {
                    return Object.entries(params)
                        .filter(([_, value]) => value !== undefined && value !== null)
                        .map(([key, value]) =>
                            `${encodeURIComponent(key)}${value ? `=${encodeURIComponent(value)}` : ''}`
                        )
                        .join('&');
                }
            }
        );
        return data;
    } catch (error) {
        console.error('API Error:', error);
        throw error;
    }
};