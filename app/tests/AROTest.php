<?php
use \Mockery;

use Acme\Service\ACL\ARO;

class AROTest extends TestCase
{
	
	public function setUp() {
		parent::setUp();
	}
	
	public function tearDown() {
		parent::tearDown();
	}
	
	public function testUserHasPermission() {
		$roles = [['user_id' => 1, 'role' => 'admin']];
		
		$a = new ARO($roles, $this->rules());

		$this->assertTrue($a->can('tasks.create'));
		$this->assertFalse($a->can('pee.bucket'));
	}

	public function testUserInsufficientPermission() {
		$roles = [['user_id' => 1, 'role' => 'agent']];
		
		$a = new ARO($roles, $this->rules());

		$this->assertFalse($a->can('users.create_system_user'));
		$this->assertTrue($a->can('interviews.create'));
		$this->assertFalse($a->can('pee.bucket'));
	}
	
	public function testMergePermissions() {
		$roles = [['user_id' => 1, 'role' => 'admin']];
		
		$a = new ARO($roles, $this->rules());
		
		$merged = $a->mergePermissions($this->rules(), $roles);
		$this->assertContains('candidates.index', $merged);
		$this->assertContains('clients.edit', $merged);
	}
	
	public function rules() {
		return [
		'admin' => ['users.create_system_user', 'users.update_system_user', 'candidate_questions.*', 'tenant_options.*', 'candidate_options.*', 'specialties.*', 'languages.*', 'merge:manager', ], 
		'manager' => [
		
		//users
		'users.create_system_agent', 'users.deactivate_system_agent',
		
		// placements
		'placements.*',
		
		// tasks
		'tasks.*',
		
		// interviews
		'interviews.*',
		
		// notes
		'interview_notes.*', 'candidate_notes.*', 'placement_notes.*', 'incident_notes.*', 'client_notes.*', 'merge:agent', ], 
		'agent' => [
		
		// interviews
		'interviews.create', 'interviews.delete', 'interviews.edit',
		
		// notes
		'candidate_notes.create', 'placement_notes.create', 'client_notes.create', 'interview_notes.create', 'incident_notes.create',
		
		// candidates
		'candidates.index', 'candidates.read', 'candidates.edit', 'candidate_blocks.*',
		
		// placements
		'placements.create', 'placements.edit', 'placements.index',
		
		// clients
		'clients.read', 'clients.edit', 'clients.index',
		
		// incidents
		'incidents.create', 'incidents.read', 'incidents.index', ], 
		'candidate' => ['candidates.dashboard', ], 
		'client' => ['clients.dashboard']];
	}
}
