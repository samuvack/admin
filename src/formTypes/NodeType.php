<?php
namespace MyApp\FormTypes;
use Doctrine\ORM\EntityRepository;
use MyApp\Entities\Node;
	use Symfony\Component\Form\AbstractType;
	use Symfony\Component\Form\FormBuilderInterface;
	use Symfony\Component\Validator\Constraints as Assert;
	use Symfony\Component\OptionsResolver\OptionsResolverInterface;
	use Silex\Application;

	class NodeType extends AbstractType
	{
		protected $app;
		private $AJAX;
		public function __construct(Application $app, $AJAX = false) {
			$this->app = $app;
			$this->AJAX = $AJAX;
		}

		public function buildForm(FormBuilderInterface $builder, array $options)
		{
			$builder
				->add('name','text', array(
				'constraints'=>array(new Assert\NotBlank(),new Assert\Length(array('min'=>3))),
				'attr' => array('class'=>'form-control', 'placeholder'=>'The name of the item')
				))
				->add('x', 'number', array(
					'constraints'=>array(new Assert\NotBlank()),
					'attr' => array('class'=>'form-control', 'placeholder'=>'The x-coordinate')
				))
				->add('y', 'number', array(
					'constraints'=>array(new Assert\NotBlank()),
					'attr' => array('class'=>'form-control', 'placeholder'=>'The y-coordinate')
				))
				->add('layer', 'entity', array(
					'query_builder' => function(EntityRepository $layerRepo) {
						$qb = $layerRepo->createQueryBuilder('l');
						$qb->where("l.feature_info = 'true'");
						return $qb;
					},
					'required' => true,
					'class' => ':Layer',
					'em' => $this->app['orm.em'],
					'choice_label' => 'name',
					'attr'=>array('class'=>'form-control type-selection','placeholder'=>'The layer for the item')
				))
				->add('description', 'textarea', array(
					'constraints'=>array(new Assert\NotBlank(),new Assert\Length(array('min'=>3))),
					'attr' => array('class'=>'form-control', 'placeholder'=>'The description of the item')
					));
			if(!$this->AJAX) {
				// Dont add another submit, if this is the form for a relationship value
				$builder->add('relations', 'collection', array(
				'type' => new RelationType($this->app),
				'allow_add' => true,
				//'by_reference'=> true,
				))
				->add('send', 'submit', array(
					'attr' => array('class' => 'btn btn-default')
				));
			}
		}

		public function setDefaultOptions(OptionsResolverInterface $resolver) {
			$resolver->setDefaults(array(
				'data_class' => 'MyApp\Entities\Node',
				'empty_data' => new Node(),
			));
		}

		public function getName()
		{
			return $this->AJAX?'DO_REPLACE_':'node';
		}
	}
