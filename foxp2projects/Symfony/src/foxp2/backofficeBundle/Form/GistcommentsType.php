<?php

namespace foxp2\backofficeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class GistcommentsType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
            $builder           
            ->add('idGist','hidden',array('label' => 'Id gist','read_only'=>true))
            ->add('filenameGist','text',array('label' => 'Filename', 'read_only' => true))
            ->add('comments','ckeditor',array('label' => 'Comments'))
        ;
           
        
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'foxp2\backofficeBundle\Entity\Gistcomments',            
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'foxp2_backofficebundle_gistcomments';
    }
}
