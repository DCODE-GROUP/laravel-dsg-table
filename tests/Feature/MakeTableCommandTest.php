<?php

namespace Dcodegroup\LaravelDsgTable\Tests\Feature;

use Dcodegroup\LaravelDsgTable\Tests\TestCase;
use Illuminate\Support\Facades\File;

class MakeTableCommandTest extends TestCase
{
    protected string $tempTablesPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tempTablesPath = $this->tempTablesPath();

        config([
            'dsg-table.tables_path' => $this->tempTablesPath,
            'dsg-table.tables_namespace' => 'App\\Tables',
        ]);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->tempTablesPath);

        parent::tearDown();
    }

    public function test_it_scaffolds_a_table_class_using_configured_namespace_and_suffix(): void
    {
        $this->artisan('make:table', ['name' => 'posts'])
            ->assertSuccessful();

        $path = $this->tempTablesPath.'/PostsTable.php';

        $this->assertFileExists($path);

        $contents = file_get_contents($path);

        $this->assertStringContainsString('namespace App\\Tables;', $contents);
        $this->assertStringContainsString('class PostsTable implements TableInterface', $contents);
    }

    public function test_it_scaffolds_multi_word_table_names(): void
    {
        $this->artisan('make:table', ['name' => 'account-users'])
            ->assertSuccessful();

        $this->assertFileExists($this->tempTablesPath.'/AccountUsersTable.php');
    }

    public function test_it_fails_when_table_class_already_exists(): void
    {
        $this->artisan('make:table', ['name' => 'posts'])->assertSuccessful();

        $this->artisan('make:table', ['name' => 'posts'])
            ->assertFailed();
    }

    public function test_it_uses_a_published_custom_stub_when_available(): void
    {
        $stubDirectory = base_path('stubs');
        File::ensureDirectoryExists($stubDirectory);

        $customStub = $stubDirectory.'/dsg-table.stub';
        File::put($customStub, <<<'STUB'
<?php

namespace {{ namespace }};

class {{ class }}
{
}
STUB);

        try {
            $this->artisan('make:table', ['name' => 'custom'])
                ->assertSuccessful();

            $this->assertStringContainsString(
                'class CustomTable',
                file_get_contents($this->tempTablesPath.'/CustomTable.php'),
            );
        } finally {
            File::delete($customStub);

            if (is_dir($stubDirectory) && count(scandir($stubDirectory)) === 2) {
                File::deleteDirectory($stubDirectory);
            }
        }
    }
}
