module.exports = {
    mode: 'development',
    entry: './js/index.ts',
    output: {
        path: __dirname + '/public/assets/js',
        filename: 'bundle.js'
    },
    module: {
        rules: [
            {
                test: /\.ts$/,
                loader: 'ts-loader'
            }
        ]
    },
    resolve: {
        extensions: ['.ts', '.js']
    }
}