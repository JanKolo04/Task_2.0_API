<?php

    namespace App\Service;

    use Symfony\Component\Serializer\SerializerInterface;
    use App\Repository\UserRepository;
    use Doctrine\ORM\EntityManagerInterface;


    class UsersHelper
    {
        public $serializer = null;
        public $userRepository = null;
        public $entityManager = null;

        public function __construct(EntityManagerInterface $entityManager, SerializerInterface $serializer, 
                                    UserRepository $userRepository) 
        {
            $this->serializer = $serializer;
            $this->userRepository = $userRepository;
            $this->entityManager = $entityManager;
        }

        public function checkUserExistByEmail(object $user): ?string
        {   
            // get email from form
            $email = $user->getEmail();
            // try to find user by email
            $find = $this->userRepository->findBy(array('email' => $email));

            // check whether user exist with entered email
            if($find) {
                // if user exist return message
                $message = "User exist with email ".$email;
                return $message;
            }
            return null;
        }

        public function hashPassword($user): string
        {
            // get entered password
            $passoword = $user->getPassword();

            $options = [
                'cost' => 12,
            ];
            $hashedPassword = password_hash($passoword, PASSWORD_BCRYPT, $options);

            // return hashed password
            return $hashedPassword;
        }

        public function checkUserExistByEmailForEdit(object $form, int $user_id): ?string
        {
            // get email from form
            $email = $form['email']->getData();
            // try to find user by email
            $find = $this->userRepository->findUserByEmailForEdit($email, $user_id);

            // check whether user exist with entered email
            if($find) {
                // if user exist return message
                $message = "User exist with email ".$email;
                return $message;
            }
            return null;
        }
    }

?>