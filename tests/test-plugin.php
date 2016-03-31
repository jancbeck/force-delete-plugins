<?php

class ForceDeletePluginsTests extends WP_UnitTestCase {

	protected static $users    = array();
	protected static $user_can = true;
	protected static $nonce    = true;
	protected static $action   = 'delete-selected';
	protected static $plugins  = array( 'hello-dolly.php' );

	public static function wpSetUpBeforeClass( $factory ) {
		self::$users = array(
			'administrator' => $factory->user->create_and_get( array( 'role' => 'administrator' ) ),
			'subscriber'    => $factory->user->create_and_get( array( 'role' => 'subscriber' ) ),
		);
	}

	public static function wpTearDownAfterClass() {
		foreach ( self::$users as $role => $user ) {
			self::delete_user( $user->ID );
		}
	}

	function setUp() {
		parent::setUp();
		// keep track of users we create
		$this->_flush_roles();

	}

	function _flush_roles() {
		// we want to make sure we're testing against the db, not just in-memory data
		// this will flush everything and reload it from the db
		unset($GLOBALS['wp_user_roles']);
		global $wp_roles;
		if ( is_object( $wp_roles ) )
			$wp_roles->_init();
	}

	function test_user_caps() {
		if ( is_multisite()) {
			return;
		}
		$user_can = user_can( self::$users['administrator'], 'activate_plugins' );
		$this->assertTrue( can_force_delete_plugins( $user_can, self::$nonce, self::$action, self::$plugins ) );

		$user_can = user_can( self::$users['subscriber'], 'activate_plugins' );
		$can_delete = can_force_delete_plugins( $user_can, self::$nonce, self::$action, self::$plugins );
		
		$this->assertInstanceOf( 'WP_Error', $can_delete );

		if ( is_wp_error( $can_delete ) ) {
			$this->assertEquals( 'user_can', $can_delete->get_error_code() );
		}
	}

	function test_actions() {
		$action = 'delete-selected';
		$this->assertTrue( can_force_delete_plugins( self::$user_can, self::$nonce, $action, self::$plugins ) );

		$action = 'deactivate';
		$can_delete = can_force_delete_plugins( self::$user_can, self::$nonce, $action, self::$plugins );
		$this->assertInstanceOf( 'WP_Error', $can_delete );

		if ( is_wp_error( $can_delete ) ) {
			$this->assertEquals( 'action', $can_delete->get_error_code() );
		}
	}

	function test_plugins() {
		$plugins = array( 'hello-dolly.php' );
		$this->assertTrue( can_force_delete_plugins( self::$user_can, self::$nonce, self::$action, $plugins ) );

		$plugins = array();
		$can_delete = can_force_delete_plugins( self::$user_can, self::$nonce, self::$action, $plugins );
		$this->assertInstanceOf( 'WP_Error', $can_delete );

		if ( is_wp_error( $can_delete ) ) {
			$this->assertEquals( 'plugins', $can_delete->get_error_code() );
		}

		$can_delete = can_force_delete_plugins( self::$user_can, self::$nonce, self::$action );
		$this->assertInstanceOf( 'WP_Error', $can_delete );

		if ( is_wp_error( $can_delete ) ) {
			$this->assertEquals( 'plugins', $can_delete->get_error_code() );
		}
	}
}

