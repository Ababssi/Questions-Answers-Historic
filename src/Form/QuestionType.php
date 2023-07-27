<?php

namespace App\Form;

use App\Dto\QuestionDTO;
use App\Entity\Enum\QuestionStatus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title',TextType::class, [
                'required' => true,
            ])
            ->add('promoted')
            ->add('status', ChoiceType::class, [
                'choices' => QuestionStatus::availableStatusesValues(),
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => QuestionDTO::class,
        ]);
    }
}
