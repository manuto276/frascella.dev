import { defineConfig } from 'vite'
import { fileURLToPath } from 'url'
import { dirname, resolve, relative } from 'path'
import fs from 'fs'

const __filename = fileURLToPath(import.meta.url)
const __dirname = dirname(__filename)

const srcPath = resolve(__dirname, 'src')

function getJsEntries(dir) {
  let entries = []
  fs.readdirSync(dir, { withFileTypes: true }).forEach(entry => {
    const fullPath = resolve(dir, entry.name)
    if (entry.isDirectory()) {
      entries = entries.concat(getJsEntries(fullPath))
    } else if (entry.isFile() && entry.name.endsWith('.js')) {
      const name = relative(srcPath, fullPath).replace(/\.js$/, '')
      entries.push([name, fullPath])
    }
  })
  return entries
}

export default defineConfig({
  root: '.',
  publicDir: false,
  build: {
    outDir: 'public/js',
    emptyOutDir: true,
    rollupOptions: {
      input: Object.fromEntries(getJsEntries(srcPath)),
      output: {
        entryFileNames: `[name].js`,
        assetFileNames: `[name].[ext]`
      }
    }
  }
})
