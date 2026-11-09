<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Project;
use App\Models\User;
use Database\Seeders\AdminPanelUserSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Memastikan panel admin AdminLTE terkunci oleh peran dan hak akses Spatie.
 */
class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    private function userWithRole(?string $role): User
    {
        $user = User::factory()->create();

        if ($role !== null) {
            $user->assignRole($role);
        }

        return $user;
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin')->assertRedirect('/login');
    }

    public function test_user_without_role_is_forbidden(): void
    {
        $this->actingAs($this->userWithRole(null))->get('/admin')->assertForbidden();
    }

    public function test_dashboard_shows_count_widgets(): void
    {
        User::factory()->has(Project::factory()->count(3))->has(Assignment::factory()->count(4))->create();

        $this->actingAs($this->userWithRole('Staf'))
            ->get('/admin')
            ->assertOk()
            ->assertSee('Proposal Agentic AI')
            ->assertSee('Riwayat Tugas')
            ->assertSee('Mahasiswa Terdaftar');
    }

    public function test_admin_and_staff_are_redirected_to_panel_after_login(): void
    {
        $admin = $this->userWithRole('Admin');

        $this->post('/login', ['email' => $admin->email, 'password' => 'password'])
            ->assertRedirect(route('admin.dashboard', absolute: false));
    }

    public function test_delete_button_only_rendered_for_admin(): void
    {
        Project::factory()->for(User::factory())->create();

        $this->actingAs($this->userWithRole('Admin'))->get('/admin/proposal')->assertSee('Hapus Data');
        $this->actingAs($this->userWithRole('Staf'))->get('/admin/proposal')->assertDontSee('Hapus Data');
    }

    public function test_staff_cannot_delete_via_direct_request(): void
    {
        $project = Project::factory()->for(User::factory())->create();
        $assignment = Assignment::factory()->for(User::factory())->create();
        $staf = $this->userWithRole('Staf');

        $this->actingAs($staf)->delete(route('admin.proposal.destroy', $project))->assertForbidden();
        $this->actingAs($staf)->delete(route('admin.tugas.destroy', $assignment))->assertForbidden();

        $this->assertModelExists($project);
        $this->assertModelExists($assignment);
    }

    public function test_admin_can_delete_data(): void
    {
        $project = Project::factory()->for(User::factory())->create();

        $this->actingAs($this->userWithRole('Admin'))
            ->from('/admin/proposal')
            ->delete(route('admin.proposal.destroy', $project))
            ->assertRedirect('/admin/proposal');

        $this->assertModelMissing($project);
    }

    public function test_search_filters_rows_and_pagination_keeps_keyword(): void
    {
        $owner = User::factory()->create();
        Project::factory()->for($owner)->create(['judul' => 'Agen Penjadwal Kuliah']);
        Project::factory()->for($owner)->count(12)->create(['judul' => 'Agen Penjadwal Lain']);
        Project::factory()->for($owner)->create(['judul' => 'Chatbot Perpustakaan']);

        $this->actingAs($this->userWithRole('Staf'))
            ->get('/admin/proposal?q=Penjadwal')
            ->assertOk()
            ->assertDontSee('Chatbot Perpustakaan')
            ->assertSee('q=Penjadwal&amp;page=2', false);
    }

    public function test_staff_cannot_open_user_management(): void
    {
        $this->actingAs($this->userWithRole('Staf'))->get('/admin/pengguna')->assertForbidden();
    }

    public function test_admin_can_change_role_but_not_remove_own_admin_role(): void
    {
        $admin = $this->userWithRole('Admin');
        $target = $this->userWithRole(null);

        $this->actingAs($admin)->patch(route('admin.pengguna.update', $target), ['peran' => 'Staf']);
        $this->assertTrue($target->fresh()->hasRole('Staf'));

        $this->actingAs($admin)
            ->patch(route('admin.pengguna.update', $admin), ['peran' => 'Staf'])
            ->assertSessionHasErrors('peran');
        $this->assertTrue($admin->fresh()->hasRole('Admin'));
    }

    public function test_demo_login_only_accepts_registered_accounts(): void
    {
        config(['demo.enabled' => true]);
        $this->seed(AdminPanelUserSeeder::class);

        $this->post('/demo-login/staf')->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs(User::where('email', 'staf@its.ac.id')->first());
    }

    public function test_demo_login_is_disabled_by_config(): void
    {
        config(['demo.enabled' => false]);

        $this->post('/demo-login/admin')->assertNotFound();
        $this->assertGuest();
    }
}
