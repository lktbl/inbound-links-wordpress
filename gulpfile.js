var gulp = require('gulp');
var sass = require('gulp-sass');
var zip = require('gulp-zip');

// keeps gulp from crashing for scss errors
gulp.task('sass', function () {
  return gulp.src('./sass/*.scss')
      .pipe(sass().on('error', sass.logError))
      .pipe(gulp.dest('./css'));
});

gulp.task('watch', function () {
  gulp.watch('./sass/*.scss', ['sass']);
});

// gulp.task('create-zip', () =>
//     gulp.src(['./assets/**/*', './css/**/*', './js/**/*', './inc/**/*', './admin-page.php', './nopassword.php', './readme.txt'], {base: '.'})
//         .pipe(zip('nopassword.zip'))
//         .pipe(gulp.dest('.'))
// );

gulp.task('css', [ 'watch', 'sass']);
gulp.task('build', ['sass', 'create-zip']);
gulp.task('default', ['build']);
