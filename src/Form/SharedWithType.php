<?php

namespace App\Form;

use App\Entity\SharedWith;
use App\Entity\Trial;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Repository\UserRepository;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;
// call the trial public release service
use App\Service\PublicReleaseTrial;

class SharedWithType extends AbstractType
{
    private $router;
    private $security;
    private $userRepo;
    private $pubRelTrialService;

    function __construct(RouterInterface $router, Security $security, UserRepository $userRepo, PublicReleaseTrial $pubRelTrialService){
        $this->router = $router;
        $this->userRepo = $userRepo;
        $this->security = $security;
        $this->pubRelTrialService = $pubRelTrialService;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $toUrlTrial = $this->router->generate('trial_create');
        
        $builder
            ->add('trial', EntityType::class, [
                'class' => Trial::class,
                'help_html' => true,
                'placeholder' => '',
                'query_builder' => $this->pubRelTrialService->getOwnedTrials(),
                'help' => 'Add a new <a href="' . $toUrlTrial .'" target="_blank">Trial</a>'
            ])            
            ->add('user', EntityType::class, [
                'class' => User::class,
                'placeholder' => '',
                'query_builder' => function() {
                    $user = $this->security->getUser();
                    $query = $this->userRepo->createQueryBuilder('u')
                            ->where('u.id != :userId')
                            ->setParameter(':userId', $user->getId());
                            
                    return $query;
                }
            ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SharedWith::class,
        ]);
    }
}
