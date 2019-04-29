<?php
    namespace App\Security;

    use App\Entity\Patient;
    use Doctrine\ORM\EntityManagerInterface;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
    use Symfony\Component\Security\Core\Exception\AuthenticationException;
    use Symfony\Component\Security\Core\User\UserInterface;
    use Symfony\Component\Security\Core\User\UserProviderInterface;
    use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

    class TokenAuthenticator extends AbstractGuardAuthenticator
    {
        private $em;

        public function __construct(EntityManagerInterface $em)
        {
            $this->em = $em;
        }

        /**
         * Called on every request to decide if this authenticator should be
         * used for the request. Returning false will cause this authenticator
         * to be skipped.
         * @param \Symfony\Component\HttpFoundation\Request $request
         * @return bool
         */
        public function supports(Request $request)
        {
            if ($request->query->has('key') || $request->headers->has('X-AUTH-TOKEN')) {
                return true;
            } else {
                return false;
            }
        }

        /**
         * Called on every request. Return whatever credentials you want to
         * be passed to getUser() as $credentials.
         * @param \Symfony\Component\HttpFoundation\Request $request
         * @return array
         */
        public function getCredentials(Request $request)
        {
            if ($request->query->has('key')) {
                return [
                    'token' => $request->query->get('key'),
                ];
            } else {
                return [
                    'token' => $request->headers->get('X-AUTH-TOKEN'),
                ];
            }
        }

        public function getUser($credentials, UserProviderInterface $userProvider)
        {
            $apiToken = $credentials['token'];

            if (null === $apiToken) {
                return;
            }

            // if a User object, checkCredentials() is called
            return $this->em->getRepository(Patient::class)
                ->findOneBy(['apiToken' => $apiToken]);
        }

        public function checkCredentials($credentials, UserInterface $user)
        {
            // check credentials - e.g. make sure the password is valid
            // no credential check is needed in this case

            // return true to cause authentication success
            return true;
        }

        /**
         * @param \Symfony\Component\HttpFoundation\Request                            $request
         * @param \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token
         * @param string                                                               $providerKey
         * @return null|\Symfony\Component\HttpFoundation\Response
         */
        public function onAuthenticationSuccess( Request $request, TokenInterface $token, $providerKey)
        {
            // on success, let the request continue
            return null;
        }

        /**
         * @param \Symfony\Component\HttpFoundation\Request                          $request
         * @param \Symfony\Component\Security\Core\Exception\AuthenticationException $exception
         * @return null|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
         */
        public function onAuthenticationFailure( Request $request, AuthenticationException $exception)
        {
            $data = [
                'message' => strtr($exception->getMessageKey(), $exception->getMessageData())

                // or to translate this message
                // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
            ];

            return new JsonResponse($data, Response::HTTP_FORBIDDEN);
        }

        /**
         * Called when authentication is needed, but it's not sent
         * @param \Symfony\Component\HttpFoundation\Request                               $request
         * @param \Symfony\Component\Security\Core\Exception\AuthenticationException|null $authException
         * @return \Symfony\Component\HttpFoundation\JsonResponse
         */
        public function start(Request $request, AuthenticationException $authException = null)
        {
            $data = [
                // you might translate this message
                'message' => 'Authentication Required'
            ];

            return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
        }

        public function supportsRememberMe()
        {
            return false;
        }
    }