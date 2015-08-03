<?php 
	use Symfony\Component\Form\AbstractType;
	use Symfony\Component\Form\formBuilderInterface;
	use Symfony\Component\Validator\Constraints as Assert;
	use Symfony\Component\OptionsResolver\OptionsResolverInterface;

	class RelationType extends AbstractType
	{
		public function buildForm(FormBuilderInterface $builder, array $options)
		{
			$builder
			->add('property', 'choice', array(
				//'choices'=> array('1'=>'Is of Type', '2'=>'Pr2', '3'=>'Pr3'),
				'choices'=>$this->getProperties(),
				'attr'=>array('class'=>'form-control','placeholder'=>'The property for the item')
				))
			->add('value', 'text', array(
				'constraints'=>array(new Assert\NotBlank()),
				'attr' => array('class'=>'form-control', 'placeholder'=>'The value for the property or relation')
				))
			->add('rank', 'choice', array(
				'choices'=>array('normal'=>'No special ranking', 'preferred'=>'Preferred value to other similar property', 'deprecated'=>'Not longer valid or true'),
				'attr' => array('class'=>'form-control', 'placeholder'=>'The rank of this statement')
				))
			->add('qualifier', 'integer', array(
				'attr' => array('class'=>'form-control', 'placeholder'=>'The id for the qualifier statement'),
				'required'=>false
				))
			;
		}
		
		function getProperties() {
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
		}
		
		public function getName()
		{
			return 'relation';
		}
	}
?>