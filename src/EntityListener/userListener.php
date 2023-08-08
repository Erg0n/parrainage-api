<?php


namespace App\EntityListener;

use App\Entity\Transaction;
use App\Entity\User;
use App\Repository\TransactionRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class userListener extends AbstractController
{
    public function __construct(private UserPasswordHasherInterface $hasher, private UserRepository $userRepository, private TransactionRepository $transactionRepository, private EntityManagerInterface $entityManager)
    {
    }

    public function prePersist(User $user)
    {
        $this->encodePass($user);
        $this->createCodeParrainnage($user);
        $this->checkHasParrain($user);
    }

    public function preUpdate(User $user)
    {
        $this->encodePass($user);
    }

    //Encodage du mot de passe
    private function encodePass(User $user)
    {

        $user->setPassword(
            $this->hasher->hashPassword(
                $user,
                $user->getPassword()
            )
        );
    }

    private function createCodeParrainnage(User $user)
    {

        $nom = trim($user->getNom());
        $prenom = trim($user->getPrenom());
        $contact = trim($user->getContact());

        if (empty($nom) || empty($prenom) || empty($contact)) {
            return;
        }

        $parts = [];
        $parts[] = substr($nom, 0, 2);
        $parts[] = substr($prenom, 0, 2);
        $parts[] = substr($contact, 10);

        $codeParrainnage = implode($parts);

        $row = $this->userRepository->findByCodeparrainage($codeParrainnage);

        if (!empty($row)) {
            $prefix = '';
            foreach ($row as $value) {
                if (strlen($value->getCodeParrainage()) > 6) {
                    $flag = substr($value->getCodeParrainage(), 6);
                    if ($prefix < $flag) {
                        $prefix = $flag;
                    }
                }
            }

            if (empty($prefix)) {
                $codeParrainnage = $codeParrainnage . 'a';
            } else {
                $codeParrainnage = $codeParrainnage . ++$prefix;
            }
        }

        $user->setCodeParrainage($codeParrainnage);
    }

    private function checkHasParrain(User $user)
    {
        $codeParrain = $user->getCodeParrain();
        if ($codeParrain !== null) {
            $dotation = (int)$this->getParameter('app.montant_parrain'); //Dotation donné à un parrain quand son code est enregistré
            $idParrain = $this->userRepository->findByCodeparrain($codeParrain);
            if ($idParrain === null) {
                return;
            }

            //Fait la mise à jour du solde du parrain avec la dotation
            $parrain = $this->userRepository->find($idParrain);
            $solde = (int)$parrain->getSolde();
            $parrain->setSolde($solde + $dotation);
            $this->entityManager->persist($parrain);
            $this->entityManager->flush();

            //Définit le code du parrain de l'utilisateur courrant
            $user->setCodeParrain($parrain->getCodeParrainage());

            //Crée la transaction relative au parrainnage
            $transaction = new Transaction();
            $transaction->setMontant($dotation);
            $transaction->setCreditedId($parrain);
            $transaction->setEmailSender($user->getEmail());
            $this->entityManager->persist($transaction);
            $this->entityManager->flush();
        }
    }
}
