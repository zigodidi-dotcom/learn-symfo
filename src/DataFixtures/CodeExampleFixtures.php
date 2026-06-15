<?php

namespace App\DataFixtures;

use App\Entity\CodeExample;
use App\Entity\Feature;
use App\Enum\CodeLanguage;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CodeExampleFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [FeatureFixtures::class];
    }

    public function load(ObjectManager $manager): void
    {
        foreach ($this->getExamples() as $data) {
            if (!$this->hasReference('feature-' . $data['feature'], Feature::class)) {
                continue;
            }

            $example = new CodeExample();
            $example->setTitle($data['title']);
            $example->setCode($data['code']);
            $example->setLanguage($data['language']);
            $example->setDescription($data['description'] ?? null);
            $example->setFeature($this->getReference('feature-' . $data['feature'], Feature::class));
            $manager->persist($example);
        }

        $manager->flush();
    }

    private function getExamples(): array
    {
        return [

            // ─── ROUTING ──────────────────────────────────────────────────────────────
            [
                'feature' => 'route-attribute',
                'title' => 'Route simple avec attribut',
                'language' => CodeLanguage::Php,
                'description' => 'Déclaration d\'une route GET sur une méthode de contrôleur.',
                'code' => <<<'CODE'
#[Route('/articles/{id}', name: 'article_show', methods: ['GET'])]
public function show(int $id): Response
{
    // ...
}
CODE,
            ],
            [
                'feature' => 'route-class-prefix',
                'title' => 'Préfixe de route sur la classe',
                'language' => CodeLanguage::Php,
                'description' => 'Toutes les routes du contrôleur seront préfixées par /api/articles.',
                'code' => <<<'CODE'
#[Route('/api/articles')]
class ArticleController extends AbstractController
{
    #[Route('', name: 'article_list', methods: ['GET'])]
    public function list(): Response { /* GET /api/articles */ }

    #[Route('/{id}', name: 'article_show', methods: ['GET'])]
    public function show(int $id): Response { /* GET /api/articles/42 */ }

    #[Route('', name: 'article_create', methods: ['POST'])]
    public function create(): Response { /* POST /api/articles */ }
}
CODE,
            ],
            [
                'feature' => 'map-query-parameter',
                'title' => '#[MapQueryParameter] — paramètre typé depuis la query string',
                'language' => CodeLanguage::Php,
                'description' => 'Injection automatique et castée d\'un paramètre de query string.',
                'code' => <<<'CODE'
#[Route('/articles', name: 'article_list')]
public function list(
    #[MapQueryParameter] int $page = 1,
    #[MapQueryParameter] int $perPage = 20,
    #[MapQueryParameter] string $sort = 'createdAt',
    #[MapQueryParameter] array $tags = [],
): Response {
    // GET /articles?page=2&perPage=10&tags[]=php&tags[]=symfony
}
CODE,
            ],
            [
                'feature' => 'map-request-payload',
                'title' => '#[MapRequestPayload] — DTO depuis le JSON body',
                'language' => CodeLanguage::Php,
                'description' => 'Désérialise et valide automatiquement le corps JSON dans un DTO.',
                'code' => <<<'CODE'
// DTO avec contraintes de validation
class CreateArticleInput
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 5, max: 255)]
    public string $title;

    #[Assert\NotBlank]
    public string $content;
}

// Contrôleur
#[Route('/articles', methods: ['POST'])]
public function create(
    #[MapRequestPayload] CreateArticleInput $input
): Response {
    // Si validation échoue → 422 Unprocessable Entity automatiquement
    // $input->title et $input->content sont déjà validés
}
CODE,
            ],

            // ─── DEPENDENCY INJECTION ─────────────────────────────────────────────────
            [
                'feature' => 'autowire-attribute',
                'title' => '#[Autowire] — injection explicite',
                'language' => CodeLanguage::Php,
                'description' => 'Injecter un paramètre de conteneur, une variable d\'env ou un service nommé.',
                'code' => <<<'CODE'
class PaymentService
{
    public function __construct(
        #[Autowire('%app.stripe_key%')]
        private readonly string $stripeKey,

        #[Autowire(env: 'PAYMENT_TIMEOUT')]
        private readonly int $timeout,

        #[Autowire(service: 'monolog.logger.payment')]
        private readonly LoggerInterface $logger,
    ) {}
}
CODE,
            ],
            [
                'feature' => 'target-attribute',
                'title' => '#[Target] — désambiguïsation d\'interface',
                'language' => CodeLanguage::Php,
                'description' => 'Choisir lequel des services LoggerInterface injecter par son nom.',
                'code' => <<<'CODE'
class OrderService
{
    public function __construct(
        // Injecte monolog.logger.order (nommé "orderLogger")
        #[Target('orderLogger')]
        private readonly LoggerInterface $logger,

        // Injecte monolog.logger.payment (nommé "paymentLogger")
        #[Target('paymentLogger')]
        private readonly LoggerInterface $paymentLogger,
    ) {}
}
CODE,
            ],
            [
                'feature' => 'when-attribute',
                'title' => '#[When] — service conditionnel par environnement',
                'language' => CodeLanguage::Php,
                'description' => 'Ce service n\'existe que dans l\'environnement dev.',
                'code' => <<<'CODE'
#[When(env: 'dev')]
class DebugQueryLogger implements QueryLoggerInterface
{
    public function logQuery(string $sql, array $params): void
    {
        dump($sql, $params);
    }
}

// En production, ce service n'est pas enregistré du tout
// L'interface QueryLoggerInterface pointe vers une autre implémentation
CODE,
            ],
            [
                'feature' => 'service-decorator',
                'title' => 'Décorateur de service',
                'language' => CodeLanguage::Php,
                'description' => 'Décorer un service existant sans modifier son code source.',
                'code' => <<<'CODE'
#[AsDecorator(decorates: UserRepository::class)]
class CachedUserRepository implements UserRepositoryInterface
{
    public function __construct(
        #[AutowireDecorated]
        private readonly UserRepositoryInterface $inner,
        private readonly CacheInterface $cache,
    ) {}

    public function findById(int $id): ?User
    {
        return $this->cache->get(
            "user_{$id}",
            fn() => $this->inner->findById($id)
        );
    }
}
CODE,
            ],

            // ─── SECURITY ─────────────────────────────────────────────────────────────
            [
                'feature' => 'voter',
                'title' => 'Voter — autorisation sur une ressource',
                'language' => CodeLanguage::Php,
                'description' => 'Voter centralisant les droits d\'accès à un Article.',
                'code' => <<<'CODE'
class ArticleVoter extends Voter
{
    const EDIT = 'edit';
    const DELETE = 'delete';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::DELETE])
            && $subject instanceof Article;
    }

    protected function voteOnAttribute(
        string $attribute,
        mixed $subject,
        TokenInterface $token
    ): bool {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        return match($attribute) {
            self::EDIT   => $subject->getAuthor() === $user,
            self::DELETE => $subject->getAuthor() === $user
                         || $this->security->isGranted('ROLE_ADMIN'),
            default      => false,
        };
    }
}
CODE,
            ],
            [
                'feature' => 'is-granted-attribute',
                'title' => '#[IsGranted] sur un contrôleur',
                'language' => CodeLanguage::Php,
                'description' => 'Protéger une action de contrôleur avec un rôle ou un voter.',
                'code' => <<<'CODE'
class ArticleController extends AbstractController
{
    // Exige le rôle ROLE_ADMIN
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/articles', name: 'admin_articles')]
    public function adminList(): Response { /* ... */ }

    // Utilise le ArticleVoter avec l'objet courant
    #[IsGranted('edit', subject: 'article')]
    #[Route('/articles/{id}/edit', name: 'article_edit')]
    public function edit(Article $article): Response { /* ... */ }

    // Message d'erreur personnalisé
    #[IsGranted('ROLE_EDITOR', message: 'Accès réservé aux éditeurs.')]
    #[Route('/articles/create', name: 'article_create')]
    public function create(): Response { /* ... */ }
}
CODE,
            ],
            [
                'feature' => 'authenticator',
                'title' => 'Authenticator JWT personnalisé',
                'language' => CodeLanguage::Php,
                'description' => 'Authentification sans état par token Bearer JWT.',
                'code' => <<<'CODE'
class JwtAuthenticator extends AbstractAuthenticator
{
    public function supports(Request $request): ?bool
    {
        return $request->headers->has('Authorization')
            && str_starts_with($request->headers->get('Authorization'), 'Bearer ');
    }

    public function authenticate(Request $request): Passport
    {
        $token = substr($request->headers->get('Authorization'), 7);
        $payload = $this->jwtDecoder->decode($token); // votre logique

        return new SelfValidatingPassport(
            new UserBadge($payload['email'])
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null; // continue la requête
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse(['error' => 'Invalid token'], 401);
    }
}
CODE,
            ],

            // ─── CONSOLE ──────────────────────────────────────────────────────────────
            [
                'feature' => 'as-command',
                'title' => 'Commande Console complète',
                'language' => CodeLanguage::Php,
                'description' => 'Structure complète d\'une commande Symfony avec argument, option et SymfonyStyle.',
                'code' => <<<'CODE'
#[AsCommand(
    name: 'app:import:users',
    description: 'Importe des utilisateurs depuis un CSV',
    aliases: ['app:users:import'],
)]
class ImportUsersCommand extends Command
{
    public function __construct(
        private readonly UserImporter $importer,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, 'Chemin vers le fichier CSV')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Simuler sans persister')
            ->addOption('batch-size', 'b', InputOption::VALUE_REQUIRED, 'Taille des lots', 100);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $file = $input->getArgument('file');
        $dryRun = $input->getOption('dry-run');

        $io->title('Import des utilisateurs');
        $io->note("Fichier : {$file}" . ($dryRun ? ' [DRY-RUN]' : ''));

        $count = $this->importer->import($file, $dryRun);

        $io->success("{$count} utilisateurs importés avec succès.");

        return Command::SUCCESS;
    }
}
CODE,
            ],

            // ─── FORMS ────────────────────────────────────────────────────────────────
            [
                'feature' => 'form-type',
                'title' => 'Form Type complet',
                'language' => CodeLanguage::Php,
                'description' => 'Type de formulaire Symfony avec validation et data_class.',
                'code' => <<<'CODE'
class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'constraints' => [
                    new NotBlank(),
                    new Length(min: 5, max: 255),
                ],
            ])
            ->add('content', TextareaType::class, ['label' => 'Contenu'])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
            ])
            ->add('publishedAt', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('save', SubmitType::class, ['label' => 'Publier']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
CODE,
            ],

            // ─── DOCTRINE ─────────────────────────────────────────────────────────────
            [
                'feature' => 'entity-attributes',
                'title' => 'Entité Doctrine complète',
                'language' => CodeLanguage::Php,
                'description' => 'Exemple d\'entité avec toutes les annotations courantes.',
                'code' => <<<'CODE'
#[ORM\Entity(repositoryClass: ArticleRepository::class)]
#[ORM\Table(name: 'articles')]
#[ORM\HasLifecycleCallbacks]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $title;

    #[ORM\Column(type: Types::TEXT)]
    private string $content;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $author;

    #[ORM\ManyToMany(targetEntity: Tag::class)]
    #[ORM\JoinTable(name: 'article_tags')]
    private Collection $tags;

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }
}
CODE,
            ],
            [
                'feature' => 'doctrine-querybuilder',
                'title' => 'QueryBuilder — requête complexe',
                'language' => CodeLanguage::Php,
                'description' => 'Requête avec JOIN, WHERE, pagination dans un Repository.',
                'code' => <<<'CODE'
class ArticleRepository extends ServiceEntityRepository
{
    public function findPublishedByTag(string $tagSlug, int $page = 1, int $limit = 20): array
    {
        return $this->createQueryBuilder('a')
            ->join('a.tags', 't')
            ->join('a.author', 'u')
            ->addSelect('u', 't')
            ->where('t.slug = :tag')
            ->andWhere('a.publishedAt IS NOT NULL')
            ->andWhere('a.publishedAt <= :now')
            ->setParameter('tag', $tagSlug)
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('a.publishedAt', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
CODE,
            ],

            // ─── SERIALIZER ───────────────────────────────────────────────────────────
            [
                'feature' => 'serializer-groups',
                'title' => 'Groupes de sérialisation',
                'language' => CodeLanguage::Php,
                'description' => 'Exposer des champs différents selon le contexte de sérialisation.',
                'code' => <<<'CODE'
class User
{
    #[Groups(['user:read', 'user:list'])]
    public int $id;

    #[Groups(['user:read', 'user:list', 'user:write'])]
    public string $name;

    #[Groups(['user:read', 'user:write'])]
    #[SerializedName('emailAddress')]
    public string $email;

    // Non exposé en lecture (mot de passe hashé)
    #[Groups(['user:write'])]
    public string $password;

    // Uniquement dans la vue détail, pas dans la liste
    #[Groups(['user:read'])]
    public array $roles;
}

// Utilisation dans un contrôleur
$json = $this->serializer->serialize($user, 'json', [
    'groups' => ['user:read'],
]);
CODE,
            ],

            // ─── EVENT DISPATCHER ─────────────────────────────────────────────────────
            [
                'feature' => 'as-event-listener',
                'title' => '#[AsEventListener] — listener de requête',
                'language' => CodeLanguage::Php,
                'description' => 'Intercepter chaque requête pour logger ou modifier son comportement.',
                'code' => <<<'CODE'
class RequestLocaleListener
{
    #[AsEventListener(event: KernelEvents::REQUEST, priority: 20)]
    public function onRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $locale = $request->query->get('_locale', 'fr');
        $request->setLocale($locale);
    }

    #[AsEventListener(event: KernelEvents::EXCEPTION)]
    public function onException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if ($exception instanceof NotFoundHttpException) {
            // Journalisation personnalisée des 404
        }
    }
}
CODE,
            ],

            // ─── MESSENGER ────────────────────────────────────────────────────────────
            [
                'feature' => 'as-message-handler',
                'title' => 'Message + Handler Messenger',
                'language' => CodeLanguage::Php,
                'description' => 'Message immuable et son handler asynchrone.',
                'code' => <<<'CODE'
// Message (DTO immuable)
final class SendWelcomeEmailMessage
{
    public function __construct(
        public readonly int $userId,
        public readonly string $email,
    ) {}
}

// Handler
#[AsMessageHandler]
class SendWelcomeEmailHandler
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly UserRepository $users,
    ) {}

    public function __invoke(SendWelcomeEmailMessage $message): void
    {
        $user = $this->users->find($message->userId);

        $email = (new TemplatedEmail())
            ->to($message->email)
            ->subject('Bienvenue !')
            ->htmlTemplate('emails/welcome.html.twig')
            ->context(['user' => $user]);

        $this->mailer->send($email);
    }
}

// Dispatch depuis un contrôleur (envoi asynchrone)
$this->bus->dispatch(new SendWelcomeEmailMessage($user->getId(), $user->getEmail()));
CODE,
            ],
            [
                'feature' => 'messenger-routing',
                'title' => 'Configuration routing Messenger',
                'language' => CodeLanguage::Yaml,
                'description' => 'Routage des messages vers les transports dans messenger.yaml.',
                'code' => <<<'CODE'
# config/packages/messenger.yaml
framework:
    messenger:
        transports:
            async:
                dsn: '%env(MESSENGER_TRANSPORT_DSN)%'
                retry_strategy:
                    max_retries: 3
                    delay: 1000
                    multiplier: 2
            async_priority_high:
                dsn: '%env(MESSENGER_TRANSPORT_DSN)%'
                options:
                    queue_name: high_priority
            failed:
                dsn: 'doctrine://default?queue_name=failed'

        routing:
            App\Message\SendWelcomeEmailMessage: async
            App\Message\ProcessPaymentMessage: async_priority_high
            App\Message\GenerateReportMessage: async
CODE,
            ],

            // ─── SCHEDULER ────────────────────────────────────────────────────────────
            [
                'feature' => 'as-cron-task',
                'title' => '#[AsCronTask] — tâche planifiée',
                'language' => CodeLanguage::Php,
                'description' => 'Exécution automatique d\'une tâche selon une expression cron.',
                'code' => <<<'CODE'
#[AsCronTask('0 2 * * *', timezone: 'Europe/Paris')]
class DailyReportHandler
{
    public function __construct(
        private readonly ReportGenerator $generator,
        private readonly MailerInterface $mailer,
    ) {}

    public function __invoke(): void
    {
        $report = $this->generator->generateDaily();
        $this->mailer->send(/* email du rapport */);
    }
}

// Lancer le scheduler worker :
// symfony console messenger:consume scheduler_default
CODE,
            ],

            // ─── CACHE ────────────────────────────────────────────────────────────────
            [
                'feature' => 'cache-pools',
                'title' => 'Utiliser le Cache',
                'language' => CodeLanguage::Php,
                'description' => 'Mettre en cache le résultat d\'un calcul coûteux.',
                'code' => <<<'CODE'
class StatsService
{
    public function __construct(
        private readonly CacheInterface $cache,
    ) {}

    public function getMonthlyStats(int $year, int $month): array
    {
        $key = "stats_{$year}_{$month}";

        return $this->cache->get($key, function (ItemInterface $item) use ($year, $month): array {
            $item->expiresAfter(3600); // 1 heure
            $item->tag(["stats", "stats-{$year}"]);

            return $this->computeExpensiveStats($year, $month);
        });
    }

    public function invalidateYear(int $year): void
    {
        // Invalide tous les items du cache taggés "stats-2024"
        $this->cache->invalidateTags(["stats-{$year}"]);
    }
}
CODE,
            ],

            // ─── CLOCK ────────────────────────────────────────────────────────────────
            [
                'feature' => 'clock-interface',
                'title' => 'ClockInterface — service et test',
                'language' => CodeLanguage::Php,
                'description' => 'Injecter ClockInterface pour des tests déterministes.',
                'code' => <<<'CODE'
// Service — injecte ClockInterface
class SubscriptionService
{
    public function __construct(
        private readonly ClockInterface $clock,
    ) {}

    public function isActive(Subscription $sub): bool
    {
        return $sub->getExpiresAt() > $this->clock->now();
    }

    public function createTrial(): Subscription
    {
        return new Subscription(
            createdAt: $this->clock->now(),
            expiresAt: $this->clock->now()->modify('+30 days'),
        );
    }
}

// Test — MockClock pour figer le temps
class SubscriptionServiceTest extends TestCase
{
    public function testIsActiveWithFrozenTime(): void
    {
        $clock = new MockClock('2024-06-01 12:00:00');
        $service = new SubscriptionService($clock);

        $sub = new Subscription(expiresAt: new \DateTimeImmutable('2024-12-31'));
        $this->assertTrue($service->isActive($sub));

        // Avancer le temps dans le test
        $clock->modify('+1 year');
        $this->assertFalse($service->isActive($sub));
    }
}
CODE,
            ],

            // ─── TESTING ──────────────────────────────────────────────────────────────
            [
                'feature' => 'web-test-case',
                'title' => 'Test fonctionnel HTTP',
                'language' => CodeLanguage::Php,
                'description' => 'Test d\'une API REST avec WebTestCase.',
                'code' => <<<'CODE'
class ArticleApiTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testGetArticleList(): void
    {
        $this->client->request('GET', '/api/articles', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($data);
    }

    public function testCreateArticleRequiresAuth(): void
    {
        $this->client->request('POST', '/api/articles', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['title' => 'Test']));

        $this->assertResponseStatusCodeSame(401);
    }
}
CODE,
            ],

            // ─── ENV VARS ─────────────────────────────────────────────────────────────
            [
                'feature' => 'env-vars',
                'title' => 'Variables d\'environnement — types et processeurs',
                'language' => CodeLanguage::Yaml,
                'description' => 'Utilisation de processeurs de variables d\'environnement dans la configuration.',
                'code' => <<<'CODE'
# .env
APP_ENV=dev
DATABASE_URL=mysql://root:@127.0.0.1:3306/myapp
MAILER_DSN=smtp://localhost
APP_DEBUG=true
MAX_UPLOAD_SIZE=10485760
ALLOWED_ORIGINS=["https://app.example.com","https://www.example.com"]

# config/packages/framework.yaml — utilisation des processeurs
framework:
    # env(bool:...) → cast en booléen
    debug: '%env(bool:APP_DEBUG)%'

    # env(int:...) → cast en entier
    http_method_override: false

# config/packages/monolog.yaml
monolog:
    handlers:
        main:
            # env(resolve:...) → résout les vars dans la valeur
            path: '%env(resolve:LOG_PATH)%'
CODE,
            ],

            // ─── TWIG ─────────────────────────────────────────────────────────────────
            [
                'feature' => 'twig-extension',
                'title' => 'Extension Twig personnalisée',
                'language' => CodeLanguage::Php,
                'description' => 'Filtre et fonction Twig custom avec autoconfiguration.',
                'code' => <<<'CODE'
// Taggé automatiquement "twig.extension" par autoconfiguration
class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('price', [$this, 'formatPrice']),
            new TwigFilter('truncate', [$this, 'truncate'], ['is_safe' => ['html']]),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('initials', [$this, 'getInitials']),
        ];
    }

    public function formatPrice(float $amount, string $currency = 'EUR'): string
    {
        return number_format($amount, 2, ',', ' ') . ' ' . $currency;
    }

    public function truncate(string $text, int $length = 100): string
    {
        return mb_strlen($text) > $length
            ? mb_substr($text, 0, $length) . '…'
            : $text;
    }

    public function getInitials(string $name): string
    {
        return implode('', array_map(
            fn($word) => mb_strtoupper(mb_substr($word, 0, 1)),
            explode(' ', $name)
        ));
    }
}
CODE,
            ],

            // ─── RATE LIMITER ─────────────────────────────────────────────────────────
            [
                'feature' => 'rate-limit-attribute',
                'title' => '#[RateLimit] sur un endpoint API',
                'language' => CodeLanguage::Php,
                'description' => 'Limiter les appels à 10 par minute sur un endpoint sensible.',
                'code' => <<<'CODE'
// config/packages/rate_limiter.yaml
// framework:
//   rate_limiter:
//     api_login:
//       policy: sliding_window
//       limit: 10
//       interval: '1 minute'

#[RateLimit(policy: 'api_login')]
#[Route('/api/auth/login', methods: ['POST'])]
public function login(#[MapRequestPayload] LoginInput $input): JsonResponse
{
    // Si la limite est dépassée → 429 Too Many Requests automatique
    // Header Retry-After ajouté à la réponse
}
CODE,
            ],

            // ─── WORKFLOW ─────────────────────────────────────────────────────────────
            [
                'feature' => 'workflow-vs-statemachine',
                'title' => 'Configuration State Machine (article)',
                'language' => CodeLanguage::Yaml,
                'description' => 'State machine pour le cycle de vie d\'un article.',
                'code' => <<<'CODE'
# config/packages/workflow.yaml
framework:
    workflows:
        article_publishing:
            type: state_machine
            marking_store:
                type: method
                property: status
            supports:
                - App\Entity\Article
            initial_marking: draft
            places:
                - draft
                - review
                - published
                - rejected
                - archived
            transitions:
                submit_for_review:
                    from: draft
                    to: review
                publish:
                    from: review
                    to: published
                reject:
                    from: review
                    to: rejected
                archive:
                    from: published
                    to: archived
                revise:
                    from: [review, rejected]
                    to: draft
CODE,
            ],
            [
                'feature' => 'workflow-guards',
                'title' => 'Guard de workflow',
                'language' => CodeLanguage::Php,
                'description' => 'Bloquer une transition selon des conditions métier.',
                'code' => <<<'CODE'
#[AsEventListener(event: 'workflow.article_publishing.guard.publish')]
class ArticlePublishGuard
{
    public function __invoke(GuardEvent $event): void
    {
        /** @var Article $article */
        $article = $event->getSubject();

        if (empty($article->getTitle())) {
            $event->setBlocked(true, 'Le titre est obligatoire pour publier.');
        }

        if ($article->getWordCount() < 300) {
            $event->setBlocked(true, 'L\'article doit faire au moins 300 mots.');
        }
    }
}

// Usage dans un contrôleur
public function publish(Article $article, WorkflowInterface $workflow): Response
{
    if ($workflow->can($article, 'publish')) {
        $workflow->apply($article, 'publish');
    }
}
CODE,
            ],

            // ─── ASSET MAPPER ─────────────────────────────────────────────────────────
            [
                'feature' => 'asset-mapper',
                'title' => 'AssetMapper — configuration et usage Twig',
                'language' => CodeLanguage::Twig,
                'description' => 'Intégrer AssetMapper dans le template de base.',
                'code' => <<<'CODE'
{# templates/base.html.twig #}
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{% block title %}{% endblock %}</title>

    {# Injecte l'importmap et charge app.js #}
    {{ importmap('app') }}

    {# CSS géré par AssetMapper (avec hash de version) #}
    <link rel="stylesheet" href="{{ asset('styles/app.css') }}">
</head>
<body>
    {% block body %}{% endblock %}
</body>
</html>
CODE,
            ],
            [
                'feature' => 'importmap',
                'title' => 'importmap.php — configuration des dépendances JS',
                'language' => CodeLanguage::Php,
                'description' => 'Fichier de mapping des modules ES importés depuis CDN.',
                'code' => <<<'CODE'
<?php
// importmap.php — géré par "symfony importmap:require"
return [
    'app' => [
        'path' => './assets/app.js',
        'entrypoint' => true,
    ],
    '@hotwired/stimulus' => [
        'version' => '3.2.2',
    ],
    '@symfony/stimulus-bundle' => [
        'path' => './vendor/symfony/stimulus-bundle/assets/dist/loader.js',
    ],
    'bootstrap' => [
        'version' => '5.3.3',
    ],
    'alpinejs' => [
        'version' => '3.14.0',
    ],
];
CODE,
            ],

            // ─── VALIDATOR ────────────────────────────────────────────────────────────
            [
                'feature' => 'custom-constraint',
                'title' => 'Contrainte de validation personnalisée',
                'language' => CodeLanguage::Php,
                'description' => 'Créer une contrainte #[IsSlug] vérifiant le format d\'un slug.',
                'code' => <<<'CODE'
// La contrainte (métadonnée)
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class IsSlug extends Constraint
{
    public string $message = 'La valeur "{{ value }}" n\'est pas un slug valide.';
}

// Le validateur (logique)
class IsSlugValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (null === $value || '' === $value) {
            return;
        }

        if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}

// Utilisation
class Article
{
    #[IsSlug]
    #[Assert\Length(max: 255)]
    private string $slug;
}
CODE,
            ],
        ];
    }
}
