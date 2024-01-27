/** @type {import('tailwindcss').Config} */
export default {
  content: [],
  purge: ['./public/**/*.html', './src/**/*.php'], 
  theme: {
    extend: {},
  },
  plugins: [
    require('@tailwindcss/typography'),
  ],
}

