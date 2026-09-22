<?php

namespace Tests\Feature;

use App\Filament\Resources\TeamMembers\Pages\CreateTeamMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * An upload field is a form too, and the file it takes is served back to
 * visitors from this site's own origin.
 *
 * An SVG is a document rather than a picture. It can carry script, and that
 * script runs as this site when somebody opens the file. Filament's image()
 * does not stop it: the check is mimetypes:image/*, and image/svg+xml matches
 * that perfectly well.
 */
class UploadSafetyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        $this->actingAs(User::factory()->create());
    }

    public function test_an_svg_carrying_a_script_is_refused(): void
    {
        $svg = UploadedFile::fake()->createWithContent(
            'portrait.svg',
            '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(document.cookie)</script></svg>',
        );

        Livewire::test(CreateTeamMember::class)
            ->fillForm(['name' => 'Ada Example', 'photo' => [$svg]])
            ->call('create')
            ->assertHasFormErrors(['photo']);

        $this->assertDatabaseCount('team_members', 0);
    }

    public function test_an_svg_renamed_to_look_like_a_png_is_still_refused(): void
    {
        // Filament reports the type of the file that arrived, so a rename
        // alone does not get past it. Asserted because the rule checks the
        // extension too, and that check must not be the only one working.
        $svg = UploadedFile::fake()->createWithContent(
            'portrait.png',
            '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>',
        );

        Livewire::test(CreateTeamMember::class)
            ->fillForm(['name' => 'Ada Example', 'photo' => [$svg]])
            ->call('create')
            ->assertHasFormErrors(['photo']);
    }

    public function test_an_ordinary_photograph_still_uploads(): void
    {
        // The other half of the rule. Blocking everything would pass the tests
        // above and make the admin useless.
        Livewire::test(CreateTeamMember::class)
            ->fillForm([
                'name' => 'Ada Example',
                'role.en' => 'Engineer',
                'photo' => [UploadedFile::fake()->image('portrait.jpg', 600, 600)],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('team_members', ['name' => 'Ada Example']);
    }
}
