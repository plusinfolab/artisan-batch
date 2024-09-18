<?php

namespace PlusInfoLab\ArtisanBatch\Tests;

use PHPUnit\Framework\TestCase;
use Illuminate\Console\Application;
use Illuminate\Foundation\Console\Kernel;
use PlusInfoLab\ArtisanBatch\Commands\RunBatchCommand;
use PlusInfoLab\ArtisanBatch\Commands\ListBatchCommand;

class ArtisanBatchTest extends TestCase
{
    private RunBatchCommand $runCommand;
    private ListBatchCommand $listCommand;

    protected function setUp(): void
    {
        parent::setUp();
        $this->runCommand = new RunBatchCommand();
        $this->listCommand = new ListBatchCommand();
    }

    public function testRunCommandSignature()
    {
        $this->assertStringContainsString('batch:run', $this->runCommand->getSignature());
    }

    public function testListCommandSignature()
    {
        $this->assertStringContainsString('batch:list', $this->listCommand->getSignature());
    }

    public function testRunCommandHasDescription()
    {
        $this->assertNotEmpty($this->runCommand->getDescription());
    }

    public function testListCommandHasDescription()
    {
        $this->assertNotEmpty($this->listCommand->getDescription());
    }

    public function testRunCommandOptions()
    {
        $signature = $this->runCommand->getSignature();

        $this->assertStringContainsString('--dry', $signature);
        $this->assertStringContainsString('--no-stop', $signature);
        $this->assertStringContainsString('--verbose', $signature);
        $this->assertStringContainsString('--json', $signature);
    }

    public function testListCommandOptions()
    {
        $signature = $this->listCommand->getSignature();

        $this->assertStringContainsString('--json', $signature);
        $this->assertStringContainsString('--detailed', $signature);
    }
}