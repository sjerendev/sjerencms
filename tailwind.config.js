import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
			colors: {
                blue: {
                    600: '#1E40AF',  // Your custom blue color
                    700: '#1E3A8A',  // Darker shade for hover
                },
                gray: {
                    600: '#4B5563',  // Your custom gray color
                    700: '#374151',  // Darker shade for hover
                },
            },
        },
    },
    plugins: [
        require('@tailwindcss/typography'),
    ],
};
