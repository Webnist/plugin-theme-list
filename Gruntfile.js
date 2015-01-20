/* vim: set ft=javascript expandtab shiftwidth=2 tabstop=2: */

module.exports = function( grunt ) {

  // Project configuration
  grunt.initConfig( {
    pkg:    grunt.file.readJSON( 'package.json' ),
    jshint: {
      all: [
        'Gruntfile.js',
        'js/plugin-theme-list.js',
        'js/admin-plugin-theme-list.js',
        'js/test/**/*.js'
      ],
      options: {
        curly:   true,
        eqeqeq:  true,
        immed:   true,
        latedef: true,
        newcap:  true,
        noarg:   true,
        sub:     true,
        undef:   true,
        boss:    true,
        eqnull:  true,
                browser: true,
                devel:   true,
                jquery:  true,
        globals: {
          exports: true,
          module:  false
        }
      }
    },
    uglify: {
      all: {
        files: {
          
          'js/admin-plugin-theme-list.min.js': [
            'js/admin-plugin-theme-list.js'
          ],
          
          'js/plugin-theme-list.min.js': [
            'js/plugin-theme-list.js'
          ]
        },
        options: {
          banner: '/**\n' +
            ' * <%= pkg.title %> - v<%= pkg.version %>\n' +
            ' *\n' +
            ' * <%= pkg.homepage %>\n' +
            ' * <%= pkg.repository.url %>\n' +
            ' *\n' +
            ' * Copyright <%= grunt.template.today("yyyy") %>, <%= pkg.author.name %> (<%= pkg.author.url %>)\n' +
            ' * Released under the <%= pkg.license %>\n' +
            ' */\n',
          mangle: {
            except: ['jQuery']
          }
        }
      }
    },
    test:   {
      files: ['js/test/**/*.js']
    },
    
    sass:   {
      all: {
        options: {
          style: 'expanded',
          sourcemap: 'auto'
        },
        files: {
          'css/admin-plugin-theme-list.css': 'css/admin-plugin-theme-list.scss',
          'css/plugin-theme-list.css': 'css/plugin-theme-list.scss'
        }
      }
    },
    
    cssmin: {
      options: {
        banner: '/**\n' +
            ' * <%= pkg.title %> - v<%= pkg.version %>\n' +
            ' *\n' +
            ' * <%= pkg.homepage %>\n' +
            ' * <%= pkg.repository.url %>\n' +
            ' *\n' +
            ' * Copyright <%= grunt.template.today("yyyy") %>, <%= pkg.author.name %> (<%= pkg.author.url %>)\n' +
            ' * Released under the <%= pkg.license %>\n' +
            ' */\n'
      },
      minify: {
        expand: true,
        cwd: 'css/',
        src: [
          'admin-plugin-theme-list.css',
          'plugin-theme-list.css'
        ],
        dest: 'css/',
        ext: '.min.css'
      }
    },
    watch:  {
      
      sass: {
        files: ['css/*.scss'],
        tasks: ['sass', 'cssmin'],
        options: {
          debounceDelay: 500
        }
      },
      
      scripts: {
        files: ['js/*.js', '!js/*.min.js'],
        tasks: ['jshint', 'uglify'],
        options: {
          debounceDelay: 500
        }
      }
    }
  } );

  // Load other tasks
  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-cssmin');
  
  grunt.loadNpmTasks('grunt-contrib-sass');
  
  grunt.loadNpmTasks('grunt-contrib-watch');

  // Default task.
  
  grunt.registerTask('default', ['jshint', 'uglify', 'sass', 'cssmin']);
  

  grunt.util.linefeed = '\n';
};
