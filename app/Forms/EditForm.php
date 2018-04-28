<?php

namespace Sardoj\Uccello\Forms;

use Kris\LaravelFormBuilder\Form;
use Debugbar;
use Sardoj\Uccello\Field;

class EditForm extends Form
{
    /**
     * Build the form.
     *
     * @return void
     */
    public function buildForm()
    {
        // Get domain data
        $domain = $this->getData('domain');

        // Get module data
        $module = $this->getData('module');

        // Options
        $this->formOptions = [
            'method' => 'POST', // Use POST method
            'url' => route('store', ['domain' => $domain->slug, 'module' => $module->name]), // URL to call      
            'novalidate', // Deactivate HTML5 validation
        ];

        // Add all fields
        foreach($module->fields as $field)
        {
            // Get translated field label
            $fieldLabel = uctrans($field->label, $module);

            // Repeated field
            if ($field->data->repeated ?? false) {
                $this->add($field->name, 'repeated', [
                    'type' => $field->uitype,                  
                    'first_options' => [
                        'label' => $fieldLabel,
                        'label_attr' => ['class' => 'form-label'],
                        'rules' => $field->data->rules ?? null,
                        'default_value' => $field->data->default ?? null,
                        'attr' => [
                            'class' => 'form-control'
                        ],
                    ],
                    'second_options' => [
                        'label' => uctrans($field->label.'_confirmation', $module),
                        'label_attr' => ['class' => 'form-label'],
                        'default_value' => $field->data->default ?? null,
                        'attr' => [
                            'class' => 'form-control'
                        ],
                    ]
                ]);
            }
            // Classic field
            else {
                $this->add($field->name, $field->uitype, [
                    'label' => $fieldLabel,
                    'label_attr' => ['class' => 'form-label'],
                    'rules' => $field->data->rules ?? null,
                    'default_value' => $field->data->default ?? null,
                    'attr' => [
                        'class' => 'form-control'
                    ]
                ]);
            }
        }

        // Add a save button
        $this->add('submit_btn', 'submit', [
            'label' => uctrans('button.save', $module),
            'attr' => [
                'class' => 'btn btn-success pull-right'
            ]
        ]);
    }
}