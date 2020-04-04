const webpack = require('webpack')
const BrowserSyncPlugin = require('browser-sync-webpack-plugin')
const MiniCssExtractPlugin = require('mini-css-extract-plugin')

module.exports = env => {
  return {
    entry: {
      bundle: __dirname + '/entry.js'
    },
    output: {
      path: __dirname + `/dist/`
    },
    devtool: env.development ? 'source-map' : false,
    module: {
      rules: [
        {
          test: /\.s?[ac]ss$/,
          use: [
            {
              loader: MiniCssExtractPlugin.loader,
              options: {
                publicPath: '../../'
              }
            },
            {
              loader: 'css-loader',
              options: {
                sourceMap: true,
                url: false
              }
            },
            {
              loader: 'postcss-loader',
              options: {
                plugins: () => [require('autoprefixer')]
              }
            },
            {
              loader: 'resolve-url-loader',
              options: {
                debug: true
              }
            },
            {
              loader: 'sass-loader',
              options: {
                sourceMap: true
              }
            }
          ]
        },
        {
          test: /\.js?$/,
          exclude: /node_modules/,
          loader: 'babel-loader',
          query: {
            presets: ['@babel/preset-env', '@babel/preset-react']
          }
        },
        {
          test: require.resolve('jquery'),
          use: [
            {
              loader: 'expose-loader',
              options: '$'
            }
          ]
        }
      ]
    },
    plugins: [
      new MiniCssExtractPlugin({
        filename: 'css/style.css'
      }),
      new webpack.ProvidePlugin({
        $: 'jquery',
        jQuery: 'jquery'
      }),
      new BrowserSyncPlugin({
        host: 'localhost',
        port: 3000,
        proxy: `http://localhost:8191`,
        files: [
          `./urbosa`, // Set the watch folder to reload
          '!./node_modules/'
        ],
        notify: false,
        reloadDelay: 0
      })
    ]
  }
}
