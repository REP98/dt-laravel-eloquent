import { defineConfig } from 'vite';
import { resolve } from 'path';


export default defineConfig({
  build: {
    outDir: "dist",
    minify: 'terser',
    rollupOptions: {
      input: {
        main: resolve(__dirname, 'src/resources/js/datatable.js')
      },
      output: [
        {
          format: 'umd',
          entryFileNames: 'datatable.min.js'
        }
      ]
    }
  }
});
