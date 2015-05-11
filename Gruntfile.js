'use strict';
module.exports = function(grunt) {

  grunt.initConfig({
    jshint: {
      options: {
        jshintrc: '.jshintrc'
      },
      all: [
        'Gruntfile.js',
        'public/js/_*.js',
        '!public/js/base.min.js',
        'public/js/ng/common/guest/*.js',
        '!public/js/ng/guest.min.js'
      ]
    },
    copy: {
      main: {
        files: [{
            expand: true,
            cwd: 'bower_components/bootstrap/dist/fonts/',
            src: ['**'],
            dest: 'public/css/fonts/'
        }]
      }
    },
    less: {
      dist: {
        files: {
          'public/css/base.min.css': [
            'public/css/less/layout.less',
            'public/css/less/spacing_helpers.less',
            'public/css/less/animation_helpers.less'
          ],
          'public/css/splash.min.css': [
            'public/css/less/splash.less'
          ],
          'public/css/bootstrap/bootstrap.min.css': [
            'public/css/bootstrap/bootstrap.less'
          ]
        },
        options: {
          compress: true,
          // LESS source map
          // To enable, set sourceMap to true and update sourceMapRootpath based on your install
          sourceMap: false,
          sourceMapFilename: 'public/css/base.min.css.map',
          sourceMapRootpath: '/public/css/'
        }
      }
    },
    uglify: {
      dist: {
        files: {
          'public/js/base.min.js': [
            'public/js/_*.js'
          ],
          'public/js/ng/angular.min.js': [
            'bower_components/angular/angular.js',
            'bower_components/angular-animate/angular-animate.js',
            //'bower_components/angular-route/angular-route.js',
            'bower_components/angular-sanitize/angular-sanitize.js',
            'bower_components/underscore/underscore.js',
            'bower_components/restangular/dist/restangular.js',
            'bower_components/angular-ui-router/release/angular-ui-router.js',
          ],
          'public/js/ng/ui-bootstrap.min.js': [
            'bower_components/angular-bootstrap/ui-bootstrap.js',
            'bower_components/angular-bootstrap/ui-bootstrap-tpls.js'
          ],
          'public/js/ng/ng-flow-standalone.min.js': [
            'bower_components/ng-flow/dist/ng-flow-standalone.js'
          ],
          'public/js/ng/angular-ui-tinymce.min.js': [
            'bower_components/tinymce/tinymce.js',
            'bower_components/angular-ui-tinymce/src/tinymce.js'
          ],
          'public/js/ng/guest.min.js': [
            'public/js/ng/common/guest/*.js'
          ]
        },
        options: {
          // JS source map: to enable, uncomment the lines below and update sourceMappingURL based on your install
          // sourceMap: 'assets/js/scripts.min.js.map',
          // sourceMappingURL: '/app/themes/roots/assets/js/scripts.min.js.map'
        }
      }
    },
    concat: {
      acme: {
        src: [
          'public/js/ng/coming_soon/*.js',
          '!public/js/ng/coming_soon/build.js'
        ],
        dest: 'public/js/ng/coming_soon/build.js'
      }
      /*,
       angular: {
       dest: 'public/js/ng/angular.min.js',
       src: [
       'bower_components/angular/angular.js',
       'bower_components/angular-animate/angular-animate.js',
       'bower_components/angular-route/angular-route.js',
       'bower_components/angular-sanitize/angular-sanitize.js',
       'bower_components/underscore/underscore.js',
       'bower_components/restangular/dist/restangular.js',
       'bower_components/angular-ui-router/release/angular-ui-router.js',
       ]
       },
       tinymce: {
       src: [
       'bower_components/tinymce/timymce.min.js',
       'bower_components/angular-ui-tinymce/src/timymce.js',
       'public/js/ng/ui-tinymce.js'
       ]
       }*/
    },
    watch: {
      concat: {
        files: [
          'public/js/ng/*/*.js',
          '!public/js/ng/*/build.js',
          //'public/js/ng/.../*.js',
          //'!public/js/ng/.../build.js'
        ],
        tasks: ['concat']
      },
      less: {
        files: [
          'public/css/less/*.less',
          'public/css/bootstrap/*.less'
        ],
        tasks: ['less']
      },
      js: {
        files: [
          '<%= jshint.all %>'
        ],
        tasks: ['jshint', 'uglify']
      },
      livereload: {
        // Browser live reloading
        // https://github.com/gruntjs/grunt-contrib-watch#live-reloading
        options: {
          livereload: false
        },
        files: [
          'public/css/base.min.css',
          'public/js/base.min.js'
        ]
      }
    },
    clean: {
      dist: [
        'public/css/base.min.css',
        'public/js/base.min.js'
      ]
    }
  });


  // Load tasks
  grunt.loadNpmTasks('grunt-contrib-clean');
  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-less');
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-copy');

  // Register tasks
  grunt.registerTask('default', [
    'clean',
    'less',
    'uglify'
  ]);

  grunt.registerTask('dev', [
    'watch'
  ]);

};
