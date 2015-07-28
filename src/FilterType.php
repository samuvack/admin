<?php 
	use Symfony\Component\Form\AbstractType;
	use Symfony\Component\Form\formBuilderInterface;
	use Symfony\Component\Validator\Constraints as Assert;
	use Symfony\Component\OptionsResolver\OptionsResolverInterface;
	use Symfony\Component\Form\FormEvent;
	use Symfony\Component\Form\FormEvents;
	use Symfony\Component\Form\FormInterface;

	class FilterType extends AbstractType
	{
		public function buildForm(FormBuilderInterface $builder, array $options)
		{
			$builder
			->add('type', 'choice', array(
				'choices'=> array('time'=>'time', 'geometry'=>'geometry', 'other'=>'other'),
				'attr'=>array('class'=>'form-control'),
				'placeholder' => '',
				'label'=>'Filter on'
				))

			;
			
			//callback function for modifying the form
			$formModifier = function(FormInterface $form, $type) {
				//get the properties for this datatype
				if($type === null){
					$properties = array();
				} elseif ($type = 'other'){
					$properties = array();
					$returned_props = Property::getAll();
					foreach($returned_props as $p){
						$datatype = $p->getDataType();
						if($datatype = 'time' or $datatype = 'geometry'){
							//do nothing
						} else {
							array_push($properties, $p);
						}
						
					}
				} else {
					$properties = Property::findByType($type);
				}
				
				$prop_options = array();
				foreach($properties as $p){
					$prop_options[$p->getId()]=$p->getName();
				}
				
				
				//add the property, value and submit fields to the form
				$form->add('property', 'choice', array(
					'choices'=> $prop_options,
					'attr'=>array('class'=>'form-control'),
					'placeholder' => '',
					'label'=>'Where node has property or relation:',
				));
				$form->add('value', 'text', array(
					'constraints'=>array(new Assert\NotBlank(),new Assert\Length(array('min'=>3))),
					'attr' => array('class'=>'form-control', 'placeholder'=>'The filter value'),
					'label'=>'With value:'
				));
				$form->add('send', 'submit', array(
					'attr' => array('class'=>'btn btn-default'),
					'label' => 'Filter',
				));
			};
			
			$builder->addEventListener(
				FormEvents::PRE_SET_DATA,
				function(FormEvent $event) use ($formModifier){
					$data = $event->getData();					
					$formModifier($event->getForm(), $data);
				}
			);
			
			$builder->get('type')->addEventListener(
				FormEvents::POST_SUBMIT,
				function(FormEvent $event) use ($formModifier) {
					$datatype = $event->getForm()->getData();
					
					$formModifier($event->getForm()->getParent(), $datatype['type']);
				}
			);
		}
		
		
		public function getName()
		{
			return 'filter';
		}
	}
?>