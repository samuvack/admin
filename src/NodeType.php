<?php 
	use Symfony\Component\Form\AbstractType;
	use Symfony\Component\Form\formBuilderInterface;
	use Symfony\Component\Validator\Constraints as Assert;

	class NodeType extends AbstractType
	{
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
			/*->add('property', 'choice', array(
				'choices'=>$options,
				'attr'=>array('class'=>'form-control','placeholder'=>'The property for the item')
				))*/
			->add('value', 'text', array(
				'constraints'=>array(new Assert\NotBlank(),new Assert\Length(array('min'=>3))),
				'attr' => array('class'=>'form-control', 'placeholder'=>'The value for the property or relation')
				))
			->add('send', 'submit', array(
				'attr' => array('class'=>'btn btn-default')
				))
			;
		}
		
		public function getName()
		{
			return 'node';
		}
	}
?>