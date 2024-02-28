// ES Module imports
import gulp from 'gulp';
import sassModule from 'gulp-sass';
import autoprefixer from 'gulp-autoprefixer';
import cleanCSS from 'gulp-clean-css';
import rename from 'gulp-rename';
import * as sass from 'sass';

const sassCompiler = sassModule(sass);

function compileSass() {
  return gulp.src('./sass/main.scss')
    .pipe(sassCompiler().on('error', sassCompiler.logError))
    .pipe(autoprefixer())
    .pipe(cleanCSS({compatibility: 'ie8'}))
    .pipe(rename('style.css'))
    .pipe(gulp.dest('./'));
}

function watch() {
  gulp.watch('./sass/**/*.scss', compileSass);
}

// Exporting tasks
export {
  compileSass,
  watch
};
