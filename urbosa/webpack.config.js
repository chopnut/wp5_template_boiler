const webpack = require('webpack')
const BrowserSyncPlugin = require('browser-sync-webpack-plugin')
const MiniCssExtractPlugin = require('mini-css-extract-plugin')

module.exports = env => {
  var plugins = [
    new MiniCssExtractPlugin({
      filename: 'css/[name].css',
      chunkFilename: 'css/[id].css'
    }),
    /*
      Note: If you need to remove jquery from the build uncomment below. 
      new webpack.IgnorePlugin(/jquery/), 
     */
    new webpack.ProvidePlugin({
      $: 'jquery',
      jQuery: 'jquery'
    }),

    new BrowserSyncPlugin({
      host: 'localhost',
      port: 3000,
      proxy: `http://localhost:8191`,
      files: [
        {
          match: [
            './',  
            '!./assets/dist/js/*.js',
            '!**/*.scss',
            '!**/*.sass',
            '!**/*.map',
            '!./node_modules/',
            '!./inc/acf/json'
          ],
          fn: (event, file) => {
            if (event == 'change') {
              const bs = require("browser-sync").get("bs-webpack-plugin");
              const extension = file.split('.').pop();
              
              if(extension=='css'){

                console.log('CSS changed...', file);
                bs.reload("*.css");

              } else {

                console.log('Others changed...', file);
                bs.reload();

              } 

            }
          }
        },
      ],
      notify: false,
      reloadDelay: 0
    },
    {
      reload: false,
      name: 'bs-webpack-plugin'
    })
  ]

  return {
    entry: {
      bundle: __dirname + '/entry.js',
      style: __dirname + `/assets/sass/level/_style.scss`,
      layout: __dirname + `/assets/sass/level/_layout.scss`,
      blocks: __dirname + `/assets/sass/level/_blocks.scss`,
      template: __dirname + `/assets/sass/level/_template.scss`,
      admin: __dirname + `/assets/sass/level/_admin.scss`,
      essentials: __dirname + `/assets/sass/level/_essentials.scss`
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
                publicPath: '../../',
                hmr: true
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
