<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\KaizenProject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class KaizenWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $manager;
    protected $chairman;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create Roles
        $rUser = Role::create(['role_name' => 'user']);
        $rManager = Role::create(['role_name' => 'manager']);
        $rChairman = Role::create(['role_name' => 'chairman']);

        // Create Users
        $this->user = User::factory()->create(['role_id' => $rUser->id]);
        $this->manager = User::factory()->create(['role_id' => $rManager->id]);
        $this->chairman = User::factory()->create(['role_id' => $rChairman->id]);
    }

    public function test_full_kaizen_workflow()
    {
        // 1. User Submits
        $response = $this->actingAs($this->user)
            ->post(route('activities.store'), [
                'title' => 'Test Kaizen',
                'problem' => 'Test Problem',
                'improvement' => 'Test Improvement',
                'result' => 'Test Result',
                'improvement_types' => ['reduce_expenses', 'reduce_time'],
            ]);
        
        $response->assertRedirect(route('activities.index'));
        $this->assertDatabaseHas('kaizen_projects', ['title' => 'Test Kaizen', 'status' => 'pending']);
        $project = KaizenProject::where('title', 'Test Kaizen')->first();

        // 2. Manager Approves Proposal (Round 1)
        $response = $this->actingAs($this->manager)
            ->post(route('activities.updateStatus', $project->id), [
                'status' => 'approved',
                'note' => 'Looks good'
            ]);
        
        $response->assertStatus(200);
        $this->assertEquals('in_progress', $project->fresh()->status);

        // 3. User Reports Result
        $response = $this->actingAs($this->user)
            ->post(route('activities.saveReport', $project->id), [
                'result' => 'Actual Result Updated',
            ]);
        
        $response->assertRedirect(route('activities.status'));
        $this->assertEquals('waiting_for_result_approval', $project->fresh()->status);
        $this->assertEquals('Actual Result Updated', $project->fresh()->result);

        // 4. Manager Approves Result (Round 2)
        $response = $this->actingAs($this->manager)
            ->post(route('activities.updateStatus', $project->id), [
                'status' => 'approved',
            ]);
        
        $this->assertEquals('waiting_for_chairman_approval', $project->fresh()->status);

        // 5. Chairman Approves (Final)
        $response = $this->actingAs($this->chairman)
            ->post(route('activities.updateStatus', $project->id), [
                'status' => 'approved',
            ]);
        
        $this->assertEquals('completed', $project->fresh()->status);
    }
}
