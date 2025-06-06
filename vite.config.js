import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import { resolve } from "path";

   process.env.HOST = "localhost";
   process.env.VITE_HOST = "localhost";

   export default defineConfig({
       plugins: [
           laravel({
               input: "resources/js/app.js",
               ssr: "resources/js/ssr.js",
               refresh: true,
           }),
           vue({
               template: {
                   transformAssetUrls: {
                       base: null,
                       includeAbsolute: false,
                   },
               },
           }),
       ],
       resolve: {
           alias: {
               "@": resolve(__dirname, "resources/js"),
           },
       },
       server: {
           host: process.env.HOST,
           port: process.env.PORT,
           strictPort: true,
       },
   });
