export function getSizeImage(url) {
    return new Promise((resolve, reject) => {
        const img = new Image();
        img.onload = () => {
            resolve({ w: img.width, h: img.height, url: url });
        };
        img.onerror = (error) => {
            reject(error);
        };
        img.src = url;
    });
}

export const imageExists = async (url) => {
    try {
        const response = await fetch(url, { method: "HEAD" });
        if (response.status === 404) {
            return false;
        }
        return true;
    } catch (error) {
        return false;
    }
};
