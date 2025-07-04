<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
        <style>
            * {
                box-sizing: border-box;
            }
            
            html, body {
                scroll-behavior: smooth;
                overflow-x: hidden;
                overflow-y: auto;
            }
            
            .auth-gradient {
                background: linear-gradient(135deg, #1e40af 0%, #10b981 50%, #f59e0b 100%);
                position: relative;
                min-height: 100vh;
            }
            
            .auth-gradient::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: 
                    radial-gradient(circle at 20% 80%, rgba(16, 185, 129, 0.3) 0%, transparent 50%),
                    radial-gradient(circle at 80% 20%, rgba(245, 158, 11, 0.3) 0%, transparent 50%),
                    radial-gradient(circle at 40% 40%, rgba(30, 64, 175, 0.3) 0%, transparent 50%);
                animation: float 6s ease-in-out infinite;
            }
            
            @keyframes float {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-20px); }
            }
            
            .auth-card {
                backdrop-filter: blur(16px);
                background: rgba(255, 255, 255, 0.95);
                border: 1px solid rgba(255, 255, 255, 0.2);
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
                animation: slideUp 0.6s ease-out;
            }
            
            .dark .auth-card {
                background: rgba(17, 24, 39, 0.95);
                border: 1px solid rgba(75, 85, 99, 0.2);
            }
            
            @keyframes slideUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            .logo-glow {
                filter: drop-shadow(0 0 20px rgba(16, 185, 129, 0.5));
                animation: pulse 2s ease-in-out infinite;
            }
            
            @keyframes pulse {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(1.05); }
            }
            
            .floating-shapes {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                overflow: hidden;
                pointer-events: none;
                z-index: 1;
            }
            
            .floating-shapes .shape {
                position: absolute;
                background: linear-gradient(45deg, rgba(16, 185, 129, 0.1), rgba(245, 158, 11, 0.1));
                border-radius: 50%;
                animation: floatShapes 8s linear infinite;
            }
            
            .floating-shapes .shape:nth-child(1) {
                width: 60px;
                height: 60px;
                left: 10%;
                animation-delay: 0s;
            }
            
            .floating-shapes .shape:nth-child(2) {
                width: 80px;
                height: 80px;
                left: 80%;
                animation-delay: 2s;
            }
            
            .floating-shapes .shape:nth-child(3) {
                width: 40px;
                height: 40px;
                left: 60%;
                animation-delay: 4s;
            }
            
            @keyframes floatShapes {
                0% {
                    transform: translateY(100vh) rotate(0deg);
                    opacity: 0;
                }
                10% {
                    opacity: 1;
                }
                90% {
                    opacity: 1;
                }
                100% {
                    transform: translateY(-100px) rotate(360deg);
                    opacity: 0;
                }
            }
        </style>
    </head>
    <body class="antialiased auth-gradient">
        <div class="floating-shapes fixed inset-0 pointer-events-none">
            <div class="shape"></div>
            <div class="shape"></div>
            <div class="shape"></div>
        </div>
        
        <div class="flex min-h-screen flex-col items-center justify-center gap-6 p-6 md:p-10 relative z-10">
            <div class="flex w-full max-w-md flex-col gap-2 my-8">
                <a href="{{ route('home') }}" class="flex flex-col items-center gap-2 font-medium mb-6" wire:navigate>
                    <span class="flex h-20 w-30 mb-1 items-center justify-center rounded-lg logo-glow">
                        <x-app-logo-icon class="w-30 h-20 fill-current text-white rounded-lg" />
                    </span>
                    <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
                </a>
                <div class="flex flex-col gap-1 auth-card rounded-2xl p-8">
                    {{ $slot }}
                </div>
            </div>
        </div>
        @fluxScripts
    </body>
</html>
