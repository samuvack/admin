<?php
namespace MyApp\FormTypes;
use MyApp\Entities\Node;
	use Symfony\Component\Form\AbstractType;
	use Symfony\Component\Form\FormBuilderInterface;
	use Symfony\Component\Validator\Constraints as Assert;
	use Symfony\Component\OptionsResolver\OptionsResolverInterface;
	use Silex\Application;

	class GeometryType extends AbstractType
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
				->add('geom','text', array(
				'constraints'=>array(new Assert\NotBlank(),new Assert\Length(array('min'=>3))),
				'attr' => array('class'=>'form-control', 'placeholder'=>'The geometry of the item')
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
				'data_class' => 'MyApp\Entities\Geometry',
				//'empty_data' => new Geometry(),
			));
		}

		public function getName()
		{
			return $this->AJAX?'node':'DO_REPLACE_';
		}
	}
