<?php

namespace Sardoj\Uccello\Http\Controllers\Core;

use Kris\LaravelFormBuilder\FormBuilder;
use Illuminate\Http\Request;
use Sardoj\Uccello\Forms\EditForm;
use Sardoj\Uccello\Events\BeforeSaveEvent;
use Sardoj\Uccello\Events\AfterSaveEvent;
use Sardoj\Uccello\Models\Domain;
use Sardoj\Uccello\Models\Module;

class EditController extends Controller
{
    protected $viewName = 'edit.main';
    protected $formBuilder;

    /**
     * Check user permissions
     */
    protected function checkPermissions()
    {
        if (request()->has('id')) {
            $this->middleware('uccello.permissions:update');
        } else {
            $this->middleware('uccello.permissions:create');
        }
    }

    public function __construct(FormBuilder $formBuilder)
    {
        $this->formBuilder = $formBuilder;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    public function process(Domain $domain, Module $module, Request $request)
    {
        // Pre-process
        $this->preProcess($domain, $module, $request);

        // Retrieve record or get a new empty instance
        $record = $this->getRecordFromRequest();

        // Get form
        $form = $this->getForm($record);

        // Get mode
        $mode = !is_null($record->id) ? 'edit' : 'create';

        return $this->autoView([
            'structure' => $this->getModuleStructure(),
            'form' => $form,
            'record' => $record,
            'mode' => $mode
        ]);
    }

    /**
     * Create or update record into database
     *
     * @param Domain $domain
     * @param Module $module
     * @return void
     */
    public function store(Domain $domain, Module $module, Request $request)
    {
        // Pre-process
        $this->preProcess($domain, $module, $request);

        // Get entity class used by the module
        $entityClass = $this->module->entity_class;

        try
        {
            // Retrieve record or get a new empty instance
            $record = $this->getRecordFromRequest();

            // Get form
            $form = $this->getForm($record);

            // Redirect if form not valid (the record is made here)
            $form->redirectIfNotValid();

            $mode = $record->id ? 'edit' : 'create';

            event(new BeforeSaveEvent($domain, $module, $request, $record, $mode));

            // Save record
            $form->getModel()->save();

            event(new AfterSaveEvent($domain, $module, $request, $record, $mode));

            // Redirect to detail view
            return redirect()->route('uccello.detail', ['domain' => $domain->slug, 'module' => $module->name, 'id' => $record->id]);
        }
        catch (\Exception $e) {
        }

        // If there was an error, redirect to previous page
        return back()->withInput();
    }

    public function getForm($record = null)
    {
        return $this->formBuilder->create(EditForm::class, [
            'model' => $record,
            'data' => [
                'domain' => $this->domain,
                'module' => $this->module
            ]
        ]);
    }

    /**
     * Get module structure : tabs > blocks > fields
     * @return Module
     */
    protected function getModuleStructure()
    {
        return Module::find($this->module->id);
    }
}