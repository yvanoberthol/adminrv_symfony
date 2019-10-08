<?php

namespace App\Form;

use App\Entity\Medecin;
use App\Entity\Specialite;
use App\Repository\SpecialiteRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MedecinType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //$nom = 'o';
        $builder
            ->add('nom',TextType::class)
            ->add('prenom',TextType::class)
            ->add('email',EmailType::class)
            ->add('matricule',TextType::class)
            ->add('telephone',NumberType::class)
            ->add('ville',TextType::class)
            ->add('date_naissance',DateType::class)
            ->add('image',ImageType::class)
            ->add('specialites',EntityType::class,
                [
                    'class'=>Specialite::class,
                    'choice_label'=>'nom',
                    'multiple'=>true,
                    /*'query_builder'=>function(SpecialiteRepository $specialiteRepository)
                        use($nom){
                            return $specialiteRepository->specialiteLikeNom($nom);
                    }*/
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Medecin::class,
        ]);
    }
}
