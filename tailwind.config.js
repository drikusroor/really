/** @type {import('tailwindcss').Config} */
export default {
  content: [],
  purge: [
    './content/**/*.(md|html)',
    './src/**/*.(php|twig|html|js)',
    './index.{php,html}',
  ],
  theme: {
    extend: {
      colors: {
        'lemon': {
          100: '#FFF9C4', // Lighter lemon for backgrounds
          500: '#FCD34D', // Default lemon for elements
          700: '#F59E0B', // Darker lemon for accents
        },
        'orange': {
          100: '#FFE5D0', // Lighter orange for backgrounds
          500: '#FB923C', // Default orange for elements
          700: '#EA580C', // Darker orange for accents
        },
      },
    },
  },
  plugins: [
    require('@tailwindcss/typography'),
  ],
}

