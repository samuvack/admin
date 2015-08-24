<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 24/08/15
 * Time: 11:12
 */

namespace MyApp\FormTypes;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\formBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Silex\Application;


class TextType extends AbstractType {

	/**
	 * Returns the name of this type.
	 *
	 * @return string The name of this type
	 */
	public function getName() {
		return 'DO_REPLACE_';
	}

	public function buildForm(FormBuilderInterface $builder, array $options) {
		//add an event listener to populate the field property at page loading
		$builder->add('text', 'text', array(
			'constraints'=>array(new Assert\NotBlank(),new Assert\Length(array('min'=>3))),
			'attr' => array('class'=>'form-control', 'placeholder'=>'The filter value', 'id'=>'values'),
			'label'=>'With value:'
		));
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'csrf_protection' => false,
			'data_class' => 'MyApp\Values\TextValue',
			'empty_data' => new \MyApp\Values\TextValue('')
		));
	}
}
