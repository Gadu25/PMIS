/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [

  ],
  theme: {
    extend: {},
  },
  plugins: [],
}

const mix = require("laravel-mix");

mix.js("resources/js/app.js", "public/js")
    .vue()
    .postCss("resources/css/app.css", "public/css", [require("tailwindcss")]);