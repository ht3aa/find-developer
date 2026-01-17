@extends('layouts.app')

@section('title', 'Pricing Plans')

@section('content')
    <div class="pricing-container">
        <div class="pricing-header">
            <h1 class="pricing-title">Choose Your Plan</h1>
            <p class="pricing-subtitle">Select the perfect plan for your needs</p>
        </div>

        <div class="pricing-cards">
            <!-- Free Plan -->
            <div class="pricing-card pricing-card-free">
                <div class="pricing-card-header">
                    <h3 class="pricing-card-title">Free</h3>
                    <div class="pricing-card-price">
                        <span class="pricing-price-amount">0</span>
                        <span class="pricing-price-currency">IQD</span>
                    </div>
                </div>
                <div class="pricing-card-body">
                    <ul class="pricing-features">
                        <li class="pricing-feature">
                            <svg class="pricing-feature-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Create a developer card to be displayed in the website</span>
                        </li>
                    </ul>
                    <a href="{{ route('register') }}" class="pricing-button pricing-button-free">
                        Get Started
                    </a>
                </div>
            </div>

            <!-- Pro Plan -->
            <div class="pricing-card pricing-card-pro">
                <div class="pricing-card-badge">Popular</div>
                <div class="pricing-card-header">
                    <h3 class="pricing-card-title">Pro</h3>
                    <div class="pricing-card-price-wrapper">
                        <div class="pricing-price-original-container">
                            <span class="pricing-price-original">10,000 IQD</span>
                            <span class="pricing-discount-badge">50% OFF</span>
                        </div>
                        <div class="pricing-card-price">
                            <span class="pricing-price-amount">5,000</span>
                            <span class="pricing-price-currency">IQD</span>
                            <span class="pricing-price-period">/month</span>
                        </div>
                    </div>
                </div>
                <div class="pricing-card-body">
                    <ul class="pricing-features">
                        <li class="pricing-feature">
                            <svg class="pricing-feature-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Be in the pro section of the website</span>
                        </li>
                    </ul>
                    <a href="https://wa.me/9647708246418?text=Hello,%20I%20would%20like%20to%20subscribe%20to%20the%20Pro%20plan" 
                       target="_blank" 
                       class="pricing-button pricing-button-pro">
                        Contact Us
                    </a>
                </div>
            </div>

            <!-- Premium Plan -->
            <div class="pricing-card pricing-card-premium">
                <div class="pricing-card-badge">Best Value</div>
                <div class="pricing-card-header">
                    <h3 class="pricing-card-title">Premium</h3>
                    <div class="pricing-card-price-wrapper">
                        <div class="pricing-price-original-container">
                            <span class="pricing-price-original">25,000 IQD</span>
                            <span class="pricing-discount-badge">60% OFF</span>
                        </div>
                        <div class="pricing-card-price">
                            <span class="pricing-price-amount">10,000</span>
                            <span class="pricing-price-currency">IQD</span>
                            <span class="pricing-price-period">/month</span>
                        </div>
                    </div>
                </div>
                <div class="pricing-card-body">
                    <ul class="pricing-features">
                        <li class="pricing-feature">
                            <svg class="pricing-feature-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Be in the premium section of the website</span>
                        </li>
                        <li class="pricing-feature">
                            <svg class="pricing-feature-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Have user account to the admin dashboard</span>
                        </li>
                        <li class="pricing-feature">
                            <svg class="pricing-feature-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Can display 6 projects directly into your card</span>
                        </li>
                    </ul>
                    <a href="https://wa.me/9647708246418?text=Hello,%20I%20would%20like%20to%20subscribe%20to%20the%20Premium%20plan" 
                       target="_blank" 
                       class="pricing-button pricing-button-premium">
                        Contact Us
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
