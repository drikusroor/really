/** @type {import('tailwindcss').Config} */
export default {
  content: [],
  purge: [
    './content/**/*.(md|html)',
    './src/**/*.(php|twig|html|js)',
    './index.{php,html}',
  ],
  theme: {
    extend: {},
  },
  plugins: [
    require('@tailwindcss/typography'),
  ],
}

