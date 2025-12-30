import gulp from 'gulp';
import uglify from 'gulp-uglify';
import rename from 'gulp-rename';
import csso from 'gulp-csso';

function scripts() {
    return gulp.src('src/index.js')
        .pipe(uglify())
        .pipe(rename('index.min.js'))
        .pipe(gulp.dest('./dist'));
}

function styles() {
    return gulp.src('src/style.css')
        .pipe(csso())
        .pipe(rename('style.min.css'))
        .pipe(gulp.dest('./dist'));
}

export default gulp.parallel(scripts, styles);
