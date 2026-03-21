import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Outfit', 'Figtree', ...defaultTheme.fontFamily.sans],
                display: ['Epilogue', 'sans-serif'],
                serif: ['Playfair Display', 'Georgia', 'serif'],
            },
            colors: {
                brand: {
                    25: '#fff5f6',
                    50: '#ffeaed',
                    100: '#ffd5db',
                    200: '#ffb0bb',
                    300: '#ff8095',
                    400: '#f4566c',
                    500: '#e94560',
                    600: '#d13352',
                    700: '#b32443',
                    800: '#921d38',
                    900: '#771a31',
                    950: '#450d1b',
                },
                // Editorial Vintage theme
                primary: '#8b1a1f',
                'primary-dark': '#6e1419',
                'bg-light': '#f5f0eb',
                'bg-dark': '#1a0f10',
                'warm-gray': '#3d3532',
                'warm-gray-light': '#6b6260',
                surface: '#ebe6dc',
                muted: '#7a7571',
                success: {
                    50: '#ecfdf3',
                    100: '#d1fadf',
                    500: '#12b76a',
                    600: '#039855',
                    700: '#027a48',
                },
                error: {
                    50: '#fef3f2',
                    100: '#fee4e2',
                    500: '#f04438',
                    600: '#d92d20',
                    700: '#b42318',
                },
                warning: {
                    50: '#fffaeb',
                    500: '#f79009',
                },
            },
            zIndex: {
                1: '1',
                9: '9',
                99: '99',
                999: '999',
                9999: '9999',
                99999: '99999',
            },
            fontSize: {
                'theme-xs': ['12px', '18px'],
                'theme-sm': ['14px', '20px'],
            },
            boxShadow: {
                'theme-xs': '0px 1px 2px 0px rgba(16, 24, 40, 0.05)',
                'theme-sm': '0px 1px 3px 0px rgba(16, 24, 40, 0.1), 0px 1px 2px 0px rgba(16, 24, 40, 0.06)',
                'theme-md': '0px 4px 8px -2px rgba(16, 24, 40, 0.1), 0px 2px 4px -2px rgba(16, 24, 40, 0.06)',
                'theme-lg': '0px 12px 16px -4px rgba(16, 24, 40, 0.08), 0px 4px 6px -2px rgba(16, 24, 40, 0.03)',
            },
        },
    },

    plugins: [forms],
};
