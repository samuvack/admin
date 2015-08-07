<?php
use MyApp\Entities\Relation;
use MyApp\Entities\Property;
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
            ));
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
