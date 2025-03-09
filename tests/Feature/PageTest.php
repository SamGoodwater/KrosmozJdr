class PageTest extends TestCase
{
public function test_user_can_view_public_page()
{
$page = Page::factory()->create(['is_public' => true]);

$response = $this->get(route('pages.show', $page));

$response->assertStatus(200);
}

public function test_admin_can_create_page()
{
$user = User::factory()->create(['role' => User::ROLES['admin']]);

$response = $this->actingAs($user)
->post(route('pages.store'), [
'name' => 'Test Page',
'slug' => 'test-page',
'is_public' => true
]);

$response->assertRedirect(route('pages.index'));
$this->assertDatabaseHas('pages', ['name' => 'Test Page']);
}
}