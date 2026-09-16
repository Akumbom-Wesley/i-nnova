import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            // Free equivalents for the guide's Kinektic Bold / Larsseit —
            // see the typography note in resources/css/app.css
            fonts: [
                bunny('Archivo', { weights: [600, 700] }),
                bunny('Figtree', { weights: [400, 500, 600, 700] }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
