/**
*This is the default Gulpfile
*it contains all needed tasks
*/

var gulp 	= require('gulp'),
	less 	= require('gulp-less'),
	cssmin  = require('gulp-cssmin'),
	rename 	= require('gulp-rename'),
	coffee  = require('gulp-coffee'),
	uglify  = require('gulp-uglify'),
	clean 	= require('gulp-clean');

gulp.task('watch', function() {
	gulp.watch('./public/assets/less/**/*.less', ['less']);
	gulp.watch('./public/assets/coffee/**/*.coffee', ['coffee', 'compress', 'clean']);
});

gulp.task('less', function () {

  return gulp.src(['./public/assets/less/**/*.less', '!./public/assets/less/bootstrap/**/*.less'])
  .pipe(less().on('error', function (err) {
    console.log(err);
  }))
  .pipe(cssmin().on('error', function(err) {
    console.log(err);
  }))
  .pipe(rename({suffix: '.min'}))
  .pipe(gulp.dest('./public/dist/css'));

});

gulp.task('coffee',function() {
  return gulp.src('./public/assets/coffee/**/*.coffee')
    .pipe(coffee({bare: true}))
    .pipe(gulp.dest('./public/tmp'))
});

gulp.task('compress',['coffee'],function() {
  return gulp.src('./public/tmp/**/*.js')
    .pipe(uglify())
    .pipe(rename({suffix: '.min'}))
    .pipe(gulp.dest('./public/dist/js'))
});

gulp.task('clean', ['coffee', 'compress'] ,function() {
	 return gulp.src('./public/tmp/**/*.js')
        .pipe(clean({force: true, read: false}))
        .pipe(gulp.dest('./public/dist/js'));
    });
gulp.task('default', ['less','coffee', 'compress', 'clean' ,'watch']);
