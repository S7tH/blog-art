<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use FM\ElfinderBundle\Form\Type\ElFinderType;

use Symfony\Component\OptionsResolver\OptionsResolver;


class ArticleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('mainImage', ElFinderType::class, array('instance' => 'form', 'enable' => true))
            ->add('title', TextType::class)
            ->add('subtitle', TextType::class)
            ->add('description',CKEditorType::class, array(
                'required' => false
                    ))
            ->add('save', SubmitType::class, array('label' => 'Enregistrer'));      
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'intention' => 'task_form',/*csrf key uniq per form*/
            'data_class' => 'AppBundle\Entity\Article'
        ));
    }
}