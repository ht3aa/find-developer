@extends('layouts.app')

@section('title', 'Pricing Plans')
@section('seo_title', 'Pricing Plans - FindDeveloper | Developer & Student Plans')
@section('seo_description', 'Choose the perfect plan for developers or students. Free developer profiles, Pro plans, Premium plans, and student portfolio packages. Affordable pricing in IQD.')
@section('seo_keywords', 'developer plans, pricing plans, student portfolio, developer subscription, pro plan, premium plan, portfolio packages, developer pricing')

@section('content')
    <style>
        .pricing-button-hr:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.5);
        }
        .pricing-card-hr:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 35px -5px rgba(99, 102, 241, 0.4);
        }
        .pricing-card-hr {
            transition: all 0.3s ease;
        }
    </style>
    <div class="pricing-container">
        <!-- Developer Plans Section -->
        <div class="pricing-section">
            <div class="pricing-header">
                <h1 class="pricing-title">Developer Plans</h1>
            </div>

            <div class="pricing-cards">
            <!-- Hosting Plan (Subdomain - Free) -->
            <div class="pricing-card pricing-card-hosting" style="position: relative; border: 2px solid var(--color-primary); background: linear-gradient(135deg, rgba(99, 102, 241, 0.05) 0%, rgba(139, 92, 246, 0.05) 100%); box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.3);">
                <div class="pricing-card-header">
                    <h3 class="pricing-card-title" style="color: var(--color-primary); font-size: 2rem; font-weight: 700;">Hosting (Subdomain)</h3>
                    <div class="pricing-card-price-wrapper">
                        <div class="pricing-card-price">
                            <span class="pricing-price-amount" style="font-size: 2.5rem; font-weight: 800; background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Free</span>
                        </div>
                    </div>
                </div>
                <div class="pricing-card-body">
                    <ul class="pricing-features">
                        <li class="pricing-feature" style="padding: 1rem 0; border-bottom: 1px solid rgba(99, 102, 241, 0.1);">
                            <svg class="pricing-feature-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 1.5rem; height: 1.5rem; color: var(--color-primary); margin-right: 0.75rem;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span style="font-weight: 500; color: var(--text-secondary);">Hosting Your Portfolio with a subdomain (e.g., yourname.find-developer.com or <a href="https://hasan-tahseen.find-developer.com/" target="_blank" rel="noopener noreferrer" style="color: var(--color-primary); text-decoration: underline;">hasan-tahseen.find-developer.com</a>)</span>
                        </li>
                        <li class="pricing-feature" style="padding: 1rem 0;">
                            <svg class="pricing-feature-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 1.5rem; height: 1.5rem; color: var(--color-primary); margin-right: 0.75rem;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span style="font-weight: 500; color: var(--text-secondary);">Support for any problem you encounter</span>
                        </li>
                    </ul>
                    <a href="mailto:{{ config('app.contact_email') }}?subject=Hosting%20Plan%20Subscription&body=Hello,%20I%20would%20like%20to%20subscribe%20to%20the%20Hosting%20plan" 
                       target="_blank" 
                       rel="noopener noreferrer"
                       class="pricing-button pricing-button-hr" 
                       style="display: block; width: 100%; text-align: center; padding: 1rem 2rem; background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 100%); color: white; border: none; border-radius: 0.75rem; font-size: 1rem; font-weight: 600; text-decoration: none; margin-top: 1.5rem; transition: all 0.3s ease; box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.4);">
                        Contact Us
                    </a>
                </div>
            </div>

            <!-- Hosting with .iq Domain Plan -->
            <div class="pricing-card pricing-card-hosting" style="position: relative; border: 2px solid var(--color-primary); background: linear-gradient(135deg, rgba(99, 102, 241, 0.05) 0%, rgba(139, 92, 246, 0.05) 100%); box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.3);">
                <div class="pricing-card-header">
                    <h3 class="pricing-card-title" style="color: var(--color-primary); font-size: 2rem; font-weight: 700;">Hosting with .iq Domain</h3>
                    <div class="pricing-card-price-wrapper">
                        <div class="pricing-card-price" style="margin-bottom: 0.25rem;">
                            <span class="pricing-price-amount" style="font-size: 1.5rem; font-weight: 800; background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">80,000 IQD / year</span>
                            <span style="font-size: 1rem; color: var(--text-muted); margin-left: 0.25rem;">hosting</span>
                        </div>
                        <div style="font-size: 0.875rem; color: var(--text-muted);">+ domain from <strong style="color: var(--text-secondary);">50,000 IQD / year</strong> (depends on name)</div>
                    </div>
                </div>
                <div class="pricing-card-body">
                    <ul class="pricing-features">
                        <li class="pricing-feature" style="padding: 1rem 0; border-bottom: 1px solid rgba(99, 102, 241, 0.1);">
                            <svg class="pricing-feature-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 1.5rem; height: 1.5rem; color: var(--color-primary); margin-right: 0.75rem;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span style="font-weight: 500; color: var(--text-secondary);">Hosting Your Portfolio with a custom domain </span>
                        </li>
                        <li class="pricing-feature" style="padding: 1rem 0; border-bottom: 1px solid rgba(99, 102, 241, 0.1);">
                            <svg class="pricing-feature-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 1.5rem; height: 1.5rem; color: var(--color-primary); margin-right: 0.75rem;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span style="font-weight: 500; color: var(--text-secondary);">Custom domain ending with <strong>.iq</strong> like <strong>yourname.iq</strong> (price from 50,000 IQD / year, depends on domain name). <em style="font-size: 0.875rem; color: var(--text-muted);">Provided by <a href="https://www.dev-house.com" target="_blank" rel="noopener noreferrer" style="color: var(--color-primary); text-decoration: underline;"><span dir="rtl">شركة بيت التطوير العراقي للتسويق للدعاية و الاعلان و المسجل المعتمد لدى هيئة الاعلام و الاتصالات لحجز النطاقات</span></a>.</em></span>
                        </li>
                        <li class="pricing-feature" style="padding: 1rem 0;">
                            <svg class="pricing-feature-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 1.5rem; height: 1.5rem; color: var(--color-primary); margin-right: 0.75rem;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span style="font-weight: 500; color: var(--text-secondary);">Support for any problem you encounter</span>
                        </li>
                    </ul>
                    <a href="mailto:{{ config('app.contact_email') }}?subject=Hosting%20with%20.iq%20Domain%20Plan%20Subscription&body=Hello,%20I%20would%20like%20to%20subscribe%20to%20the%20Hosting%20with%20.iq%20Domain%20plan" 
                       target="_blank" 
                       rel="noopener noreferrer"
                       class="pricing-button pricing-button-hr" 
                       style="display: block; width: 100%; text-align: center; padding: 1rem 2rem; background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 100%); color: white; border: none; border-radius: 0.75rem; font-size: 1rem; font-weight: 600; text-decoration: none; margin-top: 1.5rem; transition: all 0.3s ease; box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.4);">
                        Contact Us
                    </a>
                </div>
            </div>

            {{-- <!-- Free Plan -->
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
                    <a href="mailto:{{ config('app.contact_email') }}?subject=Pro%20Plan%20Subscription&body=Hello,%20I%20would%20like%20to%20subscribe%20to%20the%20Pro%20plan" 
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
                        <li class="pricing-feature">
                            <svg class="pricing-feature-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>We will provide a developer projects page for you only</span>
                        </li>
                    </ul>
                    <a href="mailto:{{ config('app.contact_email') }}?subject=Premium%20Plan%20Subscription&body=Hello,%20I%20would%20like%20to%20subscribe%20to%20the%20Premium%20plan" 
                       target="_blank" 
                       class="pricing-button pricing-button-premium">
                        Contact Us
                    </a>
                </div>
            </div> --}}
        </div>
        </div>

        <!-- HR Plans Section -->
        <div class="pricing-section">
            <div class="pricing-header">
                <h1 class="pricing-title">HR Plans</h1>
                <p class="pricing-subtitle">For Recruiters and HR Professionals</p>
            </div>

            <div class="pricing-cards">
                <!-- HR Plan -->
                <div class="pricing-card pricing-card-hr" style="position: relative; border: 2px solid var(--color-primary); background: linear-gradient(135deg, rgba(99, 102, 241, 0.05) 0%, rgba(139, 92, 246, 0.05) 100%); box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.3);">
                    <div class="pricing-card-badge" style="position: absolute; top: 1rem; right: 1rem; background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 100%); color: white; padding: 0.5rem 1rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.4);">
                        Professional
                    </div>
                    <div class="pricing-card-header">
                        <h3 class="pricing-card-title" style="color: var(--color-primary); font-size: 2rem; font-weight: 700;">HR</h3>
                        <div class="pricing-card-price-wrapper">
                            <div class="pricing-price-original-container" style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                                <span class="pricing-price-original" style="font-size: 1rem; color: var(--text-muted); text-decoration: line-through; font-weight: 500;">100,000 IQD</span>
                                <span class="pricing-discount-badge" style="background: linear-gradient(135deg, #ef4444 0%, #f97316 100%); color: white; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; box-shadow: 0 2px 4px -1px rgba(239, 68, 68, 0.4);">50% OFF</span>
                            </div>
                            <div class="pricing-card-price">
                                <span class="pricing-price-amount" style="font-size: 2.5rem; font-weight: 800; background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">50,000</span>
                                <span class="pricing-price-currency" style="font-size: 1.25rem; font-weight: 600; color: var(--text-tertiary);">IQD</span>
                                <span class="pricing-price-period" style="font-size: 1rem; color: var(--text-muted);">/month</span>
                            </div>
                        </div>
                    </div>
                    <div class="pricing-card-body">
                        <ul class="pricing-features">
                            <li class="pricing-feature" style="padding: 1rem 0; border-bottom: 1px solid rgba(99, 102, 241, 0.1);">
                                <svg class="pricing-feature-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 1.5rem; height: 1.5rem; color: var(--color-primary); margin-right: 0.75rem;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span style="font-weight: 500; color: var(--text-secondary);">The user will have access to the dashboard as HR user And all the features that the admin dashboard provides</span>
                            </li>
                            <li class="pricing-feature" style="padding: 1rem 0;">
                                <svg class="pricing-feature-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 1.5rem; height: 1.5rem; color: var(--color-primary); margin-right: 0.75rem;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span style="font-weight: 500; color: var(--text-secondary);">Can see the salary and any hidden fields in the developers card</span>
                            </li>
                        </ul>
                        <a href="mailto:{{ config('app.contact_email') }}?subject=HR%20Plan%20Subscription&body=Hello,%20I%20would%20like%20to%20subscribe%20to%20the%20HR%20plan" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           class="pricing-button pricing-button-hr" 
                           style="display: block; width: 100%; text-align: center; padding: 1rem 2rem; background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 100%); color: white; border: none; border-radius: 0.75rem; font-size: 1rem; font-weight: 600; text-decoration: none; margin-top: 1.5rem; transition: all 0.3s ease; box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.4);">
                            Contact Us
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Students Plans Section -->
        <div class="pricing-section">
            <div class="pricing-header">
                <h1 class="pricing-title">Students Plans</h1>
                <p class="pricing-subtitle">Create Your Portfolio</p>
            </div>

            <div class="pricing-cards">
                <!-- Basic Portfolio Plan -->
                <div class="pricing-card pricing-card-student-basic">
                    <div class="pricing-card-header">
                        <h3 class="pricing-card-title">Basic Portfolio</h3>
                        <div class="pricing-card-price-wrapper">
                            <div class="pricing-price-original-container">
                                <span class="pricing-price-original">100,000 IQD</span>
                                <span class="pricing-discount-badge">25% OFF</span>
                            </div>
                            <div class="pricing-card-price">
                                <span class="pricing-price-amount">75,000</span>
                                <span class="pricing-price-currency">IQD</span>
                            </div>
                        </div>
                    </div>
                    <div class="pricing-card-body">
                        <ul class="pricing-features">
                            <li class="pricing-feature">
                                <svg class="pricing-feature-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>An AI-designed portfolio managed by an expert developer</span>
                            </li>
                            <li class="pricing-feature">
                                <svg class="pricing-feature-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>A domain with yourname.find-domain.com suffix</span>
                            </li>
                        </ul>
                        <a href="mailto:{{ config('app.contact_email') }}?subject=Basic%20Portfolio%20Plan%20Subscription&body=Hello,%20I%20would%20like%20to%20subscribe%20to%20the%20Basic%20Portfolio%20plan" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           class="pricing-button pricing-button-student-basic">
                            Contact Us
                        </a>
                    </div>
                </div>

                <!-- Standard Portfolio Plan -->
                <div class="pricing-card pricing-card-student-standard">
                    <div class="pricing-card-badge">Popular</div>
                    <div class="pricing-card-header">
                        <h3 class="pricing-card-title">Standard Portfolio</h3>
                        <div class="pricing-card-price-wrapper">
                            <div class="pricing-price-original-container">
                                <span class="pricing-price-original">200,000 IQD</span>
                                <span class="pricing-discount-badge">25% OFF</span>
                            </div>
                            <div class="pricing-card-price">
                                <span class="pricing-price-amount">150,000</span>
                                <span class="pricing-price-currency">IQD</span>
                            </div>
                        </div>
                    </div>
                    <div class="pricing-card-body">
                        <ul class="pricing-features">
                            <li class="pricing-feature">
                                <svg class="pricing-feature-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>A simple UI/UX design done by an expert designer</span>
                            </li>
                            <li class="pricing-feature">
                                <svg class="pricing-feature-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>A front end developer who will programme the design</span>
                            </li>
                            <li class="pricing-feature">
                                <svg class="pricing-feature-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>A domain with yourname.find-domain.com suffix</span>
                            </li>
                        </ul>
                        <a href="mailto:{{ config('app.contact_email') }}?subject=Standard%20Portfolio%20Plan%20Subscription&body=Hello,%20I%20would%20like%20to%20subscribe%20to%20the%20Standard%20Portfolio%20plan" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           class="pricing-button pricing-button-student-standard">
                            Contact Us
                        </a>
                    </div>
                </div>

                <!-- Premium Portfolio Plan -->
                <div class="pricing-card pricing-card-student-premium">
                    <div class="pricing-card-badge">Best Value</div>
                    <div class="pricing-card-header">
                        <h3 class="pricing-card-title">Premium Portfolio</h3>
                        <div class="pricing-card-price-wrapper">
                            <div class="pricing-price-original-container">
                                <span class="pricing-price-original">400,000 IQD</span>
                                <span class="pricing-discount-badge">25% OFF</span>
                            </div>
                            <div class="pricing-card-price">
                                <span class="pricing-price-amount">300,000</span>
                                <span class="pricing-price-currency">IQD</span>
                            </div>
                        </div>
                    </div>
                    <div class="pricing-card-body">
                        <ul class="pricing-features">
                            <li class="pricing-feature">
                                <svg class="pricing-feature-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>A detailed and unique UI/UX design done by expert designer</span>
                            </li>
                            <li class="pricing-feature">
                                <svg class="pricing-feature-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>A front end developer who will programme the design</span>
                            </li>
                            <li class="pricing-feature">
                                <svg class="pricing-feature-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>A custom domain specific for you</span>
                            </li>
                        </ul>
                        <a href="mailto:{{ config('app.contact_email') }}?subject=Premium%20Portfolio%20Plan%20Subscription&body=Hello,%20I%20would%20like%20to%20subscribe%20to%20the%20Premium%20Portfolio%20plan" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           class="pricing-button pricing-button-student-premium">
                            Contact Us
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
