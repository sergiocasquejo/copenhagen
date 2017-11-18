var gulp = require('gulp'),
    uglify = require('gulp-uglify'),
    jshint = require('gulp-jshint'),
    concat = require('gulp-concat'),
    cleanCSS = require('gulp-clean-css'),
    sass = require('gulp-sass');


gulp.task('fonts', function() {
    return gulp.src('fonts/**/*')
        // .pipe(flatten())
        .pipe(gulp.dest('public/dist/fonts'));
    // .pipe(browserSync.stream());
});

gulp.task('compress-sass', function() {
    return gulp.src([
            'public/raw-assets/lib/bootstrap/dist/css/bootstrap.min.css',
            'public/raw-assets/lib/font-awesome.min.css',
            'public/raw-assets/lib/angular-bootstrap-toggle/dist/angular-bootstrap-toggle.min.css',
            'public/raw-assets/lib/angular-moment-picker/dist/angular-moment-picker.min.css',
            'public/raw-assets/lib/angular-bootstrap-calendar/dist/css/angular-bootstrap-calendar.min.css',
            'public/raw-assets/lib/angularjs-slider/dist/rzslider.min.css',
            'public/raw-assets/lib/angular-bootstrap-lightbox/dist/angular-bootstrap-lightbox.min.css',
            'public/raw-assets/lib/textangular/dist/textAngular.css',
            'public/raw-assets/sass/main.scss'
        ])
        .pipe(sass.sync().on('error', sass.logError))
        .pipe(cleanCSS({ compatibility: 'ie8' }))
        .pipe(concat('app.min.css'))
        .pipe(gulp.dest('public/dist/styles/'));
});

gulp.task('compress-js', function(cb) {
    return gulp.src([
            'public/raw-assets/lib/angular/angular.min.js',
            'public/raw-assets/lib/angular-ui-router/release/angular-ui-router.min.js',
            'public/raw-assets/lib/angular-file-upload/dist/angular-file-upload.min.js',
            'public/raw-assets/lib/angular-bootstrap-toggle/dist/angular-bootstrap-toggle.min.js',
            'public/raw-assets/lib/angular-bootstrap/ui-bootstrap-tpls.min.js',
            'public/raw-assets/lib/moment/moment.js',
            'public/raw-assets/lib/angular-moment-picker/dist/angular-moment-picker.min.js',
            'public/raw-assets/lib/angular-bootstrap-calendar/dist/js/angular-bootstrap-calendar-tpls.min.js',
            'public/raw-assets/lib/ng-country-select/dist/ng-country-select.min.js',
            'public/raw-assets/lib/angular-animate/angular-animate.min.js',
            'public/raw-assets/lib/angularjs-slider/dist/rzslider.min.js',
            'public/raw-assets/lib/angular-bootstrap-lightbox/dist/angular-bootstrap-lightbox.min.js',
            'public/raw-assets/lib/angular-cookies/angular-cookies.min.js',
            'public/raw-assets/lib/textangular/dist/textAngular-rangy.min.js',
            'public/raw-assets/lib/textangular/dist/textAngular-sanitize.min.js',
            'public/raw-assets/lib/textangular/dist/textAngular.min.js',
            'public/raw-assets/scripts/app.js',
            'public/raw-assets/scripts/routes/index.js',
            'public/raw-assets/scripts/services/api.js',
            'public/raw-assets/scripts/controllers/auth.js',
            'public/raw-assets/scripts/controllers/home.js',
            'public/raw-assets/scripts/controllers/admin.js',
            'public/raw-assets/scripts/controllers/rate.js',
            'public/raw-assets/scripts/controllers/room.js',
            'public/raw-assets/scripts/controllers/calendar.js',
            'public/raw-assets/scripts/controllers/bookings.js',
            'public/raw-assets/scripts/controllers/page.js',
        ])
        .pipe(jshint())
        .pipe(jshint.reporter('default'))
        .pipe(uglify({ mangle: false }))
        .pipe(concat('app.min.js'))
        .pipe(gulp.dest('public/dist/scripts'));
});

gulp.task('watch', function() {
    gulp.watch('public/raw-assets/**/*.scss', ['compress-sass']);
    gulp.watch('public/raw-assets/**/*.js', ['compress-js']);
})


gulp.task('default', ['fonts', 'compress-js', 'compress-sass', 'watch']);