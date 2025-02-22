<?php 
    namespace App\Service;

    use Symfony\Bundle\SecurityBundle\Security;
    use Symfony\Component\HttpFoundation\RedirectResponse;
    use Symfony\Component\Routing\RouterInterface;

    class RoleCheckerService
    {
        private Security $security;
        private RouterInterface $router;

        public function __construct(Security $security, RouterInterface $router)
        {
            $this->security = $security;
            $this->router = $router;
        }

        public function checkManagerRole(): ?RedirectResponse
        {
            if (!$this->security->isGranted('ROLE_MANAGER')) {
                return new RedirectResponse($this->router->generate('app_home')); // Remplace par ta page de redirection
            }

            return null;
        }
    }

?>