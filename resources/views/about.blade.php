@extends('layouts.app')

@section('title', 'About Us')
@section('seo_title', 'About Us - FindDeveloper | Connecting Developers with Opportunities')
@section('seo_description', 'Learn about FindDeveloper - a platform connecting skilled developers with clients and opportunities. Discover our mission and services.')
@section('seo_keywords', 'about finddeveloper, developer platform, connect developers, developer marketplace, find developer iraq')

@section('content')
    <div class="about-container">
        <div class="about-header">
            <h1 class="about-title">About Us</h1>
            <p class="about-subtitle">Connecting talented developers with opportunities</p>
        </div>

        <div class="about-content">
            <div class="about-section">
                <h2 class="about-section-title">Our Mission</h2>
                <p class="about-section-text">
                    FindDeveloper is a platform dedicated to connecting skilled developers with clients and opportunities. 
                    We provide a space where developers can showcase their talents, skills, and projects, making it easier 
                    for businesses and individuals to find the perfect developer for their needs.
                </p>
            </div>

            <div class="about-section">
                <h2 class="about-section-title">What We Offer</h2>
                <div class="about-features">
                    <div class="about-feature-item">
                        <svg class="about-feature-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <div>
                            <h3 class="about-feature-title">Developer Profiles</h3>
                            <p class="about-feature-text">Comprehensive developer profiles showcasing skills, experience, and portfolios</p>
                        </div>
                    </div>

                    <div class="about-feature-item">
                        <svg class="about-feature-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <div>
                            <h3 class="about-feature-title">Advanced Search</h3>
                            <p class="about-feature-text">Powerful search and filtering tools to find developers by skills, location, and experience</p>
                        </div>
                    </div>

                    <div class="about-feature-item">
                        <svg class="about-feature-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <div>
                            <h3 class="about-feature-title">Verified Profiles</h3>
                            <p class="about-feature-text">All developer profiles are verified and approved by our team</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="about-section">
                <h2 class="about-section-title">Contact Us</h2>
                <p class="about-section-text">
                    Have questions or need assistance? We're here to help! Reach out to us via email:
                </p>
                <div class="contact-methods">
                    <a href="mailto:ht3aa2001@gmail.com" class="contact-method contact-method-email">
                        <div class="contact-method-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="contact-method-content">
                            <h3 class="contact-method-title">Email</h3>
                            <p class="contact-method-value">ht3aa2001@gmail.com</p>
                        </div>
                        <svg class="contact-method-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
