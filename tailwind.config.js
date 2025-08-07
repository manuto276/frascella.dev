/** @type {import('tailwindcss').Config} */
module.exports = {
  prefix: 'tw-',
  content: [
    "./views/**/*.php",
    "./public/**/*.php",
    "./public/js/**/*.js"
  ],
  theme: {
    extend: {},
  },
  plugins: [],
  darkMode: 'class'
}

