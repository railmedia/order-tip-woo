const path = require('path')
const MiniCssExtractPlugin = require('mini-css-extract-plugin')
const {CleanWebpackPlugin} = require('clean-webpack-plugin')
const StyleLintPlugin = require('stylelint-webpack-plugin')
const sharedConfig = {
	entry: {
		admin: [
			path.resolve('assets/src/js/admin', 'admin.js')
		],
		adminReports: [
			path.resolve('assets/src/js/admin', 'admin-reports.js'),
			path.resolve('assets/src/scss/admin', 'admin-reports.scss'),
		],
		front: [
			path.resolve('assets/src/js/front', 'front.js'),
			path.resolve('assets/src/scss/front', 'front.scss'),
		]
		// product: [path.resolve('assets/scripts/src/product', 'index.js')],
		// productFooter: [path.resolve('assets/scripts/src/product', 'index-product-footer.js')],
		// cart: [path.resolve('assets/scripts/src/cart', 'index.js')],
	},
	output: {
		filename: '[name].bundle.js',
		path: path.resolve(__dirname, '../build'),
		publicPath: '/',
	},
	stats: 'minimal',
	externals: {
		jquery: 'jQuery',
		// react: 'React',
		// 'react-dom': 'ReactDOM',
		// slick: 'slick',
	},
	module: {
		rules: [
			// perform js babelization on all .js files
			{
				test: /\.js$/,
				exclude: /node_modules/,
				use: {
					loader: 'babel-loader',
					options: {
						presets: ['@babel/preset-env'],
						cacheDirectory: true,
					},
				},
			},
			{
				test: /\.(png|jpg)$/,
				loader: 'file-loader',
				options: {
					name: '[name].[ext]',
					outputPath: 'assets/images',
				},
			},
			{
				test: /\.(woff(2)?|ttf|eot|svg)(\?v=\d+\.\d+\.\d+)?$/,
				use: [
					{
						loader: 'file-loader',
						options: {
							name: '[name].[ext]',
							outputPath: 'assets/fonts',
						},
					},
				],
			},
		],
	},
	plugins: [
		new CleanWebpackPlugin({
			cleanOnceBeforeBuildPatterns: [
				'**/*',
				'!css/**',
				'!fonts/**',
				'!icons/**',
				'!images/**',
				'!styles/**',
				'!scripts/src/**',
				'!scripts/vendors/**',
				'!scripts/index.php',
			],
		}),
		// extract css into dedicated file
		new StyleLintPlugin({
			files: './assets/src/styles/**/*.s?(a|c)ss',
			fix: true,
			failOnError: false,
			syntax: 'scss',
		}),
		new MiniCssExtractPlugin({
			filename: '../css/[name].css',
		}),
	],
	devtool: 'cheap-module-source-map',
};
module.exports = sharedConfig;
