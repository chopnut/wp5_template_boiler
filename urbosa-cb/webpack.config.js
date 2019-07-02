const webpack = require("webpack");
const BrowserSyncPlugin = require("browser-sync-webpack-plugin");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");

module.exports = env => {
  return {
    entry: {
      bundle: __dirname + "/entry.js",
    },
    output: {
      path: __dirname + `/dist/`,
      filename: "blocks.build.js?[hash]"
    },
    devtool: (env.development) ? "source-map" : false,

    //-----------------------
    //  Splitting sass files
    //-----------------------

    optimization: {
      splitChunks:{
        cacheGroups:{
          style: {
              name: 'style',
              test: /style\.s?css$/,
              chunks: 'all',
              enforce: true,
          },
          editor: {
              name: 'editor',
              test: /editor\.s?css$/,
              chunks: 'all',
              enforce: true,
          },
        }
      }
    },
    //-----------------------
    //  All test modules
    //-----------------------
    module: {
      rules: [
        {
          test: /\.scss$/,
          use: [
            {
              loader: MiniCssExtractPlugin.loader,
              options: {
                publicPath: './',
              }
            },
            {
              loader: "css-loader",
              options: {
                sourceMap: true
              }
            },
            {
              loader: "postcss-loader",
              options: {
                plugins: () => [require("autoprefixer")]
              }
            },
            {
              loader: "resolve-url-loader",
              options: {
                debug: true
              }
            },
            {
              loader: "sass-loader",
              options: {
                sourceMap: true,
              }
            }
          ]
        },
        {
          test: /\.js?$/,
          exclude: /node_modules/,
          loader: "babel-loader",
          query: {
            presets: ["@babel/preset-env","@babel/preset-react"],
          }
        },
        {
          test: /\.(png|jpg|gif|svg)$/,
          loader: "url-loader",
          options: {
            outputPath: "img/", // Where to put any resource file
          }
        },
        {
          test: /\.(eot|woff2|woff|ttf)$/,
          loader: "url-loader",
          options: {
            outputPath: "fonts/", // Where to put any resource file
          }
        },
        {
          test: require.resolve('jquery'),
          use: [{
              loader: 'expose-loader',
              options: '$'
          }]
        },
      ]
    },
    plugins: [
      new MiniCssExtractPlugin({
        filename: "blocks.[name].build.css",
      }),
      new webpack.ProvidePlugin({
        $: "jquery",
        jQuery: "jquery",
      }),
      new BrowserSyncPlugin({
        host: "localhost",
        port: 3000,
        proxy: `http://localhost:${process.env.WP_PORT}`,
        files: [
          `./`, // Will watch any changes from this folder
          "!./node_modules/",
        ],
        notify: false,
        reloadDelay: 0,
      })
    ]
  }
}