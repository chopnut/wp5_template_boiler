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
          {
            match :['./'],
            fn: function(e,f){
              if(e == 'change'){
                switch(true){
                  case f.indexOf('node_modules')>=0:
                  case f.indexOf('.json')>=0:
                    break;
                  default:
                    this.reload();
                }
              }
            }
          },
        ],

        notify: false,
        reloadDelay: 0
      })
    )
  }

  return {
    entry: {
      bundle: __dirname + '/entry.js',
      style: __dirname + `/assets/sass/level/_style.scss`,
      layout: __dirname + `/assets/sass/level/_layout.scss`,
      blocks: __dirname + `/assets/sass/level/_blocks.scss`,
      template: __dirname + `/assets/sass/level/_template.scss`,
      admin: __dirname + `/assets/sass/level/_admin.scss`
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
            },
            {
              loader: 'expose-loader',
              options: 'jQuery'
            }
          ]
        }
      ]
    },
    plugins: plugins
  }
}
