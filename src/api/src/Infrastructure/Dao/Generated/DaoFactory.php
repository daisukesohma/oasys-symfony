<?php
/**
 * This file has been automatically generated by TDBM.
 * DO NOT edit this file, as it might be overwritten.
 */

declare(strict_types=1);

namespace App\Infrastructure\Dao\Generated;

/**
 * The DaoFactory provides an easy access to all DAOs generated by TDBM.
 */
class DaoFactory
{

    /**
     * @var \Psr\Container\ContainerInterface
     */
    private $container = null;

    /**
     * @var \App\Infrastructure\Dao\CoachSpecialityDao|null
     */
    private $coachSpecialityDao = null;

    /**
     * @var \App\Infrastructure\Dao\CompanyDao|null
     */
    private $companyDao = null;

    /**
     * @var \App\Infrastructure\Dao\DocumentDao|null
     */
    private $documentDao = null;

    /**
     * @var \App\Infrastructure\Dao\DocumentCategoryDao|null
     */
    private $documentCategoryDao = null;

    /**
     * @var \App\Infrastructure\Dao\DocumentSignerDao|null
     */
    private $documentSignerDao = null;

    /**
     * @var \App\Infrastructure\Dao\EventModelDao|null
     */
    private $eventModelDao = null;

    /**
     * @var \App\Infrastructure\Dao\EventDao|null
     */
    private $eventDao = null;

    /**
     * @var \App\Infrastructure\Dao\EventRateDao|null
     */
    private $eventRateDao = null;

    /**
     * @var \App\Infrastructure\Dao\FileDescriptorDao|null
     */
    private $fileDescriptorDao = null;

    /**
     * @var \App\Infrastructure\Dao\MigrationVersionDao|null
     */
    private $migrationVersionDao = null;

    /**
     * @var \App\Infrastructure\Dao\ProfessionalCategoryDao|null
     */
    private $professionalCategoryDao = null;

    /**
     * @var \App\Infrastructure\Dao\ProgramCoachingIndividualDao|null
     */
    private $programCoachingIndividualDao = null;

    /**
     * @var \App\Infrastructure\Dao\ProgramModelDao|null
     */
    private $programModelDao = null;

    /**
     * @var \App\Infrastructure\Dao\ProgramOutplacementDao|null
     */
    private $programOutplacementDao = null;

    /**
     * @var \App\Infrastructure\Dao\ProgramPicDao|null
     */
    private $programPicDao = null;

    /**
     * @var \App\Infrastructure\Dao\ProgramDao|null
     */
    private $programDao = null;

    /**
     * @var \App\Infrastructure\Dao\QuestionDao|null
     */
    private $questionDao = null;

    /**
     * @var \App\Infrastructure\Dao\ResetPasswordTokenDao|null
     */
    private $resetPasswordTokenDao = null;

    /**
     * @var \App\Infrastructure\Dao\RightDao|null
     */
    private $rightDao = null;

    /**
     * @var \App\Infrastructure\Dao\RoleDao|null
     */
    private $roleDao = null;

    /**
     * @var \App\Infrastructure\Dao\TodoDao|null
     */
    private $todoDao = null;

    /**
     * @var \App\Infrastructure\Dao\UpdateEmailTokenDao|null
     */
    private $updateEmailTokenDao = null;

    /**
     * @var \App\Infrastructure\Dao\UserDao|null
     */
    private $userDao = null;

    /**
     * @var \App\Infrastructure\Dao\UserTypeDao|null
     */
    private $userTypeDao = null;

    /**
     * @var \App\Infrastructure\Dao\VilleFranceDao|null
     */
    private $villeFranceDao = null;

    public function __construct(\Psr\Container\ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getCoachSpecialityDao() : \App\Infrastructure\Dao\CoachSpecialityDao
    {
        if (!$this->coachSpecialityDao) {
            $this->coachSpecialityDao = $this->container->get('App\\Infrastructure\\Dao\\CoachSpecialityDao');
        }

        return $this->coachSpecialityDao;
    }

    public function setCoachSpecialityDao(\App\Infrastructure\Dao\CoachSpecialityDao $coachSpecialityDao) : void
    {
        $this->coachSpecialityDao = $coachSpecialityDao;
    }

    public function getCompanyDao() : \App\Infrastructure\Dao\CompanyDao
    {
        if (!$this->companyDao) {
            $this->companyDao = $this->container->get('App\\Infrastructure\\Dao\\CompanyDao');
        }

        return $this->companyDao;
    }

    public function setCompanyDao(\App\Infrastructure\Dao\CompanyDao $companyDao) : void
    {
        $this->companyDao = $companyDao;
    }

    public function getDocumentDao() : \App\Infrastructure\Dao\DocumentDao
    {
        if (!$this->documentDao) {
            $this->documentDao = $this->container->get('App\\Infrastructure\\Dao\\DocumentDao');
        }

        return $this->documentDao;
    }

    public function setDocumentDao(\App\Infrastructure\Dao\DocumentDao $documentDao) : void
    {
        $this->documentDao = $documentDao;
    }

    public function getDocumentCategoryDao() : \App\Infrastructure\Dao\DocumentCategoryDao
    {
        if (!$this->documentCategoryDao) {
            $this->documentCategoryDao = $this->container->get('App\\Infrastructure\\Dao\\DocumentCategoryDao');
        }

        return $this->documentCategoryDao;
    }

    public function setDocumentCategoryDao(\App\Infrastructure\Dao\DocumentCategoryDao $documentCategoryDao) : void
    {
        $this->documentCategoryDao = $documentCategoryDao;
    }

    public function getDocumentSignerDao() : \App\Infrastructure\Dao\DocumentSignerDao
    {
        if (!$this->documentSignerDao) {
            $this->documentSignerDao = $this->container->get('App\\Infrastructure\\Dao\\DocumentSignerDao');
        }

        return $this->documentSignerDao;
    }

    public function setDocumentSignerDao(\App\Infrastructure\Dao\DocumentSignerDao $documentSignerDao) : void
    {
        $this->documentSignerDao = $documentSignerDao;
    }

    public function getEventModelDao() : \App\Infrastructure\Dao\EventModelDao
    {
        if (!$this->eventModelDao) {
            $this->eventModelDao = $this->container->get('App\\Infrastructure\\Dao\\EventModelDao');
        }

        return $this->eventModelDao;
    }

    public function setEventModelDao(\App\Infrastructure\Dao\EventModelDao $eventModelDao) : void
    {
        $this->eventModelDao = $eventModelDao;
    }

    public function getEventDao() : \App\Infrastructure\Dao\EventDao
    {
        if (!$this->eventDao) {
            $this->eventDao = $this->container->get('App\\Infrastructure\\Dao\\EventDao');
        }

        return $this->eventDao;
    }

    public function setEventDao(\App\Infrastructure\Dao\EventDao $eventDao) : void
    {
        $this->eventDao = $eventDao;
    }

    public function getEventRateDao() : \App\Infrastructure\Dao\EventRateDao
    {
        if (!$this->eventRateDao) {
            $this->eventRateDao = $this->container->get('App\\Infrastructure\\Dao\\EventRateDao');
        }

        return $this->eventRateDao;
    }

    public function setEventRateDao(\App\Infrastructure\Dao\EventRateDao $eventRateDao) : void
    {
        $this->eventRateDao = $eventRateDao;
    }

    public function getFileDescriptorDao() : \App\Infrastructure\Dao\FileDescriptorDao
    {
        if (!$this->fileDescriptorDao) {
            $this->fileDescriptorDao = $this->container->get('App\\Infrastructure\\Dao\\FileDescriptorDao');
        }

        return $this->fileDescriptorDao;
    }

    public function setFileDescriptorDao(\App\Infrastructure\Dao\FileDescriptorDao $fileDescriptorDao) : void
    {
        $this->fileDescriptorDao = $fileDescriptorDao;
    }

    public function getMigrationVersionDao() : \App\Infrastructure\Dao\MigrationVersionDao
    {
        if (!$this->migrationVersionDao) {
            $this->migrationVersionDao = $this->container->get('App\\Infrastructure\\Dao\\MigrationVersionDao');
        }

        return $this->migrationVersionDao;
    }

    public function setMigrationVersionDao(\App\Infrastructure\Dao\MigrationVersionDao $migrationVersionDao) : void
    {
        $this->migrationVersionDao = $migrationVersionDao;
    }

    public function getProfessionalCategoryDao() : \App\Infrastructure\Dao\ProfessionalCategoryDao
    {
        if (!$this->professionalCategoryDao) {
            $this->professionalCategoryDao = $this->container->get('App\\Infrastructure\\Dao\\ProfessionalCategoryDao');
        }

        return $this->professionalCategoryDao;
    }

    public function setProfessionalCategoryDao(\App\Infrastructure\Dao\ProfessionalCategoryDao $professionalCategoryDao) : void
    {
        $this->professionalCategoryDao = $professionalCategoryDao;
    }

    public function getProgramCoachingIndividualDao() : \App\Infrastructure\Dao\ProgramCoachingIndividualDao
    {
        if (!$this->programCoachingIndividualDao) {
            $this->programCoachingIndividualDao = $this->container->get('App\\Infrastructure\\Dao\\ProgramCoachingIndividualDao');
        }

        return $this->programCoachingIndividualDao;
    }

    public function setProgramCoachingIndividualDao(\App\Infrastructure\Dao\ProgramCoachingIndividualDao $programCoachingIndividualDao) : void
    {
        $this->programCoachingIndividualDao = $programCoachingIndividualDao;
    }

    public function getProgramModelDao() : \App\Infrastructure\Dao\ProgramModelDao
    {
        if (!$this->programModelDao) {
            $this->programModelDao = $this->container->get('App\\Infrastructure\\Dao\\ProgramModelDao');
        }

        return $this->programModelDao;
    }

    public function setProgramModelDao(\App\Infrastructure\Dao\ProgramModelDao $programModelDao) : void
    {
        $this->programModelDao = $programModelDao;
    }

    public function getProgramOutplacementDao() : \App\Infrastructure\Dao\ProgramOutplacementDao
    {
        if (!$this->programOutplacementDao) {
            $this->programOutplacementDao = $this->container->get('App\\Infrastructure\\Dao\\ProgramOutplacementDao');
        }

        return $this->programOutplacementDao;
    }

    public function setProgramOutplacementDao(\App\Infrastructure\Dao\ProgramOutplacementDao $programOutplacementDao) : void
    {
        $this->programOutplacementDao = $programOutplacementDao;
    }

    public function getProgramPicDao() : \App\Infrastructure\Dao\ProgramPicDao
    {
        if (!$this->programPicDao) {
            $this->programPicDao = $this->container->get('App\\Infrastructure\\Dao\\ProgramPicDao');
        }

        return $this->programPicDao;
    }

    public function setProgramPicDao(\App\Infrastructure\Dao\ProgramPicDao $programPicDao) : void
    {
        $this->programPicDao = $programPicDao;
    }

    public function getProgramDao() : \App\Infrastructure\Dao\ProgramDao
    {
        if (!$this->programDao) {
            $this->programDao = $this->container->get('App\\Infrastructure\\Dao\\ProgramDao');
        }

        return $this->programDao;
    }

    public function setProgramDao(\App\Infrastructure\Dao\ProgramDao $programDao) : void
    {
        $this->programDao = $programDao;
    }

    public function getQuestionDao() : \App\Infrastructure\Dao\QuestionDao
    {
        if (!$this->questionDao) {
            $this->questionDao = $this->container->get('App\\Infrastructure\\Dao\\QuestionDao');
        }

        return $this->questionDao;
    }

    public function setQuestionDao(\App\Infrastructure\Dao\QuestionDao $questionDao) : void
    {
        $this->questionDao = $questionDao;
    }

    public function getResetPasswordTokenDao() : \App\Infrastructure\Dao\ResetPasswordTokenDao
    {
        if (!$this->resetPasswordTokenDao) {
            $this->resetPasswordTokenDao = $this->container->get('App\\Infrastructure\\Dao\\ResetPasswordTokenDao');
        }

        return $this->resetPasswordTokenDao;
    }

    public function setResetPasswordTokenDao(\App\Infrastructure\Dao\ResetPasswordTokenDao $resetPasswordTokenDao) : void
    {
        $this->resetPasswordTokenDao = $resetPasswordTokenDao;
    }

    public function getRightDao() : \App\Infrastructure\Dao\RightDao
    {
        if (!$this->rightDao) {
            $this->rightDao = $this->container->get('App\\Infrastructure\\Dao\\RightDao');
        }

        return $this->rightDao;
    }

    public function setRightDao(\App\Infrastructure\Dao\RightDao $rightDao) : void
    {
        $this->rightDao = $rightDao;
    }

    public function getRoleDao() : \App\Infrastructure\Dao\RoleDao
    {
        if (!$this->roleDao) {
            $this->roleDao = $this->container->get('App\\Infrastructure\\Dao\\RoleDao');
        }

        return $this->roleDao;
    }

    public function setRoleDao(\App\Infrastructure\Dao\RoleDao $roleDao) : void
    {
        $this->roleDao = $roleDao;
    }

    public function getTodoDao() : \App\Infrastructure\Dao\TodoDao
    {
        if (!$this->todoDao) {
            $this->todoDao = $this->container->get('App\\Infrastructure\\Dao\\TodoDao');
        }

        return $this->todoDao;
    }

    public function setTodoDao(\App\Infrastructure\Dao\TodoDao $todoDao) : void
    {
        $this->todoDao = $todoDao;
    }

    public function getUpdateEmailTokenDao() : \App\Infrastructure\Dao\UpdateEmailTokenDao
    {
        if (!$this->updateEmailTokenDao) {
            $this->updateEmailTokenDao = $this->container->get('App\\Infrastructure\\Dao\\UpdateEmailTokenDao');
        }

        return $this->updateEmailTokenDao;
    }

    public function setUpdateEmailTokenDao(\App\Infrastructure\Dao\UpdateEmailTokenDao $updateEmailTokenDao) : void
    {
        $this->updateEmailTokenDao = $updateEmailTokenDao;
    }

    public function getUserDao() : \App\Infrastructure\Dao\UserDao
    {
        if (!$this->userDao) {
            $this->userDao = $this->container->get('App\\Infrastructure\\Dao\\UserDao');
        }

        return $this->userDao;
    }

    public function setUserDao(\App\Infrastructure\Dao\UserDao $userDao) : void
    {
        $this->userDao = $userDao;
    }

    public function getUserTypeDao() : \App\Infrastructure\Dao\UserTypeDao
    {
        if (!$this->userTypeDao) {
            $this->userTypeDao = $this->container->get('App\\Infrastructure\\Dao\\UserTypeDao');
        }

        return $this->userTypeDao;
    }

    public function setUserTypeDao(\App\Infrastructure\Dao\UserTypeDao $userTypeDao) : void
    {
        $this->userTypeDao = $userTypeDao;
    }

    public function getVilleFranceDao() : \App\Infrastructure\Dao\VilleFranceDao
    {
        if (!$this->villeFranceDao) {
            $this->villeFranceDao = $this->container->get('App\\Infrastructure\\Dao\\VilleFranceDao');
        }

        return $this->villeFranceDao;
    }

    public function setVilleFranceDao(\App\Infrastructure\Dao\VilleFranceDao $villeFranceDao) : void
    {
        $this->villeFranceDao = $villeFranceDao;
    }
}