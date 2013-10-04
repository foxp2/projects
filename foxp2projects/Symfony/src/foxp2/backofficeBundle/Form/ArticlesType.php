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
                ->add('articleName', 'text', array('label' => '<strong>libellé de l\'article</strong>'))
                ->add('category', 'entity', array(
                    'class' => 'foxp2backofficeBundle:Categories',
                    'empty_value' => 'choisir ...',
                    'property' => 'categoriesName',
                    'query_builder' => function(CategoriesRepository $er) {
                        return $er->getCategoriesList();
                    },
                    'required' => true,
                    'label' => '<strong>Choisir la catégorie :</strong>'
                ))
                ->add('articleGistReference', 'choice', array('choices' => $options['gists'], 'label' => '<strong>Lier avec un gist</strong><br /><em class="muted">Facultatif</em>', 'required' => false, 'empty_value' => 'Choisir un numéro de gist', 'attr' => array('class' => 'span5')))
                ->add('articleTitle', 'text', array('label' => '<strong>Titre de l\'article</strong>'))
                ->add('articleSubTitle', 'text', array('label' => '<strong>Sous titre de l\'article</strong>'))
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
                    'label' => '<br /><strong>Description courte de l\'article</strong><br /><br />'))
                ->add('articleLongDescription', 'ckeditor', array('config' => array('toolbar' => 'basic'), 'label' => '<br /><strong>Description longue de article</strong><br /><br />'));
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
