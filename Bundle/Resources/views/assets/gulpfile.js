/* eslint no-var: off */
var gulp = require('gulp');
var uglify = require('gulp-uglify');
var rename = require('gulp-rename');
var csso = require('gulp-csso');
gulp.task('default', function() {
    gulp.src('src/index.js')
        .pipe(uglify())
        .pipe(rename('index.min.js'))
        .pipe(gulp.dest('./dist'));

    gulp.src('src/style.css')
        .pipe(csso())
        .pipe(rename('style.min.css'))
        .pipe(gulp.dest('./dist'));
});
