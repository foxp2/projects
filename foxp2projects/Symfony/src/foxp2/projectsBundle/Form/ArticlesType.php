<?php

namespace foxp2\projectsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use foxp2\projectsBundle\Entity\CategoriesRepository;

class ArticlesType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('articleName')
            ->add('articleTitle')
            ->add('articleSubTitle')
            ->add('articleShortDescription')
            ->add('articleLongDescription')
            ->add('articleDateCreated')
            ->add('articleDateModified')
            ->add('articleGistReference')
            //->add('category') comment : Formatage du menu déroulant -> use foxp2\projectsBundle\Entity\CategoriesRepository;
            ->add('category','entity', array(
                  'class' => 'foxp2projectsBundle:Categories',
                  'empty_value' => 'choisir ...',
                  'property' => 'categoriesName',
                  'query_builder' => function(CategoriesRepository $er){
                            return $er->getCategoriesList();                              
                  },                  
                  'required' => true,
                  'label' => '<strong>Catégorie parente :</strong>'                  
                 ));   

    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'foxp2\projectsBundle\Entity\Articles'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'foxp2_projectsbundle_articlestype';
    }
}
