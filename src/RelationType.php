<?php
use MyApp\Entities\Relation;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\formBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Silex\Application;

class RelationType extends AbstractType
{
    protected $app;
    public function __construct(Application $app) {
        $this->app = $app;
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
            ));

        $builder->addEventListener(FormEvents::POST_SET_DATA, array($this, 'onPreSetData'));
        $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));
    }

    private function renderFormType($form, $type) {
        $formType = $this->app['mapping.manager']->getFormType($type);
        $form->add('value', $formType);
        $form->add('rank', 'choice', array(
            'choices' => array('normal' => 'No special ranking', 'preferred' => 'Preferred value to other similar property', 'deprecated' => 'Not longer valid or true'),
            'attr' => array('class' => 'form-control', 'placeholder' => 'The rank of this statement')
        ))
        ->add('qualifier', 'integer', array(
            'attr' => array('class' => 'form-control', 'placeholder' => 'The id for the qualifier statement'),
            'required' => false
        ));
    }

    public function onPreSetData(FormEvent $event) {
        $form = $event->getForm();

        $type = 'text';
        if(sizeof($event->getData())>0) {
            $type = $event->getData()->getProperty()->getDataType();
        }
        $this->renderFormType($form,$type);
    }


    public function onPreSubmit(FormEvent $event) {
        $form = $event->getForm();
        $data = $event->getData();
        $type = $this->app['orm.em']->getRepository(':Property')->find($data['property'])->getDataType();
        $this->renderFormType($form, $type);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'MyApp\Entities\Relation',
            'empty_data' => new Relation(),
        ));
    }

    public function getName() {
        return 'relation';
    }
}
