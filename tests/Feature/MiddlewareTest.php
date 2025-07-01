<?php

use App\Http\Middleware\EnsureRoleVerified;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\FileUploadRateLimit;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Retailer;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\RateLimiter;

describe('EnsureRoleVerified Middleware', function () {
    beforeEach(function () {
        $this->middleware = new EnsureRoleVerified();
    });

    it('allows verified users to proceed', function () {
        $user = User::factory()->create(['role' => 'vendor', 'is_verified' => true]);
        $request = Request::create('/dashboard');
        $request->setUserResolver(fn() => $user);

        $response = $this->middleware->handle($request, fn() => new Response('success'));

        expect($response->getContent())->toBe('success');
    });

    it('redirects unverified vendors to vendor verification', function () {
        $user = User::factory()->create(['role' => 'vendor', 'is_verified' => false]);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);
        $request = Request::create('/dashboard');
        $request->setUserResolver(fn() => $user);

        $response = $this->middleware->handle($request, fn() => new Response('success'));

        expect($response->getStatusCode())->toBe(302);
        expect($response->getTargetUrl())->toContain('/verification/vendor');
    });

    it('redirects unverified retailers to retailer verification', function () {
        $user = User::factory()->create(['role' => 'retailer', 'is_verified' => false]);
        $retailer = Retailer::factory()->create([
            'user_id' => $user->id,
            'demographics_completed' => false
        ]);
        $request = Request::create('/dashboard');
        $request->setUserResolver(fn() => $user);

        $response = $this->middleware->handle($request, fn() => new Response('success'));

        expect($response->getStatusCode())->toBe(302);
        expect($response->getTargetUrl())->toContain('/verification/retailer');
    });

    it('allows admin users without verification', function () {
        $user = User::factory()->create(['role' => 'admin', 'is_verified' => false]);
        $request = Request::create('/dashboard');
        $request->setUserResolver(fn() => $user);

        $response = $this->middleware->handle($request, fn() => new Response('success'));

        expect($response->getContent())->toBe('success');
    });
});

describe('SecurityHeaders Middleware', function () {
    beforeEach(function () {
        $this->middleware = new SecurityHeaders();
    });

    it('adds security headers to responses', function () {
        $request = Request::create('/');

        $response = $this->middleware->handle($request, fn() => new Response('test'));

        expect($response->headers->get('X-Content-Type-Options'))->toBe('nosniff');
        expect($response->headers->get('X-Frame-Options'))->toBe('DENY');
        expect($response->headers->get('X-XSS-Protection'))->toBe('1; mode=block');
        expect($response->headers->get('Referrer-Policy'))->toBe('strict-origin-when-cross-origin');
        expect($response->headers->has('Content-Security-Policy'))->toBeTrue();
    });

    it('adds HSTS header for secure requests', function () {
        $request = Request::create('https://example.com/');
        $request->server->set('HTTPS', 'on');

        $response = $this->middleware->handle($request, fn() => new Response('test'));

        expect($response->headers->get('Strict-Transport-Security'))->toContain('max-age=31536000');
    });
});

describe('FileUploadRateLimit Middleware', function () {
    beforeEach(function () {
        $this->middleware = new FileUploadRateLimit();
        RateLimiter::clear('file_upload:1');
    });

    it('allows requests without file uploads', function () {
        $user = User::factory()->create();
        $request = Request::create('/test', 'POST');
        $request->setUserResolver(fn() => $user);

        $response = $this->middleware->handle($request, fn() => new Response('success'));

        expect($response->getContent())->toBe('success');
    });

    it('allows file uploads within rate limit', function () {
        $user = User::factory()->create();
        $request = Request::create('/test', 'POST');
        $request->files->set('pdfFile', 'test.pdf');
        $request->setUserResolver(fn() => $user);

        $response = $this->middleware->handle($request, fn() => new Response('success'));

        expect($response->getContent())->toBe('success');
    });

    it('blocks excessive file uploads', function () {
        $user = User::factory()->create();

        // Exhaust the rate limit
        for ($i = 0; $i < 3; $i++) {
            $request = Request::create('/test', 'POST');
            $request->files->set('pdfFile', 'test.pdf');
            $request->setUserResolver(fn() => $user);
            $this->middleware->handle($request, fn() => new Response('success'));
        }

        // This should be blocked
        $request = Request::create('/test', 'POST');
        $request->files->set('pdfFile', 'test.pdf');
        $request->setUserResolver(fn() => $user);

        $response = $this->middleware->handle($request, fn() => new Response('success'));

        expect($response->getStatusCode())->toBe(429);
    });
});
