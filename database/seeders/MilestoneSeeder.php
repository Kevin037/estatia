<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Milestone;

class MilestoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $milestones = [
            [
                'name' => 'Project Initiation',
                'desc' => 'Initial project setup, team formation, and requirement gathering phase',
            ],
            [
                'name' => 'Design Phase',
                'desc' => 'Complete architectural design, UI/UX mockups, and technical specifications',
            ],
            [
                'name' => 'Development Sprint 1',
                'desc' => 'Core functionality development and database structure implementation',
            ],
            [
                'name' => 'Testing & QA',
                'desc' => 'Comprehensive testing including unit tests, integration tests, and user acceptance testing',
            ],
            [
                'name' => 'Beta Release',
                'desc' => 'Limited release to beta testers for feedback and bug identification',
            ],
            [
                'name' => 'Final Review',
                'desc' => 'Final code review, documentation completion, and deployment preparation',
            ],
            [
                'name' => 'Production Deployment',
                'desc' => 'Deploy to production environment and monitor initial performance',
            ],
            [
                'name' => 'Post-Launch Support',
                'desc' => 'Ongoing maintenance, bug fixes, and user support for the first month',
            ],
        ];

        foreach ($milestones as $milestone) {
            Milestone::create($milestone);
        }
    }
}
