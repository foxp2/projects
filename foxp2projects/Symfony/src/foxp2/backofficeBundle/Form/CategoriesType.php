<?php

namespace foxp2\backofficeBundle\Form;

use foxp2\backofficeBundle\Entity\CategoriesRepository;
use foxp2\backofficeBundle\Form\EventListener\CategoriesDateSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CategoriesType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {        
        $builder            
            ->add('categoriesName','text',array('label'=>'<strong>Nom de la catégorie :</strong>'))
            ->add('categoriesTitle','text',array('label' => '<strong>Titre de la catégorie :</strong>'))
            ->add('categoriesSubTitle', 'text', array('label' => '<strong>Sous titre de la catégorie :</strong><br /><em class="muted">facultatif</em>', 'required' => false))
            ->add('parentId','entity', array(
                  'class' => 'foxp2backofficeBundle:Categories',
                  'empty_value' => 'choisir ...',
                  'property' => 'categoriesName',
                  'query_builder' => function(CategoriesRepository $er){
                            return $er->getCategoriesList();                              
                  },                  
                  'required' => false,
                  'label' => '<strong>Catégorie parente :</strong>'                  
                 ))        
            ->add('categoriesDescription', 'ckeditor',array('label' => '<strong>Description de la catégorie :</strong>'));
                 
        $builder->addEventSubscriber(new CategoriesDateSubscriber());
        
    }
   
     /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(            
            'data_class' => 'foxp2\backofficeBundle\Entity\Categories',            
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'foxp2_backofficeBundle_categoriestype';
    }
}
