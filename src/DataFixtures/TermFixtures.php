<?php

namespace App\DataFixtures;

use App\Entity\Term;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TermFixtures extends Fixture
{
    public const TERMS = [
        [
            'name' => 'Autowiring',
            'slug' => 'autowiring',
            'definition' => 'Mécanisme du conteneur de services Symfony qui injecte automatiquement les dépendances d\'un service en analysant les types des paramètres de son constructeur. Élimine la configuration manuelle des services dans la plupart des cas.',
        ],
        [
            'name' => 'Autoconfiguration',
            'slug' => 'autoconfiguration',
            'definition' => 'Fonctionnalité du conteneur qui détecte automatiquement les interfaces ou attributs implémentés par un service et applique les tags correspondants (ex: EventSubscriberInterface → tag kernel.event_subscriber).',
        ],
        [
            'name' => 'Service Tag',
            'slug' => 'service-tag',
            'definition' => 'Métadonnée attachée à un service pour l\'identifier comme participant à un mécanisme particulier (ex: twig.extension, kernel.event_listener). Utilisé par les compiler passes pour collecter des services.',
        ],
        [
            'name' => 'Compiler Pass',
            'slug' => 'compiler-pass',
            'definition' => 'Classe qui modifie le conteneur de services pendant la phase de compilation. Permet d\'inspecter et de modifier les définitions de services avant que le conteneur soit figé.',
        ],
        [
            'name' => 'Kernel',
            'slug' => 'kernel',
            'definition' => 'Cœur de l\'application Symfony. Orchestre le cycle de vie d\'une requête HTTP : chargement des bundles, compilation du conteneur, dispatch des événements (request → controller → response).',
        ],
        [
            'name' => 'Bundle',
            'slug' => 'bundle',
            'definition' => 'Plugin réutilisable pour Symfony. Depuis Symfony 4, les bundles sont réservés aux bibliothèques partagées. Les applications utilisent directement src/ sans bundle applicatif.',
        ],
        [
            'name' => 'Firewall',
            'slug' => 'firewall',
            'definition' => 'Couche de sécurité Symfony qui définit comment les requêtes vers une section de l\'application doivent être authentifiées. Plusieurs firewalls peuvent coexister (ex: api, main).',
        ],
        [
            'name' => 'Voter',
            'slug' => 'voter',
            'definition' => 'Classe qui centralise la logique d\'autorisation pour une ressource ou un attribut donné. Implémente VoterInterface et retourne ACCESS_GRANTED, ACCESS_DENIED ou ACCESS_ABSTAIN.',
        ],
        [
            'name' => 'Event Dispatcher',
            'slug' => 'event-dispatcher',
            'definition' => 'Implémentation du pattern Observer. Permet d\'émettre des événements nommés et de leur attacher des listeners ou subscribers qui réagiront de manière découplée.',
        ],
        [
            'name' => 'Middleware',
            'slug' => 'middleware',
            'definition' => 'Dans le contexte Messenger, couche de traitement encapsulant l\'envoi ou la réception d\'un message. Permet d\'ajouter de la logique transversale (logging, transactions, validation).',
        ],
        [
            'name' => 'Message Bus',
            'slug' => 'message-bus',
            'definition' => 'Interface centrale de Messenger par laquelle transitent tous les messages. Symfony peut configurer plusieurs bus (command bus, query bus, event bus) selon le pattern CQRS.',
        ],
        [
            'name' => 'Transport',
            'slug' => 'transport',
            'definition' => 'Dans Messenger, couche d\'abstraction pour l\'envoi et la réception de messages via une technologie donnée (AMQP, Redis, Doctrine, SQS, in-memory...).',
        ],
        [
            'name' => 'Normalizer',
            'slug' => 'normalizer',
            'definition' => 'Composant du Serializer qui convertit un objet PHP en tableau de données primitives (normalisation) ou l\'inverse (dénormalisation). La chaîne de normalizers est parcourue jusqu\'au premier qui supporte l\'objet.',
        ],
        [
            'name' => 'Encoder',
            'slug' => 'encoder',
            'definition' => 'Composant du Serializer qui convertit un tableau en chaîne de caractères dans un format donné (JSON, XML, CSV, YAML) et vice-versa.',
        ],
        [
            'name' => 'Form Type',
            'slug' => 'form-type',
            'definition' => 'Classe qui définit la structure d\'un formulaire Symfony : ses champs, leurs types, options et contraintes. Hérite de AbstractType et implémente buildForm().',
        ],
        [
            'name' => 'Data Transformer',
            'slug' => 'data-transformer',
            'definition' => 'Classe qui convertit les données entre la représentation du formulaire (ex: string) et le modèle PHP (ex: objet DateTime). Implémente DataTransformerInterface.',
        ],
        [
            'name' => 'Repository',
            'slug' => 'repository',
            'definition' => 'Classe Doctrine responsable de la récupération des entités depuis la base de données. Hérite de ServiceEntityRepository et peut contenir des méthodes de requête personnalisées.',
        ],
        [
            'name' => 'Entity',
            'slug' => 'entity',
            'definition' => 'Classe PHP mappée à une table de base de données par Doctrine ORM via des attributs #[ORM\\Entity], #[ORM\\Column], etc. Représente un enregistrement de la base.',
        ],
        [
            'name' => 'Migration',
            'slug' => 'migration',
            'definition' => 'Fichier PHP versionnant une modification du schéma de base de données. Généré via doctrine:migrations:diff, exécuté via doctrine:migrations:migrate. Permet un suivi des évolutions du schéma.',
        ],
        [
            'name' => 'DQL',
            'slug' => 'dql',
            'definition' => 'Doctrine Query Language. Langage de requête orienté objet propre à Doctrine qui opère sur des entités et leurs propriétés plutôt que sur des tables SQL.',
        ],
        [
            'name' => 'QueryBuilder',
            'slug' => 'querybuilder',
            'definition' => 'API fluente de Doctrine pour construire des requêtes DQL de façon programmatique, avec autocomplétion IDE et protection contre les injections SQL.',
        ],
        [
            'name' => 'Cache Pool',
            'slug' => 'cache-pool',
            'definition' => 'Instance de cache nommée avec un adaptateur et un namespace spécifiques. Symfony permet de définir plusieurs pools avec des durées de vie et backends différents.',
        ],
        [
            'name' => 'Cache Tag',
            'slug' => 'cache-tag',
            'definition' => 'Métadonnée attachée à un item de cache permettant d\'invalider plusieurs items par tag plutôt qu\'individuellement. Implémenté par TagAwareCacheInterface.',
        ],
        [
            'name' => 'Stamp',
            'slug' => 'stamp',
            'definition' => 'Métadonnée attachée à une enveloppe de message Messenger pour transporter des informations supplémentaires (transport utilisé, délai, tentatives, ID de message...).',
        ],
        [
            'name' => 'Envelope',
            'slug' => 'envelope',
            'definition' => 'Conteneur Messenger qui emballe un message avec ses stamps. Permet d\'enrichir le message avec des métadonnées sans modifier la classe du message elle-même.',
        ],
        [
            'name' => 'Constraint',
            'slug' => 'constraint',
            'definition' => 'Règle de validation du composant Validator. S\'applique via l\'attribut #[Assert\\*] sur les propriétés d\'entité ou les paramètres de méthode.',
        ],
        [
            'name' => 'Constraint Validator',
            'slug' => 'constraint-validator',
            'definition' => 'Classe associée à une contrainte qui exécute la logique de validation. Hérite de ConstraintValidator et implémente validate(). Ajoutée automatiquement via autowiring.',
        ],
        [
            'name' => 'Security Passport',
            'slug' => 'security-passport',
            'definition' => 'Objet retourné par un Authenticator::authenticate() contenant les credentials et badges (PasswordCredentials, CsrfTokenBadge, RememberMeBadge...) à vérifier.',
        ],
        [
            'name' => 'Authenticator',
            'slug' => 'authenticator',
            'definition' => 'Classe gérant un mécanisme d\'authentification complet. Remplace les Guard Authenticators depuis Symfony 5.3. Implémente AuthenticatorInterface.',
        ],
        [
            'name' => 'Service Locator',
            'slug' => 'service-locator',
            'definition' => 'Conteneur léger qui ne résout les services que lors de leur accès effectif (lazy). Utilisé pour injecter un sous-ensemble de services sans les instancier tous.',
        ],
        [
            'name' => 'Lazy Service',
            'slug' => 'lazy-service',
            'definition' => 'Service instancié uniquement lors de son premier appel effectif, via un proxy généré. Utile pour les services coûteux non toujours utilisés.',
        ],
        [
            'name' => 'Importmap',
            'slug' => 'importmap',
            'definition' => 'Mécanisme browser natif (et son émulation) permettant de résoudre les imports ES modules sans bundler. Géré par AssetMapper via importmap.php.',
        ],
        [
            'name' => 'CSRF Token',
            'slug' => 'csrf-token',
            'definition' => 'Token aléatoire inclus dans les formulaires pour prévenir les attaques Cross-Site Request Forgery. Symfony le gère automatiquement dans les formulaires et via CsrfTokenManager.',
        ],
        [
            'name' => 'SymfonyStyle',
            'slug' => 'symfony-style',
            'definition' => 'Classe d\'aide pour les commandes Console offrant une API haut niveau pour afficher des titres, sections, tables, listes, questions et barres de progression.',
        ],
        [
            'name' => 'HttpClient',
            'slug' => 'http-client',
            'definition' => 'Composant Symfony pour effectuer des requêtes HTTP sortantes. Supporte les requêtes asynchrones, le streaming, les retries automatiques et est testable via MockHttpClient.',
        ],
        [
            'name' => 'Profiler',
            'slug' => 'profiler',
            'definition' => 'Outil de débogage Symfony collectant des données sur chaque requête (temps, requêtes SQL, events, logs...). Accessible via la Web Debug Toolbar en développement.',
        ],
        [
            'name' => 'Data Collector',
            'slug' => 'data-collector',
            'definition' => 'Classe qui collecte des données pour un panneau spécifique du Profiler. Implémente DataCollectorInterface et peut être créée pour ajouter un panneau personnalisé.',
        ],
        [
            'name' => 'Marking Store',
            'slug' => 'marking-store',
            'definition' => 'Dans le composant Workflow, mécanisme qui persiste l\'état courant (place) d\'un objet. Par défaut utilise une propriété de l\'objet, peut être personnalisé.',
        ],
        [
            'name' => 'Place',
            'slug' => 'place',
            'definition' => 'Dans le composant Workflow, état dans lequel peut se trouver un objet. Un objet peut être dans plusieurs places simultanément dans un Workflow (pas dans une StateMachine).',
        ],
        [
            'name' => 'Transition',
            'slug' => 'transition',
            'definition' => 'Dans le composant Workflow, action qui fait passer un objet d\'une ou plusieurs places à d\'autres. Peut être soumise à des guards (conditions).',
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::TERMS as $data) {
            $term = new Term();
            $term->setName($data['name']);
            $term->setSlug($data['slug']);
            $term->setDefinition($data['definition']);
            $manager->persist($term);
            $this->addReference('term-' . $data['slug'], $term);
        }

        $manager->flush();
    }
}
