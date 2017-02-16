var gulp = require('gulp'),
    uglify = require('gulp-uglify'),
    jshint = require('gulp-jshint'),
    concat = require('gulp-concat'),
    cleanCSS = require('gulp-clean-css'),
    sass = require('gulp-sass');


gulp.task('compress-sass', function() {
    return gulp.src('public/raw-assets/sass/main.scss')
        .pipe(sass.sync().on('error', sass.logError))
        .pipe(cleanCSS({ compatibility: 'ie8' }))
        .pipe(concat('app.min.css'))
        .pipe(gulp.dest('public/css'));
});

// gulp.task('compress-js', function(cb) {
//     return gulp.src([
//             'public/lib/angular/angular.min.js',
//             'public/lib/angular-ui-router/release/angular-ui-router.min.js',
//             'public/js/app.js',
//             'public/js/factory/accountFactory.js',
//             'public/js/routes/userRoute.js',
//             'public/js/controllers/homeCtrl.js',
//             'public/js/controllers/userCtrl.js'
//         ])
//         .pipe(jshint())
//         .pipe(jshint.reporter('default'))
//         .pipe(uglify())
//         .pipe(concat('app.min.js'))
//         .pipe(gulp.dest('public/js'));
// });

gulp.task('watch', function() {
    gulp.watch('public/raw-assets/**/*.scss', ['compress-sass']);
})


gulp.task('default', ['compress-sass', 'watch']);