<?php
namespace User\Service;

use Acl\Entity\Role;
use User\Entity\Polo;
use User\Entity\Turma;
use User\Entity\User;
use Zend\Crypt\Password\Bcrypt;
use Zend\Math\Rand;
use Zend\Hydrator;

/**
 * This service is responsible for adding/editing users
 * and changing user password.
 */
class UserManager
{
    /**
     * Doctrine entity manager.
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * Constructs the service.
     */
    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;

        $this->entity = User::class;
     
    }
    
    /**
     * This method adds a new user.
     */
    public function addUser(array $data)
    {

        // Do not allow several users with the same email address.
        $entity = new $this->entity($data);

        $role = $this->entityManager->getReference(Role::class, $data['role']);
        $entity->setRole($role);

        $polo = $this->entityManager->getReference(Polo::class, $data['polo']);
        $entity->setPolo($polo);


        // Encrypt password and store the password in encrypted state.
        $bcrypt = new Bcrypt();
        $passwordHash = $bcrypt->create($data['password']);
        $entity->setPassword($passwordHash);

                 
        // Add the entity to the entity manager.
        $this->entityManager->persist($entity);
        
        // Apply changes to database.
        $this->entityManager->flush();

        return $entity;
    }

    public function registerUser(array $data)
    {
       /** @var \User\Entity\User $user */
            $user = new $this->entity($data);

            $user->setEmail($data['email']);
            $user->setFullName($data['full_name']);

            $role = $this->entityManager->getReference(Role::class, '1');
            $user->setRole($role);

            $polo = $this->entityManager->getReference(Polo::class, $data['polo']);
            $user->setPolo($polo);

            $bcrypt = new Bcrypt();
            $passwordHash = $bcrypt->create($data['password']);
            $user->setPassword($passwordHash);
            $user->setStatus(User::STATUS_RETIRED);
            $user->setCelular($data['celular']);
            $user->setProfissao($data['profissao']);
            $user->setIsteacher('0');


            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $user;

    }
    
    /**
     * This method updates data of an existing user.
     */
    public function updateUser($user, $data)
    {

        unset($data['password']);
        unset($data['confirm_password']);

        $entity = (new Hydrator\ClassMethods())->hydrate($data, $user);

        $role = $this->entityManager->getReference(Role::class, $data['role']);
        $entity->setRole($role);

        $polo = $this->entityManager->getReference(Polo::class, $data['polo']);
        $user->setPolo($polo);


        $this->entityManager->persist($entity);

        // Apply changes to database.
        $this->entityManager->flush();

        return $entity;
    }

    public function updateImagem($user)
    {

            $user->setFile('');
            $user->setThumb('');

            // Apply changes to database.
            $this->entityManager->flush();

            return true;

    }

    public function updateThumb($user, $data)
    {
        $thumb = $data['thumb'];

        $user->setThumb($thumb);

        // Apply changes to database.
        $this->entityManager->flush();

        return true;
    }

    public function updateContrato($user)
    {

        $user->setContrato('');

        // Apply changes to database.
        $this->entityManager->flush();

        return true;

    }

    public function createAdminUserIfNotExists()
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy([]);
        if ($user==null) {
            $user = new User();
            $user->setEmail('admin@example.com');
            $user->setFullName('Admin');
            $bcrypt = new Bcrypt();
            $passwordHash = $bcrypt->create('Secur1ty');        
            $user->setPassword($passwordHash);
            $user->setStatus(User::STATUS_ACTIVE);
            $user->setDateCreated(date('Y-m-d H:i:s'));
            
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }
    }

    public function checkUserExists($email) {
        
        $user = $this->entityManager->getRepository(User::class)
                ->findOneByEmail($email);
        
        return $user !== null;
    }

    public function activate($key)
    {
        $repo = $this->entityManager->getRepository(User::class);

        $user = $repo->findOneByActivationKey($key);


        if($user == null){
            return false;
        }else{

            $salt = base64_encode(Rand::getBytes(8, true));
            $novoActivation = md5($user->getEmail() .  $salt);

            $user->setStatus('1');
            $user->setActivationKey($novoActivation);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return true;
        }
    }

    public function validatePassword($user, $password) 
    {
        $bcrypt = new Bcrypt();
        $passwordHash = $user->getPassword();
        
        if ($bcrypt->verify($password, $passwordHash)) {
            return true;
        }
        
        return false;
    }

    public function generatePasswordResetToken($user)
    {

        // Generate a token.
        $token = Rand::getString(32, '0123456789abcdefghijklmnopqrstuvwxyz', true);
        $user->setPasswordResetToken($token);

        $currentDate = date('Y-m-d H:i:s');
        $user->setPasswordResetTokenCreationDate($currentDate);

        $this->entityManager->flush();

        return $token;


    }

    public function validatePasswordResetToken($passwordResetToken)
    {
        $user = $this->entityManager->getRepository(User::class)
                ->findOneByPasswordResetToken($passwordResetToken);
        
        if($user==null) {
            return false;
        }
        
        $tokenCreationDate = $user->getPasswordResetTokenCreationDate();
        $tokenCreationDate = strtotime($tokenCreationDate);
        
        $currentDate = strtotime('now');
        
        if ($currentDate - $tokenCreationDate > 24*60*60) {
            return false; // expired
        }
        
        return true;
    }

    public function setNewPasswordByToken($passwordResetToken, $newPassword)
    {
        if (!$this->validatePasswordResetToken($passwordResetToken)) {
           return false; 
        }
        
        $user = $this->entityManager->getRepository(User::class)
                ->findOneByPasswordResetToken($passwordResetToken);
        
        if ($user==null) {
            return false;
        }
                
        // Set new password for user        
        $bcrypt = new Bcrypt();
        $passwordHash = $bcrypt->create($newPassword);        
        $user->setPassword($passwordHash);
                
        // Remove password reset token
        $user->setPasswordResetToken(null);
        $user->setPasswordResetTokenCreationDate(null);
        
        $this->entityManager->flush();
        
        return true;
    }

    public function changePassword($user, $data)
    {
       // $oldPassword = $data['old_password'];
        
//        // Check that old password is correct
//        if (!$this->validatePassword($user, $oldPassword)) {
//            return false;
//        }
        
        $newPassword = $data['new_password'];
        
        // Check password length
        if (strlen($newPassword)<6 || strlen($newPassword)>64) {
            return false;
        }
        
        // Set new password for user        
        $bcrypt = new Bcrypt();
        $passwordHash = $bcrypt->create($newPassword);
        $user->setPassword($passwordHash);
        
        // Apply changes
        $this->entityManager->flush();

        return true;
    }
    
}

