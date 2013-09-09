<?php
namespace foxp2\backofficeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('search','text',array('attr' => array('class' => 'search-query input-large','required'=>false)));
    }
    
//    public function setDefaultOptions(OptionsResolverInterface $resolver)
//    {
//        $resolver->setDefaults(array(            
//            'placeholder' => 'placeholder'
//        ));
//    }
    /**
     * @return string
     */
    public function getName()
    {
        return 'foxp2_backofficeBundle_searchtype';
    }
}
?>
