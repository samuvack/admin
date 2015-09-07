<?php

namespace MyApp\FormTypes\Import;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\formBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use MyApp\Entities\Property;
use Doctrine\ORM\EntityRepository;
use Silex\Application;
class ColumnType extends AbstractType {
	private $app;

	public function __construct(Application $app) {
		$this->app = $app;
	}
	public function getName() {
		return "column";
	}

	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add('property', 'entity', array(
			'query_builder' => function(EntityRepository $propertyRepo) {
				return $propertyRepo->createQueryBuilder("p");
			},
			'required' => true,
			'class' => ':Property',
			'em' => $this->app['orm.em'],
			'choice_label' => 'name',
			'attr'=>array('class'=>'form-control type-selection','placeholder'=>'The property for the item')
		))
			->add('column', 'integer');
	}
}
