<?php 
	use Symfony\Component\Form\AbstractType;
	use Symfony\Component\Form\FormBuilderInterface;
	use Symfony\Component\Validator\Constraints as Assert;
	use Symfony\Component\OptionsResolver\OptionsResolverInterface;

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
			->add('relations', 'collection', array(
				'type' => new RelationType(),
				'allow_add' => true,
				'by_reference'=>false,				
				))
			->add('send', 'submit', array(
				'attr' => array('class'=>'btn btn-default')
				))
			;
		}
		
		public function setDefaultOptions(OptionsResolverInterface $resolver) {
			$resolver->setDefaults(array(
				'data_class' => 'Node',
				'empty_data' => new Node(null, null, null, null),
			));
		}
		
		public function getName()
		{
			return 'node';
		}
	}
?>