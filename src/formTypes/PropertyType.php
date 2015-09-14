<?php
namespace MyApp\FormTypes;
namespace MyApp\FormTypes;

use MyApp\Entities\Property;
use MyApp\Values\TextValue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Silex\Application;
class PropertyType extends AbstractType {
	private $app;

	public function __construct(Application $app) {
		$this->app = $app;
	}
	public function getName() {
		return 'property';
	}
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$choices = [];
		foreach($this->app['mapping.manager']->getDataTypes() as $datatype) {
			$choices[$datatype] = $datatype;
		}
		$builder->add('name','text', array(
				'constraints'=>array(new Assert\NotBlank(),new Assert\Length(array('min'=>3))),
				'attr' => array('class'=>'form-control', 'place-holder'=>'The name of the property.')
			))
			->add('description', 'text', array(
				'constraints'=>array(new Assert\NotBlank(),new Assert\Length(array('min'=>3))),
				'attr' => array('class'=>'form-control', 'place-holder'=>'The description of the property.')
			))
			->add('datatype', 'choice',
				array(
					'constraints'=>array(new Assert\NotBlank()),
					'choices'=>$choices,
					'attr' => array('class'=>'form-control'),
					'required'=>true,
					'multiple'=>false
				))
		->add('Submit','submit');
	}
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'data_class' => 'MyApp\Entities\Property',
			'empty_data' => new Property(),
		));
	}
}
