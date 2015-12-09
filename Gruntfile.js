module.exports = function (grunt) {

    'use strict'

    require('load-grunt-tasks')(grunt)

    grunt.initConfig({

        mochaTest: {
            test: {
                src: ['js/test/**/*.js']
            }
        },

        eslint: {
            options: {
                configFile: 'js/.eslintrc'
            },
            js: [
                'Gruntfile.js',
                'js/src/*.js',
                'js/test/*.js'
            ]
        },

        jscs: {
            options: {
                config: 'js/.jscsrc'
            },
            files: {
                src: [
                    'Gruntfile.js',
                    'js/src/*.js',
                    'js/test/*.js'
                ]
            }
        },

        codacy: {
            all: {
                src: ['build/lcov.info']
            }
        }
    })

    grunt.registerTask('test', ['eslint', 'jscs', 'mochaTest'])
}
