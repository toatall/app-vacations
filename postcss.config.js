const purgecss = require('@fullhuman/postcss-purgecss')({
    content: [            
        './resource/**/*.vue',
    ],
    defaultExtractor: content => content.match(/[A-Za-z0-9-_:/]+/g) || []
})

module.exports = {
    plugins: [
        require('postcss-import'),
        require('tailwindcss'),
        require('autoprefixer'),
        ...process.env.NODE_ENV === 'production'
            ? [purgecss, require('cssnano')]
            : []
    ]
}
