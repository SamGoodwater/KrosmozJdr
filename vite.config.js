import { defineConfig } from "vite";
import tailwindcss from "@tailwindcss/vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import { resolve } from "path";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/js/app.js",
                "resources/css/app.css",
                "resources/css/custom.css"
            ],
            ssr: "resources/js/ssr.js",
            refresh: true,
            hotFile: 'public/hot',
            buildDirectory: 'build',
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        tailwindcss(),
    ],
    resolve: {
        alias: {
            "@": resolve(__dirname, "resources/js"),
            "@scss": resolve(__dirname, "resources/scss"),
        },
    },
    css: {
        preprocessorOptions: {
            scss: {
                includePaths: ['resources/scss']
            }
        }
    },
    server: {
        host: "127.0.0.1",
        port: 5173,
        strictPort: true,
        hmr: {
            host: 'localhost',
            overlay: false,
        },
        watch: {
            usePolling: false,
            interval: 1000,
            ignored: [
                '**/node_modules/**',
                '**/vendor/**',
                '**/storage/**',
                '**/public/build/**',
                '**/resources/css/app.css',
                '**/resources/css/custom.css',
                '**/resources/css/theme.css',
            ]
        },
    },
    optimizeDeps: {
        include: ['vue', '@inertiajs/vue3', 'axios']
    },
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['vue', '@inertiajs/vue3'],
                    utils: ['axios']
                }
            }
        }
    }
});
