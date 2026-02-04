const gulp = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const postcss = require('gulp-postcss');
const autoprefixer = require('autoprefixer');
const cssnano = require('cssnano');
const terser = require('gulp-terser');
const concat = require('gulp-concat');
const sourcemaps = require('gulp-sourcemaps');
const imagemin = require('gulp-imagemin');

const paths = {
    styles: {
        src: 'src/assets/scss/**/*.scss',
        dest: 'dist/'
    },
    scripts: {
        src: 'src/assets/scripts/**/*.js',
        dest: 'dist/'
    },
    images: {
        src: 'src/assets/images/**/*',
        dest: 'dist/images/'
    }
};

function styles() {
    return gulp.src(paths.styles.src)
        .pipe(sourcemaps.init())
        .pipe(sass().on('error', sass.logError))
        .pipe(postcss([autoprefixer(), cssnano()]))
        .pipe(concat('theme.css'))
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest(paths.styles.dest));
}

function scripts() {
    return gulp.src(paths.scripts.src)
        .pipe(sourcemaps.init())
        .pipe(concat('theme.js'))
        .pipe(terser())
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest(paths.scripts.dest));
}

function images() {
    return gulp.src(paths.images.src, { allowEmpty: true, encoding: false })
        .pipe(imagemin([
            imagemin.gifsicle({ interlaced: true }),
            imagemin.mozjpeg({ quality: 80, progressive: true }),
            imagemin.optipng({ optimizationLevel: 5 }),
            imagemin.svgo({
                plugins: [
                    { name: 'removeViewBox', active: false },
                    { name: 'cleanupIDs', active: false }
                ]
            })
        ], { verbose: true }))
        .pipe(gulp.dest(paths.images.dest));
}

function watchFiles() {
    gulp.watch(paths.styles.src, styles);
    gulp.watch(paths.scripts.src, scripts);
}

const build = gulp.parallel(styles, scripts, images);
const watch = gulp.series(build, watchFiles);

exports.styles = styles;
exports.scripts = scripts;
exports.images = images;
exports.build = build;
exports.watch = watch;
exports.default = build;
