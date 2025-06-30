<div class="w-full">
    <!-- File Upload Area -->
    <div class="relative">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>

        @if($description)
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">{{ $description }}</p>
        @endif

        @if(!$uploadedFile)
            <!-- Drop Zone -->
            <div class="relative border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 hover:border-gray-400 dark:hover:border-gray-500 transition-colors duration-200">
                <input
                    type="file"
                    wire:model="file"
                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                    @if($accept) accept="{{ $accept }}" @endif
                    @if($multiple) multiple @endif
                >

                <div class="text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500 mb-4" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>

                    <div class="flex text-sm text-gray-600 dark:text-gray-400">
                        <span class="relative cursor-pointer bg-white dark:bg-gray-800 rounded-md font-medium text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300">
                            Click to upload
                        </span>
                        <p class="pl-1">or drag and drop</p>
                    </div>

                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                        @if($accept)
                            {{ strtoupper(str_replace(['.', ','], [' ', ', '], $accept)) }} files up to {{ number_format($maxSize/1024, 0) }}MB
                        @else
                            Files up to {{ number_format($maxSize/1024, 0) }}MB
                        @endif
                    </p>
                </div>
            </div>
        @else
            <!-- Uploaded File Display -->
            <div class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <!-- File Icon -->
                        <div class="flex-shrink-0">
                            @if(str_contains($uploadedFile->getMimeType(), 'pdf'))
                                <svg class="h-8 w-8 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                </svg>
                            @elseif(str_contains($uploadedFile->getMimeType(), 'image'))
                                <svg class="h-8 w-8 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                                </svg>
                            @else
                                <svg class="h-8 w-8 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                </svg>
                            @endif
                        </div>

                        <!-- File Info -->
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                {{ $uploadedFile->getClientOriginalName() }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ number_format($uploadedFile->getSize() / 1024, 1) }} KB
                            </p>
                        </div>
                    </div>

                    <!-- Remove Button -->
                    <button
                        type="button"
                        wire:click="removeFile"
                        class="flex-shrink-0 ml-4 text-gray-400 hover:text-red-500 dark:text-gray-500 dark:hover:text-red-400 transition-colors duration-200"
                    >
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>

                <!-- Upload Progress -->
                @if($showProgress && $isUploading)
                    <div class="mt-3">
                        <div class="flex items-center justify-between text-xs text-gray-600 dark:text-gray-400 mb-1">
                            <span>Uploading...</span>
                            <span>{{ $uploadProgress }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: {{ $uploadProgress }}%"></div>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- Upload Progress (when no file displayed) -->
        @if($showProgress && $isUploading && !$uploadedFile)
            <div class="mt-4">
                <div class="flex items-center justify-between text-xs text-gray-600 dark:text-gray-400 mb-1">
                    <span>Uploading...</span>
                    <span>{{ $uploadProgress }}%</span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: {{ $uploadProgress }}%"></div>
                </div>
            </div>
        @endif

        <!-- Error Display -->
        @error('file')
            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>
</div>
