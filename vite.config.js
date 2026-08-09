import { defineConfig } from "vite";
import tailwindcss from "@tailwindcss/vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import { dirname, resolve } from "path";
import { fileURLToPath } from "url";

const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/js/app.js",
                "resources/css/app.css",
                "resources/css/custom.css"
            ],
            ssr: "resources/js/ssr.js",
            // Éviter les full-reload pendant sync seeders / jobs (sinon ERR_CONNECTION_RESET côté navigateur).
            refresh: [
                "resources/views/**",
                "routes/**",
                "app/Http/Controllers/**",
            ],
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
                includePaths: ['resources/scss'],
                api: 'modern-compiler'
            }
        }
    },
    server: {
        // Aligner host HTTP + HMR (évite WS localhost ↔ HTTP 127.0.0.1 sous WSL).
        host: "localhost",
        port: 5173,
        strictPort: true,
        hmr: {
            host: "localhost",
            protocol: "ws",
            clientPort: 5173,
            overlay: false,
        },
        watch: {
            usePolling: false,
            interval: 1000,
            ignored: [
                "**/node_modules/**",
                "**/vendor/**",
                "**/storage/**",
                "**/database/**",
                "**/public/build/**",
                "**/public/hot",
                "**/resources/css/app.css",
                "**/resources/css/custom.css",
                "**/resources/css/theme.css",
            ],
        },
    },
    optimizeDeps: {
        include: ['vue', '@inertiajs/vue3', 'axios']
    },
    build: {
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (id.includes('node_modules/vue') || id.includes('node_modules/@vue') || id.includes('node_modules/@inertiajs')) {
                        return 'vendor';
                    }
                    if (id.includes('resources/js/Pages/Layouts/Main.vue')) {
                        return 'layout';
                    }
                    if (id.includes('resources/js/Utils/Formatters')) {
                        return 'formatters';
                    }
                    if (id.includes('node_modules/cally')) {
                        return 'cally';
                    }
                    if (id.includes('node_modules/axios')) {
                        return 'utils';
                    }
                },
            }
        }
    }
});
