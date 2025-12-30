const path = require('path');

module.exports = {
    entry: {
        'coin-selector': './coin-selector.js',
        'paypal-gateway': './paypal-gateway.jsx'
    },
    output: {
        path: path.resolve(__dirname, '../resource'),
        filename: (pathData) => {
            if (pathData.chunk.name === 'coin-selector') {
                return 'give-multi-currency-coin-selector.js';
            }
            if (pathData.chunk.name === 'paypal-gateway') {
                return 'payPalCommerceGateway.js';
            }
            return '[name].js';
        }
    },
    module: {
        rules: [
            {
                test: /\.(js|jsx)$/,
                exclude: /node_modules/,
                use: {
                    loader: 'babel-loader'
                }
            }
        ]
    },
    resolve: {
        extensions: ['.js', '.jsx']
    },
    externals: {
        'react': 'React',
        'react-dom': 'ReactDOM'
    }
};
