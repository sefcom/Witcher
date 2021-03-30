
'use strict';

module.exports = function(grunt) {

  // Load grunt tasks automatically
  require('load-grunt-tasks')(grunt);

  // Time how long tasks take. Can help when optimizing build times
  require('time-grunt')(grunt);

  // Configurable paths for the application
  var app = require('./package.json');
  var appConfig = {
    name: app.name
  };

  // Define the configuration for all the tasks
  grunt.initConfig({

    // Project settings
    pkg: appConfig,

    watch: {
      bower: {
        files: ['bower.json'],
        tasks: ['build']
      },
      scss: {
        files: ['scss/**/*.scss'],
        tasks: ['sass:dev']
      },
      html: {
        files: ['app/{,*/}*.html'],
        options: {
          livereload: '<%= connect.options.livereload %>'
        }
      },
      css: {
        files: ['css/{,*/}*.css'],
        options: {
          livereload: '<%= connect.options.livereload %>'
        }
      },
      js: {
        files: ['js/{,*/}*.js'],
        options: {
          livereload: '<%= connect.options.livereload %>'
        }
      }
    },

    // The actual grunt server settings
    connect: {
      options: {
        port: 9000,
        open: true,
        livereload: 35729,
        // Change this to '0.0.0.0' to access the server from outside
        hostname: 'localhost'
      },
      templates: {
        options: {
          open: true,
          middleware: function (connect) {
            return [
              connect().use('/bower_components', connect.static('./bower_components')),
              connect().use('/lib', connect.static('./lib')),
              connect().use('/img', connect.static('./img')),
              connect().use('/css', connect.static('./css')),
              connect().use('/js', connect.static('./js')),
              connect().use('/app', connect.static('./app')),
              connect.static('app'),
            ];
          }
        }
      }
    },

    // Compiles scss files to css
    sass: {
      dist: {
        options: { style: 'compressed', sourcemap: 'none' },
        files: { 'css/amanda.min.css': 'scss/amanda.scss' }
      },
      dev: {
        options: { style: 'expanded', sourcemap: 'none' },
        files: {
          'css/amanda.css': 'scss/amanda.scss'
        }
      }
    },

    // Make sure code styles are up to par and there are no obvious mistakes
    jshint: {
      options: {
        jshintrc: '.jshintrc',
        reporter: require('jshint-stylish')
      },
      all: {
        src: [
          'Gruntfile.js',
          'js/**/*.js'
        ]
      }
    },

    bower: {
      install: {
        options: {
          cleanTargetDir: true,
          layout: 'byComponent'
        }
      }
    }
  });

  grunt.registerTask('default', [
    'sass',
    'connect',
    'watch'
  ]);

  grunt.registerTask('build', ['bower']);

};
