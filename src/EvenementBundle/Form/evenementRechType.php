<?php

namespace EvenementBundle\Form;


use Doctrine\DBAL\Types\DateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class evenementRechType extends AbstractType
{
    /**
     * {@inheritdoc}
*/
public function buildForm(FormBuilderInterface $builder, array $options)
{
    $builder->add('lieu' , TextType::class , array('required' => false))
            ->add('communaute',ChoiceType::class , array('choices' => array(' ' => ' ',
                                                                                       'sport' => 'sport',
                                                                                       'music' => 'music',
                                                                                        'nature' =>'nature'))
                                                            ,array('required' => false))
            ->add('recherche' , SubmitType::class);

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
