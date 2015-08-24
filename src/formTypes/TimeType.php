<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 24/08/15
 * Time: 16:05
 */

namespace MyApp\FormTypes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\formBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Silex\Application;


class TimeType extends AbstractType {

	/**
	 * Returns the name of this type.
	 *
	 * @return string The name of this type
	 */
	public function getName() {
		return 'DO_REPLACE_';
	}

	/*public function buildForm(FormBuilderInterface $builder, array $options) {
		//add an event listener to populate the field property at page loading
		//echo "test";die();
		$builder->add('time', 'text', array(
			'attr' => array('class'=>'form-control', 'placeholder'=>'The filter value'),
			'label'=>'With value:'
		));
	}*/
	public function buildForm(FormBuilderInterface $builder, array $options) {
		//add an event listener to populate the field property at page loading
		$builder->add('time', 'text', array(
			'constraints'=>array(new Assert\NotBlank(),new Assert\Length(array('min'=>3))),
			'attr' => array('class'=>'form-control', 'placeholder'=>'The filter value', 'id'=>'values'),
			'label'=>'With value:'
		));
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'csrf_protection' => false
		));
	}

}
