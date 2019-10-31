<?php

namespace Rcm\SwitchUser\Service;

use Rcm\Acl\IsAllowedByUser;
use Rcm\SwitchUser\Acl\DoesAclSayUserCanSU;
use RcmUser\Api\Authentication\GetIdentity;
use RcmUser\Api\GetPsrRequest;
use RcmUser\User\Entity\UserInterface;

/**
 * @author James Jervis - https://github.com/jerv13
 */
class SwitchUserAclService
{
    /**
     * @var DoesAclSayUserCanSU
     */
    protected $doesAclSayUserCanSU;

    /**
     * @var GetIdentity
     */
    protected $getIdentity;

    /**
     * @var SwitchUserService
     */
    protected $switchUserService;

    public function __construct(
        DoesAclSayUserCanSU $doesAclSayUserCanSU,
        GetIdentity $getIdentity,
        SwitchUserService $switchUserService
    ) {
        $this->doesAclSayUserCanSU = $doesAclSayUserCanSU;
        $this->getIdentity = $getIdentity;
        $this->switchUserService = $switchUserService;
    }

    /**
     * getAclUser
     *
     * @param $user
     *
     * @return mixed|null
     */
    public function getAclUser($user)
    {
        if (empty($user)) {
            return null;
        }

        $adminUser = $this->switchUserService->getImpersonatorUser($user);
        $targetUser = $user;

        if (empty($adminUser)) {
            $adminUser = $targetUser;
        }

        return $adminUser;
    }

    /**
     * isAllowed
     *
     * @param string $resourceId
     * @param string $privilege
     * @param null $providerId // @deprecated
     * @param UserInterface $user
     *
     * @return bool|mixed
     */
    public function isUserAllowed($resourceId, $privilege, $providerId = null, $user)
    {
        $suUser = $this->getAclUser($user);

        if (empty($suUser)) {
            return false;
        }

        return $this->isUserAllowed->__invoke(
            $suUser,
            $resourceId,
            $privilege
        );
    }

    /**
     * isImpersonatorUserAllowed
     *
     * @param string $resourceId
     * @param string $privilege
     * @param null $providerId // @deprecated
     * @param UserInterface $user
     *
     * @return bool|mixed
     */
    public function isImpersonatorUserAllowed(
        $resourceId,
        $privilege,
        $providerId = null,
        $user
    ) {
        $user = $this->switchUserService->getImpersonatorUser($user);

        if (empty($user)) {
            return false;
        }

        return $this->isUserAllowed->__invoke(
            $user,
            $resourceId,
            $privilege
        );
    }

    /**
     * isCurrentImpersonatorUserAllowed
     *
     * @param $resourceId
     * @param $privilege
     * @param $providerId
     *
     * @return bool|mixed
     */
    public function isCurrentImpersonatorUserAllowed(
        $resourceId,
        $privilege,
        $providerId = null
    ) {
        $psrRequest = GetPsrRequest::invoke();

        $user = $this->getIdentity->__invoke($psrRequest);

        return $this->isImpersonatorUserAllowed(
            $resourceId,
            $privilege,
            $providerId,
            $user
        );
    }

    /**
     * isSuAllowed
     *
     * this is only a basic access check,
     * the restrictions should catch and log any access attempts
     *
     * @param $suUser
     *
     * @return bool|mixed
     */
    public function isSuAllowed($suUser)
    {
        return $this->doesAclSayUserCanSU->__invoke($suUser);
    }

    /**
     * currentUserIsAllowed
     *
     * @return bool|mixed
     */
    public function currentUserIsSuAllowed()
    {
        $psrRequest = GetPsrRequest::invoke();

        $user = $this->getIdentity->__invoke($psrRequest);

        $adminUser = $this->getAclUser($user);

        return $this->doesAclSayUserCanSU->__invoke($adminUser);
    }
}
