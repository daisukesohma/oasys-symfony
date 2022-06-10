<?php

declare(strict_types=1);

namespace App\Application\User;

use App\Domain\Enum\EventTypeEnum;
use App\Domain\Enum\UserTypeEnum;
use App\Domain\Repository\UserRepository;
use function array_map;
use function implode;
use function str_replace;

final class ExportUsersCrossTalent
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @return string[]
     */
    protected function getHeaderValues(): array
    {
        return [
            'MATRICULE',
            'TYPE',
            'CIVILITE',
            'DATE DE NAISSANCE',
            'NOM',
            'PRENOM',
            'COACH CONSULTANT REFERENT',
            'TYPE DE PRESTATATION',
            'SOCIETE',
            'FONCTION',
            'DATE D\'ANCIENETE DANS L\'ENTREPRISE',
            'ANCIENNETE DANS LA FONTION',
            'SERVICE',
            'VILLE',
            'DEPARTEMENT',
            'CODE POSTAL',
            'REMUNERATION ANNUELLE BRUTE',
            'CATEGORIE PROFESSIONNELLE',
            'EMAIL',
            'TELEPHONE',
            'ADRESSE',
        ];
    }

    /**
     * @param string[] $types
     */
    public function export(?string $search = null, ?string $companyName = null, ?array $types = [], ?string $roleId = null, ?string $companyId = null, ?string $programId = null, ?string $coachId = null, ?string $sortColumn = 'createdAt', ?string $sortDirection = 'desc'): string
    {
        $users = $this->userRepository->findByFilters($search, $companyName, $types, $roleId, $companyId, $programId, $coachId, $sortColumn, $sortDirection);
        $rows = [$this->getHeaderValues()];

        foreach ($users as $user) {
            $professionalCategory = $user->getProfessionalCategory();
            if ($user->getType()->getId() !== UserTypeEnum::CANDIDATE && ! isset($rows[$user->getId()])) {
                $rows[$user->getId()] = [
                    $user->getId(),
                    $user->getType()->getId(),
                    $user->getCivility() === 'mme' ? 'Madame' : 'Monsieur',
                    $user->getBirthDate() !== null ? $user->getBirthDate()->format('d/m/Y') : null,
                    $user->getLastName(),
                    $user->getFirstName(),
                    $user->getCoach() !== null ? $user->getCoach()->getId() : null,
                    $user->getProgramType(),
                    $user->getCompany() !== null ? $user->getCompany()->getName() : null,
                    $user->getFunction() ? $user->getFunction() :'',
                    $user->getSeniorityDate() !== null ? $user->getSeniorityDate()->format('d/m/Y') : null,
                    $user->getPreviousFunction(),
                    $user->getService(),
                    $user->getUserCity(),
                    $user->getVille() && $user->getDepartment() && $user->getPostCode() ? $user->getVille() . ' ' . $user->getDepartment() . ' ' . $user->getPostCode() : '',
                    $user->getUserCodePostal(),
                    $user->getAnnualCompensation(),
                    $professionalCategory !== null ? $professionalCategory->getLabel() : '',
                    $user->getEmail(),
                    (string) $user->getPhone(),
                    str_replace(["\r", "\n", "\t", ',', '.', ';', ':', '<', '>'], ' ', $user->getAddress() ?? ''),
                ];
            } else {
                if (empty($user->getEventsByEventsUsers())) {
                    continue;
                }
                foreach ($user->getEventsByEventsUsers() as $event) {
                    if ($event->getType() !== EventTypeEnum::INDIVIDUAL_SESSION || isset($rows[$user->getId()])) {
                        continue;
                    }

                    $rows[$user->getId()] = [
                        $user->getId(),
                        $user->getType()->getId(),
                        $user->getCivility() === 'mme' ? 'Madame' : 'Monsieur',
                        $user->getBirthDate() !== null ? $user->getBirthDate()->format('d/m/Y') : null,
                        $user->getLastName(),
                        $user->getFirstName(),
                        $user->getCoach() !== null ? $user->getCoach()->getId() : null,
                        $user->getProgramType(),
                        $user->getCompany() !== null ? $user->getCompany()->getName() : null,
                        $user->getFunction() ? $user->getFunction() :'',
                        $user->getSeniorityDate() !== null ? $user->getSeniorityDate()->format('d/m/Y') : null,
                        $user->getPreviousFunction(),
                        $user->getService(),
                        $user->getUserCity(),
                        $user->getVille() && $user->getDepartment() && $user->getPostCode() ? $user->getVille() . ' ' . $user->getDepartment() . ' ' . $user->getPostCode() : '',
                        $user->getUserCodePostal(),
                        $user->getAnnualCompensation(),
                        $professionalCategory !== null ? $professionalCategory->getLabel() : '',
                        $user->getEmail(),
                        (string) $user->getPhone(),
                        str_replace(["\r", "\n", "\t", ',', '.', ';', ':', '<', '>'], ' ', $user->getAddress() ?? ''),
                    ];
                }
            }
        }

        return implode(
            "\n",
            array_map(
                static fn (array $row) => implode(
                    ',',
                    array_map(static fn (?string $column) => $column === null ? '' : str_replace(',', '\\,', $column), $row)
                ),
                $rows
            )
        );
    }
}
