'use strict';

var gulp        = require('gulp'),
    browserSync = require('browser-sync').create(),
    reload      = browserSync.reload,
    sass        = require('gulp-sass'),
    rigger      = require('gulp-rigger'),
    combineMq   = require('gulp-combine-mq'),
    prefixer    = require('gulp-autoprefixer'),
    cssmin      = require('gulp-clean-css'),
    uglify      = require('gulp-uglify'),
    runSequence = require('run-sequence'),
    svgstore    = require("gulp-svgstore"),
    rename      = require("gulp-rename");

var path = {
    root: '/',
    images: {
        root: 'assets/img/',
        icons: 'assets/img/icons/*.svg',
    },
    build: {
        js:    'assets/js/',
        style: 'assets/css/',
    },
    src: {
        js:    'assets/src/js/*.js',
        style:  'assets/src/scss/*.scss',
    },
    watch: {
        js:    'assets/src/js/**/*.js',
        style: 'assets/src/scss/**/*.scss',
    }
};

gulp.task('style', function() {
    gulp.src(path.src.style)
        .pipe(sass({
            includePaths: [path.src.style],
            sourceMap: false,
            errLogToConsole: true
        }).on('error', sass.logError))
        .pipe(combineMq())
        .pipe(prefixer({browsers: ['last 9 version', 'IE 9', '> 1%']}))
        .pipe(cssmin({ restructuring: false }))
        .pipe(gulp.dest(path.build.style))
        .pipe(browserSync.stream());
});

gulp.task('js', function() {
    gulp.src(path.src.js)
        .pipe(rigger())
        .pipe(uglify().on('error', function(e){console.log(e.message)}))
        .pipe(gulp.dest(path.build.js))
        .pipe(browserSync.stream());
});

// Sync
gulp.task('init-sync', function () {
    browserSync.init({
        open: 'external',
        host: 'wordpress.local',
        proxy: 'wordpress.local',
    });
});

// Build
gulp.task('build', function (cb) {
    runSequence(
        [ 'style', 'js' ],
        cb
    );
});

// Watch
gulp.task('watch', function() {
    global.watch = true;
    gulp.watch([path.watch.style], function(event, cb) {
        gulp.start('style');
    });
    gulp.watch([path.watch.js], function(event, cb) {
        gulp.start('js');
    });
});

// Default
gulp.task('default', ['build', 'watch', 'init-sync']);