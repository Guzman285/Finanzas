const path = require("path");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
module.exports = {
  mode: "development",
  entry: {
    "js/app": "./src/js/app.js",
    "js/inicio": "./src/js/inicio.js",
    "js/cuentas/index": "./src/js/cuentas/index.js",
    "js/bancos/index": "./src/js/bancos/index.js",
    "js/categorias/index": "./src/js/categorias/index.js",
  },
  output: {
    filename: "[name].js",
    path: path.resolve(__dirname, "public/build"),
  },
  plugins: [
    new MiniCssExtractPlugin({
      filename: "styles.css",
    }),
  ],
  module: {
    rules: [
      {
        test: /\.(c|sc|sa)ss$/,
        use: [
          {
            loader: MiniCssExtractPlugin.loader,
          },
          "css-loader",
          "sass-loader",
        ],
      },
      {
        test: /\.(png|svg|jpe?g|gif)$/,
        type: "asset/resource",
      },
    ],
  },
};
