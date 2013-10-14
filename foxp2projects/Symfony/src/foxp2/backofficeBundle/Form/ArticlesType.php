<?php

namespace foxp2\backofficeBundle\Form;

use foxp2\backofficeBundle\Entity\CategoriesRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ArticlesType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('articleName', 'text', array('label' => 'Wording of article'))
                ->add('category', 'entity', array(
                    'class' => 'foxp2backofficeBundle:Categories',
                    'empty_value' => 'Please select ...',
                    'property' => 'categoriesName',
                    'query_builder' => function(CategoriesRepository $er) {
                        return $er->getCategoriesList();
                    },
                    'required' => true,
                    'label' => 'Choose category'
                ))
                ->add('articleGistReference', 'choice', array('choices' => $options['gists'], 'label' => 'Link with a gist', 'required' => false, 'empty_value' => 'Choose a gist ...', 'attr' => array('class' => 'span5')))
                ->add('articleTitle', 'text', array('label' => 'Title of article'))
                ->add('articleSubTitle', 'text', array('label' => 'Sub title of article'))
                ->add('articleShortDescription', 'ckeditor', array(
                    'config' => array(
                        'toolbar' => array(
                            array(
                                'name' => 'basicstyles',
                                'items' => array('Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat')
                            ),
                            array(
                                'name' => 'paragraph',
                                'items' => array('NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl')
                            ),
                            array(
                                'name' => 'styles',
                                'items' => array('Styles', 'Format', 'Font', 'FontSize')
                            )
                        )),
                    'label' => 'Short description'))
                ->add('articleLongDescription', 'ckeditor', array('config' => array('toolbar' => 'basic'), 'label' => 'Long description'));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'foxp2\backofficeBundle\Entity\Articles',
            'gists' => 'gists',
            'gist_selected' => 'gist_selected'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'foxp2_backofficeBundle_articlestype';
    }

}
