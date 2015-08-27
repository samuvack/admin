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


class YearPeriodType extends AbstractType {
	private $yearPeriod;

	public function __construct($yearPeriod = null) {
		$this->yearPeriod = $yearPeriod;
	}

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
		$builder->add('startyear', 'integer', array(
			'constraints'=>array(new Assert\NotBlank()),
			'attr' => array('class'=>'form-control', 'placeholder'=>'The filter value', 'id'=>'values'),
			'label'=>'start year'
		))
		->add('endyear', 'integer', array(
			'constraints'=>array(new Assert\NotBlank()),
			'attr' => array('class'=>'form-control', 'placeholder'=>'The filter value', 'id'=>'values'),
			'label' => 'end year'
		));
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'csrf_protection' => false,
			'data_class' => 'MyApp\Values\YearPeriodValue',
			'empty_data' => (isset($this->yearPeriod))? $this->yearPeriod : new \MyApp\Values\YearPeriodValue(0,2015)
		));
	}

}
