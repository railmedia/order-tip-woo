const { merge } = require('webpack-merge');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const autoprefixer = require('autoprefixer');
const PostCSSMixins = require('postcss-mixins');
const PostCSSCustomProps = require('postcss-custom-properties');
const PostCSSNested = require('postcss-nested');
const PostCSSSimpleVars = require('postcss-simple-vars');
const PixRem = require('pixrem');
const sortMediaQueries = require('postcss-sort-media-queries');
const SVGSpritemapPlugin = require('svg-spritemap-webpack-plugin');
const sharedConfig = require('./shared');
const path = require('path');
const devConfig = merge(sharedConfig, {
	mode: 'development',
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
						options: { sourceMap: true, url: false },
					},
					{
						loader: 'postcss-loader',
						options: {
							sourceMap: true,
							postcssOptions: {
								plugins: () => [
									sortMediaQueries({
										sort: 'mobile-first',
									}),
									autoprefixer(),
									PostCSSNested(),
									PostCSSSimpleVars(),
									PostCSSCustomProps(),
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
							sourceMap: true,
						},
					},
				],
			},
		],
	},
	optimization: {
		minimize: false,
	},
	plugins: [
		// new SVGSpritemapPlugin('assets/icons/**/*.svg', {
		// 	output: {
		// 		// filename: 'assets/images/sprite.svg',
		// 		filename: '../../images/sprite.svg',
		// 		svgo: true,
		// 		svg: {
		// 			// Disable `width` and `height` attributes on the root SVG element
		// 			// as these will skew the sprites when using the <view> via fragment identifiers
		// 			sizes: false,
		// 		},
		// 	},
		// 	styles: {
		// 		filename: '~sprites.scss',
		// 		// Specify that we want to use URLs with fragment identifiers in a styles file as well
		// 		format: 'fragment',

		// 		// Path to the styles file, note that this method uses the `output.publicPath` webpack option
		// 		// to generate the path/URL to the spritemap itself so you might have to look into that
		// 		// filename: 'assets/styles/src/common/_sprites.scss',
		// 	},
		// 	sprite: {
		// 		generate: {
		// 			title: false,
		// 			// Generate <use> tags within the spritemap as the <view> tag will use this
		// 			use: true,

		// 			// Generate <view> tags within the svg to use in css via fragment identifier url
		// 			// and add -fragment suffix for the identifier to prevent naming collisions with the symbol identifier
		// 			view: '-fragment',

		// 			// Generate <symbol> tags within the SVG to use in HTML via <use> tag
		// 			symbol: true,
		// 		},
		// 	},
		// }),
	],
});

module.exports = devConfig;
