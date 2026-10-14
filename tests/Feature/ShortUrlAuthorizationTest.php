<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ShortUrlAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_create_a_short_url(): void
    {
        $company = Company::create([
            'name' => 'Sembark Tech',
            'slug' => 'sembark-tech',
        ]);

        $member = User::factory()->create([
            'company_id' => $company->id,
            'role' => User::ROLE_MEMBER,
        ]);

        $response = $this->actingAs($member)->post(
            route('short-urls.store'),
            ['original_url' => 'https://example.com']
        );

        $response->assertRedirect(route('short-urls.index'));

        $this->assertDatabaseHas('short_urls', [
            'company_id' => $company->id,
            'user_id' => $member->id,
            'original_url' => 'https://example.com',
        ]);
    }

    public function test_super_admin_cannot_create_a_short_url(): void
    {
        $superAdmin = User::factory()->create([
            'company_id' => null,
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        $response = $this->actingAs($superAdmin)->post(
            route('short-urls.store'),
            ['original_url' => 'https://example.com']
        );

        $response->assertForbidden();

        $this->assertDatabaseMissing('short_urls', [
            'user_id' => $superAdmin->id,
            'original_url' => 'https://example.com',
        ]);
    }

    public function test_authenticated_user_can_view_dashboard_and_export_csv(): void
    {
        $company = Company::create([
            'name' => 'Sembark Tech',
            'slug' => 'sembark-tech',
        ]);

        $admin = User::factory()->create([
            'company_id' => $company->id,
            'role' => User::ROLE_ADMIN,
        ]);

        $dashboardResponse = $this->actingAs($admin)->get(route('dashboard'));
        $dashboardResponse->assertOk();

        $exportResponse = $this->actingAs($admin)->get(route('short-urls.export', ['period' => 'this_month']));
        $exportResponse->assertOk();
        $this->assertStringContainsString('text/csv', (string) $exportResponse->headers->get('content-type'));
    }

    public function test_super_admin_can_view_dashboard_with_invite_client_modal(): void
    {
        $superAdmin = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        $response = $this->actingAs($superAdmin)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('invite-client-modal');
        $response->assertSee('Invite New Client');
    }

    public function test_guest_can_view_invitation_form_and_accept(): void
    {
        $company = Company::create([
            'name' => 'Acme Corp',
            'slug' => 'acme-corp',
        ]);

        $inviter = User::factory()->create([
            'company_id' => $company->id,
            'role' => User::ROLE_ADMIN,
        ]);

        $token = Str::random(64);
        $invitation = Invitation::create([
            'company_id' => $company->id,
            'email' => 'invitee@example.com',
            'role' => User::ROLE_MEMBER,
            'token_hash' => hash('sha256', $token),
            'invited_by' => $inviter->id,
            'expires_at' => now()->addDays(7),
        ]);

        $response = $this->get(route('invitations.accept.form', ['token' => $token]));
        $response->assertOk();
        $response->assertSee('Acme Corp');
        $response->assertSee('invitee@example.com');
        $response->assertSee('Accept & Join');

        $acceptResponse = $this->post(route('invitations.accept', ['token' => $token]), [
            'name' => 'Invited Member',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $acceptResponse->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'email' => 'invitee@example.com',
            'company_id' => $company->id,
            'role' => User::ROLE_MEMBER,
        ]);
    }

    public function test_authenticated_user_can_view_invitation_form_and_accept(): void
    {
        $company = Company::create([
            'name' => 'Acme Corp',
            'slug' => 'acme-corp-auth-test',
        ]);

        $admin = User::factory()->create([
            'company_id' => $company->id,
            'role' => User::ROLE_ADMIN,
        ]);

        $token = \Illuminate\Support\Str::random(64);
        $invitation = Invitation::create([
            'company_id' => $company->id,
            'email' => 'auth_invitee@example.com',
            'role' => User::ROLE_MEMBER,
            'token_hash' => hash('sha256', $token),
            'invited_by' => $admin->id,
            'expires_at' => now()->addDays(7),
        ]);

        $response = $this->actingAs($admin)->get(route('invitations.accept.form', ['token' => $token]));
        $response->assertOk();
        $response->assertSee('Acme Corp');
        $response->assertSee('auth_invitee@example.com');
        $response->assertSee($admin->email);

        $acceptResponse = $this->actingAs($admin)->post(route('invitations.accept', ['token' => $token]), [
            'name' => 'New Member',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $acceptResponse->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertEquals('auth_invitee@example.com', auth()->user()->email);
    }

    public function test_admin_can_view_dashboard_with_invite_team_modal(): void
    {
        $company = Company::create([
            'name' => 'Acme Corp',
            'slug' => 'acme-corp',
        ]);

        $admin = User::factory()->create([
            'company_id' => $company->id,
            'role' => User::ROLE_ADMIN,
        ]);

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('invite-team-modal');
        $response->assertSee('Invite New Team Member');
    }

    public function test_user_can_view_dashboard_with_generate_short_url_modal_and_submit(): void
    {
        $company = Company::create([
            'name' => 'Acme Corp',
            'slug' => 'acme-corp',
        ]);

        $member = User::factory()->create([
            'company_id' => $company->id,
            'role' => User::ROLE_MEMBER,
        ]);

        $dashboardResponse = $this->actingAs($member)->get(route('dashboard'));
        $dashboardResponse->assertOk();
        $dashboardResponse->assertSee('generate-short-url-modal');
        $dashboardResponse->assertSee('Generate Short URL');

        $createResponse = $this->actingAs($member)->post(route('short-urls.store'), [
            'original_url' => 'https://example.com/popup-test',
            'redirect_to' => route('dashboard'),
        ]);

        $createResponse->assertRedirect(route('dashboard'));
        $createResponse->assertSessionHas('status', 'Short URL created successfully.');

        $this->assertDatabaseHas('short_urls', [
            'company_id' => $company->id,
            'user_id' => $member->id,
            'original_url' => 'https://example.com/popup-test',
        ]);
    }

    public function test_dashboard_ajax_filter_returns_json_html_and_export_url(): void
    {
        $company = Company::create([
            'name' => 'Acme Corp',
            'slug' => 'acme-corp',
        ]);

        $member = User::factory()->create([
            'company_id' => $company->id,
            'role' => User::ROLE_MEMBER,
        ]);

        $response = $this->actingAs($member)->getJson(route('dashboard', ['period' => 'today']), [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertOk();
        $response->assertJsonStructure(['html', 'exportUrl']);
        $this->assertStringContainsString('period=today', $response->json('exportUrl'));
    }

    public function test_super_admin_has_access_to_whole_system_data(): void
    {
        $company1 = Company::create(['name' => 'Company One', 'slug' => 'company-one']);
        $company2 = Company::create(['name' => 'Company Two', 'slug' => 'company-two']);

        $user1 = User::factory()->create(['company_id' => $company1->id, 'role' => User::ROLE_ADMIN, 'name' => 'Admin One']);
        $user2 = User::factory()->create(['company_id' => $company2->id, 'role' => User::ROLE_MEMBER, 'name' => 'Member Two']);

        $url1 = \App\Models\ShortUrl::create([
            'company_id' => $company1->id,
            'user_id' => $user1->id,
            'code' => 'comp1url',
            'original_url' => 'https://company1.com',
            'clicks' => 5,
        ]);

        $url2 = \App\Models\ShortUrl::create([
            'company_id' => $company2->id,
            'user_id' => $user2->id,
            'code' => 'comp2url',
            'original_url' => 'https://company2.com',
            'clicks' => 10,
        ]);

        $superAdmin = User::factory()->create(['company_id' => null, 'role' => User::ROLE_SUPER_ADMIN]);

        $response = $this->actingAs($superAdmin)->get(route('dashboard'));
        $response->assertOk();
        $response->assertSee('Company One');
        $response->assertSee('Company Two');
        $response->assertSee('Admin One');
        $response->assertSee('Member Two');
        $response->assertSee('https://company1.com');
        $response->assertSee('https://company2.com');
        $response->assertSee('System Users');

        // CSV export check
        $export = $this->actingAs($superAdmin)->get(route('short-urls.export', ['period' => 'all']));
        $export->assertOk();
        $content = $export->streamedContent();
        $this->assertStringContainsString('Company One', $content);
        $this->assertStringContainsString('Company Two', $content);
        $this->assertStringContainsString('Admin One', $content);
        $this->assertStringContainsString('Member Two', $content);
    }

    public function test_super_admin_can_only_create_company_and_invite_for_admin_role(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        // 1. Create company and invite admin
        $response = $this->actingAs($superAdmin)->post(route('invitations.company-admin'), [
            'company_name' => 'Brand New Corp',
            'email' => 'newadmin@brandnew.com',
        ]);
        $response->assertSessionHas('status', 'Company Admin invited.');

        $company = Company::where('name', 'Brand New Corp')->first();
        $this->assertNotNull($company);
        $this->assertDatabaseHas('invitations', [
            'company_id' => $company->id,
            'email' => 'newadmin@brandnew.com',
            'role' => User::ROLE_ADMIN,
        ]);

        // 2. Invite admin to existing company
        $response2 = $this->actingAs($superAdmin)->post(route('invitations.company-admin'), [
            'company_id' => $company->id,
            'email' => 'secondadmin@brandnew.com',
        ]);
        $response2->assertSessionHas('status', 'Company Admin invited.');
        $this->assertDatabaseHas('invitations', [
            'company_id' => $company->id,
            'email' => 'secondadmin@brandnew.com',
            'role' => User::ROLE_ADMIN,
        ]);

        // 3. Super admin cannot call team-invitations (which can invite members)
        $teamResponse = $this->actingAs($superAdmin)->post(route('invitations.team'), [
            'email' => 'member@brandnew.com',
            'role' => User::ROLE_MEMBER,
        ]);
        $teamResponse->assertForbidden();

        // 4. Super admin cannot create short URLs
        $urlResponse = $this->actingAs($superAdmin)->post(route('short-urls.store'), [
            'original_url' => 'https://example.com/blocked',
        ]);
        $urlResponse->assertForbidden();
    }

    public function test_admin_has_access_to_all_data_of_own_company_and_can_invite_admin_and_member(): void
    {
        $companyA = Company::create(['name' => 'Alpha Corp', 'slug' => 'alpha-corp']);
        $companyB = Company::create(['name' => 'Beta Corp', 'slug' => 'beta-corp']);

        $adminA = User::factory()->create(['company_id' => $companyA->id, 'role' => User::ROLE_ADMIN, 'name' => 'Alpha Admin']);
        $memberA = User::factory()->create(['company_id' => $companyA->id, 'role' => User::ROLE_MEMBER, 'name' => 'Alpha Member']);
        $userB = User::factory()->create(['company_id' => $companyB->id, 'role' => User::ROLE_MEMBER, 'name' => 'Beta Member']);

        $urlA = \App\Models\ShortUrl::create([
            'company_id' => $companyA->id,
            'user_id' => $memberA->id,
            'code' => 'alphaurl',
            'original_url' => 'https://alpha.example.com',
            'clicks' => 3,
        ]);

        $urlB = \App\Models\ShortUrl::create([
            'company_id' => $companyB->id,
            'user_id' => $userB->id,
            'code' => 'betaurl',
            'original_url' => 'https://beta.example.com',
            'clicks' => 7,
        ]);

        // Admin views company data on dashboard
        $response = $this->actingAs($adminA)->get(route('dashboard'));
        $response->assertOk();
        $response->assertSee('https://alpha.example.com');
        $response->assertDontSee('https://beta.example.com');
        $response->assertSee('Alpha Member');
        $response->assertDontSee('Beta Member');

        // Admin cannot see company list section
        $response->assertDontSee('invite-client-modal');

        // Admin can invite another admin for same company
        $inviteAdminResponse = $this->actingAs($adminA)->post(route('invitations.team'), [
            'email' => 'alpha2@alpha.example.com',
            'role' => User::ROLE_ADMIN,
        ]);
        $inviteAdminResponse->assertSessionHas('status', 'Team invitation sent.');
        $this->assertDatabaseHas('invitations', [
            'company_id' => $companyA->id,
            'email' => 'alpha2@alpha.example.com',
            'role' => User::ROLE_ADMIN,
        ]);

        // Admin can invite a member for same company
        $inviteMemberResponse = $this->actingAs($adminA)->post(route('invitations.team'), [
            'email' => 'alpha3@alpha.example.com',
            'role' => User::ROLE_MEMBER,
        ]);
        $inviteMemberResponse->assertSessionHas('status', 'Team invitation sent.');
        $this->assertDatabaseHas('invitations', [
            'company_id' => $companyA->id,
            'email' => 'alpha3@alpha.example.com',
            'role' => User::ROLE_MEMBER,
        ]);

        // Admin cannot create companies
        $createCompanyResponse = $this->actingAs($adminA)->post(route('invitations.company-admin'), [
            'company_name' => 'Illegal Company',
            'email' => 'illegal@example.com',
        ]);
        $createCompanyResponse->assertForbidden();
    }

    public function test_member_has_only_access_to_data_created_by_itself(): void
    {
        $company = Company::create(['name' => 'Delta Corp', 'slug' => 'delta-corp']);

        $admin = User::factory()->create(['company_id' => $company->id, 'role' => User::ROLE_ADMIN]);
        $member1 = User::factory()->create(['company_id' => $company->id, 'role' => User::ROLE_MEMBER]);
        $member2 = User::factory()->create(['company_id' => $company->id, 'role' => User::ROLE_MEMBER]);

        $urlAdmin = \App\Models\ShortUrl::create([
            'company_id' => $company->id,
            'user_id' => $admin->id,
            'code' => 'adminurl',
            'original_url' => 'https://admin-delta.com',
            'clicks' => 50,
        ]);

        $urlMember1 = \App\Models\ShortUrl::create([
            'company_id' => $company->id,
            'user_id' => $member1->id,
            'code' => 'm1url',
            'original_url' => 'https://member1-delta.com',
            'clicks' => 5,
        ]);

        $urlMember2 = \App\Models\ShortUrl::create([
            'company_id' => $company->id,
            'user_id' => $member2->id,
            'code' => 'm2url',
            'original_url' => 'https://member2-delta.com',
            'clicks' => 8,
        ]);

        // Member1 views dashboard: only sees their own URL
        $response = $this->actingAs($member1)->get(route('dashboard'));
        $response->assertOk();
        $response->assertSee('https://member1-delta.com');
        $response->assertDontSee('https://member2-delta.com');
        $response->assertDontSee('https://admin-delta.com');

        // Member1 cannot see team members or companies
        $response->assertDontSee('Team Members');
        $response->assertDontSee('invite-team-modal');
        $response->assertDontSee('invite-client-modal');

        // Member1 cannot invite anyone
        $this->actingAs($member1)->post(route('invitations.team'), [
            'email' => 'test@delta.com',
            'role' => User::ROLE_MEMBER,
        ])->assertForbidden();

        $this->actingAs($member1)->post(route('invitations.company-admin'), [
            'company_name' => 'Test',
            'email' => 'test@delta.com',
        ])->assertForbidden();

        // Policy checks
        $policy = new \App\Policies\ShortUrlPolicy();
        $this->assertTrue($policy->view($member1, $urlMember1));
        $this->assertFalse($policy->view($member1, $urlMember2));
        $this->assertFalse($policy->view($member1, $urlAdmin));
    }

    public function test_short_url_redirects_to_original_url_and_increments_clicks(): void
    {
        $company = Company::create(['name' => 'Redirect Co', 'slug' => 'redirect-co']);
        $user = User::factory()->create(['company_id' => $company->id, 'role' => User::ROLE_MEMBER]);

        $shortUrl = \App\Models\ShortUrl::create([
            'company_id' => $company->id,
            'user_id' => $user->id,
            'code' => 'TESTXYZ',
            'original_url' => 'https://example.com/target-destination',
            'clicks' => 0,
        ]);

        $response = $this->get(route('short-urls.redirect', ['code' => 'TESTXYZ']));

        $response->assertRedirect('https://example.com/target-destination');
        $this->assertEquals(1, $shortUrl->fresh()->clicks);

        // Click again
        $this->get(route('short-urls.redirect', ['code' => 'TESTXYZ']));
        $this->assertEquals(2, $shortUrl->fresh()->clicks);
    }

    public function test_short_url_redirect_returns_404_for_invalid_code(): void
    {
        $response = $this->get(route('short-urls.redirect', ['code' => 'NONEXISTENT']));
        $response->assertNotFound();
    }
}