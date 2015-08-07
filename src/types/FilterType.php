<?php
	namespace MyApp\Types;
	use Symfony\Component\Form\AbstractType;
	use Symfony\Component\Form\formBuilderInterface;
	use Symfony\Component\Validator\Constraints as Assert;
	use Symfony\Component\OptionsResolver\OptionsResolverInterface;
	use Symfony\Component\Form\FormEvent;
	use Symfony\Component\Form\FormEvents;
	use Symfony\Component\Form\FormInterface;
	use MyApp\Entities\Property;
	use Silex\Application;

	class FilterType extends AbstractType {

		protected $app;
		public function __construct(Application $app) {
			parent::__construct();
			$this->app = $app;
		}

		public function buildForm(FormBuilderInterface $builder, array $options)
		{
			$builder
			->add('type', 'choice', array(
				'choices'=> array('time'=>'time', 'geometry'=>'geometry', 'other'=>'other'),
				'attr'=>array('class'=>'form-control'),
				'placeholder' => '',
				'label'=>'Filter on'
				));
			$propertyRepo = $this->app["orm.em"]->getRepository(":Property");
			
			//callback function for modifying the form
			$formModifier = function(FormInterface $form, $type) use($propertyRepo) {
				//get the properties for this datatype
				if($type === null){ //no filter type is selected
					$properties = array();
				} elseif ($type == 'other'){ // the filter type 'other' is selected
					$properties = array();
					$returned_props = $propertyRepo->getAll();
					foreach($returned_props as $p){
						$datatype = $p->getDataType();
						if($datatype == 'time' or $datatype == 'geometry'){
							//do nothing
						} else {
							array_push($properties, $p);
						}
						
					}
				} else { // time or geometry was selected as fitler type
					$properties = $propertyRepo->findBy(array('datatype'=>$type));
				}
				
				//create an array with the property id as index and the name as value
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
			
			//add an event listener to populate the field property at page loading
			$builder->addEventListener(
				FormEvents::PRE_SET_DATA,
				function(FormEvent $event) use ($formModifier){
					$data = $event->getData();
					$type = $data['type'];
					$formModifier($event->getForm(), $type);
				}
			);
			
			//add event listener to populate the field when the type field has been submitted
			$builder->get('type')->addEventListener(
				FormEvents::POST_SUBMIT,
				function(FormEvent $event) use ($formModifier) {
					$datatype = $event->getForm()->getData();
					$formModifier($event->getForm()->getParent(), $datatype);
				}
			);
		}
		
		
		public function getName()
		{
			return 'filter';
		}
	}
?>
