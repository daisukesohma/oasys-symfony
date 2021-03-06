<?php
/*
 * This file has been automatically generated by TDBM.
 * You can edit this file as it will not be overwritten.
 */

declare(strict_types=1);

namespace App\Infrastructure\Dao;

use App\Domain\Enum\DocumentCategoryEnum;
use App\Domain\Enum\DocumentEnum;
use App\Domain\Enum\DocumentTypeEnum;
use App\Domain\Enum\ProcedureYouSignStatusEnum;
use App\Domain\Enum\UserTypeEnum;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Document;
use App\Domain\Repository\DocumentRepository;
use App\Domain\Repository\UserRepository;
use App\Infrastructure\Dao\Generated\AbstractDocumentDao;
use App\Infrastructure\Logging\ModelLogger;
use Doctrine\Common\Cache\Cache;
use Mouf\Database\MagicQuery;
use Mouf\Database\SchemaAnalyzer\SchemaAnalyzer;
use Safe\DateTimeImmutable;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use TheCodingMachine\TDBM\ConfigurationInterface;
use TheCodingMachine\TDBM\OrderByAnalyzer;
use TheCodingMachine\TDBM\ResultIterator;
use TheCodingMachine\TDBM\TDBMSchemaAnalyzer;
use TheCodingMachine\TDBM\TDBMService;
use function assert;
use function explode;
use function implode;
use function in_array;
use function Safe\preg_split;
use function strtolower;

/**
 * The DocumentDao class will maintain the persistence of Document class into the documents table.
 */
class DocumentDao extends AbstractDocumentDao implements DocumentRepository
{
    use ModelLogger;
    use CompositionDao;

    private TokenStorageInterface $tokenStorage;
    private UserRepository $userRepository;
    private Cache $cache;
    private OrderByAnalyzer $orderByAnalyzer;
    private SchemaAnalyzer $schemaAnalyzer;
    private TDBMSchemaAnalyzer $tdbmSchemaAnalyzer;
    private MagicQuery $magicQuery;
    private string $cachePrefix;

    public const SORT_COLUMNS = [
        'name' => 'documents.name',
        'createdAt' => 'documents.created_at',
    ];

    public const AVOID_SEARCH = [
        'le',
        'de',
        'la',
        'un',
        'les',
        'des',
    ];

    public function __construct(TDBMService $tdbmService, TokenStorageInterface $tokenStorage, ConfigurationInterface $configuration, UserRepository $userRepository)
    {
        parent::__construct($tdbmService);

        $this->tokenStorage = $tokenStorage;
        $this->cache = $configuration->getCache();
        $this->schemaAnalyzer = $configuration->getSchemaAnalyzer();
        $this->tdbmSchemaAnalyzer = new TDBMSchemaAnalyzer($this->tdbmService->getConnection(), $this->cache, $this->schemaAnalyzer);
        $this->cachePrefix = $this->tdbmSchemaAnalyzer->getCachePrefix();
        $this->orderByAnalyzer = new OrderByAnalyzer($this->cache, $this->cachePrefix);
        $this->magicQuery = new MagicQuery($this->tdbmService->getConnection(), $this->cache, $this->schemaAnalyzer);
        $this->userRepository = $userRepository;
    }

    public function save(Document $document): void
    {
        $this->log($document);
        parent::save($document);
    }

    public function saveNoLog(Document $document): void
    {
        parent::save($document);
    }

    /**
     * @throws NotFound
     */
    public function mustFindOneById(string $id): Document
    {
        $document = $this->findOne(['id' => $id]);
        if ($document === null) {
            throw new NotFound(Document::class, ['id' => $id]);
        }

        return $document;
    }

    /**
     * @throws NotFound
     */
    public function mustFindOneByProcedureId(string $id): Document
    {
        $document = $this->findOne(['procedure_id' => $id]);
        if ($document === null) {
            throw new NotFound(Document::class, ['id' => $id]);
        }

        return $document;
    }

    /**
     * @return mixed[]
     */
    private function getBaseFilters(?string $search = null, ?string $tagSearch = null, ?string $visibility = null, ?string $sortColumn = null, ?string $sortDirection = null, ?string $avoidProgram = null, ?string $avoidEvent = null, ?string $type = null, ?bool $displayedInHomePage = null, ?bool $avoidHidden = false, ?string $categoryId = null, ?string $createdAt = null): array
    {
        $query = 'documents';

        $filters = ['documents.deleted = 0'];
        $parameter = [];

        if (! empty($search)) {
            $searchList = explode(' ', $search);
            foreach ($searchList as $key => $value) {
                if (in_array(strtolower($value), self::AVOID_SEARCH)) {
                    continue;
                }

                $filters[] = '(documents.name LIKE :value' . $key . ' OR documents.description LIKE :value' . $key . ')';
                $parameter['value' . $key] = '%' . $value . '%';
            }
        }

        if (! empty($tagSearch)) {
            $tags = preg_split('/[\s,]+/', $tagSearch);
            if ($tags !== false) {
                $tagSearchQuery = [];
                foreach ($tags as $k => $tag) {
                    $tagSearchQuery[] = 'FIND_IN_SET(:tag' . $k . ', tags)';
                    $parameter['tag' . $k] = $tag;
                }
                $filters[] = implode(' OR ', $tagSearchQuery);
            }
        }

        if (! empty($avoidProgram)) {
            $query .= "\nLEFT JOIN documents_programs ON (documents.id = documents_programs.document_id AND documents_programs.program_id = :programId)";
            $filters[] = 'documents_programs.program_id IS NULL';
            $parameter['programId'] = $avoidProgram;
        }

        if (! empty($avoidEvent)) {
            $query .= "\nLEFT JOIN documents_events ON (documents.id = documents_events.document_id AND documents_events.event_id = :eventId)";
            $filters[] = 'documents_events.event_id IS NULL';
            $parameter['eventId'] = $avoidEvent;
        }

        if (! empty($categoryId)) {
            $filters[] = 'documents.category_id = :category';
            $parameter['category'] = $categoryId;
        }

        if (! empty($visibility)) {
            $filters[] = 'documents.visibility = :visibility';
            $parameter['visibility'] = $visibility;
        }

        if ($avoidHidden) {
            $filters[] = 'documents.hidden = :hidden';
            $parameter['hidden'] = false;
        }

        if ($displayedInHomePage !== null) {
            $filters[] = 'documents.to_be_displayed_in_home_page = :displayedInHomePage';
            $parameter['displayedInHomePage'] = $displayedInHomePage;
        }

        if (! empty($type)) {
            $filters[] = 'documents.type = :type';
            $parameter['type'] = $type;
        }

        if (! empty($createdAt)) {
            $date = new DateTimeImmutable($createdAt);
            $filters[] = 'documents.created_at >= :minDate AND documents.created_at <= :maxDate';
            $parameter['minDate'] = $date->format('Y-m-d') . ' 00:00:00';
            $parameter['maxDate'] = $date->format('Y-m-d') . ' 23:59:59';
        }

        $orderBy = null;
        if (! empty($sortColumn) && isset(self::SORT_COLUMNS[$sortColumn])) {
            $orderBy = self::SORT_COLUMNS[$sortColumn] . ' ' . ($sortDirection === 'asc' ? 'ASC' : 'DESC');
        }

        return [$query, $filters, $parameter, $orderBy];
    }

    /**
     * @return Document[]|ResultIterator
     */
    public function findByFilters(
        ?string $search,
        ?string $tagSearch,
        ?string $visibility,
        ?string $sortColumn,
        ?string $sortDirection,
        ?string $avoidProgram,
        ?string $avoidEvent,
        ?string $type,
        ?bool $displayedInHomePage,
        ?bool $avoidHidden = false,
        ?string $categoryId = null
    ): ResultIterator {
        [$query, $filters, $parameter, $orderBy] = $this->getBaseFilters($search, $tagSearch, $visibility, $sortColumn, $sortDirection, $avoidProgram, $avoidEvent, $type, $displayedInHomePage, $avoidHidden, $categoryId);

        return $this->findFromSql($query, $filters, $parameter, $orderBy);
    }

    public function findForLivrable(string $search, ?string $programId = null): ResultIterator
    {
        $query = 'documents';

        $filters = ['deleted = 0 AND category_id = :livrable'];
        $parameter = [
            'livrable' => DocumentCategoryEnum::LIVRABLE,
        ];

        if (! empty($search)) {
            $searchList = explode(' ', $search);
            foreach ($searchList as $key => $value) {
                if (in_array(strtolower($value), self::AVOID_SEARCH)) {
                    continue;
                }

                $filters[] = '(documents.name LIKE :value' . $key . ' OR documents.description LIKE :value' . $key . ')';
                $parameter['value' . $key] = '%' . $value . '%';
            }
        }

        if ($programId !== null) {
            $query .= "\nLEFT JOIN documents_programs ON (documents.id = documents_programs.document_id AND documents_programs.program_id = :programId)";
            $filters[] = 'documents_programs.program_id IS NOT NULL';
            $parameter['programId'] = $programId;
        }

        if ($this->_getUser()->getType()->getId() !== UserTypeEnum::ADMINISTRATOR) {
            [$query, $filters, $parameter] = $this->getDefaultFiltersForCoach($query, $filters, $parameter);
        }

        return $this->findFromSql($query, $filters, $parameter, 'documents.created_at DESC');
    }

    /**
     * @param mixed[] $filters
     * @param mixed[] $parameter
     *
     * @return mixed[]
     */
    private function getBaseFiltersForCoachAndAdmin(
        string $query,
        array $filters,
        array $parameter,
        ?bool $signaturePending,
        ?bool $signedByCoach,
        ?bool $signedByCandidate,
        ?string $livrableId,
        ?string $programId,
        ?string $eventId
    ): array {
        if ($signaturePending !== null || $signedByCandidate !== null || $signedByCoach !== null) {
            $query .= "\nLEFT JOIN documents_signers ON (documents.id = documents_signers.document_id)";
            $query .= "\nLEFT JOIN users ON (documents_signers.user_id = users.id)";

            if ($signaturePending) {
                $filters[] = 'documents_signers.status_signature = :statusPending';
                $parameter['statusPending'] = ProcedureYouSignStatusEnum::MEMBER_PENDING;
            }

            if ($signedByCoach && $signedByCandidate) {
                $filters[] = 'documents_signers.status_signature = :statusDone';
                $parameter['statusDone'] = ProcedureYouSignStatusEnum::MEMBER_DONE;
            }

            if ($signedByCoach && ! $signedByCandidate) {
                $filters[] = 'documents_signers.status_signature = :statusDone';
                $filters[] = 'users.type_id = :typeCoach';
                $parameter['statusDone'] = ProcedureYouSignStatusEnum::MEMBER_DONE;
                $parameter['typeCoach'] = UserTypeEnum::COACH;
            }

            if ($signedByCandidate && ! $signedByCoach) {
                $filters[] = 'documents_signers.status_signature = :statusDone';
                $filters[] = 'users.type_id = :typeCandidate';
                $parameter['statusDone'] = ProcedureYouSignStatusEnum::MEMBER_DONE;
                $parameter['typeCandidate'] = UserTypeEnum::CANDIDATE;
            }
        }

        if (! empty($livrableId)) {
            $filters[] = 'documents.livrable_id = :livrableId';
            $parameter['livrableId'] = $livrableId;
        }

        if (! empty($programId)) {
            $query .= "\nLEFT JOIN documents_programs AS program_filter ON (documents.id = program_filter.document_id AND program_filter.program_id = :filterProgramId)";
            $query .= "\nLEFT JOIN documents_events AS document_event_filter ON (document_event_filter.document_id = documents.id)";
            $query .= "\nLEFT JOIN events ON (events.program_id = :filterProgramId AND document_event_filter.event_id = events.id)";
            $filters[] = 'program_filter.program_id IS NOT NULL OR events.id IS NOT NULL';
            $parameter['filterProgramId'] = $programId;
        }

        if (! empty($eventId)) {
            $query .= "\nLEFT JOIN documents_events ON (documents.id = documents_events.document_id AND documents_events.event_id = :eventId)";
            $filters[] = 'documents_events.event_id IS NOT NULL';
            $parameter['eventId'] = $eventId;
        }

        return [$query, $filters, $parameter];
    }

    /**
     * @param mixed[] $filters
     * @param mixed[] $parameter
     *
     * @return mixed[]
     */
    private function getDefaultFiltersForCoach(string $query, array $filters, array $parameter): array
    {
        // Apply the default coach filters
        $parameter['currentUserId'] = $this->_getUser()->getId();

        // 1. Coach should get documents related to their programs
        $query .= "\nLEFT JOIN documents_programs AS filter_pc ON (documents.id = filter_pc.document_id)";
        $query .= "\nLEFT JOIN programs_coaches AS filter_pc_coaches ON (filter_pc_coaches.program_id = filter_pc.program_id AND filter_pc_coaches.coach_id = :currentUserId)";

        // 2. Coach should get documents related to their programs
        $query .= "\nLEFT JOIN documents_events AS filter_pe ON (documents.id = filter_pe.document_id)";
        $query .= "\nLEFT JOIN events AS filter_pe_event ON (filter_pe_event.id = filter_pe.event_id AND filter_pe_event.organizer = :currentUserId)";

        // 3. Documents having visibility public and category = toolbox
        // 4. Documents having visibility public and category = home_page and that do not have a program
        $filters[] = '
            filter_pc_coaches.coach_id IS NOT NULL 
            OR filter_pe_event.id IS NOT NULL 
            OR (
                visibility = :visibilityPublic 
                AND (
                    category_id = :categoryToolbox 
                    OR (
                        category_id = :categoryHomepage 
                        AND filter_pc.program_id IS NULL
                    )
                )
            )';

        $parameter['visibilityPublic'] = DocumentEnum::PUBLIC_CODE;
        $parameter['categoryToolbox'] = DocumentCategoryEnum::TOOLBOX;
        $parameter['categoryHomepage'] = DocumentCategoryEnum::HOMEPAGE;

        return [$query, $filters, $parameter];
    }

    /**
     * @return Document[]|ResultIterator
     */
    public function findByFiltersForCoach(
        ?string $search,
        ?string $tagSearch,
        ?string $visibility,
        ?string $sortColumn,
        ?string $sortDirection,
        ?string $avoidProgram,
        ?string $avoidEvent,
        ?string $type,
        ?bool $displayedInHomePage,
        ?bool $avoidHidden,
        ?string $categoryId,
        ?bool $signaturePending,
        ?bool $signedByCoach,
        ?bool $signedByCandidate,
        ?string $livrableId,
        ?string $programId,
        ?string $eventId,
        ?string $createdAt
    ): ResultIterator {
        [$query, $filters, $parameter, $orderBy] = $this->getBaseFilters($search, $tagSearch, $visibility, $sortColumn, $sortDirection, $avoidProgram, $avoidEvent, $type, $displayedInHomePage, $avoidHidden, $categoryId, $createdAt);
        [$query, $filters, $parameter] = $this->getBaseFiltersForCoachAndAdmin($query, $filters, $parameter, $signaturePending, $signedByCoach, $signedByCandidate, $livrableId, $programId, $eventId);
        [$query, $filters, $parameter] = $this->getDefaultFiltersForCoach($query, $filters, $parameter);

        return $this->findFromSql($query, $filters, $parameter, $orderBy);
    }

    /**
     * @return mixed[]
     */
    public function findByFiltersForExport(
        ?string $search,
        ?string $tagSearch,
        ?string $visibility,
        ?string $sortColumn,
        ?string $sortDirection,
        ?string $avoidProgram,
        ?string $avoidEvent,
        ?string $type,
        ?bool $displayedInHomePage,
        ?bool $avoidHidden,
        ?string $categoryId,
        ?bool $signaturePending,
        ?bool $signedByCoach,
        ?bool $signedByCandidate,
        ?string $livrableId,
        ?string $programId,
        ?string $authorId,
        ?string $eventId,
        ?string $createdAt
    ): array {
        [$query, $filters, $parameter] = $this->getBaseFilters($search, $tagSearch, $visibility, $sortColumn, $sortDirection, $avoidProgram, $avoidEvent, $type, $displayedInHomePage, $avoidHidden, $categoryId, $createdAt);
        [$query, $filters, $parameter] = $this->getBaseFiltersForCoachAndAdmin($query, $filters, $parameter, $signaturePending, $signedByCoach, $signedByCandidate, $livrableId, $programId, $eventId);

        if (! empty($authorId)) {
            $filters[] = 'documents.author = :authorId';
            $parameter['authorId'] = $authorId;
        }

        $query .= '
            JOIN users AS author ON (documents.author = author.id)
            LEFT JOIN file_descriptors ON (documents.file_descriptor_id = file_descriptors.id)
            LEFT JOIN documents_programs AS documentProgram ON (documents.id = documentProgram.document_id)
            LEFT JOIN programs AS p ON (p.id = documentProgram.program_id)
            LEFT JOIN documents_events AS documentEvent ON (documents.id = documentEvent.document_id)
            LEFT JOIN events AS e ON (e.id = documentEvent.event_id)
            LEFT JOIN programs AS pe ON (pe.id = e.program_id)
            LEFT JOIN (
                SELECT document_id
                FROM documents_signers
                    JOIN users ON (documents_signers.user_id = users.id)
                WHERE users.type_id = :typeCandidate
                    AND status_signature = :statusDone
                GROUP BY document_id
            ) AS signedByCandidate ON (documents.id = signedByCandidate.document_id)
            LEFT JOIN (
                SELECT document_id
                FROM documents_signers
                    JOIN users ON (documents_signers.user_id = users.id)
                WHERE users.type_id = :typeCoach
                    AND status_signature = :statusDone
                GROUP BY document_id
            ) AS signedByCoach ON (documents.id = signedByCoach.document_id)
            LEFT JOIN (
                SELECT document_id
                FROM documents_signers
                WHERE user_id IS NULL
                    AND status_signature = :statusDone
                GROUP BY document_id
            ) AS signedByN1 ON (documents.id = signedByN1.document_id)
        ';

        $parameter['statusDone'] = ProcedureYouSignStatusEnum::MEMBER_DONE;
        $parameter['typeCandidate'] = UserTypeEnum::CANDIDATE;
        $parameter['typeCoach'] = UserTypeEnum::COACH;

        $result = $this->findCompositionObjects(
            ['documents'],
            $query,
            $filters,
            $parameter,
            'documents.created_at DESC',
            static function (array $tuple, array $row) {
                $document = $tuple[0];
                assert($document instanceof Document);

                return [
                    $document->getName(),
                    $document->getCreatedAt() !== null ? $document->getCreatedAt()->format('d/m/Y') : '',
                    $row['first_name'] . ' ' . $row['last_name'],
                    $document->getCategory() !== null ? $document->getCategory()->getLabel() : '',
                    $row['programName'],
                    $row['eventName'],
                    $document->getDescription(),
                    $document->getType() === DocumentTypeEnum::ARTICLE ? $document->getArticleLink() : '',
                    $row['fileName'],
                    $document->getToBeSigned() ? 'Oui' : 'Non',
                    $row['signedByCandidate'] ? 'Oui' : 'Non',
                    $row['signedByCoach'] ? 'Oui' : 'Non',
                    $row['signedByN1'] ? 'Oui' : 'Non',
                    implode(', ', explode(',', $document->getTags())),
                ];
            },
            [
                'IF (signedByCoach.document_id IS NULL, 0, 1) AS signedByCoach',
                'IF (signedByCandidate.document_id IS NULL, 0, 1) AS signedByCandidate',
                'IF (signedByN1.document_id IS NULL, 0, 1) AS signedByN1',
                'author.first_name',
                'author.last_name',
                'IF (p.name IS NULL, pe.name, p.name) AS programName',
                'e.name AS eventName',
                'file_descriptors.name AS fileName',
            ],
        );

        $return = [];
        foreach ($result as $row) {
            $return[] = $row;
        }

        return $return;
    }

    /**
     * @return Document[]|ResultIterator
     */
    public function findByFiltersForAdmin(
        ?string $search,
        ?string $tagSearch,
        ?string $visibility,
        ?string $sortColumn,
        ?string $sortDirection,
        ?string $avoidProgram,
        ?string $avoidEvent,
        ?string $type,
        ?bool $displayedInHomePage,
        ?bool $avoidHidden,
        ?string $categoryId,
        ?bool $signaturePending,
        ?bool $signedByCoach,
        ?bool $signedByCandidate,
        ?string $livrableId,
        ?string $programId,
        ?string $authorId,
        ?string $eventId,
        ?string $createdAt
    ): ResultIterator {
        [$query, $filters, $parameter, $orderBy] = $this->getBaseFilters($search, $tagSearch, $visibility, $sortColumn, $sortDirection, $avoidProgram, $avoidEvent, $type, $displayedInHomePage, $avoidHidden, $categoryId, $createdAt);
        [$query, $filters, $parameter] = $this->getBaseFiltersForCoachAndAdmin($query, $filters, $parameter, $signaturePending, $signedByCoach, $signedByCandidate, $livrableId, $programId, $eventId);

        if (! empty($authorId)) {
            $filters[] = 'documents.author = :authorId';
            $parameter['authorId'] = $authorId;
        }

        return $this->findFromSql($query, $filters, $parameter, $orderBy);
    }

    /**
     * @return Document[]|ResultIterator
     */
    public function findByFiltersForCandidate(
        ?string $search,
        ?string $tagSearch,
        ?string $categoryId,
        ?string $programId,
        ?string $createdAt,
        ?string $sortColumn,
        ?string $sortDirection
    ): ResultIterator {
        $query = '
            documents 
            LEFT JOIN documents_programs ON (documents.id = documents_programs.document_id)
            LEFT JOIN programs_users ON (programs_users.program_id = documents_programs.program_id AND programs_users.user_id = :currentUserId)
            LEFT JOIN documents_events ON (documents.id = documents_events.document_id)
            LEFT JOIN events_users ON (events_users.event_id = documents_events.event_id AND events_users.user_id = :currentUserId)';

        $filters = [
            'deleted = 0',
            'programs_users.user_id IS NOT NULL OR events_users.user_id IS NOT NULL',
            'documents.category_id != :categoryToolbox',
        ];

        $parameter = [
            'currentUserId' => $this->_getUser()->getId(),
            'categoryToolbox' => DocumentCategoryEnum::TOOLBOX,
        ];

        $orderBy = null;
        if (! empty($sortColumn) && isset(self::SORT_COLUMNS[$sortColumn])) {
            $orderBy = self::SORT_COLUMNS[$sortColumn] . ' ' . ($sortDirection === 'asc' ? 'ASC' : 'DESC');
        }

        if (! empty($search)) {
            $searchList = explode(' ', $search);
            foreach ($searchList as $key => $value) {
                if (in_array(strtolower($value), self::AVOID_SEARCH)) {
                    continue;
                }

                $filters[] = '(documents.name LIKE :value' . $key . ' OR documents.description LIKE :value' . $key . ')';
                $parameter['value' . $key] = '%' . $value . '%';
            }
        }

        if (! empty($tagSearch)) {
            $tags = preg_split('/[\s,]+/', $tagSearch);
            if ($tags !== false) {
                $tagSearchQuery = [];
                foreach ($tags as $k => $tag) {
                    $tagSearchQuery[] = 'FIND_IN_SET(:tag' . $k . ', tags)';
                    $parameter['tag' . $k] = $tag;
                }
                $filters[] = implode(' OR ', $tagSearchQuery);
            }
        }

        if (! empty($categoryId)) {
            $filters[] = 'category_id = :category';
            $parameter['category'] = $categoryId;
        }

        if (! empty($programId)) {
            $query .= "\nLEFT JOIN documents_programs AS program_filter ON (documents.id = program_filter.document_id AND program_filter.program_id = :filterProgramId)";
            $filters[] = 'program_filter.program_id IS NOT NULL';
            $parameter['filterProgramId'] = $programId;
        }

        if (! empty($createdAt)) {
            $date = new DateTimeImmutable($createdAt);
            $filters[] = 'documents.created_at >= :minDate AND documents.created_at <= :maxDate';
            $parameter['minDate'] = $date->format('Y-m-d') . ' 00:00:00';
            $parameter['maxDate'] = $date->format('Y-m-d') . ' 23:59:59';
        }

        $filters[] = '(category_id = :categoryHomePage AND to_be_displayed_in_home_page = :toBeDisplayedInHomePage AND type = :typeArticle) OR category_id = :categoryCustom OR category_id = :categoryLivrable';
        $parameter['categoryHomePage'] = DocumentCategoryEnum::HOMEPAGE;
        $parameter['categoryCustom'] = DocumentCategoryEnum::CUSTOM;
        $parameter['categoryLivrable'] = DocumentCategoryEnum::LIVRABLE;
        $parameter['toBeDisplayedInHomePage'] = true;
        $parameter['typeArticle'] = DocumentTypeEnum::ARTICLE;

        return $this->findFromSql($query, $filters, $parameter, $orderBy);
    }

    protected function getTokenStorage(): TokenStorageInterface
    {
        return $this->tokenStorage;
    }

    protected function getUserRepository(): UserRepository
    {
        return $this->userRepository;
    }
}
