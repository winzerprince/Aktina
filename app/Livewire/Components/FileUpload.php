<?php

namespace App\Livewire\Components;

use Livewire\Component;
use Livewire\WithFileUploads;

class FileUpload extends Component
{
    use WithFileUploads;

    public $file;
    public $accept = '';
    public $maxSize = 10240; // 10MB default
    public $label = 'Upload File';
    public $description = '';
    public $required = false;
    public $multiple = false;
    public $showProgress = true;
    public $uploadedFile = null;
    public $isUploading = false;
    public $uploadProgress = 0;

    protected $listeners = ['fileUploaded'];

    public function mount($accept = '', $maxSize = 10240, $label = 'Upload File', $description = '', $required = false, $multiple = false)
    {
        $this->accept = $accept;
        $this->maxSize = $maxSize;
        $this->label = $label;
        $this->description = $description;
        $this->required = $required;
        $this->multiple = $multiple;
    }

    public function updatedFile()
    {
        $this->validate([
            'file' => 'required|file|max:' . $this->maxSize . ($this->accept ? '|mimes:' . str_replace('.', '', $this->accept) : ''),
        ]);

        $this->uploadedFile = $this->file;
        $this->dispatch('fileSelected', $this->file);
    }

    public function removeFile()
    {
        $this->file = null;
        $this->uploadedFile = null;
        $this->uploadProgress = 0;
        $this->dispatch('fileRemoved');
    }

    public function render()
    {
        return view('livewire.components.file-upload');
    }
}
