<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public const CATEGORIES = [
        ['name' => 'Routing',                    'slug' => 'routing',          'icon' => '🗺️',  'color' => '#6366f1', 'description' => 'Système de routage HTTP, attributs de routes, génération d\'URL.'],
        ['name' => 'Dependency Injection',        'slug' => 'di',               'icon' => '🔧',  'color' => '#14b8a6', 'description' => 'Conteneur de services, autowiring, autoconfiguration, attributs DI.'],
        ['name' => 'HTTP Foundation',             'slug' => 'http-foundation',  'icon' => '🌐',  'color' => '#f97316', 'description' => 'Request, Response, Session, Cookies, UploadedFile.'],
        ['name' => 'HTTP Kernel',                 'slug' => 'http-kernel',      'icon' => '⚙️',  'color' => '#8b5cf6', 'description' => 'Cycle de vie du kernel, events, sous-requêtes, résolution des contrôleurs.'],
        ['name' => 'Security',                    'slug' => 'security',         'icon' => '🔒',  'color' => '#dc2626', 'description' => 'Authentification, autorisation, firewalls, voters, CSRF.'],
        ['name' => 'Console',                     'slug' => 'console',          'icon' => '💻',  'color' => '#84cc16', 'description' => 'Commandes CLI, helpers, styles, progression, tableaux.'],
        ['name' => 'Forms',                       'slug' => 'forms',            'icon' => '📝',  'color' => '#f59e0b', 'description' => 'Formulaires, types, validation, transformateurs de données, events.'],
        ['name' => 'Doctrine ORM',                'slug' => 'doctrine',         'icon' => '🗄️',  'color' => '#0ea5e9', 'description' => 'Entités, repositories, relations, migrations, lifecycle callbacks.'],
        ['name' => 'Serializer',                  'slug' => 'serializer',       'icon' => '🔄',  'color' => '#10b981', 'description' => 'Normalisation, dénormalisation, encodage JSON/XML, groupes de sérialisation.'],
        ['name' => 'Validator',                   'slug' => 'validator',        'icon' => '✅',  'color' => '#a78bfa', 'description' => 'Contraintes de validation, groupes, contraintes personnalisées.'],
        ['name' => 'Event Dispatcher',            'slug' => 'event-dispatcher', 'icon' => '📡',  'color' => '#e879f9', 'description' => 'Événements, listeners, subscribers, stoppable events, attribut #[AsEventListener].'],
        ['name' => 'Cache',                       'slug' => 'cache',            'icon' => '⚡',  'color' => '#fbbf24', 'description' => 'Pools de cache, adaptateurs, tags, invalidation, cache HTTP.'],
        ['name' => 'Mailer',                      'slug' => 'mailer',           'icon' => '✉️',  'color' => '#06b6d4', 'description' => 'Envoi d\'emails, MIME, pièces jointes, transports, tests.'],
        ['name' => 'Messenger',                   'slug' => 'messenger',        'icon' => '📨',  'color' => '#3b82f6', 'description' => 'Bus de messages, handlers, transports, retry, messages échoués.'],
        ['name' => 'Twig',                        'slug' => 'twig',             'icon' => '🌿',  'color' => '#34d399', 'description' => 'Moteur de templates, extensions, filtres, fonctions, héritage.'],
        ['name' => 'Configuration',               'slug' => 'configuration',    'icon' => '🛠️',  'color' => '#94a3b8', 'description' => 'Fichiers de config YAML/PHP, variables d\'env, secrets, paramètres.'],
        ['name' => 'Testing',                     'slug' => 'testing',          'icon' => '🧪',  'color' => '#f472b6', 'description' => 'Tests fonctionnels, unitaires, WebTestCase, KernelTestCase, mocks.'],
        ['name' => 'AssetMapper',                 'slug' => 'asset-mapper',     'icon' => '📦',  'color' => '#fb923c', 'description' => 'Gestion des assets front-end sans bundler, importmap, composants JS.'],
        ['name' => 'Scheduler',                   'slug' => 'scheduler',        'icon' => '⏰',  'color' => '#818cf8', 'description' => 'Tâches planifiées avec #[AsPeriodicTask] et #[AsCronTask].'],
        ['name' => 'Workflow',                    'slug' => 'workflow',         'icon' => '🔀',  'color' => '#2dd4bf', 'description' => 'Machine à états, transitions, guards, events de workflow.'],
        ['name' => 'Translation',                 'slug' => 'translation',      'icon' => '🌍',  'color' => '#fb7185', 'description' => 'Catalogues de traduction, pluralisation, format ICU, extraction automatique.'],
        ['name' => 'Rate Limiter',                'slug' => 'rate-limiter',     'icon' => '🚦',  'color' => '#f87171', 'description' => 'Limitation de débit, politiques token bucket, sliding window.'],
        ['name' => 'Lock',                        'slug' => 'lock',             'icon' => '🔑',  'color' => '#a3e635', 'description' => 'Verrous distribués, stores Redis/Semaphore/Flock, locks partagés.'],
        ['name' => 'Clock',                       'slug' => 'clock',            'icon' => '🕐',  'color' => '#67e8f9', 'description' => 'Abstraction du temps, NativeClock, MockClock pour les tests.'],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::CATEGORIES as $data) {
            $cat = new Category();
            $cat->setName($data['name']);
            $cat->setSlug($data['slug']);
            $cat->setIcon($data['icon']);
            $cat->setColor($data['color']);
            $cat->setDescription($data['description']);
            $manager->persist($cat);
            $this->addReference('cat-' . $data['slug'], $cat);
        }

        $manager->flush();
    }
}
