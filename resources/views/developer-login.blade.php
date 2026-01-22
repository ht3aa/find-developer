@extends('layouts.app')

@section('title', 'Developer Login')
@section('seo_title', 'FindDeveloper - Developer Login')
@section('seo_description', 'Login as a developer to recommend other developers and access developer features.')
@section('seo_keywords', 'developer login, recommend developers, developer account')

@section('content')
<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <h1 class="login-title">Developer Login</h1>
            <p class="login-subtitle">Login to recommend other developers</p>
        </div>

        <div class="login-info-note">
            <svg class="info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="info-content">
                <p class="info-title">Why you need to contact us to get your login credentials?</p>
                <p class="info-text">
                    To ensure our platform remains trustworthy and delivers accurate, high-quality results, we carefully verify all developers before granting other actions privileges. This verification process helps us maintain the integrity of our community and ensures that actions come from verified, credible sources.
                </p>
                <p class="info-text">
                    If you're a registered developer and would like to get your login credentials to start to do actions, please 
                    <a href="mailto:ht3aa2001@gmail.com?subject=Developer Login Credentials Request" class="info-link">contact us via email</a>.
                </p>
            </div>
        </div>

        @if($errors->any())
            <div class="login-error">
                <svg class="error-icon" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
                <div class="error-messages">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('developer.login') }}" class="login-form">
            @csrf

            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="{{ old('email') }}" 
                    class="form-input @error('email') form-input-error @enderror" 
                    placeholder="Enter your email"
                    required 
                    autofocus
                >
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="form-input @error('password') form-input-error @enderror" 
                    placeholder="Enter your password"
                    required
                >
            </div>

            <div class="form-group form-group-checkbox">
                <label class="checkbox-label">
                    <input type="checkbox" name="remember" class="checkbox-input">
                    <span>Remember me</span>
                </label>
            </div>

            <button type="submit" class="login-button">
                <span>Login</span>
                <svg class="button-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                </svg>
            </button>
        </form>

        <div class="login-footer">
            <p class="login-footer-text">
                Need help? <a href="mailto:ht3aa2001@gmail.com?subject=Developer Login Support" class="footer-link">Contact us</a>
            </p>
        </div>
    </div>
</div>

<style>
.login-container {
    min-height: calc(100vh - 200px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: var(--spacing-2xl) var(--spacing-md);
}

.login-card {
    width: 100%;
    max-width: 450px;
    background: var(--bg-secondary);
    border-radius: var(--radius-xl);
    padding: var(--spacing-2xl);
    box-shadow: var(--shadow-2xl);
    border: 1px solid var(--border-primary);
}

.login-header {
    text-align: center;
    margin-bottom: var(--spacing-2xl);
}

.login-title {
    font-size: var(--font-size-2xl);
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: var(--spacing-sm);
}

.login-subtitle {
    font-size: var(--font-size-base);
    color: var(--text-secondary);
}

.login-error {
    background: rgba(239, 68, 68, 0.1);
    border: 1px solid rgba(239, 68, 68, 0.3);
    border-radius: var(--radius-lg);
    padding: var(--spacing-md);
    margin-bottom: var(--spacing-xl);
    display: flex;
    gap: var(--spacing-sm);
}

.error-icon {
    width: 1.25rem;
    height: 1.25rem;
    color: #ef4444;
    flex-shrink: 0;
    margin-top: 0.125rem;
}

.error-messages {
    flex: 1;
}

.error-messages p {
    color: #ef4444;
    font-size: var(--font-size-sm);
    margin: 0;
}

.login-form {
    margin-bottom: var(--spacing-xl);
}

.form-group {
    margin-bottom: var(--spacing-lg);
}

.form-label {
    display: block;
    font-size: var(--font-size-sm);
    font-weight: 500;
    color: var(--text-primary);
    margin-bottom: var(--spacing-xs);
}

.form-input {
    width: 100%;
    padding: var(--spacing-md);
    background: var(--bg-primary);
    border: 1px solid var(--border-primary);
    border-radius: var(--radius-md);
    color: var(--text-primary);
    font-size: var(--font-size-base);
    transition: all var(--transition-base);
}

.form-input:focus {
    outline: none;
    border-color: var(--color-primary);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.form-input-error {
    border-color: #ef4444;
}

.form-input-error:focus {
    border-color: #ef4444;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
}

.form-group-checkbox {
    margin-bottom: var(--spacing-xl);
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    font-size: var(--font-size-sm);
    color: var(--text-secondary);
    cursor: pointer;
}

.checkbox-input {
    width: 1rem;
    height: 1rem;
    cursor: pointer;
}

.login-button {
    width: 100%;
    padding: var(--spacing-md) var(--spacing-xl);
    background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 100%);
    color: white;
    border: none;
    border-radius: var(--radius-lg);
    font-size: var(--font-size-base);
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--spacing-sm);
    transition: all var(--transition-base);
}

.login-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.4);
}

.login-button:active {
    transform: translateY(0);
}

.button-icon {
    width: 1.25rem;
    height: 1.25rem;
}

.login-footer {
    text-align: center;
    padding-top: var(--spacing-xl);
    border-top: 1px solid var(--border-primary);
}

.login-footer-text {
    font-size: var(--font-size-sm);
    color: var(--text-muted);
}

.footer-link {
    color: var(--color-primary);
    text-decoration: none;
    font-weight: 500;
    transition: color var(--transition-base);
}

.footer-link:hover {
    color: var(--color-secondary);
    text-decoration: underline;
}

.login-info-note {
    background: rgba(59, 130, 246, 0.1);
    border: 1px solid rgba(59, 130, 246, 0.3);
    border-radius: var(--radius-lg);
    padding: var(--spacing-lg);
    margin-bottom: var(--spacing-xl);
    display: flex;
    gap: var(--spacing-md);
}

.info-icon {
    width: 1.5rem;
    height: 1.5rem;
    color: var(--color-primary);
    flex-shrink: 0;
    margin-top: 0.125rem;
}

.info-content {
    flex: 1;
}

.info-title {
    font-size: var(--font-size-base);
    font-weight: 600;
    color: var(--text-primary);
    margin: 0 0 var(--spacing-xs) 0;
}

.info-text {
    font-size: var(--font-size-sm);
    color: var(--text-secondary);
    line-height: 1.6;
    margin: 0 0 var(--spacing-sm) 0;
}

.info-text:last-child {
    margin-bottom: 0;
}

.info-link {
    color: var(--color-primary);
    text-decoration: none;
    font-weight: 500;
    transition: all var(--transition-base);
}

.info-link:hover {
    color: var(--color-secondary);
    text-decoration: underline;
}
</style>
@endsection
