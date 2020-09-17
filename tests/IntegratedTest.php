<?php

namespace JeroenG\Packager\Tests;

use Illuminate\Support\Facades\Artisan;

class IntegratedTest extends TestCase
{
    public function test_new_package_is_created()
    {
        Artisan::call('packager:new', ['vendor' => 'MyVendor', 'name' => 'MyPackage']);

        $this->seeInConsoleOutput('Package created successfully!');
        $this->assertTrue(is_link(base_path('vendor/myvendor/mypackage')));
    }

    public function test_new_package_is_installed()
    {
        Artisan::call('packager:new', ['vendor' => 'MyVendor', 'name' => 'MyPackage']);

        $composer = file_get_contents(base_path('composer.json'));

        $this->assertStringContainsString('MyVendor/MyPackage', $composer);
    }

    public function test_new_package_studly_install()
    {
        Artisan::call('packager:new', ['vendor' => 'my-vendor', 'name' => 'my-package']);

        $this->seeInConsoleOutput('Package created successfully!');
        $this->assertStringContainsString('my-vendor/my-package', file_get_contents(base_path('composer.json')));
        $this->assertTrue(is_file(base_path('packages/my-vendor/my-package/src/MyPackageServiceProvider.php')));
    }

    public function test_new_package_is_installed_from_custom_skeleton()
    {
        Artisan::call('packager:new', [
            'vendor' => 'AnotherVendor',
            'name' => 'AnotherPackage',
            '--skeleton' => 'http://github.com/Jeroen-G/packager-skeleton/archive/master.zip',
        ]);

        $composer = file_get_contents(base_path('composer.json'));

        $this->assertStringContainsString('AnotherVendor/AnotherPackage', $composer);
    }

    public function test_get_package()
    {
        Artisan::call('packager:get',
            ['url' => 'https://github.com/Jeroen-G/packager-skeleton', 'vendor' => 'MyVendor', 'name' => 'MyPackage']);

        $this->seeInConsoleOutput('Package downloaded successfully!');
    }

    public function test_get_existing_package_with_git()
    {
        Artisan::call('packager:git',
            ['url' => 'https://github.com/Seldaek/monolog', 'vendor' => 'monolog', 'name' => 'monolog']);

        $this->seeInConsoleOutput('Package cloned successfully!');
        $this->assertTrue(is_link(base_path('vendor/monolog/monolog')));
    }

    public function test_get_existing_package_with_get()
    {
        Artisan::call('packager:get',
            ['url' => 'https://github.com/Seldaek/monolog', 'vendor' => 'monolog', 'name' => 'monolog']);

        $this->seeInConsoleOutput('Package downloaded successfully!');
        $this->assertTrue(is_link(base_path('vendor/monolog/monolog')));
    }

    public function test_list_packages()
    {
        Artisan::call('packager:new', ['vendor' => 'MyVendor', 'name' => 'MyPackage']);
        Artisan::call('packager:list');

        $this->seeInConsoleOutput('MyVendor');
    }

    public function test_removing_package()
    {
        Artisan::call('packager:new', ['vendor' => 'MyVendor', 'name' => 'MyPackage']);
        $this->seeInConsoleOutput('MyVendor');

        Artisan::call('packager:remove', ['vendor' => 'MyVendor', 'name' => 'MyPackage', '--no-interaction' => true]);
        $this->seeInConsoleOutput('Package removed successfully!');
    }

    public function test_new_package_is_uninstalled()
    {
        Artisan::call('packager:new', ['vendor' => 'MyVendor', 'name' => 'MyPackage']);
        Artisan::call('packager:remove', ['vendor' => 'MyVendor', 'name' => 'MyPackage', '--no-interaction' => true]);

        $composer = file_get_contents(base_path('composer.json'));

        $this->assertStringNotContainsString('MyVendor/MyPackage', $composer);
    }
}
