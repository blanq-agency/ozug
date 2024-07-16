const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/views/**/*.antlers.html',
        './resources/views/**/*.antlers.php',
        './resources/js/**/*.vue',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                regular: ['librecaslon', 'regular', ...defaultTheme.fontFamily.sans],
                sans: ['worksans', 'sans-serif', ...defaultTheme.fontFamily.sans],
                serif: ['librecaslon', 'serif', ...defaultTheme.fontFamily.serif],
            },
            colors: {
              'ok-gray':        '#707070',
              'ok-dark-gray':   '#575757',
              'ok-light-gray':  '#CCCCCC',
              'ok-beige':       '#f4f4f2',
              'ok-light-beige': '#EFEFEF',
              'ok-yellow':      '#e3e7ff',
              'ok-orange':      '#fff3d4',
              'ok-blue':        '#d9f7e7',
              'ok-red':         '#E06C56',
            },
            listStyleType: {
              square: 'square',
              circle: 'circle',
              'upper-roman': 'upper-roman',
              'lower-roman': 'lower-roman',
            },
        },
    },

    plugins: [require('@tailwindcss/forms'), require('@tailwindcss/typography')],
};
