<?php

namespace App\Livewire\Admin;

use App\Services\AlertEnhancementService;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Livewire\Attributes\Validate;

class AlertThresholdManager extends Component
{
    #[Validate('required|string')]
    public $selectedCategory = 'products';
    
    #[Validate('required|string')]
    public $selectedType = 'critical';
    
    #[Validate('required|integer|min:1')]
    public $newThreshold;
    
    public $thresholds = [];
    public $categories = [];
    public $types = [];
    public $categoryTypes = [
        'products' => ['critical', 'warning'],
        'resources' => ['critical', 'warning'],
        'performance' => ['cpu_usage', 'memory_usage', 'disk_usage', 'response_time']
    ];
    public $isEditing = false;
    public $successMessage = '';
    
    protected $listeners = ['refreshThresholds' => 'loadThresholds'];

    public function mount(AlertEnhancementService $alertService)
    {
        $this->loadThresholds();
        $this->categories = array_keys($this->thresholds);
        $this->updateAvailableTypes();
    }
    
    public function render()
    {
        return view('livewire.admin.alert-threshold-manager');
    }
    
    public function loadThresholds()
    {
        $alertService = app(AlertEnhancementService::class);
        $this->thresholds = $alertService->getAlertThresholds();
        $this->newThreshold = $this->thresholds[$this->selectedCategory][$this->selectedType] ?? null;
    }
    
    public function startEditing()
    {
        $this->isEditing = true;
        $this->newThreshold = $this->thresholds[$this->selectedCategory][$this->selectedType] ?? null;
    }
    
    public function cancelEditing()
    {
        $this->isEditing = false;
        $this->resetValidation();
    }
    
    public function updateThreshold()
    {
        $this->validate([
            'selectedCategory' => 'required|string',
            'selectedType' => 'required|string',
            'newThreshold' => 'required|integer|min:1'
        ]);
        
        $alertService = app(AlertEnhancementService::class);
        $result = $alertService->updateAlertThreshold(
            $this->selectedCategory, 
            $this->selectedType, 
            (int)$this->newThreshold
        );
        
        if ($result) {
            $this->successMessage = 'Threshold updated successfully!';
            $this->isEditing = false;
            $this->loadThresholds();
            
            // Clear success message after 3 seconds
            $this->dispatch('clearMessage');
        } else {
            $this->addError('newThreshold', 'Failed to update threshold. Please try again.');
        }
    }
    
    public function updatedSelectedCategory()
    {
        $this->updateAvailableTypes();
        $this->selectedType = $this->types[0] ?? '';
        $this->newThreshold = $this->thresholds[$this->selectedCategory][$this->selectedType] ?? null;
    }
    
    public function updatedSelectedType()
    {
        $this->newThreshold = $this->thresholds[$this->selectedCategory][$this->selectedType] ?? null;
    }
    
    protected function updateAvailableTypes()
    {
        $this->types = $this->categoryTypes[$this->selectedCategory] ?? [];
    }
}
