module.exports = function( grunt ) {
	
	grunt.initConfig({
	  wp_plugin: {
	    deploy: {
	      options: {
	        assets_dir: 'assets',   // Relative path to your assets directory (optional).
	        deploy_dir: 'build',   // Relative path to your deploy directory (required).
	        plugin_slug: 'force-delete-plugins',
	        svn_username: 'jancbeck'
	      }
	    }
	  },
	  watch: {
			files: ['build/**/*', 'tests/**/*'],
			tasks: ['phpunit'],
			options: {
	      debounceDelay: 2000,
	    },
	  },
	  phpunit: {
      classes: {} 
    }
	});

	grunt.loadNpmTasks( 'grunt-wp-plugin' );
	grunt.loadNpmTasks( 'grunt-contrib-watch' );
	grunt.loadNpmTasks( 'grunt-phpunit' );

	// Register tasks
  grunt.registerTask('tests', [
    'phpunit'
  ]);

};

