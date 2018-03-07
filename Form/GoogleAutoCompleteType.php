<?php

namespace Arthem\Bundle\CoreBundle\Form;

use Arthem\Bundle\CoreBundle\Model\GoogleAddressInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\DataMapper\PropertyPathMapper;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GoogleAutoCompleteType extends AbstractType implements DataMapperInterface
{
    private $apiKey;

    protected $propertyPathMapper;

    /**
     * @param $apiKey
     */
    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
        $this->propertyPathMapper = new PropertyPathMapper();
    }

    public function getBlockPrefix()
    {
        return 'google_autocomplete';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setDataMapper($this)
            ->add('inputAddress', null, [
                'label' => false,
                'required' => $options['required'],
            ])
            ->add('latitude', HiddenType::class)
            ->add('longitude', HiddenType::class)
            ->add('addressLine1', HiddenType::class)
            ->add('postalCode', HiddenType::class)
            ->add('city', HiddenType::class)
            ->add('region', HiddenType::class)
            ->add('country', HiddenType::class);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['api_key'] = $this->apiKey;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => GoogleAddressInterface::class,
            'error_bubbling' => false,
            'required' => false,
        ]);
    }

    public function mapDataToForms($data, $forms)
    {
        $this->propertyPathMapper->mapDataToForms($data, $forms);
    }

    public function mapFormsToData($forms, &$data)
    {
        /** @var FormInterface[] $fields */
        $fields = iterator_to_array($forms);
        $this->propertyPathMapper->mapFormsToData($fields, $data);
        if (empty($fields['addressLine1']->getData())) {
            $className = $fields['addressLine1']->getParent()->getConfig()->getOption('data_class');
            $data = new $className();
        }
    }
}
