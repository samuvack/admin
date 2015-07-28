<?php 
	use Symfony\Component\Form\AbstractType;
	use Symfony\Component\Form\formBuilderInterface;
	use Symfony\Component\Validator\Constraints as Assert;
	use Symfony\Component\OptionsResolver\OptionsResolverInterface;

	class FilterType extends AbstractType
	{
		public function buildForm(FormBuilderInterface $builder, array $options)
		{
			$builder
			->add('type', 'choice', array(
				'choices'=> array('1'=>'time', '2'=>'geometry', '3'=>'other'),
				'attr'=>array('class'=>'form-control','placeholder'=>'The filter type'),
				'label'=>'Filter on'
				))
			->add('property', 'text', array(
				'constraints'=>array(new Assert\NotBlank(),new Assert\Length(array('min'=>3))),
				'attr' => array('class'=>'form-control', 'placeholder'=>'The filter property or relation'),
				'label'=>'Where node has property or relation:'
			))
			->add('value', 'text', array(
				'constraints'=>array(new Assert\NotBlank(),new Assert\Length(array('min'=>3))),
				'attr' => array('class'=>'form-control', 'placeholder'=>'The filter value'),
				'label'=>'With value:'
			))
			->add('send', 'submit', array(
				'attr' => array('class'=>'btn btn-default'),
				'label' => 'Filter',
			))
			;
		}
		
		/*function getProperties() {
			//get the available properties (id, name)	
			$properties = Property::getAll();
			
			//store the properties in an array format id=>name for the choice form field
			$property_choice = array();
			foreach($properties as $p){
				$property_choice[$p->getId()]=$p->getName();
			}
			
			return $property_choice;
		}
		
		public function setDefaultOptions(OptionsResolverInterface $resolver) {
			$resolver->setDefaults(array(
				'data_class' => 'Relation',
				'empty_data' => new Relation(null, null, null, null, null, null),
			));
		}*/
		
		public function getName()
		{
			return 'filter';
		}
	}
?>