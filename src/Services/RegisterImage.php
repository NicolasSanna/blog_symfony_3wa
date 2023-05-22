<?php

namespace App\Services;

use Symfony\Component\Form\Form;

class RegisterImage
{
    private Form $form;

    public function setForm(Form $form)
    {
        $this->form = $form;
    }

    public function saveImage(): string
    {
        $file = $this->form->get('image')->getData();
        
        if(!empty($file))
        {
            $file->move('image_directory', $file->getClientOriginalName());

            return $file->getClientOriginalName();
        }
        else
        {
            return '';
        }
    }
}