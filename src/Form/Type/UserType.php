<?php

    namespace App\Form\Type;

    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\Extension\Core\Type\SubmitType;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\Extension\Core\Type\EmailType;
    use Symfony\Component\Form\Extension\Core\Type\PasswordType;
    use Symfony\Component\Form\FormBuilderInterface;

    class UserType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $option): void
        {
            $builder->add("name", TextType::class, ['attr' => ['maxlength' => 255]]);
            $builder->add("surname", TextType::class, ['attr' => ['maxlength' => 255]]);
            $builder->add("email", EmailType::class, ['attr' => ['maxlength' => 255]]);
            $builder->add("password", PasswordType::class, ['attr' => ['maxlength' => 255]]);
            $builder->add("avatar", TextType::class, ['attr' => ['maxlength' => 7]]);
            $builder->add("send", SubmitType::class);
        }
    }

?>