const webpack = require("webpack");
const BrowserSyncPlugin = require("browser-sync-webpack-plugin");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");

module.exports = env => {
  return {
    entry: {
      bundle: __dirname + "/entry.js",
      css: __dirname + "/src/Blocks/css.js",
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
      splitChunks: {
        cacheGroups: {
          style: {
            name: 'style',
            test: /style\.s?[ac]ss$/,
            chunks: 'all',
            enforce: true,
          },
          editor: {
            name: 'editor',
            test: /editor\.s?[ac]ss$/,
            chunks: 'all',
            enforce: true,
          },
        }
      }
    },

    module: {
      rules: [
        {
          test: /\.s?[ac]ss$/,
          use: [
            {
              loader: MiniCssExtractPlugin.loader,
              options: {
                publicPath: '../',
              }
            },
            {
              loader: "css-loader",
              options: {
                sourceMap: true,
                url: false,
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
          options: {
            presets: [
              "@babel/preset-env",
              "@babel/preset-react",
            ],
            plugins: [
              "@babel/plugin-proposal-class-properties"
            ]
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
        filename: "blocks.[name].build.css", // coming from cacheGroups
      }),
      new webpack.ProvidePlugin({
        $: "jquery",
        jQuery: "jquery",
      }),
      new BrowserSyncPlugin({
        host: "localhost",
        port: 3000,
        proxy: `http://localhost:8182`,
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