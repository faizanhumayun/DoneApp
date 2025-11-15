<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Invitation;
use App\Models\SignupPending;
use App\Models\User;
use App\Notifications\TeamInvitation;
use App\Notifications\VerifyEmailSignup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SignupFlowTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanViewEmailCaptureForm(): void
    {
        $response = $this->get(route('signup.email'));

        $response->assertStatus(200);
        $response->assertViewIs('signup.email');
    }

    public function testUserCanSubmitEmailAndReceiveVerificationEmail(): void
    {
        Notification::fake();

        $response = $this->post(route('signup.email.submit'), [
            'work_email' => 'test@example.com',
        ]);

        $response->assertRedirect(route('signup.check-email'));
        $response->assertSessionHas('email', 'test@example.com');

        $this->assertDatabaseHas('signup_pending', [
            'email' => 'test@example.com',
        ]);

        Notification::assertSentOnDemand(VerifyEmailSignup::class);
    }

    public function testUserCannotSubmitInvalidEmail(): void
    {
        $response = $this->post(route('signup.email.submit'), [
            'work_email' => 'invalid-email',
        ]);

        $response->assertSessionHasErrors('work_email');
        $this->assertDatabaseMissing('signup_pending', [
            'email' => 'invalid-email',
        ]);
    }

    public function testUserCannotSubmitExistingEmail(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->post(route('signup.email.submit'), [
            'work_email' => 'existing@example.com',
        ]);

        $response->assertSessionHasErrors('work_email');
    }

    public function testUserCanViewCheckEmailScreen(): void
    {
        $response = $this->withSession(['email' => 'test@example.com'])
            ->get(route('signup.check-email'));

        $response->assertStatus(200);
        $response->assertViewIs('signup.check-email');
        $response->assertSee('test@example.com');
    }

    public function testUserCanResendVerificationEmail(): void
    {
        Notification::fake();

        $signupPending = SignupPending::factory()->create([
            'email' => 'test@example.com',
        ]);

        $response = $this->post(route('signup.resend'), [
            'email' => 'test@example.com',
        ]);

        $response->assertRedirect(route('signup.check-email'));
        $response->assertSessionHas('resent', true);

        Notification::assertSentOnDemand(VerifyEmailSignup::class);
    }

    public function testUserCanVerifyEmailWithValidToken(): void
    {
        $signupPending = SignupPending::factory()->create([
            'email' => 'test@example.com',
            'verified_at' => null,
        ]);

        $response = $this->get(route('signup.verify', ['token' => $signupPending->token]));

        $response->assertRedirect(route('signup.profile'));
        $response->assertSessionHas('signup_email', 'test@example.com');

        $this->assertNotNull($signupPending->fresh()->verified_at);
    }

    public function testUserCannotVerifyEmailWithExpiredToken(): void
    {
        $signupPending = SignupPending::factory()->expired()->create([
            'email' => 'test@example.com',
        ]);

        $response = $this->get(route('signup.verify', ['token' => $signupPending->token]));

        $response->assertRedirect(route('signup.email'));
        $response->assertSessionHasErrors('token');
    }

    public function testUserCanViewProfileForm(): void
    {
        $response = $this->withSession(['signup_email' => 'test@example.com'])
            ->get(route('signup.profile'));

        $response->assertStatus(200);
        $response->assertViewIs('signup.profile');
        $response->assertSee('test@example.com');
    }

    public function testUserCanSubmitProfileAndCreateAccount(): void
    {
        SignupPending::factory()->verified()->create([
            'email' => 'test@example.com',
        ]);

        $response = $this->withSession(['signup_email' => 'test@example.com'])
            ->post(route('signup.profile.submit'), [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'password' => 'Password123!',
                'password_confirmation' => 'Password123!',
                'timezone' => 'America/New_York',
            ]);

        $response->assertRedirect(route('signup.company'));
        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'test@example.com',
            'timezone' => 'America/New_York',
        ]);
    }

    public function testUserCannotSubmitProfileWithMismatchedPasswords(): void
    {
        $response = $this->withSession(['signup_email' => 'test@example.com'])
            ->post(route('signup.profile.submit'), [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'password' => 'Password123!',
                'password_confirmation' => 'DifferentPassword123!',
                'timezone' => 'America/New_York',
            ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    public function testUserCanViewCompanyForm(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withSession(['signup_step_1_complete' => true])
            ->get(route('signup.company'));

        $response->assertStatus(200);
        $response->assertViewIs('signup.company');
    }

    public function testUserCanSubmitCompanyDetails(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withSession(['signup_step_1_complete' => true])
            ->post(route('signup.company.submit'), [
                'company_name' => 'Acme Inc.',
                'company_size' => '11-50',
                'industry_type' => 'Technology',
            ]);

        $response->assertRedirect(route('signup.team'));

        $this->assertDatabaseHas('companies', [
            'name' => 'Acme Inc.',
            'size' => '11-50',
            'industry' => 'Technology',
        ]);

        $company = Company::where('name', 'Acme Inc.')->first();
        $this->assertTrue($user->companies->contains($company));
        $this->assertEquals('owner', $user->companies->first()->pivot->role);
    }

    public function testUserCanViewTeamInvitationForm(): void
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();
        $company->users()->attach($user, ['role' => 'owner']);

        $response = $this->actingAs($user)
            ->withSession([
                'signup_step_1_complete' => true,
                'signup_step_2_complete' => true,
                'signup_company_id' => $company->id,
            ])
            ->get(route('signup.team'));

        $response->assertStatus(200);
        $response->assertViewIs('signup.team');
    }

    public function testUserCanSubmitTeamInvitations(): void
    {
        Notification::fake();

        $user = User::factory()->create(['email' => 'owner@example.com']);
        $company = Company::factory()->create();
        $company->users()->attach($user, ['role' => 'owner']);

        $response = $this->actingAs($user)
            ->withSession([
                'signup_step_1_complete' => true,
                'signup_step_2_complete' => true,
                'signup_company_id' => $company->id,
            ])
            ->post(route('signup.team.submit'), [
                'team_member_emails' => [
                    'teammate1@example.com',
                    'teammate2@example.com',
                ],
            ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('welcome', true);

        $this->assertDatabaseHas('invitations', [
            'company_id' => $company->id,
            'invited_email' => 'teammate1@example.com',
            'invited_by_user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('invitations', [
            'company_id' => $company->id,
            'invited_email' => 'teammate2@example.com',
            'invited_by_user_id' => $user->id,
        ]);

        Notification::assertCount(2);
        Notification::assertSentOnDemand(TeamInvitation::class);
    }

    public function testUserCanSkipTeamInvitations(): void
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();
        $company->users()->attach($user, ['role' => 'owner']);

        $response = $this->actingAs($user)
            ->withSession([
                'signup_step_1_complete' => true,
                'signup_step_2_complete' => true,
                'signup_company_id' => $company->id,
            ])
            ->post(route('signup.team.skip'));

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('welcome', true);

        $this->assertDatabaseMissing('invitations', [
            'company_id' => $company->id,
        ]);
    }

    public function testUserCannotInviteThemselves(): void
    {
        $user = User::factory()->create(['email' => 'owner@example.com']);
        $company = Company::factory()->create();
        $company->users()->attach($user, ['role' => 'owner']);

        $response = $this->actingAs($user)
            ->withSession([
                'signup_step_1_complete' => true,
                'signup_step_2_complete' => true,
                'signup_company_id' => $company->id,
            ])
            ->post(route('signup.team.submit'), [
                'team_member_emails' => [
                    'owner@example.com',
                ],
            ]);

        $response->assertSessionHasErrors('team_member_emails.0');
    }

    public function testCompleteSignupFlow(): void
    {
        Notification::fake();

        // Step 0: Email capture
        $this->post(route('signup.email.submit'), [
            'work_email' => 'newuser@example.com',
        ])->assertRedirect(route('signup.check-email'));

        $signupPending = SignupPending::where('email', 'newuser@example.com')->first();
        $this->assertNotNull($signupPending);

        // Email verification
        $this->get(route('signup.verify', ['token' => $signupPending->token]))
            ->assertRedirect(route('signup.profile'));

        // Step 1: Profile
        $this->post(route('signup.profile.submit'), [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
            'timezone' => 'America/Los_Angeles',
        ])->assertRedirect(route('signup.company'));

        $this->assertAuthenticated();
        $user = User::where('email', 'newuser@example.com')->first();
        $this->assertNotNull($user);

        // Step 2: Company
        $this->post(route('signup.company.submit'), [
            'company_name' => 'Tech Startup',
            'company_size' => '1-10',
            'industry_type' => 'Technology',
        ])->assertRedirect(route('signup.team'));

        $company = Company::where('name', 'Tech Startup')->first();
        $this->assertNotNull($company);

        // Step 3: Team invitations
        $this->post(route('signup.team.submit'), [
            'team_member_emails' => [
                'colleague@example.com',
            ],
        ])->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('invitations', [
            'company_id' => $company->id,
            'invited_email' => 'colleague@example.com',
        ]);

        // Verify cleanup
        $this->assertDatabaseMissing('signup_pending', [
            'email' => 'newuser@example.com',
        ]);
    }
}
