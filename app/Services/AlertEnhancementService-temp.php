    /**
     * Get the current alert threshold value for a specific metric
     * 
     * @param string $metricName
     * @param mixed $defaultValue
     * @return mixed
     */
    public function getAlertThreshold(string $metricName, $defaultValue = null)
    {
        $parts = explode('.', $metricName);
        
        // Try to get from cache first
        $cacheKey = 'alert_threshold_' . $metricName;
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        
        // If not in cache, get from thresholds array
        $value = $this->thresholds;
        foreach ($parts as $part) {
            if (!isset($value[$part])) {
                return $defaultValue;
            }
            $value = $value[$part];
        }
        
        return $value;
    }
    
    /**
     * Set or update an alert threshold value
     * 
     * @param string $metricName
     * @param mixed $value
     * @return bool
     */
    public function setAlertThreshold(string $metricName, $value): bool
    {
        try {
            // Cache the new value
            $cacheKey = 'alert_threshold_' . $metricName;
            Cache::put($cacheKey, $value, now()->addDays(30));
            
            // If we had a persistent storage for thresholds, we would update it here
            
            Log::info("Alert threshold updated: {$metricName} = {$value}");
            
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to update alert threshold: {$metricName}", [
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }
    
    /**
     * Get all defined alert thresholds
     * 
     * @return array
     */
    public function getAllAlertThresholds(): array
    {
        $thresholds = $this->thresholds;
        
        // Update with any cached values
        $this->updateThresholdsWithCachedValues($thresholds);
        
        return $thresholds;
    }
    
    /**
     * Update the thresholds array with any cached values
     *
     * @param array &$thresholds
     * @return void
     */
    protected function updateThresholdsWithCachedValues(array &$thresholds): void
    {
        // Check for cached product thresholds
        if (Cache::has('alert_threshold_products.critical')) {
            $thresholds['products']['critical'] = Cache::get('alert_threshold_products.critical');
        }
        
        if (Cache::has('alert_threshold_products.warning')) {
            $thresholds['products']['warning'] = Cache::get('alert_threshold_products.warning');
        }
        
        // Check for cached resource thresholds
        if (Cache::has('alert_threshold_resources.critical')) {
            $thresholds['resources']['critical'] = Cache::get('alert_threshold_resources.critical');
        }
        
        if (Cache::has('alert_threshold_resources.warning')) {
            $thresholds['resources']['warning'] = Cache::get('alert_threshold_resources.warning');
        }
        
        // Check for cached performance thresholds
        foreach (['cpu_usage', 'memory_usage', 'disk_usage', 'response_time'] as $metric) {
            $cacheKey = 'alert_threshold_performance.' . $metric;
            if (Cache::has($cacheKey)) {
                $thresholds['performance'][$metric] = Cache::get($cacheKey);
            }
        }
    }
    
    /**
     * Select the appropriate user to approve an order
     * 
     * @param Order $order
     * @return User|null
     */
    public function selectOrderApprover(Order $order): ?User
    {
        try {
            // High-value orders require admin approval
            if ($order->total_amount >= 2000) {
                return User::where('role', 'admin')->first();
            }
            
            // Standard orders can be approved by production managers or admins
            return User::whereIn('role', ['admin', 'production_manager'])
                ->inRandomOrder()
                ->first();
                
        } catch (\Exception $e) {
            Log::error("Error selecting order approver: {$e->getMessage()}", [
                'order_id' => $order->id
            ]);
            
            return null;
        }
    }
