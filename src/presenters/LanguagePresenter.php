<?php

namespace App\AdminModule\Presenters;

use App\AdminModule\Form;

/**
 * Description of LanguagePresenter
 *
 * @author vsek
 */
class LanguagePresenter extends BasePresenterM{
    private $modules;
    
    /** @var \App\Model\Module\Language @inject */
    public $model;
    
    /**
     *
     * @var \Nette\Database\Table\ActiveRow
     */
    private $row = null;

    public function submitFormEdit(Form $form){
        $values = $form->getValues();
        $data = array(
            'name' => $values->name,
            'shortcut' => $values->shortcut,
        );
        $this->row->update($data);
        
        $this->flashMessage($this->translator->translate('admin.form.editSuccess'));
        $this->redirect('edit', $this->row->id);
    }
    
    private function exist($id){
        $this->row = $this->model->get($id);
        if(!$this->row){
            $this->flashMessage($this->translator->translate('admin.text.notitemNotExist'), 'error');
            $this->redirect('default');
        }
    }
    
    protected function createComponentFormEdit($name){
        $form = new Form($this, $name);
        
        $form->addText('name', $this->translator->translate('admin.form.name'))
                ->addRule(Form::FILLED, $this->translator->translate('admin.form.isRequired'));
        $form->addText('shortcut', $this->translator->translate('language.shortcut'))
                ->addRule(Form::FILLED, $this->translator->translate('admin.form.isRequired'));
        $form->addText('link', $this->translator->translate('language.link'))
                ->addRule(Form::FILLED, $this->translator->translate('admin.form.isRequired'));
        
        $form->addSubmit('send', $this->translator->translate('admin.form.edit'));
        
        $form->onSuccess[] = [$this, 'submitFormEdit'];
        
        $form->setDefaults(array(
            'name' => $this->row->name,
            'shortcut' => $this->row->shortcut,
            'link' => $this->row->link,
        ));
        
        return $form;
    }
    
    public function actionEdit($id){
        $this->exist($id);
    }
    
    public function actionDelete($id){
        $this->exist($id);
        $this->row->delete();
        $this->flashMessage($this->translator->translate('admin.text.itemDeleted'));
        $this->redirect('default');
    }
    
    public function submitFormNew(Form $form){
        $values = $form->getValues();
        
        $this->model->insert(array(
            'name' => $values->name,
            'shortcut' => $values->shortcut,
        ));
        
        $this->flashMessage($this->translator->translate('admin.text.inserted'));
        $this->redirect('default');
    }
    
    protected function createComponentFormNew($name){
        $form = new Form($this, $name);
        
        $form->addText('name', $this->translator->translate('admin.form.name'))
                ->addRule(Form::FILLED, $this->translator->translate('admin.form.isRequired'));
        $form->addText('shortcut', $this->translator->translate('language.shortcut'))
                ->addRule(Form::FILLED, $this->translator->translate('admin.form.isRequired'));
        $form->addText('shortcut', $this->translator->translate('language.link'))
                ->addRule(Form::FILLED, $this->translator->translate('admin.form.isRequired'));
        
        $form->addSubmit('send', $this->translator->translate('admin.form.create'));
        
        $form->onSuccess[] = [$this, 'submitFormNew'];
        
        return $form;
    }
    
    protected function createComponentGrid($name){
        $grid = new \App\Grid\Grid($this, $name);

        $grid->setModel($this->model->getAll());
        $grid->addColumn(new \App\Grid\Column\Column('name', $this->translator->translate('admin.form.name')));
        $grid->addColumn(new \App\Grid\Column\Column('shortcut', $this->translator->translate('language.shortcut')));
        $grid->addColumn(new \App\Grid\Column\Column('link', $this->translator->translate('language.link')));
        $grid->addColumn(new \App\Grid\Column\Column('id', $this->translator->translate('admin.grid.id')));
        
        $grid->addMenu(new \App\Grid\Menu\Update('edit', $this->translator->translate('admin.form.edit')));
        $grid->addMenu(new \App\Grid\Menu\Delete('delete', $this->translator->translate('admin.grid.delete')));
        
        return $grid;
    }
}