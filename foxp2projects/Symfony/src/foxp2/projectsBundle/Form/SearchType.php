<?php
namespace foxp2\projectsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;


class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('search','text',array('attr' => array('class' => 'search-query input-large', 'placeholder' => 'Rechercher une catégorie','required'=>false)));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'foxp2_projectsbundle_searchtype';
    }
}
?>
