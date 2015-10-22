module.exports = function(grunt) {

	// Project configuration.
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		concat: {
			options: {
				stripBanners: true,
				banner: '/*! <%= pkg.title %> - v<%= pkg.version %> - <%= grunt.template.today("yyyy-mm-dd") %>\n' +
					' * <%= pkg.homepage %>\n' +
					' * Copyright (c) <%= grunt.template.today("yyyy") %>;' +
					' * Licensed GPLv2+' +
					' */\n' +
					' jQuery(\'document\').ready( function ($){\n',
				footer: '});'
			},
			dist: {
      			src: 'js/dev/*.js',
      			dest: 'js/mjj-comment-ratings.js',
    		}
		},
		uglify: {
			options: {
				banner: '/*! <%= pkg.name %> <%= grunt.template.today("yyyy-mm-dd") %> */\n'
			},
			build: {
				expand: true,
				ext: '.min.js',
          		cwd: 'js',
				src: '*.js',
				dest: 'js'
			}
		},
		sass: {
			build: {
    			files: {
    			  	'css/mjj-comment-ratings.css': 'css/scss/mjj-comment-ratings.scss'
    			}
			}
		},
		watch: {
			sass: {
				files: ['css/scss/*.scss'],
				tasks: ['sass'],
				options: {
					debounceDelay: 500
				}
			},

			scripts: {
				files: ['js/dev/*.js'],
				tasks: ['concat', 'uglify'],
				options: {
					debounceDelay: 500
				}
			}
		}
	});

	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-sass');
	grunt.loadNpmTasks('grunt-contrib-watch');

	// Default task(s).
	grunt.registerTask('default', ['concat', 'uglify', 'sass']);

};
