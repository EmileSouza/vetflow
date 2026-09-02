/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
    
      colors: {
        primary: { DEFAULT: '#4F46E5', light: '#6366F1', dark: '#4338CA' }, // Roxo
        secondary: { DEFAULT: '#06B6D4', light: '#22D3EE', dark: '#0891B2' }, // Turquesa
        accent: { DEFAULT: '#3B82F6', light: '#60A5FA', dark: '#2563EB' }, // Azul
        success: { DEFAULT: '#10B981', light: '#34D399', dark: '#059669' }, // Verde
        neutral: { DEFAULT: '#1F2937', light: '#6B7280', dark: '#111827' }, // Cinza escuro
        background: '#F8FAFC',
        border: '#E5E7EB',
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'], // Fonte Inter
      },
    },
  },
  plugins: [],
}