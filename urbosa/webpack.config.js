const webpack = require('webpack')
const BrowserSyncPlugin = require('browser-sync-webpack-plugin')
const MiniCssExtractPlugin = require('mini-css-extract-plugin')

module.exports = env => {
  var plugins = [
    new MiniCssExtractPlugin({
      filename: 'css/[name].css',
      chunkFilename: 'css/[id].css'
    }),
    new webpack.ProvidePlugin({
      $: 'jquery',
      jQuery: 'jquery'
    })
  ]
  // When watch is called only monitor the change without autoloading
  if (!env.watch) {
    plugins.push(
      new BrowserSyncPlugin({
        host: 'localhost',
        port: 3000,
        proxy: `http://localhost:8191`,
        files: [
          `./`, // Will watch any changes from this folder
          '!./node_modules/'
        ],
        notify: false,
        reloadDelay: 0
      })
    )
  }

  return {
    entry: {
      bundle: __dirname + '/entry.js',
      style: __dirname + `/assets/sass/index.scss`,
      layout: __dirname + `/assets/sass/_layout.scss`,
      blocks: __dirname + `/assets/sass/_blocks.scss`,
      template: __dirname + `/assets/sass/_template.scss`
    },
    output: {
      path: __dirname + `/assets/dist/`,
      filename: 'js/[name].js?[hash]'
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
    plugins: plugins
  }
}
