module.exports = function(grunt){
	grunt.loadNpmTasks('grunt-ember-templates');
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.initConfig({
		emberTemplates:{
			compile:{
				options:{
					templateName: function(fileName){
						if(fileName.match(/app/) !== null && fileName.match(/app/).length === 1 ){
							return fileName.replace('public/javascript/app/templates/','');
						}
					}
				},
				files:{
					"public/javascript/app/template.js": "public/javascript/app/templates/**/*.hbs"
				}
			}
		},

		watch:{
			files:['public/javascript/app/templates/**/*.hbs', 'public/javascript/app/**/*.js'],
			tasks:['build']
		},

		concat:{
			options:{
				separator: ';',
			},
			dist: {
				src: ['public/javascript/app/config/*.js', 'public/javascript/app/models/*.js','public/javascript/app/components/*.js','public/javascript/app/routes/*.js', 'public/javascript/app/controllers/*.js', 'public/javascript/app/views/*.js'],
				dest: 'public/javascript/app/logic.js'
			}
		},

		uglify:{
			options:{
				mangle:false,
				beautify: false,
			},

			dist:{
				files:{
					'public/javascript/app/app.min.js': ['public/javascript/app/template.js', 'public/javascript/app/logic.js']
				}
			}
		}

	});

	grunt.registerTask('default', ['watch']);
		grunt.registerTask('compileEmber', ['emberTemplates'] );
		grunt.registerTask('build', ['compileEmber','concat:dist', 'uglify:dist']);
};