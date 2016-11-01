/* ---- THE FOLLOWING CONFIG SHOULD BE EDITED ---- */

var pkg = require( './package.json' );

function parseKeywords( keywords ) {
	// These keywords are useful for Packagist/NPM/Bower, but not for the WordPress theme repository.
	var disallowed = [ 'wordpress', 'theme' ];

	k = keywords;
	for ( var i in disallowed ) {
		var index = k.indexOf( disallowed[ i ] );
		if ( -1 < index ) {
			k.splice( index, 1 );
		}
	}

	return k;
}

var keywords = parseKeywords( pkg.keywords );

var config = {
	namespace: 'WPStarterTheme',
	slug: 'wp_starter_theme',
	textdomain: 'wp-starter-theme',
	themeName: 'WP Starter Theme',
	themeURI: pkg.homepage,
	author: pkg.author.name,
	authorURI: pkg.author.url,
	description: pkg.description,
	version: pkg.version,
	license: pkg.license.name,
	licenseURI: pkg.license.url,
	tags: keywords.join( ', ' ),
	translateURI: pkg.homepage
};

/* ---- DO NOT EDIT BELOW THIS LINE ---- */

// WP theme header for style.css
var themeheader = 	'/*\n' +
					'Theme Name: ' + config.themeName + '\n' +
					'Theme URI: ' + config.themeURI + '\n' +
					'Author: ' + config.author + '\n' +
					'Author URI: ' + config.authorURI + '\n' +
					'Description: ' + config.description + '\n' +
					'Version: ' + config.version + '\n' +
					'License: ' + config.license + '\n' +
					'License URI: ' + config.licenseURI + '\n' +
					'Text Domain: ' + config.textdomain + '\n' +
					'Domain Path: /languages/\n' +
					'Tags: ' + config.tags + '\n' +
					'*/';

// header for all PHP files
var phpheader = 	'/**\n' +
					' * @package ' + config.namespace + '\n' +
					' * @version ' + config.version + '\n' +
					' */';

// header for minified assets
var assetheader =	'/*!\n' +
					' * ' + config.themeName + ' Version ' + config.version + ' (' + config.themeURI + ')\n' +
					' * Licensed under ' + config.license + ' (' + config.licenseURI + ')\n' +
					' */\n';


/* ---- REQUIRED DEPENDENCIES ---- */

var gulp = require( 'gulp' );

var gutil = require( 'gulp-util' );
var rename = require( 'gulp-rename' );
var replace = require( 'gulp-replace' );
var sort = require( 'gulp-sort' );
var banner = require( 'gulp-banner' );
var wpPot = require( 'gulp-wp-pot' );
var composer = require( 'gulp-composer' );
var bower = require( 'bower' );

var php = {
	files: [ './*.php', './inc/**/*.php', './template-parts/**/*.php' ]
};

var sass = {
	files: [ './assets/src/sass/**/*.scss' ],
	src: './assets/src/sass/',
	dst: './assets/dist/css/',
	lint: require( 'gulp-csscomb' ),
	compile: require( 'gulp-sass' ),
	minify: require( 'gulp-minify-css' )
};

var js = {
	files: [ /*'./assets/vendor/bootstrap/js/src/*.js',*/ './assets/src/js/**/*.js', '!./assets/src/js/customize-preview.js' ],
	src: './assets/src/js/',
	dst: './assets/dist/js/',
	lint: require( 'gulp-jshint' ),
	concat: require( 'gulp-concat' ),
	minify: require( 'gulp-uglify' )
};

/* ---- MAIN TASKS ---- */

// general task (compile Sass and JavaScript and refresh POT file)
gulp.task( 'default', [ 'sass', 'js', 'pot' ]);

// watch Sass and JavaScript files
gulp.task( 'watch', function() {
	gulp.watch( sass.files, [ 'sass' ]);
	gulp.watch( js.files, [ 'js' ]);
});

// build the theme
gulp.task( 'build', [ 'version-replace', 'header-replace' ], function() {
	gulp.start( 'default' );
});

// set up the theme (only run this once after adjusting the config object!)
gulp.task( 'install', [ 'bower-install', 'init-replace' ], function() {
	composer({
		cwd: './',
		bin: 'composer'
	});

	gulp.start( 'build' );
});

/* ---- SUB TASKS ---- */

// compile Sass
gulp.task( 'sass', function( done ) {
	gulp.src( sass.src + 'app.scss' )
		.pipe( sass.compile({
			errLogToConsole: true
		}) )
		.pipe( sass.lint() )
		.pipe( gulp.dest( sass.dst ) )
		.pipe( sass.minify({
			keepSpecialComments: 0
		}) )
		.pipe( banner( assetheader ) )
		.pipe( rename({
			extname: '.min.css'
		}) )
		.pipe( gulp.dest( sass.dst ) )
		.on( 'end', function() {
			gulp.src( sass.src + 'editor.scss' )
				.pipe( sass.compile({
					errLogToConsole: true
				}) )
				.pipe( sass.lint() )
				.pipe( gulp.dest( sass.dst ) )
				.pipe( sass.minify({
					keepSpecialComments: 0
				}) )
				.pipe( banner( assetheader ) )
				.pipe( rename({
					extname: '.min.css'
				}) )
				.pipe( gulp.dest( sass.dst ) )
				.on( 'end', done );
		});
});

// compile JavaScript
gulp.task( 'js', function( done ) {
	gulp.src( js.files )
		.pipe( js.lint({
			lookup: true
		}) )
		.pipe( js.concat( 'app.js' ) )
		.pipe( gulp.dest( js.dst ) )
		.pipe( js.minify() )
		.pipe( banner( assetheader ) )
		.pipe( rename({
			extname: '.min.js'
		}) )
		.pipe( gulp.dest( js.dst ) )
		.on( 'end', function() {
			gulp.src( js.src + 'customize-preview.js' )
				.pipe( js.lint({
					lookup: true
				}) )
				.pipe( gulp.dest( js.dst ) )
				.pipe( js.minify() )
				.pipe( banner( assetheader ) )
				.pipe( rename({
					extname: '.min.js'
				}) )
				.pipe( gulp.dest( js.dst ) )
				.on( 'end', done );
		});
});

// generate POT file
gulp.task( 'pot', function( done ) {
	gulp.src( php.files, { base: './' })
		.pipe( sort() )
		.pipe( wpPot({
			domain: config.textdomain,
			destFile: './languages/' + config.textdomain + '.pot',
			headers: {
				'report-msgid-bugs-to': config.translateURI,
				'x-generator': 'gulp-wp-pot',
				'x-poedit-basepath': '.',
				'x-poedit-language': 'English',
				'x-poedit-country': 'UNITED STATES',
				'x-poedit-sourcecharset': 'uft-8',
				'x-poedit-keywordslist': '__;_e;_x:1,2c;_ex:1,2c;_n:1,2; _nx:1,2,4c;_n_noop:1,2;_nx_noop:1,2,3c;esc_attr__; esc_html__;esc_attr_e; esc_html_e;esc_attr_x:1,2c; esc_html_x:1,2c;',
				'x-poedit-bookmars': '',
				'x-poedit-searchpath-0': '.',
				'x-textdomain-support': 'yes'
			}
		}) )
		.pipe( gulp.dest( './' ) )
		.on( 'end', done );
});

// replace the default namespace and textdomain with the ones in the config object
gulp.task( 'init-replace', function( done ) {
	gulp.src( php.files, { base: './' })
		.pipe( replace( 'WPStarterTheme', config.namespace ) )
		.pipe( replace( 'wp-starter-theme', config.textdomain ) )
		.pipe( replace( 'wp_starter_theme', config.slug ) )
		.pipe( gulp.dest( './' ) )
		.on( 'end', function() {
			gulp.src( './composer.json', { base: './' })
				.pipe( replace( 'WPStarterTheme', config.namespace ) )
				.pipe( gulp.dest( './' ) )
				.on( 'end', function() {
					gulp.src([ './inc/WPStarterTheme/**/*' ], { base: './' })
						.pipe( rename(function( path ) {
							path.dirname = path.dirname.replace( 'WPStarterTheme', config.namespace );
						}) )
						.pipe( gulp.dest( './' ) )
						.on( 'end', done );
				});
		});
});

// replace the version header in all PHP files
gulp.task( 'version-replace', function( done ) {
	gulp.src( php.files, { base: './' })
		.pipe( replace( /\/\*\*\s+\*\s@package\s[^*]+\s+\*\s@version\s[^*]+\s\*\//, phpheader ) )
		.pipe( gulp.dest( './' ) )
		.on( 'end', done );
});

// replace the theme header in style.css
gulp.task( 'header-replace', function( done ) {
	gulp.src( './style.css' )
		.pipe( replace( /((?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:\/\/.*))/, themeheader ) )
		.pipe( gulp.dest( './' ) )
		.on( 'end', done );
});

// install Bower components
gulp.task( 'bower-install', function() {
	return bower.commands.install()
		.on( 'log', function( data ) {
			gutil.log( 'bower', gutil.colors.cyan( data.id ), data.message );
		});
});
