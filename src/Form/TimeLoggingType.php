<?php

namespace App\Form;

use App\Entity\TimeLogging;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class TimeLoggingType extends AbstractType
{
    private $router;
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $projects = $options['data']['projects'];
        $builder->add(
            'userid',
            HiddenType::class,
            [
                'data' => $options['data']['userid']
            ]
        )
        ->add(
            'projectid',
            ChoiceType::class,
            [
                'placeholder' => 'Bitte Projekt auswählen',
                'label' => 'Projekt wählen: ',
                'choices' =>
                    array_combine(
                        array_map(
                            static function ($projects) {
                                return $projects['name'];
                            },
                            $projects
                        ),
                        array_column($projects, 'id')
                    )
            ]
        )
        ->add(
            'start_logging',
            SubmitType::class,
            ['label' => 'Zeiterfassung starten']
        )
        ->setAction($this->router->generate('_start_logging'));
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
