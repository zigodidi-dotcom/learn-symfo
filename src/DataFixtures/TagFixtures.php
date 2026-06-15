<?php

namespace App\DataFixtures;

use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TagFixtures extends Fixture
{
    public const TAGS = [
        ['name' => 'Nouveauté 8.x',      'slug' => 'new-in-8',       'color' => '#10b981'],
        ['name' => 'Nouveauté 7.x',      'slug' => 'new-in-7',       'color' => '#3b82f6'],
        ['name' => 'Attribut PHP',        'slug' => 'attribute',      'color' => '#8b5cf6'],
        ['name' => 'Bonne pratique',      'slug' => 'best-practice',  'color' => '#f59e0b'],
        ['name' => 'Performance',         'slug' => 'performance',    'color' => '#ef4444'],
        ['name' => 'Sécurité',            'slug' => 'security',       'color' => '#dc2626'],
        ['name' => 'Asynchrone',          'slug' => 'async',          'color' => '#06b6d4'],
        ['name' => 'Déprécié',            'slug' => 'deprecated',     'color' => '#6b7280'],
        ['name' => 'Console',             'slug' => 'console',        'color' => '#84cc16'],
        ['name' => 'HTTP',                'slug' => 'http',           'color' => '#f97316'],
        ['name' => 'Test',                'slug' => 'test',           'color' => '#a78bfa'],
        ['name' => 'Configuration',       'slug' => 'config',         'color' => '#0ea5e9'],
        ['name' => 'Injection de dépendances', 'slug' => 'di',        'color' => '#14b8a6'],
        ['name' => 'DX (Developer Experience)', 'slug' => 'dx',       'color' => '#e879f9'],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::TAGS as $data) {
            $tag = new Tag();
            $tag->setName($data['name']);
            $tag->setSlug($data['slug']);
            $tag->setColor($data['color']);
            $manager->persist($tag);
            $this->addReference('tag-' . $data['slug'], $tag);
        }

        $manager->flush();
    }
}
