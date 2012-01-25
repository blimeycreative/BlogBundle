<?php

namespace Oxygen\BlogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class BlogType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('author')
            ->add('blog')
            ->add('image')
            ->add('tags')
            ->add('categories', 'entity', array(
                'class' => 'OxygenBlogBundle:Category',
                'multiple' => true,
                'expanded' => true
            ))
        ;
        
    }

    public function getName()
    {
        return 'oxygen_blogbundle_blogtype';
    }
}
