<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

/**
 * The client projects, with images, from a snapshot of the old Next site:
 *
 *     php artisan db:seed --class=ProjectSeeder
 *
 * The snapshot in data/portfolio/ is the working tree of sjeren-2026 on
 * 2026-10-24, uncommitted edits included. To redo it, run this from
 * ~/Code/sjeren-2026 (it only writes into sjerencms), then copy
 * messages/se.json and messages/en.json to messages-se.json and
 * messages-en.json in the same folder:
 *
 *     node --experimental-strip-types --no-warnings -e "
 *     const fs = require('fs'); const out = '/Users/patrik/Code/sjerencms/database/seeders/data/portfolio';
 *     fs.mkdirSync(out + '/images', {recursive: true});
 *     Promise.all([import('./src/data/projects.ts'), import('./src/data/about.ts')]).then(([p, a]) => {
 *       fs.writeFileSync(out + '/projects.json', JSON.stringify({projects: p.projects, featured: p.featured.map(x => x.slug)}, null, 2) + '\n');
 *       fs.writeFileSync(out + '/about.json', JSON.stringify({timeline: a.timeline, stack: a.stack, questions: a.questions}, null, 2) + '\n');
 *       for (const x of p.projects) for (const src of [x.image, x.preview, x.tall?.src].filter(Boolean))
 *         fs.copyFileSync('public' + src, out + '/images/' + src.split('/').pop());
 *     });"
 *
 * Safe to run again: a project is matched on slug and then left alone (the
 * headlines are drafts that get rewritten in the admin), and an image is
 * only added when its collection is empty. Run directly, it therefore
 * re-creates a snapshot project deleted in the admin and re-adds a removed
 * image. SitePagesSeeder only calls it while the projects table is empty.
 */
class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $dir = database_path('seeders/data/portfolio');
        $rows = json_decode(file_get_contents("{$dir}/projects.json"), true)['projects'];

        foreach ($rows as $i => $row) {
            $project = Project::firstOrCreate(['slug' => $row['slug']], [
                'client' => $row['client'],
                'headline_sv' => $row['headline']['se'],
                'headline_en' => $row['headline']['en'],
                'role_sv' => $row['role']['se'] ?? null,
                'role_en' => $row['role']['en'] ?? null,
                'services' => $row['services'],
                'tech' => $row['tech'] ?? null,
                'url' => $row['href'],
                'rebuilt' => $row['rebuilt'] ?? false,
                'position' => $i + 1,
            ]);

            $images = ['image' => $row['image'], 'preview' => $row['preview'], 'tall' => $row['tall']['src'] ?? null];

            foreach (array_filter($images) as $collection => $src) {
                if (! $project->getFirstMedia($collection)) {
                    $project->addMedia("{$dir}/images/".basename($src))
                        ->preservingOriginal()
                        ->toMediaCollection($collection);
                }
            }
        }
    }
}
