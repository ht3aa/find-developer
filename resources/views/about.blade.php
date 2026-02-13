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
                    Have questions or need assistance? We're here to help! Reach out to us via email or social media:
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
                    <a href="https://www.instagram.com/find.developer?igsh=OGY0MXF3dGNrNnJx" target="_blank" rel="noopener noreferrer" class="contact-method contact-method-instagram">
                        <div class="contact-method-icon">
                            <svg fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                            </svg>
                        </div>
                        <div class="contact-method-content">
                            <h3 class="contact-method-title">Instagram</h3>
                            <p class="contact-method-value">@find.developer</p>
                        </div>
                        <svg class="contact-method-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                    <a href="https://t.me/finddevelopers" target="_blank" rel="noopener noreferrer" class="contact-method contact-method-telegram">
                        <div class="contact-method-icon">
                            <svg fill="currentColor" viewBox="0 0 24 24">
                                <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                            </svg>
                        </div>
                        <div class="contact-method-content">
                            <h3 class="contact-method-title">Telegram</h3>
                            <p class="contact-method-value">@finddevelopers</p>
                        </div>
                        <svg class="contact-method-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                    <a href="https://www.linkedin.com/company/find-developers/?viewAsMember=true" target="_blank" rel="noopener noreferrer" class="contact-method contact-method-linkedin">
                        <div class="contact-method-icon">
                            <svg fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                            </svg>
                        </div>
                        <div class="contact-method-content">
                            <h3 class="contact-method-title">LinkedIn</h3>
                            <p class="contact-method-value">Find Developers</p>
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
