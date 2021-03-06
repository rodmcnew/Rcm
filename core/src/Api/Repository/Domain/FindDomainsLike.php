<?php

namespace Rcm\Api\Repository\Domain;

use Doctrine\ORM\EntityManager;
use Rcm\Entity\Domain;

/**
 * @author James Jervis - https://github.com/jerv13
 */
class FindDomainsLike
{
    /**
     * @var \Rcm\Repository\Domain
     */
    protected $repository;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(
        EntityManager $entityManager
    ) {
        $this->repository = $entityManager->getRepository(
            Domain::class
        );
    }

    /**
     * @param string $domainNameSearch
     * @param array  $options
     *
     * @return Domain[]
     */
    public function __invoke(
        string $domainNameSearch,
        array $options = []
    ) {
        $domainsQueryBuilder = $this->repository->createQueryBuilder('domain');
        $domainsQueryBuilder->where('domain.domain LIKE :domainSearchParam');

        $query = $domainsQueryBuilder->getQuery();
        $query->setParameter('domainSearchParam', $domainNameSearch);

        return $query->getResult();
    }
}
