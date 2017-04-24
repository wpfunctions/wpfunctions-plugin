// To define:
// - Compress: ZIP file name
// - BrowserSync: site url

module.exports = function(grunt) {
  grunt.initConfig({
    sass: {
      dist: {
        options: {
          cacheLocation: 'sass/.sass-cache',
          style: 'compressed'
        },
        files: {
          'css/styles.css': 'sass/styles.sass',
          'css/admin-styles.css': 'sass/admin-styles.sass',
          'css/editor-styles.css': 'sass/editor-styles.sass'
        }
      }
    },
    postcss: {
      options: {
        map: true,
        map: {
          inline: false,
          annotation: 'dist/css/maps/'
        },
        processors: [
          require('pixrem')(),
          require('autoprefixer')({browsers: 'last 2 versions'}),
          require('cssnano')()
        ]
      },
      dist: {
        src: 'css/styles.css'
      }
    },
    watch: {
      files: ['sass/**', 'assets/**', 'add-ons/**', 'shortcodes/**'],
      tasks: ['sass'],
    },
    compress: {
      main: {
        options: {
          archive: '../wp-functions.zip'
        },
        files: [
          {src: ['**', '!.DS_Store', '!_design-development/**', '!.git/**', '!less/**', '!node_modules/**', '!.imdone/**', '!gruntfile.js', '!package.json', '!.bowerrc', '!.csscomb.json', '!.scss-lint.yml', '!bower.json', '!composer.json', '!composer.lock', '!composer.phar'], dest: ''},
        ]
      }
    }
  });

  // Register Tasks
  grunt.registerTask('sass', ['sass']);
  grunt.registerTask('dev', ['sass','postcss']);
  grunt.registerTask('prod', ['sass','postcss','compress']);

  // Load Tasks
  grunt.loadNpmTasks('grunt-contrib-sass');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-csscomb');
  grunt.loadNpmTasks('grunt-contrib-compress');
  grunt.loadNpmTasks('grunt-postcss');
  grunt.loadNpmTasks('grunt-browser-sync');
};
