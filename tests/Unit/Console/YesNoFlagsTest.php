<?php

declare(strict_types=1);

namespace Tests\Unit\Console;

use App\Console\Commands\Project\ProjectDepsCommand;
use App\Console\Commands\Project\ProjectDevCommand;
use App\Console\Commands\Project\ProjectFixPermissionsCommand;
use App\Console\Commands\Project\ProjectInitCommand;
use App\Console\Commands\Project\ProjectPrepareCommand;
use App\Console\Commands\Project\ProjectRefreshCommand;
use App\Console\Commands\Project\SetupCommand;
use App\Console\YesNoFlags;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @example YesNoFlags::ideHelperModelsArguments(true, false, false)
 */
class YesNoFlagsTest extends TestCase
{
    public function test_yes_writes_model_files(): void
    {
        self::assertSame(
            ['--write' => true],
            YesNoFlags::ideHelperModelsArguments(true, false, false)
        );
    }

    public function test_no_writes_helper_file(): void
    {
        self::assertSame(
            ['--nowrite' => true],
            YesNoFlags::ideHelperModelsArguments(false, true, false)
        );
    }

    public function test_non_interactive_defaults_to_nowrite(): void
    {
        self::assertSame(
            ['--nowrite' => true],
            YesNoFlags::ideHelperModelsArguments(false, false, true)
        );
    }

    public function test_yes_overrides_non_interactive(): void
    {
        self::assertSame(
            ['--write' => true],
            YesNoFlags::ideHelperModelsArguments(true, false, true)
        );
    }

    public function test_interactive_without_flags_lets_ide_helper_prompt(): void
    {
        self::assertSame(
            [],
            YesNoFlags::ideHelperModelsArguments(false, false, false)
        );
    }

    public function test_project_commands_expose_yes_no_flags(): void
    {
        $command_classes = [
            ProjectPrepareCommand::class,
            ProjectDevCommand::class,
            ProjectDepsCommand::class,
            ProjectRefreshCommand::class,
            ProjectFixPermissionsCommand::class,
            ProjectInitCommand::class,
            SetupCommand::class,
        ];

        foreach ($command_classes as $command_class) {
            $reflection = new ReflectionClass($command_class);
            $signature = $reflection
                ->getProperty('signature')
                ->getValue($reflection->newInstanceWithoutConstructor());

            self::assertIsString($signature);
            self::assertStringContainsString(
                YesNoFlags::SIGNATURE,
                $signature,
                "{$command_class} doit exposer -y / --no"
            );
        }
    }
}
