module.exports = function (grunt) {

	grunt.initConfig({
		pkg: grunt.file.readJSON("package.json"),

		clean: [ "dist/**" ],

		copy: {
			main: {
				files: [
					{
						src: ["./**", "!./node_modules/**", "!./Gruntfile.js", "!./package.json"],
						dest: "dist/<%= pkg.name %>/"
					}
				]
			}
		},

		compress: {
			options: {
				archive: "./dist/<%= pkg.name %>-<%= pkg.version %>.zip",
				mode: "zip"
			},
			all: {
				files: [{
					expand: true,
					cwd: "./dist/",
					src: [ "<%= pkg.name %>/**" ]
				}]
			}
		},

		jshint: {
			all: [
				"Gruntfile.js",
				"js/*.js",
				"!js/*.min.js"
			],
			options: {
				jshintrc: ".jshintrc",
				force: true
			}
		},

		uglify: {
			build: {
				options: {
					preserveComments: /^!/
				},
				files: [{
					expand: true,
					cwd: "js",
					dest: "js",
					src: [
						"*.js",
						"!*.min.js"
					],
					ext: '.min.js'
				}]
			}
		},

		pot: {
			options: {
				text_domain: "dps-pxpay-for-wp-ecommerce",
				msgid_bugs_address: "translate@webaware.com.au",
				encoding: "UTF-8",
				dest: "languages/",
				keywords: [
					"gettext",
					"__",
					"_e",
					"_n:1,2",
					"_x:1,2c",
					"_ex:1,2c",
					"_nx:4c,1,2",
					"esc_attr__",
					"esc_attr_e",
					"esc_attr_x:1,2c",
					"esc_html__",
					"esc_html_e",
					"esc_html_x:1,2c",
					"_n_noop:1,2",
					"_nx_noop:3c,1,2",
					"__ngettext_noop:1,2"
				],
				comment_tag: "translators:"
			},
			files: {
				src: [
					"**/*.php",
					"!dist/**/*",
					"!lib/**/*",
					"!node_modules/**/*"
				],
				expand: true
			}
		}

	});

	grunt.loadNpmTasks('grunt-contrib-clean');
	grunt.loadNpmTasks("grunt-contrib-compress");
	grunt.loadNpmTasks("grunt-contrib-copy");
	grunt.loadNpmTasks("grunt-contrib-jshint");
	grunt.loadNpmTasks("grunt-contrib-uglify");
	grunt.loadNpmTasks("grunt-pot");

	grunt.registerTask("release", ["clean","copy","compress"]);
	grunt.registerTask("default", [ "jshint" ]);

};
