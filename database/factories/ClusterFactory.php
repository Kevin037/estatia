<?php

namespace Database\Factories;

use App\Models\Cluster;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cluster>
 */
class ClusterFactory extends Factory
{
    protected $model = Cluster::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $clusterNames = [
            'Cluster Asri', 'Cluster Bahagia', 'Cluster Cendana', 'Cluster Dahlia',
            'Cluster Elok', 'Cluster Flamboyan', 'Cluster Gardenia', 'Cluster Hijau',
            'Cluster Indah', 'Cluster Jaya', 'Cluster Kenanga', 'Cluster Lavender',
            'Cluster Melati', 'Cluster Nirwana', 'Cluster Orchid', 'Cluster Permata'
        ];

        // Get a random project_id from existing projects
        $projectId = \App\Models\Project::inRandomOrder()->first()->id ?? null;

        return [
            'name' => fake()->randomElement($clusterNames) . ' ' . fake()->numberBetween(1, 10),
            'project_id' => $projectId, // Will be overridden if passed explicitly
            'desc' => fake()->paragraph(3),
            'facilities' => implode(', ', fake()->randomElements([
                'Swimming Pool', 'Playground', 'Jogging Track', 'Security 24/7',
                'CCTV', 'Park', 'Community Hall', 'Fitness Center'
            ], fake()->numberBetween(3, 6))),
            'road_width' => fake()->randomFloat(1, 4, 12),
        ];
    }
}
