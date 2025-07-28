/** @type {import('tailwindcss').Config} */
module.exports = {
  prefix: 'tw-',
  content: [
    "./views/**/*.php",
    "./public/**/*.php",
    "./public/js/**/*.js",
    "./public/css/**/*.css"
  ],
  theme: {
    extend: {},
  },
  plugins: [],
  darkMode: 'class'
}

