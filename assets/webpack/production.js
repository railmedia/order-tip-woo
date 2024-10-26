const { merge } = require('webpack-merge');
// eslint-disable-next-line import/no-extraneous-dependencies
const TerserPlugin = require('terser-webpack-plugin');
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');
const CompressionPlugin = require('compression-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const autoprefixer = require('autoprefixer');
const PostCSSMixins = require('postcss-mixins');
const PostCSSNested = require('postcss-nested');
const PostCSSSimpleVars = require('postcss-simple-vars');
const PixRem = require('pixrem');
const sortMediaQueries = require('postcss-sort-media-queries');
const sharedConfig = require('./shared');
const productionConfig = {
	mode: 'production',
	devtool: 'source-map',
	module: {
		rules: [
			...sharedConfig.module.rules,
			// compile all .scss files to plain old css
			{
				test: /\.s?[c|a]ss$/,
				use: [
					MiniCssExtractPlugin.loader,
					{
						loader: 'css-loader',
						options: { sourceMap: false, url: false },
					},
					{
						loader: 'postcss-loader',
						options: {
							sourceMap: false,
							postcssOptions: {
								plugins: [
									sortMediaQueries({
										sort: 'mobile-first',
									}),
									autoprefixer(),
									PostCSSNested(),
									PostCSSSimpleVars(),
									PostCSSMixins(),
									PixRem({
										atrules: true,
										replace: false,
									}),
								],
							},
						},
					},
					{
						loader: 'sass-loader',
						options: {
							sourceMap: false,
						},
					},
				],
			},
		],
	},
	optimization: {
		minimize: true,
		minimizer: [
			// enable the js minification plugin
			new TerserPlugin({
				terserOptions: {
					compress: {
						warnings: false,
					},
					output: {
						comments: false,
					},
				},
			}),
			// enable the css minification plugin
			new CssMinimizerPlugin(),
		],
	},
	plugins: [
		...sharedConfig.plugins,
		// new CompressionPlugin({
		// 	algorithm: 'gzip',
		// }),
	],
};

module.exports = merge(sharedConfig, productionConfig);
