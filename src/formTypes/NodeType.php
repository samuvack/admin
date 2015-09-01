<?php
namespace MyApp\FormTypes;
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
		public function __construct(Application $app, $AJAX = true) {
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
			->add('description', 'textarea', array(
				'constraints'=>array(new Assert\NotBlank(),new Assert\Length(array('min'=>3))),
				'attr' => array('class'=>'form-control', 'placeholder'=>'The description of the item')
				))
			->add('relations', 'collection', array(
				'type' => new RelationType($this->app),
				'allow_add' => true,
				//'by_reference'=> true,
				));
			// Dont add another submit, if this is the form for a relationship value
			if($this->AJAX) {
				$builder->add('send', 'submit', array(
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
			return $this->AJAX?'node':'DO_REPLACE_';
		}
	}