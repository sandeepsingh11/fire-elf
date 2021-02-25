var gulp = require('gulp');

var terser = require('gulp-terser'); // https://github.com/duan602728596/gulp-terser

var sass = require('gulp-sass');
sass.compiler = require('node-sass');

var postcss = require('gulp-postcss'); // https://github.com/postcss/gulp-postcss
var sourcemaps = require('gulp-sourcemaps'); // https://github.com/gulp-sourcemaps/gulp-sourcemaps
var autoprefixer = require('autoprefixer'); // https://github.com/postcss/autoprefixer#gulp
var cssnano = require('cssnano'); // https://github.com/cssnano/cssnano

var changed = require('gulp-changed'); // https://github.com/sindresorhus/gulp-changed
var rename = require('gulp-rename') // https://github.com/hparra/gulp-rename



var sassPath = 'admin/public/styles/scss/';
var cssDevPath = 'admin/public/styles/css-dev/';
var cssProdPath = 'admin/public/styles/css/';

var jsPath = 'admin/public/js/';





// compile sass --> css
gulp.task('sass', function() {
    return gulp.src(sassPath + 'main.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(gulp.dest(cssDevPath))
});


// add vendor prefix, sourcemap, and minify css
gulp.task('css', function() {
    // use postcss

    var plugins = [
        autoprefixer(),
        cssnano()
    ];

    return gulp.src(cssDevPath + 'main.css')
        .pipe(sourcemaps.init())
        .pipe(postcss(plugins))
        .pipe(sourcemaps.write('.'))
        // .pipe(rename(function (path) {
        //     path.basename += '.min';
        // }))
        .pipe(gulp.dest(cssProdPath))
});


// minify js
gulp.task('js', function() {
    // use terser

    return gulp.src(jsPath + 'main.js')
        .pipe(terser())
        .pipe(rename(function (path) {
            path.basename += '.min';
        }))
        .pipe(gulp.dest(jsPath))
});









// build js and css for production
gulp.task('build', gulp.series(['js', 'css']), function() {
    console.log('Building js, css');
});


// build js only for production
gulp.task('build-js', gulp.series(['js']), function() {
    console.log('Building js');
});


// build css only for production
gulp.task('build-css', gulp.series(['css']), function() {
    console.log('Building css');
});


// build sass & css only for production
gulp.task('build-style', gulp.series(['sass', 'css']), function() {
    console.log('Building sass, css');
});





// watch sass files to compile on save
gulp.task('sassy', function() {
    gulp.watch(sassPath + '*.scss', gulp.series(['sass']));
});