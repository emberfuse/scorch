<?php

namespace Citadel\Tests;

use Citadel\Project;
use PHPUnit\Framework\TestCase;

class AuthTest extends TestCase
{
    public function testBasic()
    {
        $project = new Project('Example Project');

        $this->assertInstanceOf(Project::class, $project);
        $this->assertEquals('Example Project', $project->name());
    }
}
