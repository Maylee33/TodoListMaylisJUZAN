<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use AppBundle\Entity\Task;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', null, [
                'label' => false, 'attr' => array('placeholder' => 'Entrez votre tâche')
            ])
            ->add('status', ChoiceType::class, array(
                'choices'  => array(
                    'Fait' => 'done',
                    'À faire' => 'undone',
    ))
);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Task::class,
            'attr' => ['novalidate' => 'novalidate'],
        ));
    }
}
