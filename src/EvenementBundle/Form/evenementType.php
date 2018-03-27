<?php

namespace EvenementBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class evenementType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nomEvenement', null, array ('label' => 'label.nomEvenement'))
                ->add('dateDebut' , DateType::class , array ('label' => 'label.dateDebut'))
                ->add('lieu' , null , array ('label' => 'label.lieu'))
                ->add('nombreMax' , null , array('label' => 'label.nombreMax'))
                ->add('communaute' , ChoiceType::class, array('choices' => array('sport' => 'sport',
                                                                                            'music' => 'music',
                                                                                            'nature' =>'nature'))) ;
             //   ->add('User' , null , array('label' => 'label.User'));

    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'EvenementBundle\Entity\evenement'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'evenementbundle_evenement';
    }


}
